<?php
include 'db_connect.php'; // Make sure to include your database connection script

// Twilio credentials
$twilio_sid = 'ACf2e6f2830ff88f68f75ea2476c30bc89';
$twilio_token = '0459762b4b960b2f50faec34a363328a';
$twilio_number = '+19289853374';

require __DIR__ . '/vendor/autoload.php';
use Twilio\Rest\Client;

function sendOTP($phoneNumber, $otp) {
    global $twilio_sid, $twilio_token, $twilio_number;
    
    $client = new Client($twilio_sid, $twilio_token);
    
    $message = $client->messages->create(
        $phoneNumber,
        [
            'from' => $twilio_number,
            'body' => 'Your OTP code is ' . $otp
        ]
    );
    
    return $message->sid;
}

function checkPhoneNumberInUsers($conn, $phone_number) {
    $query = "SELECT * FROM users WHERE phone_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $phone_number);
    $stmt->execute();
    return $stmt->get_result();
}

function checkPhoneNumberInVerification($conn, $phone_number) {
    $query = "SELECT * FROM phone_verifications WHERE phone_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $phone_number);
    $stmt->execute();
    return $stmt->get_result();
}

function insertOrUpdateOTP($conn, $phone_number, $otp) {
    $result = checkPhoneNumberInVerification($conn, $phone_number);
    if ($result->num_rows === 0) {
        // Insert phone number and OTP into phone_verifications
        $insertQuery = "INSERT INTO phone_verifications (phone_number, otp) VALUES (?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param('ss', $phone_number, $otp);
        $stmt->execute();
    } else {
        // Update OTP for existing phone number
        $updateQuery = "UPDATE phone_verifications SET otp = ? WHERE phone_number = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('ss', $otp, $phone_number);
        $stmt->execute();
    }
}

// Check for form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['phone_number'])) {
        // Step 1: Phone number submission and OTP sending
        $phone_number = $_POST['phone_number'];

        // Check if the phone number exists in the user table
        $result = checkPhoneNumberInUsers($conn, $phone_number);

        if ($result->num_rows > 0) {
            // Phone number is already registered
            echo "Your Phone number is registered with Brand Dilation.";
        } else {
            // Phone number not registered, proceed with OTP
            $otp = rand(100000, 999999); // Generate OTP

            // Insert or update OTP in phone_verifications table
            insertOrUpdateOTP($conn, $phone_number, $otp);

            // Send OTP via Twilio
            sendOTP($phone_number, $otp);
            echo "OTP has been sent to your phone number.";

            // Show OTP form for verification
            echo '<form method="POST" action="">
                    <label for="otp">Enter OTP:</label>
                    <input type="text" name="otp" required>
                    <input type="hidden" name="phone_number" value="'.$phone_number.'">
                    <button type="submit" name="verify_otp">Verify OTP</button>
                  </form>';
        }
    } elseif (isset($_POST['verify_otp'])) {
        // Step 2: OTP Verification
        $phone_number = $_POST['phone_number'];
        $entered_otp = $_POST['otp'];

        // Check if OTP is correct
        $otpQuery = "SELECT * FROM phone_verifications WHERE phone_number = ? AND otp = ?";
        $stmt = $conn->prepare($otpQuery);
        $stmt->bind_param('ss', $phone_number, $entered_otp);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // OTP is correct
            echo "Phone number verified successfully.";

            // Check if the phone number exists in the user table
            $result2 = checkPhoneNumberInUsers($conn, $phone_number);

            if ($result2->num_rows > 0) {
                echo "Your Phone number is registered with Brand Dilation.";
            } else {
                echo "It doesn't exist but do you want to make an account on Brand Dilation?";
            }
        } else {
            echo "Invalid OTP. Please try again.";
        }
    }
}
?>

<!-- HTML form for phone number input -->
<form method="POST" action="">
    <label for="phone_number">Enter your phone number:</label>
    <input type="text" name="phone_number" required>
    <button type="submit">Check Phone Number</button>
</form>
