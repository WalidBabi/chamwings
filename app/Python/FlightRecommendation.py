import mysql.connector
import pandas as pd
import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import linear_kernel, cosine_similarity
from datetime import datetime
import sys

# Establish database connection
connection = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="chamwings"
)
cursor = connection.cursor()

# Fetch enhanced flight data from the database
cursor.execute("""
    SELECT f.flight_id, a1.city as departure_city, a1.country as departure_country,
           a2.city as arrival_city, a2.country as arrival_country, f.price,
           c.class_name, c.price_rate, c.weight_allowed, c.number_of_meals,
           sd.departure_date, st.departure_time, st.duration
    FROM flights f
    JOIN airports a1 ON f.departure_airport = a1.airport_id
    JOIN airports a2 ON f.arrival_airport = a2.airport_id
    JOIN classes c ON f.airplane_id = c.airplane_id
    JOIN schedule_days sd ON f.flight_id = sd.flight_id
    JOIN schedule_times st ON sd.schedule_day_id = st.schedule_day_id;
""")
flight_data = cursor.fetchall()

# Fetch user preferences and travel requirements
cursor.execute("""
    SELECT r.passenger_id, r.flight_id, r.round_trip, r.created_at,
           tr.age, tr.nationality, tr.country_of_residence
    FROM reservations r
    JOIN passengers p ON r.passenger_id = p.passenger_id
    JOIN travel_requirements tr ON p.travel_requirement_id = tr.travel_requirement_id;
""")
user_data = cursor.fetchall()

# Load data into pandas DataFrames
flight_df = pd.DataFrame(flight_data, columns=['flight_id', 'departure_city', 'departure_country', 'arrival_city', 'arrival_country', 'price', 'class_name', 'price_rate', 'weight_allowed', 'number_of_meals', 'departure_date', 'departure_time', 'duration'])
user_df = pd.DataFrame(user_data, columns=['user_id', 'flight_id', 'round_trip', 'created_at', 'age', 'nationality', 'country_of_residence'])

# Preprocess the data
flight_df['departure_datetime'] = pd.to_datetime(flight_df['departure_date'].astype(str) + ' ' + flight_df['departure_time'].astype(str))
flight_df['duration'] = pd.to_timedelta(flight_df['duration'])

# Create a more detailed route description
flight_df['detailed_route'] = (
    flight_df['departure_city'] + " " + flight_df['departure_country'] + " to " +
    flight_df['arrival_city'] + " " + flight_df['arrival_country'] + " | " +
    "Class: " + flight_df['class_name'] + " | " +
    "Price: " + flight_df['price'].astype(str) + " | " +
    "Departure: " + flight_df['departure_datetime'].astype(str) + " | " +
    "Duration: " + flight_df['duration'].astype(str)
)

# TF-IDF Vectorization
tfidf = TfidfVectorizer(stop_words='english')
tfidf_matrix = tfidf.fit_transform(flight_df['detailed_route'])

# Compute Cosine Similarity Matrix
cosine_sim = linear_kernel(tfidf_matrix, tfidf_matrix)

# Content-Based Recommendations Function
def get_content_recommendations(user_id, cosine_sim=cosine_sim):
    # Get user's flight history
    user_flights = user_df[user_df['user_id'] == user_id]['flight_id'].tolist()
    
    # If user has no flight history, return the 5 cheapest flights
    if not user_flights:
        return flight_df.sort_values('price', ascending=True).head(5)
    
    user_info = user_df[user_df['user_id'] == user_id].iloc[0]
    
    # Calculate the mean similarity of each flight to the user's booked flights
    sim_scores = cosine_sim[:, flight_df['flight_id'].isin(user_flights)].mean(axis=1)
    
    # Apply time-based weighting
    latest_booking = user_df[user_df['user_id'] == user_id]['created_at'].max()
    time_weights = user_df[user_df['user_id'] == user_id].apply(lambda x: 1 / (1 + (latest_booking - pd.to_datetime(x['created_at'])).days) if pd.notnull(x['created_at']) else 0, axis=1)
    sim_scores *= time_weights.mean()
    
    # Apply age-based weighting
    age_factor = 1 / (1 + abs(flight_df['price'] - user_info['age']))
    sim_scores *= age_factor
    
    # Get top similar flights (excluding already booked flights)
    top_indices = sim_scores.argsort()[::-1]
    recommended_indices = [i for i in top_indices if flight_df.iloc[i]['flight_id'] not in user_flights][:10]
    
    recommendations = flight_df.iloc[recommended_indices]
    # print(recommendations.head(5))
    return recommendations.head(5)

