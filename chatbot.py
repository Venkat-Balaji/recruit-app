import google.generativeai as genai
import os
import sys

# Set your Gemini API key
my_api_key_gemini = 'AIzaSyAPoYC30oi4qC-q1H1SPc-Iw5bw3IeIZ8U'  # Replace with your actual API key

# Configure the generative AI API
genai.configure(api_key=my_api_key_gemini)

# Function to generate a response from Gemini
def ask_gemini(question):
    try:
        # Use the generative model to generate content
        model = genai.GenerativeModel('gemini-pro')
        response = model.generate_content(question)

        # Check if there's valid text in the response
        if response.text:
            return response.text
        else:
            return "Sorry, but I think Gemini didn't want to answer that!"
    except Exception as e:
        # Handle any exceptions (like invalid API key, network issues, etc.)
        return f"An error occurred: {str(e)}"

if __name__ == "__main__":
    # Get the user's input from the command line
    user_input = sys.argv[1] if len(sys.argv) > 1 else ""
    
    if user_input:
        # Get the response from Gemini and print it
        print(ask_gemini(user_input))
    else:
        print("No input provided.")
