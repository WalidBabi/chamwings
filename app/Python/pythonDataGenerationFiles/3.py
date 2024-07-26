import csv
import random
from datetime import datetime, timedelta
from faker import Faker

# Initialize Faker for generating fake data
fake = Faker()

# Constants
num_records = 1000
statuses = ['Confirmed', 'Pending', 'Canceled']

# Generate data
data = []
for i in range(num_records):
    passenger_id = random.randint(1, 1000)  # Assuming passenger_id ranges from 1 to 1000
    flight_id = random.randint(1, 1000)  # Assuming flight_id ranges from 1 to 500
    number_of_passengers = random.randint(1, 5)  # Random number of passengers between 1 and 5
    reservation_date = fake.date_between(start_date='-1y', end_date='today')
    round_trip = random.choice([True, False])
    status = random.choice(statuses)
    created_at = fake.date_time_between(start_date='-1y', end_date='now').strftime('%Y-%m-%d %H:%M:%S')
    updated_at = created_at

    data.append([
        i + 1, passenger_id, flight_id, number_of_passengers, 
        reservation_date.strftime('%Y-%m-%d'), round_trip, status, created_at, updated_at
    ])

# Write to CSV
with open('reservations.csv', 'w', newline='') as file:
    writer = csv.writer(file)
    writer.writerow([
        'reservation_id', 'passenger_id', 'flight_id', 'number_of_passengers', 
        'reservation_date', 'round_trip', 'status', 'created_at', 'updated_at'
    ])
    writer.writerows(data)
