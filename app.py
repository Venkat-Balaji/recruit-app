from flask import Flask, render_template, request, jsonify
import google.generativeai as genai

app = Flask(__name__)

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
            return "Sorry, Gemini didn't provide an answer."
    except Exception as e:
        return f"An error occurred: {str(e)}"

# Serve the homepage
@app.route("/")
def home():
    return render_template("index.html")

# API route to handle user input and get a response
@app.route("/ask", methods=["POST"])
def ask():
    data = request.get_json()
    question = data.get("question")
    
    # Get response from Gemini
    response = ask_gemini(question)
    
    return jsonify({"response": response})

# Run the Flask app
if __name__ == "__main__":
    app.run(debug=True)