# Collaborative Filtering (with enhanced user-flight matrix)
all_user_ids = user_df['user_id'].unique()
user_flight_matrix = user_df.pivot_table(index='user_id', columns='flight_id', values='created_at', aggfunc='count', fill_value=0)
user_flight_matrix = user_flight_matrix.reindex(all_user_ids, fill_value=0)

user_similarity = cosine_similarity(user_flight_matrix)

def get_collaborative_recommendations(user_id, user_similarity=user_similarity):
    # If user is not in the matrix, fall back to content-based recommendations
    if user_id not in user_flight_matrix.index:
        return get_content_recommendations(user_id)['flight_id'].tolist()[:5]
    
    user_index = user_flight_matrix.index.get_loc(user_id)
    sim_scores = list(enumerate(user_similarity[user_index]))
    sim_scores = sorted(sim_scores, key=lambda x: x[1], reverse=True)
    user_indices = [i[0] for i in sim_scores[1:6]]
    
    similar_users_flights = user_flight_matrix.iloc[user_indices].sum().sort_values(ascending=False)
    # print(similar_users_flights.index[:5].tolist())
    return similar_users_flights.index[:5].tolist()

# Hybrid Recommendation System
def hybrid_recommendation_system(user_id):
    content_recommendations = get_content_recommendations(user_id)
    collaborative_recommendations = get_collaborative_recommendations(user_id)

    # Combine recommendations
    hybrid_recommendations = pd.concat([
        content_recommendations['flight_id'].head(3),
        pd.Series(collaborative_recommendations[:2])
    ]).drop_duplicates()

    final_recommendations = flight_df[flight_df['flight_id'].isin(hybrid_recommendations)]
    
    # Sort recommendations based on a combined score of similarity and recency
    final_recommendations = final_recommendations.copy()
    final_recommendations.loc[:, 'rec_score'] = final_recommendations.apply(
        lambda row: cosine_sim[row.name, flight_df['flight_id'] == row['flight_id']].mean() *
                    (1 / (1 + (datetime.now() - row['departure_datetime']).days)),
        axis=1
    )
    # print("reccccc",final_recommendations.sort_values('rec_score', ascending=False))
    return final_recommendations.sort_values('rec_score', ascending=False)

# Evaluation function
def evaluate_recommendations(user_id, recommended_flights, actual_bookings):
    true_positives = set(recommended_flights['flight_id']) & set(actual_bookings)
    false_positives = set(recommended_flights['flight_id']) - set(actual_bookings)
    false_negatives = set(actual_bookings) - set(recommended_flights['flight_id'])
    
    precision = len(true_positives) / (len(true_positives) + len(false_positives)) if (len(true_positives) + len(false_positives)) > 0 else 0
    recall = len(true_positives) / (len(true_positives) + len(false_negatives)) if (len(true_positives) + len(false_negatives)) > 0 else 0
    
    f1 = 2 * (precision * recall) / (precision + recall) if (precision + recall) > 0 else 0
    
    return f1

# Function to get actual bookings for a user
def get_actual_bookings(user_id):
    if not connection.is_connected():
        connection.reconnect()

    cursor = connection.cursor()
    query = """
    SELECT DISTINCT flight_id
    FROM reservations
    WHERE passenger_id = %s
    """
    cursor.execute(query, (user_id,))
    bookings = cursor.fetchall()
    cursor.close()
    return [booking[0] for booking in bookings]
  # Main function to get recommended flights
