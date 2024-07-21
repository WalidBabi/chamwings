import csv
import random
from datetime import datetime, timedelta
from faker import Faker

# Initialize Faker for generating fake data
fake = Faker()

# Constants
num_records = 1000
titles = ['Mr', 'Ms', 'Mrs']
countries = ['Syria', 'Lebanon', 'United Arab Emirates', 'Russia', 'Iraq']

# Helper function to calculate age from date of birth
def calculate_age(birthdate):
    today = datetime.today()
    return today.year - birthdate.year - ((today.month, today.day) < (birthdate.month, birthdate.day))

# Generate data
data = []
for _ in range(num_records):
    title = random.choice(titles)
    first_name = fake.first_name()
    last_name = fake.last_name()
    email = fake.unique.email()
    password = fake.password()
    date_of_birth = fake.date_of_birth(minimum_age=18, maximum_age=90)
    address = fake.address()
    city = fake.city()
    mobile = fake.phone_number()
    age = calculate_age(date_of_birth)
    gender = random.choice(['Male', 'Female', None])
    nationality = fake.country()
    country_of_residence = random.choice(countries)
    
    data.append([
        _, title, first_name, last_name, email, password, date_of_birth.strftime('%Y-%m-%d'), 
        address, city, mobile, age, gender, nationality, country_of_residence, 
        datetime.now().strftime('%Y-%m-%d %H:%M:%S'), datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    ])

# Write to CSV
with open('users_profiles.csv', 'w', newline='') as file:
    writer = csv.writer(file)
    writer.writerow([
        'user_profile_id', 'title', 'first_name', 'last_name', 'email', 'password', 'date_of_birth', 
        'address', 'city', 'mobile', 'age', 'gender', 'nationality', 'country_of_residence', 'created_at', 'updated_at'
    ])
    writer.writerows(data)
