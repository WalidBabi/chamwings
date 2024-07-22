import csv
import random
from datetime import datetime, timedelta

# Constants
num_records = 1000  # Number of records to generate
airports = list(range(1, 18))  # Airport IDs from 1 to 17
flight_numbers = list(range(1000, 2000))  # Example flight numbers
start_date = datetime.strptime('2024-07-01', '%Y-%m-%d')

# Helper function to generate random times
def random_time():
    return datetime.strptime(f'{random.randint(0, 23)}:{random.randint(0, 59)}', '%H:%M').time()

# Generate data
data = []
for i in range(num_records):
    departure_airport = random.choice(airports)
    arrival_airport = random.choice(airports)
    
    # Ensure either departure or arrival airport has the value 17
    if departure_airport != 17 and arrival_airport != 17:
        if random.choice([True, False]):
            departure_airport = 17
        else:
            arrival_airport = 17
    
    departure_date = start_date + timedelta(days=random.randint(0, 30))
    arrival_date = departure_date + timedelta(days=random.randint(0, 1))  # Arrival date can be the same or next day
    departure_time = random_time()
    arrival_time = (datetime.combine(datetime.today(), departure_time) + timedelta(hours=random.randint(1, 12))).time()
    duration = str(arrival_time.hour - departure_time.hour) + 'h ' + str(arrival_time.minute - departure_time.minute) + 'm'
    number_of_reserved_seats = random.randint(0, 300)
    price = random.randint(50, 500)
    terminal = random.choice(['A', 'B', 'C', 'D'])
    airplane_id = random.randint(1, 50)
    
    flight_number = flight_numbers.pop()  # Ensure unique flight numbers

    data.append([
        i + 1, departure_airport, arrival_airport, airplane_id, flight_number, 
        departure_date.strftime('%Y-%m-%d'), arrival_date.strftime('%Y-%m-%d'), 
        departure_time.strftime('%H:%M:%S'), arrival_time.strftime('%H:%M:%S'), 
        duration, number_of_reserved_seats, price, terminal
    ])

# Write to CSV
with open('flights.csv', 'w', newline='') as file:
    writer = csv.writer(file)
    writer.writerow([
        'flight_id', 'departure_airport', 'arrival_airport', 'airplane_id', 'flight_number', 
        'departure_date', 'arrival_date', 'departure_time', 'arrival_time', 'duration', 
        'number_of_reserved_seats', 'price', 'terminal'
    ])
    writer.writerows(data)
