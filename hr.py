import streamlit as st
import json
import pdfplumber
import os
import pandas as pd
import tempfile
import speech_recognition as sr
from gtts import gTTS
import pygame
import time
import cv
import numpy as np
import threading

def speak(text):
    with tempfile.NamedTemporaryFile(delete=False, suffix=".mp3") as temp_file:
        tts = gTTS(text=text, lang='en')
        tts.save(temp_file.name)
        print(f"Audio saved to: {temp_file.name}")

    # Initialize pygame mixer
    pygame.mixer.init()
    pygame.mixer.music.load(temp_file.name)
    pygame.mixer.music.play()

    # Wait for the music to finish playing
    while pygame.mixer.music.get_busy():
        pygame.time.Clock().tick(10)

def extract_text_from_pdf(file):
    with pdfplumber.open(file) as pdf:
        text = ''
        for page in pdf.pages:
            text += page.extract_text() + '\n'
    return text.strip()

def gemini_virtual_hr(prompt):
    return f"AI HR: {prompt}"

def initial_interview_questions():
    return [
        "What are your strengths?",
        "What are your weaknesses?",
        "Why do you want to work for our company?",
        "Can you tell us about a time when you faced a challenge and how you overcame it?",
        "Where do you see yourself in 5 years?",
        "Can you tell us about a project you worked on that you're particularly proud of?",
        "What motivates you in your work?",
        "Have you ever worked on a project that involved significant multitasking or prioritization of tasks?"
    ]

def generate_follow_up_questions(last_answer):
    return [
        f"Can you elaborate more on that experience?",
        f"What specific skills did you utilize in that situation?",
        f"How did you handle the challenges you faced during that project?",
        f"What was the outcome of that situation?"
    ]

def save_all_candidates_responses(candidate_data, filename='candidates_interview_responses.json'):
    if os.path.exists(filename):
        with open(filename, 'r') as file:
            all_candidates = json.load(file)
    else:
        all_candidates = {}

    all_candidates[candidate_data['name']] = candidate_data

    with open(filename, 'w') as file:
        json.dump(all_candidates, file, indent=4)

def save_to_excel(candidate_data, filename='candidates_data.xlsx'):
    df = pd.DataFrame({
        'Name': [candidate_data['name']],
        'Resume Text': [candidate_data['resume_text']],
        'Interview Responses': [json.dumps(candidate_data['responses'])]
    })

    if os.path.exists(filename):
        with pd.ExcelWriter(filename, mode='a', engine='openpyxl', if_sheet_exists='overlay') as writer:
            df.to_excel(writer, sheet_name='Candidates', index=False, header=False, startrow=writer.sheets['Candidates'].max_row)
    else:
        df.to_excel(filename, index=False, sheet_name='Candidates')

def listen_to_answer():
    recognizer = sr.Recognizer()
    with sr.Microphone() as source:
        st.write("Please speak your answer:")
        audio = recognizer.listen(source)

    try:
        answer = recognizer.recognize_google(audio)
        st.success(f"You said: {answer}")
        return answer
    except sr.UnknownValueError:
        st.error("Sorry, I could not understand the audio.")
        return ""
    except sr.RequestError:
        st.error("Could not request results from Google Speech Recognition service.")
        return ""

def video_capture():
    cap = cv2.VideoCapture(0)
    while True:
        ret, frame = cap.read()
        if ret:
            frame = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
            st.image(frame, channels="RGB")
        else:
            break
    cap.release()

# Streamlit application layout
st.title("AI-Powered Virtual HR Interview")

# Candidate's input
uploaded_pdf = st.file_uploader("Upload your ATS resume (PDF file only):", type=["pdf"])

if uploaded_pdf is not None:
    resume_text = extract_text_from_pdf(uploaded_pdf)
    st.write("Resume successfully extracted.")
    st.text_area("Extracted Resume Text:", value=resume_text, height=200)

    candidate_name = st.text_input("Enter your name:")
    responses = {}

    # Split the layout into two columns for AI and Candidate
    col1, col2 = st.columns(2)

    with col1:
        st.header("AI HR")
        welcome_message = gemini_virtual_hr("Welcome! Let's start the interview based on your resume.")
        st.write(welcome_message)
        speak(welcome_message)

        initial_questions = initial_interview_questions()

        for i, question in enumerate(initial_questions, 1):
            if candidate_name:
                st.write(gemini_virtual_hr(question))
                speak(question)  # Play question audio
                
                # Introduce a 5-second delay before listening to the answer
                st.write("You have 5 seconds to prepare your answer...")
                time.sleep(5)  # Wait for 5 seconds
                
                answer = listen_to_answer()  # Listen to candidate's answer
                if answer:
                    responses[f"Question {i}"] = {"question": question, "answer": answer}

                    follow_up_questions = generate_follow_up_questions(answer)
                    for j, follow_up in enumerate(follow_up_questions, 1):
                        st.write(gemini_virtual_hr(follow_up))
                        speak(follow_up)  # Play follow-up question audio
                        
                        st.write("You have 5 seconds to prepare your answer...")
                        time.sleep(5)  # Wait for 5 seconds
                        
                        follow_up_answer = listen_to_answer()
                        if follow_up_answer:
                            responses[f"Follow-up Question {j} for Q{i}"] = {"question": follow_up, "answer": follow_up_answer}

        if st.button("Submit Interview"):
            if responses:
                candidate_data = {
                    'name': candidate_name,
                    'resume_text': resume_text,
                    'responses': responses
                }
                save_all_candidates_responses(candidate_data)
                save_to_excel(candidate_data)
                st.success("Interview responses saved successfully in JSON and Excel.")
            else:
                st.warning("Please answer all questions before submitting.")

    with col2:
        st.header("Candidate Window")
        st.write("Your video feed:")
        # Start video capture in a separate thread
        threading.Thread(target=video_capture, daemon=True).start()
else:
    st.info("Please upload your resume PDF to begin the interview.")
