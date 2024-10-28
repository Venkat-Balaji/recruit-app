import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
import mysql.connector

# Function to get applicants' data including metrics
def get_applicants():
    try:
        connection = mysql.connector.connect(
            host='3307',
            user='root',  # Your MySQL username
            password='root1234',  # Your MySQL password
            database='demo'  # Your database name
        )

        cursor = connection.cursor()
        # Fetching all relevant applicant data (assumes the metrics are part of the 'process_id' or another field)
        cursor.execute("SELECT firstname, lastname, email, process_id FROM application")
        applicants = cursor.fetchall()
        return applicants

    except mysql.connector.Error as err:
        print(f"Error: {err}")
        return None
    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()

# General email sending function
def send_email(applicant, subject, body):
    try:
        firstname, lastname, email, process_id = applicant
        sender_email = "venkatbalaji4115@gmail.com"  # Your email address
        sender_password = "Frost4115"  # Your email password
        sender_password = "lbic etxc uqps sjue"

        # Create the email content
        message = MIMEMultipart()
        message["From"] = sender_email
        message["To"] = "venkat.b.cst.2021@snsce.ac.in"
        message["Subject"] = subject
        mail="venkat.b.cst.2021@snsce.ac.in"

        # Email body content
        full_body = f"""
Dear {firstname} {lastname},

{body}

Best regards,
HR
        """
        message.attach(MIMEText(full_body, "plain"))

        # Setup SMTP server and send email
        server = smtplib.SMTP('smtp.gmail.com', 587)
        server.starttls()
        server.login(sender_email, sender_password)
        text = message.as_string()
        server.sendmail(sender_email, mail, text)
        server.quit()

        print(f"Email sent to {mail}")

    except Exception as e:
        print(f"Failed to send email to {mail}: {e}")

# Metric-specific email functions

def email_for_round_1(applicant):
    subject = "Application Update: Round I"
    body = "We are pleased to inform you that your application is being considered for Round I. Please prepare accordingly."
    send_email(applicant, subject, body)

def email_round_1_passed(applicant):
    subject = "Congratulations! You Passed Round I"
    body = "We are happy to inform you that you have successfully passed Round I. We will contact you soon regarding Round II."
    send_email(applicant, subject, body)

def email_round_1_failed(applicant):
    subject = "Update: Round I Results"
    body = "Unfortunately, you did not pass Round I. We appreciate your time and effort and encourage you to apply for future openings."
    send_email(applicant, subject, body)

def email_job_offer(applicant):
    subject = "Job Offer: Congratulations!"
    body = "We are excited to offer you the position at our company. Please review the job offer attached and contact us with any questions."
    send_email(applicant, subject, body)

def email_hired(applicant):
    subject = "Welcome to the Team!"
    body = "Congratulations on being hired! We are thrilled to have you on board and will be in touch with the next steps in the onboarding process."
    send_email(applicant, subject, body)

def email_withdraw_application(applicant):
    subject = "Application Withdrawal Confirmation"
    body = "We have received your request to withdraw your application. We appreciate your interest and wish you the best in your future endeavors."
    send_email(applicant, subject, body)

# Example of sending different types of emails
if __name__ == "__main__":
    # Get the list of applicants
    applicants = get_applicants()

    if applicants is not None:
        for applicant in applicants:
            process_id = applicant[3]  # Assuming process_id indicates the current metric/stage
            
            # Check the process_id to send corresponding emails
            if process_id == 0:
                email_for_round_1(applicant)
            elif process_id == 1:
                email_round_1_passed(applicant)
            elif process_id == 2:
                email_round_1_failed(applicant)
            elif process_id == 3:
                email_job_offer(applicant)
            elif process_id == 4:
                email_hired(applicant)
            elif process_id == 5:
                email_withdraw_application(applicant)
            else:
                print(f"Unknown process_id: {process_id}")
    else:
        print("No applicants to process.")
