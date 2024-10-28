<?php
// Include PHPMailer and necessary configuration
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection settings
$host = 'localhost';
$user = 'root'; // Your MySQL username
$password = ''; // Your MySQL password
$database = 'recruitment_db'; // Your database name

// Function to get candidates
function get_candidates($host, $user, $password, $database) {
    $candidates = [];
    try {
        $connection = new mysqli($host, $user, $password, $database);
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        $sql = "SELECT id, firstname, lastname, email FROM application";
        $result = $connection->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $candidates[] = $row; // Push the candidate data into the array
            }
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        if (isset($connection) && $connection->connect_error === null) {
            $connection->close();
        }
    }

    return $candidates;
}

// Function to send email using PHPMailer
function send_email($applicant, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Set the SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'venkatbalaji4115@gmail.com'; // SMTP username
        $mail->Password = 'lbic etxc uqps sjue'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('your-email@example.com', 'Recruitment System');
        $mail->addAddress($applicant['email'], $applicant['firstname'] . ' ' . $applicant['lastname']); // Add a recipient

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        // Send the email
        $mail->send();
        echo 'Message has been sent to ' . $applicant['email'];
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Email templates based on the process
function email_for_round_1($applicant) {
    $subject = "Application Update: Round I";
    $body = "We are pleased to inform you that your application is being considered for Round I. Please prepare accordingly.";
    send_email($applicant, $subject, $body);
}

function email_round_1_passed($applicant) {
    $subject = "Congratulations! You Passed Round I";
    $body = "We are happy to inform you that you have successfully passed Round I. We will contact you soon regarding Round II.";
    send_email($applicant, $subject, $body);
}

function email_round_1_failed($applicant) {
    $subject = "Update: Round I Results";
    $body = "Unfortunately, you did not pass Round I. We appreciate your time and effort and encourage you to apply for future openings.";
    send_email($applicant, $subject, $body);
}

function email_job_offer($applicant) {
    $subject = "Job Offer: Congratulations!";
    $body = "We are excited to offer you the position at our company. Please review the job offer attached and contact us with any questions.";
    send_email($applicant, $subject, $body);
}

function email_hired($applicant) {
    $subject = "Welcome to the Team!";
    $body = "Congratulations on being hired! We are thrilled to have you on board and will be in touch with the next steps in the onboarding process.";
    send_email($applicant, $subject, $body);
}

function email_withdraw_application($applicant) {
    $subject = "Application Withdrawal Confirmation";
    $body = "We have received your request to withdraw your application. We appreciate your interest and wish you the best in your future endeavors.";
    send_email($applicant, $subject, $body);
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $candidate_id = $_POST['candidate_id'];
    $status = $_POST['status'];

    // Fetch the selected candidate's details
    $candidates = get_candidates($host, $user, $password, $database);
    $selected_applicant = null;

    foreach ($candidates as $candidate) {
        if ($candidate['id'] == $candidate_id) {
            $selected_applicant = $candidate;
            break;
        }
    }

    // Send the corresponding email based on the selected status
    if ($selected_applicant) {
        switch ($status) {
            case 'round1':
                email_for_round_1($selected_applicant);
                break;
            case 'round1_passed':
                email_round_1_passed($selected_applicant);
                break;
            case 'round1_failed':
                email_round_1_failed($selected_applicant);
                break;
            case 'job_offer':
                email_job_offer($selected_applicant);
                break;
            case 'hired':
                email_hired($selected_applicant);
                break;
            case 'withdraw':
                email_withdraw_application($selected_applicant);
                break;
            default:
                echo 'Invalid status selected.';
        }
    } else {
        echo 'Candidate not found.';
    }
}

// Fetch the list of candidates
$candidates = get_candidates($host, $user, $password, $database);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Mail to Candidates</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group select,
        .form-group button {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Send Mail to Candidates</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="candidate_id">Select Candidate:</label>
                <select id="candidate_id" name="candidate_id" required>
                    <option value="">Choose a candidate</option>
                    <?php foreach ($candidates as $candidate): ?>
                        <option value="<?= $candidate['id'] ?>"><?= htmlspecialchars($candidate['firstname'] . ' ' . $candidate['lastname']) ?> (<?= htmlspecialchars($candidate['email']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Select Status:</label>
                <select id="status" name="status" required>
                    <option value="">Choose an option</option>
                    <option value="round1">For Round I</option>
                    <option value="round1_passed">Round I PASSED</option>
                    <option value="round1_failed">Round I FAILED</option>
                    <option value="job_offer">Job Offer</option>
                    <option value="hired">Hired</option>
                    <option value="withdraw">Withdraw Application</option>
                </select>
            </div>
            <button type="submit">Send Mail</button>
        </form>
        <div class="footer">
            &copy; 2024 Recruitment Management System
        </div>
    </div>
</body>
</html>
