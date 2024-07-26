import csv
import random
from datetime import datetime
from faker import Faker

# Initialize Faker for generating fake data
fake = Faker()

# Constants
num_records = 1000

# Generate data
data = []
for i in range(num_records):
    user_profile_id = random.randint(1, 1000)  # Assuming user_profile_id ranges from 1 to 1000
    passenger_info_id = random.randint(1, 1000)  # Assuming passenger_info_id ranges from 1 to 1000
    is_traveling = random.choice([True, False])
    created_at = fake.date_time_between(start_date='-1y', end_date='now').strftime('%Y-%m-%d %H:%M:%S')
    updated_at = created_at

    data.append([
        i + 1, user_profile_id, passenger_info_id, is_traveling, created_at, updated_at
    ])

# Write to CSV
with open('passengers.csv', 'w', newline='') as file:
    writer = csv.writer(file)
    writer.writerow([
        'passenger_id', 'user_profile_id', 'passenger_info_id', 'is_traveling', 'created_at', 'updated_at'
    ])
    writer.writerows(data)