def get_recommended_flights(user_id, user_city):
      # Modify the SQL query to filter flights from the user's city
      flight_query = """
      SELECT f.flight_id, f.price, c.class_name, a1.airport_name AS departure_airport, 
           a1.airport_id AS departure_airport_id, a1.airport_code AS departure_airport_code,
           a2.airport_name AS arrival_airport, a2.airport_id AS arrival_airport_id, 
           a2.airport_code AS arrival_airport_code, f.flight_number, 
           f.departure_terminal, f.arrival_terminal
      FROM flights f
      JOIN airports a1 ON f.departure_airport = a1.airport_id
      JOIN airports a2 ON f.arrival_airport = a2.airport_id
      JOIN classes c ON f.airplane_id = c.airplane_id
      WHERE a1.city = %s
      """
      cursor.execute(flight_query, (user_city,))
      flights = cursor.fetchall()

      recommendation_data = {
          "trip_type": "outbound",
          "departure_flights": []
      }

      for flight in flights:
          flight_data = {
              "flight_id": int(flight[0]),
              "price": float(flight[1]),
              "class_name": str(flight[2]),
              "departure_airport": str(flight[3]),
              "departure_airport_id": str(flight[4]),
              "departure_airport_code": str(flight[5]),
              "arrival_airport": str(flight[6]),
              "arrival_airport_id": str(flight[7]),
              "arrival_airport_code": str(flight[8]),
              "flight_number": str(flight[9]),
              "departure_terminal": str(flight[10]),
              "arrival_terminal": str(flight[11])
          }

          # Get schedule information
          schedule_day_query = """
          SELECT departure_date, arrival_date
          FROM schedule_days
          WHERE flight_id = %s
          """
          cursor.execute(schedule_day_query, (flight[0],))
          schedule_day = cursor.fetchone()
          if schedule_day:
              flight_data.update({
                  "departure_date": schedule_day[0] if schedule_day[0] else None,
                  "arrival_date": schedule_day[1] if schedule_day[1] else None
              })

          schedule_time_query = """
          SELECT departure_time, arrival_time, duration
          FROM schedule_times
          JOIN schedule_days ON schedule_times.schedule_day_id = schedule_days.schedule_day_id
          WHERE schedule_days.flight_id = %s
          """
          cursor.execute(schedule_time_query, (flight[0],))
          schedule_time = cursor.fetchone()
          if schedule_time:
              flight_data.update({
                  "departure_time": schedule_time[0] if schedule_time[0] else None,
                  "arrival_time": schedule_time[1] if schedule_time[1] else None,
                  "duration": str(schedule_time[2]) if schedule_time[2] else None
              })

          recommendation_data["departure_flights"].append(flight_data)

      # Sort recommendations based on price or other criteria
      recommendation_data["departure_flights"].sort(key=lambda x: x["price"])
    #   print("addedflightsssssssss",recommendation_data["departure_flights"][:5])
      return recommendation_data["departure_flights"][:5]  # Return top 5 recommendations
# Main execution
if __name__ == "__main__":
    import json
    if len(sys.argv) > 2:
        user_id = int(sys.argv[1])
        user_city = sys.argv[2]
        
        # Get hybrid recommendations
        hybrid_recs = hybrid_recommendation_system(user_id)
        
        # Filter recommendations based on user's city
        city_filtered_recs = hybrid_recs[hybrid_recs['departure_city'] == user_city]
        # print(city_filtered_recs)
        # If we have less than 5 recommendations, supplement with flights from the user's city
        # if len(city_filtered_recs) < 5:
        #     additional_recs = get_recommended_flights(user_id, user_city)
        #     from pandas import concat

        #     city_filtered_recs = concat([city_filtered_recs, pd.DataFrame(additional_recs)], ignore_index=True).drop_duplicates().head(5)
        # print("add reccccc",city_filtered_recs)
        # Convert to list of dictionaries for JSON serialization
        response_data = city_filtered_recs.to_dict('records')
        
        json_output = json.dumps(response_data, default=str)
        sys.stdout.write(json_output)
        sys.exit(0)
    else:
        error_response = {
            "status": "error",
            "message": "Please provide a user ID and city as arguments."
        }
        sys.stderr.write(json.dumps(error_response))
        sys.exit(1)