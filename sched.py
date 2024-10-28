import streamlit as st
import pandas as pd
from datetime import datetime, timedelta

# Placeholder for storing booking information (in memory or CSV)
booking_df = pd.DataFrame(columns=["role", "name", "date", "slot"])

# Pre-defined HR booked dates (in memory or from CSV)
hr_booked_dates = []  # This will hold all HR booked dates
# Store booked slots for candidates per date
candidate_booked_slots = {}

# Load existing bookings from CSV if available
def load_bookings():
    try:
        return pd.read_csv('bookings.csv')
    except FileNotFoundError:
        return pd.DataFrame(columns=["role", "name", "date", "slot"])

# Save bookings to CSV
def save_bookings(df):
    df.to_csv('bookings.csv', index=False)

# Function to display available slots for a given day
def get_available_slots(date):
    all_slots = [f"{hour}:00 - {hour+1}:00" for hour in range(9, 19)]  # 10 slots from 9 AM to 6 PM
    booked_slots = booking_df[(booking_df['date'] == date) & (booking_df['role'] == 'Candidate')]['slot'].tolist()
    available_slots = [slot for slot in all_slots if slot not in booked_slots]
    return available_slots

# Streamlit UI
st.title("AI-Powered HR & Candidate Interview Scheduler")

# Load existing bookings on startup
booking_df = load_bookings()

# Extract HR booked dates from existing bookings
hr_booked_dates = booking_df[booking_df['role'] == 'HR']['date'].tolist()

# Initial user type selection
role = st.selectbox("Are you an HR or a Candidate?", ["Select", "HR", "Candidate"])

if role == "HR":
    hr_name = st.text_input("Enter your name:")
    
    if hr_name:
        # HR selects a date (next 7 days) excluding already booked dates
        today = datetime.now().date()
        available_dates = [(today + timedelta(days=i)).strftime('%Y-%m-%d') for i in range(7)]
        available_dates = [date for date in available_dates if date not in hr_booked_dates]
        
        selected_date = st.selectbox("Select a date for conducting interviews", available_dates)

        if st.button("Confirm Date"):
            if selected_date:
                hr_booked_dates.append(selected_date)
                st.success(f"Date {selected_date} is booked for your interviews.")
                new_row = pd.DataFrame({"role": ["HR"], "name": [hr_name], "date": [selected_date], "slot": ["All"]})
                booking_df = pd.concat([booking_df, new_row], ignore_index=True)
                save_bookings(booking_df)

elif role == "Candidate":
    candidate_name = st.text_input("Enter your name:")

    if candidate_name:
        # Show only the dates booked by HR to candidates
        if hr_booked_dates:
            selected_date = st.selectbox("Select a date for your interview", hr_booked_dates)
            
            available_slots = get_available_slots(selected_date)
            if not available_slots:
                st.warning("No slots available for the selected date. Please try another date.")
            else:
                selected_slot = st.selectbox("Available time slots", available_slots)

                if st.button("Confirm Slot"):
                    if selected_date not in candidate_booked_slots:
                        candidate_booked_slots[selected_date] = []
                    candidate_booked_slots[selected_date].append(selected_slot)
                    st.success(f"Your interview is scheduled on {selected_date} at {selected_slot}.")
                    new_row = pd.DataFrame({"role": ["Candidate"], "name": [candidate_name], "date": [selected_date], "slot": [selected_slot]})
                    booking_df = pd.concat([booking_df, new_row], ignore_index=True)
                    save_bookings(booking_df)

# Display the bookings (for testing purposes)
st.write("Booking Details:")
st.dataframe(booking_df)