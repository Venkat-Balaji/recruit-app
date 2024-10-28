import google.generativeai as genai
from flask import Flask, request, jsonify

# Initialize Flask app
app = Flask(__name__)

# Set your Gemini API key (replace with your actual API key)
my_api_key_gemini = 'AIzaSyAPoYC30oi4qC-q1H1SPc-Iw5bw3IeIZ8U'  # Replace with your actual API key
genai.configure(api_key=my_api_key_gemini)

# Function to generate interview questions based on a skill or introduction
def generate_interview_questions(input_text, is_technical=True):
    try:
        # Create a prompt based on the type of interview
        if is_technical:
            prompt = (
                f"Generate two unique and thoughtful technical interview questions focused on "
                f"assessing the candidate's proficiency based on the following input: {input_text}. "
                f"Ensure the questions cover different aspects and evaluate both practical knowledge "
                f"and problem-solving abilities. Separate each question with a newline."
            )
        else:
            prompt = (
                f"Generate five unique and thoughtful non-technical interview questions based on "
                f"the candidate's introduction: {input_text}. Each question should assess the candidate's "
                f"soft skills and cultural fit. Separate each question with a newline."
            )
        
        response = genai.GenerativeModel('gemini-pro').generate_content(prompt)

        if response.text:
            # Split questions by newlines
            questions = response.text.strip().split('\n')
            return questions
        return ["Sorry, no valid response from the AI."]
    except Exception as e:
        return [f"An error occurred: {str(e)}"]

# Function to generate a conversation response (chatbot)
def generate_chat_response(user_message):
    try:
        prompt = f"Respond to the following user message: '{user_message}'"
        response = genai.GenerativeModel('gemini-pro').generate_content(prompt)

        if response.text:
            return response.text.strip()
        return "Sorry, I didn't understand that. Could you rephrase?"
    except Exception as e:
        return f"An error occurred: {str(e)}"

# Flask route to handle POST requests for both chatbot and interview prompts
@app.route('/ask', methods=['POST'])
def ask():
    data = request.get_json()
    request_type = data.get('type', 'chat')  # Default to 'chat' if not specified
    input_text = data.get('input', '')

    if request_type == 'interview':
        # Determine if the interview is technical or non-technical
        is_technical = data.get('is_technical', True)  # Default to True (technical)
        ai_response = generate_interview_questions(input_text, is_technical)
        return jsonify({"questions": ai_response})

    elif request_type == 'chat':
        # Generate a chatbot response
        ai_response = generate_chat_response(input_text)
        return jsonify({"response": ai_response})

    # Handle invalid types
    return jsonify({"error": "Invalid request type. Use 'interview' or 'chat'."}), 400

if __name__ == '__main__':
    # Run the Flask app on localhost, port 5000
    app.run(host='0.0.0.0', port=5000)
