import csv
from datetime import datetime
from faker import Faker

# Initialize Faker for generating fake data
fake = Faker()

# Airplane data
airplanes = [
    {"airplane_id": 1, "model": "Boeing 737", "manufacturer": "Boeing", "range": "3000 km"},
    {"airplane_id": 2, "model": "Airbus A320", "manufacturer": "Airbus", "range": "3200 km"},
    {"airplane_id": 3, "model": "Boeing 777", "manufacturer": "Boeing", "range": "5000 km"},
    {"airplane_id": 4, "model": "Airbus A380", "manufacturer": "Airbus", "range": "8000 km"}
]

# Class data
classes = ["Economy", "Business"]
price_rates = {
    "Economy": [100, 200],
    "Business": [300, 400]
}
weight_allowed = {
    "Economy": "20 kg",
    "Business": "30 kg"
}
number_of_meals = {
    "Economy": 1,
    "Business": 2
}
number_of_seats = {
    "Economy": [150, 180],
    "Business": [50, 80]
}

# Generate data
data = []
class_id = 1
for airplane in airplanes:
    for class_name in classes:
        price_rate = fake.random_int(min=price_rates[class_name][0], max=price_rates[class_name][1])
        weight = weight_allowed[class_name]
        meals = number_of_meals[class_name]
        seats = fake.random_int(min=number_of_seats[class_name][0], max=number_of_seats[class_name][1])
        created_at = fake.date_time_between(start_date='-1y', end_date='now').strftime('%Y-%m-%d %H:%M:%S')
        updated_at = created_at

        data.append([
            class_id, airplane["airplane_id"], class_name, price_rate, weight, meals, seats, created_at, updated_at
        ])
        class_id += 1

# Write to CSV
with open('classes.csv', 'w', newline='') as file:
    writer = csv.writer(file)
    writer.writerow([
        'class_id', 'airplane_id', 'class_name', 'price_rate', 'weight_allowed', 'number_of_meals', 'number_of_seats', 'created_at', 'updated_at'
    ])
    writer.writerows(data)
