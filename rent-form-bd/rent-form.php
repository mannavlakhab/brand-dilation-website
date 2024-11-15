<?php
session_start();

// Initialize error message variable
$error_message = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare and bind SQL statement
    $stmt = $conn->prepare("INSERT INTO RentForm (name, companyName, phoneNumber, email, systemType, processor, RAM, SSD, quantity, duration) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        $error_message = "Prepare failed: " . $conn->error;
    } else {
        $stmt->bind_param("ssssssiiii", $name, $companyName, $phoneNumber, $email, $systemType, $processor, $RAM, $SSD, $quantity, $duration);

        // Set parameters
        $name = $_POST["name"];
        $companyName = $_POST["companyName"];
        $phoneNumber = $_POST["phoneNumber"];
        $email = $_POST["email"];
        $systemType = $_POST["systemType"];
        $processor = $_POST["processor"];
        $RAM = $_POST["RAM"];
        $SSD = $_POST["SSD"];
        $quantity = $_POST["quantity"];
        $duration = $_POST["duration"];

        // Execute the prepared statement
        if ($stmt->execute()) {
            echo "Form submitted successfully!";
          
        } else {
            $error_message = "Error submitting form: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    }
    
    // Display error message if exists
    if (!empty($error_message)) {
        echo $error_message;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Form</title>
    <link rel="shortcut icon" href="../assets/img/bd.png" type="image/x-icon">
    <link rel="stylesheet" href="rent.css">
      <!--=============== file loader ===============-->
    <!--=============== header ===============-->
    <script src="../assets/js/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>

    <script>
        $(function () {
            $('#header').load('../pages/header.php');

        });
    </script>
    <!--=============== footer ===============-->
    <script>
        $(function () {
            $('#footer').load('../pages/footer.php');

        });
    </script>

    <!--=============== closing file loader ===============-->
</head>
<body>
    
<header>
        
        <!--=============== HEADER ===============-->
        <div id="header"></div>
            </header>
    <div class="container">
        <h2>Rent Form</h2>
        <form action="../submit_form.php" method="post">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="companyName">Company Name:</label>
                <input type="text" id="companyName" name="companyName" required>
            </div>
            <div class="form-group">
                <label for="phoneNumber">Phone Number:</label>
                <input type="text" id="phoneNumber" name="phoneNumber" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="systemType">System Type:</label>
                <select name="systemType" id="systemType"required>
    <option value="" disabled selected>Select Your Devices</option>
    <option value="computer">Computers</option>
    <option value="Laptops">Laptops</option>
    </select>
            </div>
            <div class="form-group">
                <label for="processor">Processor:</label>
                <select name="processor" id="processor" required>
    <option value="" disabled selected>Select the Processor</option>
    <option value="due">Core due</option>
                    <option value="I5">I5</option>
                    <option value="I3">I3</option>
                    <option value="I7">I7</option>
    </select>
            </div>
            <div class="form-group">
                <label for="RAM">RAM (GB):</label>
                <select name="RAM" id="RAM"required>
                <option value="" disabled selected>Select the RAM</option>
                    <option value="4">4GB</option>
                    <option value="8">8GB</option>
                    <option value="12">12GB</option>

                </select>
            </div>
            <div class="form-group">
                <label for="SSD">SSD (GB):</label>
                <select name="SSD" id="SSD" required>
                <option value="" disabled selected>Select the Storage</option>
                    <option value="128">128 GB</option>
                    <option value="256">256 GB</option>
                    <option value="512">512 GB</option>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" required>
            </div>
            <div class="form-group">
                <label for="duration">Duration (MONTHS):</label>
                <input type="number" id="duration" name="duration" required>
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>
        <!--=============== HEADER ===============-->
        <div id="footer"></div>

</body>
</html>
