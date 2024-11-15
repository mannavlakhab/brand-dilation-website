import pymysql
pymysql.install_as_MySQLdb()

from flask import Flask, render_template, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from sqlalchemy import text
import re

app = Flask(__name__)
app.secret_key = 'your_secret_key'  # Required for session management

# Configure SQLAlchemy
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql://root:@localhost/shop'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
db = SQLAlchemy(app)

# Define stop words and suggestion keywords
STOP_WORDS = set(['with', 'the', 'a', 'an', 'and', 'or', 'is', 'in', 'to', 'of', 'for'])
SUGGESTION_KEYWORDS = set(['suggest', 'suggestions', 'suggested', 'recommend', 'recommendations'])

def preprocess_text(text):
    if text is None:
        return ''
    # Lowercase and remove non-alphanumeric characters
    text = text.lower()
    text = re.sub(r'[^a-z0-9\s]', '', text)
    return text

def remove_stop_words(text):
    return ' '.join(word for word in text.split() if word not in STOP_WORDS)

def calculate_match_percentage(description, keywords):
    description_words = set(description.split())
    keyword_words = set(keywords.split())
    if not keyword_words:
        return 0
    return len(description_words.intersection(keyword_words)) / len(keyword_words)

def contains_suggestion_keywords(text):
    words = set(text.split())
    return not SUGGESTION_KEYWORDS.isdisjoint(words)

@app.route('/')
def home():
    return render_template('index.html')

@app.route('/predict', methods=['POST'])
def predict():
    user_input = request.form.get('user_input')
    print(f"User input: {user_input}")

    if user_input:
        # Preprocess and remove stop words from user input
        user_input_clean = preprocess_text(user_input)
        user_input_filtered = remove_stop_words(user_input_clean)

        # Check for suggestion keywords
        if contains_suggestion_keywords(user_input_filtered):
            response = 'I see you are looking for suggestions. Could you please tell me the purpose for buying the laptop and your budget?'
            return jsonify({'message': response})

        # Handle user responses about purpose and budget
        try:
            # Fetch all products
            query = text("SELECT product_id, title, short_des, description FROM products")
            result = db.session.execute(query)
            products = result.fetchall()

            # Extract product details
            matched_ids = []
            for row in products:
                product_id = row[0]
                title = preprocess_text(row[1])
                short_des = preprocess_text(row[2])
                description = preprocess_text(row[3])

                # Combine and clean text fields
                combined_text = f"{title} {short_des} {description}"
                combined_text_cleaned = remove_stop_words(combined_text)
                
                # Calculate match percentage
                match_percentage = calculate_match_percentage(combined_text_cleaned, user_input_filtered)
                
                if match_percentage >= 0.7:  # 70% threshold
                    matched_ids.append(product_id)

            # Return matched IDs along with the original search keywords
            if matched_ids:
                response = f'Matching products: {matched_ids}'
                return jsonify({'message': response})
            else:
                response = 'No matching products found'
                return jsonify({'message': response}), 404

        except Exception as e:
            response = f'Error: {str(e)}'
            print(f"Error occurred: {response}")
            return jsonify({'message': response}), 500

    else:
        response = 'No input provided'
        return jsonify({'message': response}), 400

if __name__ == '__main__':
    app.run(debug=True)
