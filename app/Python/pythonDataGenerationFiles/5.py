import csv
import random
from datetime import datetime, timedelta
from faker import Faker

# Initialize Faker for generating fake data
fake = Faker()

# Constants
num_records = 1000

# Generate data
data = []
for i in range(num_records):
    passport = fake.passport_number()
    passport_issued_country = fake.country()
    passport_expiry_date = (datetime.today() + timedelta(days=random.randint(365, 3650))).strftime('%Y-%m-%d')
    mobile_during_travel = fake.phone_number()
    passport_image = fake.file_name(category='image')
    id_number = fake.random_int(min=100000000, max=999999999)
    created_at = fake.date_time_between(start_date='-1y', end_date='now').strftime('%Y-%m-%d %H:%M:%S')
    updated_at = created_at

    data.append([
        i + 1, passport, passport_issued_country, passport_expiry_date, mobile_during_travel, passport_image, id_number, created_at, updated_at
    ])

# Write to CSV
with open('passengers_info.csv', 'w', newline='') as file:
    writer = csv.writer(file)
    writer.writerow([
        'passenger_info_id', 'passport', 'passport_issued_country', 'passport_expiry_date', 'mobile_during_travel', 'passport_image', 'id_number', 'created_at', 'updated_at'
    ])
    writer.writerows(data)
