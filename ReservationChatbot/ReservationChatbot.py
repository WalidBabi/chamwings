import os
# os.environ['LANGCHAIN_TRACING_V2'] = 'true'
# os.environ['LANGCHAIN_API_KEY'] = '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'
from langchain import OpenAI, LLMChain, PromptTemplate
from langchain.memory import ConversationBufferMemory
from langchain.tools import Tool
from langchain.agents import initialize_agent, AgentType
import sqlalchemy
from sqlalchemy import create_engine, text
import re
import json

# Database connection
DATABASE_URL = os.getenv("DATABASE_URL", "mysql://root:@localhost/chamwings")
engine = create_engine(DATABASE_URL)

def search_flights(query):
    departure_airport, arrival_airport = query.split('-')
    try:
        with engine.connect() as connection:
            result = connection.execute(text("""
                SELECT 
                    f.flight_id, f.flight_number, f.departure_date, f.arrival_date, 
                    f.departure_time, f.arrival_time, f.duration, f.price, f.terminal,
                    f.number_of_reserved_seats,
                    dep.airport_name AS departure_airport_name, 
                    dep.city AS departure_city, 
                    dep.country AS departure_country,
                    arr.airport_name AS arrival_airport_name, 
                    arr.city AS arrival_city, 
                    arr.country AS arrival_country,
                    a.model AS airplane_model, 
                    a.manufacturer AS airplane_manufacturer, 
                    a.range AS airplane_range
                FROM flights f
                JOIN airports dep ON f.departure_airport = dep.airport_id
                JOIN airports arr ON f.arrival_airport = arr.airport_id
                JOIN airplanes a ON f.airplane_id = a.airplane_id
                WHERE dep.airport_name = :departure_airport 
                AND arr.airport_name = :arrival_airport
                ORDER BY f.departure_date, f.departure_time
            """), {"departure_airport": departure_airport, "arrival_airport": arrival_airport})
            
            flights = result.fetchall()
            
            if flights:
                flight_info = []
                for f in flights:
                    info = f"Flight {f.flight_number}:\n"
                    info += f"  Departure: {f.departure_airport_name} ({f.departure_city}, {f.departure_country})\n"
                    info += f"    Date: {f.departure_date}, Time: {f.departure_time}, Terminal: {f.terminal}\n"
                    info += f"  Arrival: {f.arrival_airport_name} ({f.arrival_city}, {f.arrival_country})\n"
                    info += f"    Date: {f.arrival_date}, Time: {f.arrival_time}\n"
                    info += f"  Duration: {f.duration}\n"
                    info += f"  Price: ${f.price}\n"
                    info += f"  Aircraft: {f.airplane_manufacturer} {f.airplane_model} (Range: {f.airplane_range})\n"
                    info += f"  Available Seats: {100 - f.number_of_reserved_seats}\n"  # Assuming 100 seats per plane
                    flight_info.append(info)
                return f"Flights from {departure_airport} to {arrival_airport}:\n\n" + "\n".join(flight_info)
            else:
                return f"No flights found from {departure_airport} to {arrival_airport}."
    except Exception as e:
        return f"An error occurred while searching for flights: {str(e)}"
# Create tools for flight search and booking
flight_search_tool = Tool(
    name="Flight Search",
    func=search_flights,
    description="Useful for searching available flights between two cities. Input should be in the format 'departure_airport-arrival_airport'."
)

# Initialize the language model
llm = OpenAI(base_url="http://localhost:1234/v1", api_key="not-needed")

# Set up the prompt template
template = """
You are a helpful flight reservation chatbot for Chamwings Airlines. Your goal is to assist users in booking flights.
Use the Flight Search tool to find available flights and the Book Flight tool to make reservations.
When booking, ask if the passenger has any companions and collect their names.

Current conversation:
{chat_history}
Human: {human_input}
AI Assistant: Let's address this step by step:
"""

prompt = PromptTemplate(
    input_variables=["chat_history", "human_input"],
    template=template
)

# Set up the conversation memory
memory = ConversationBufferMemory(memory_key="chat_history", return_messages=True)

# Create the conversation chain
conversation = LLMChain(
    llm=llm,
    prompt=prompt,
    memory=memory
)

# Initialize the agent
agent = initialize_agent(
    [flight_search_tool],
    llm,
    agent=AgentType.CONVERSATIONAL_REACT_DESCRIPTION,
    verbose=True,
    memory=memory
)

# Main chat loop
print("Welcome to the Chamwings Airlines Flight Reservation Chatbot! How can I assist you today?")
while True:
    user_input = input("You: ")
    if user_input.lower() in ['exit', 'quit', 'bye']:
        print("Thank you for using Chamwings Airlines Flight Reservation Chatbot. Goodbye!")
        break
    
    response = agent.run(user_input)
    print("Chatbot:", response)
