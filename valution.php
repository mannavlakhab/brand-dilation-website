<?php
session_start();
require 'vendor/autoload.php';

// Define a function to fetch data from an API
function fetchData($url) {
    $client = new \GuzzleHttp\Client();
    $apiKey = '2dd4d84462msh5753ce245a29990p1e4e95jsn169bad05ff21'; // Replace with your actual API key

    try {
        $response = $client->request('GET', $url, [
            'headers' => [
                'X-Api-Key' => $apiKey, // Include your API key here
            ],
        ]);
        return json_decode($response->getBody(), true);
    } catch (Exception $e) {
        // Handle exception
        echo "Error fetching data: " . $e->getMessage();
        return [];
    }
}


// Define headers for API calls
$headers = [
    'x-rapidapi-host' => 'laptopdb1.p.rapidapi.com',
    'x-rapidapi-key' => '2dd4d84462msh5753ce245a29990p1e4e95jsn169bad05ff21', // Replace with your actual API key
];

// Step processing
$current_step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store data in session based on step
    if ($current_step == 1) {
        $_SESSION['brand'] = $_POST['brand'];
    } elseif ($current_step == 2) {
        $_SESSION['model'] = $_POST['model'];
    } elseif ($current_step == 3) {
        $_SESSION['is_on'] = $_POST['is_on'];
    } elseif ($current_step == 4) {
        $_SESSION['processor'] = $_POST['processor'];
        $_SESSION['ram'] = $_POST['ram'];
        $_SESSION['storage'] = $_POST['storage'];
    } elseif ($current_step == 5) {
        $_SESSION['screen_size'] = $_POST['screen_size'];
        $_SESSION['graphics_card'] = $_POST['graphics_card'];
    } elseif ($current_step == 6) {
        $_SESSION['condition'] = $_POST['condition'];
    } elseif ($current_step == 7) {
        $_SESSION['age'] = $_POST['age'];
    } elseif ($current_step == 8) {
        $_SESSION['screen_condition'] = $_POST['screen_condition'];
    } elseif ($current_step == 9) {
        $_SESSION['physical_condition'] = $_POST['physical_condition'];
    } elseif ($current_step == 10) {
        // Final step - calculate valuation based on stored session data
        // Here, you can implement your valuation logic based on session variables
        // For example:
        $valuation = 0;
        // Add valuation calculations here...
        $_SESSION['valution'] = $valuation;
    }
    header("Location: valution.php?step=" . ($current_step + 1));
    exit;
}

// Fetch data for brands and models
$brands = fetchData("https://api.api-ninjas.com/v1/logo");
$models = fetchData("https://laptopdb1.p.rapidapi.com/companies", $headers);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laptop Valuation</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Laptop Valuation Step <?php echo $current_step; ?></h2>

        <form method="POST">
            <?php if ($current_step == 1): ?>
                <label class="block mb-2">Select Brand:</label>
                <select name="brand" required class="block w-full p-2 border border-gray-300 rounded">
                   <option value="lenovo">Lenovo</option>
                </select>
            <?php elseif ($current_step == 2): ?>
                <label class="block mb-2">Select Model:</label>
                <select name="model" required class="block w-full p-2 border border-gray-300 rounded">
                    <?php foreach ($models as $model): ?>
                        <option value="<?php echo $model['name']; ?>">
                            <?php echo $model['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php elseif ($current_step == 3): ?>
                <label class="block mb-2">Does the Laptop switch on?</label>
                <select name="is_on" required class="block w-full p-2 border border-gray-300 rounded">
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            <?php elseif ($current_step == 4): ?>
                <label class="block mb-2">Select Processor:</label>
                <input type="text" name="processor" required class="block w-full p-2 border border-gray-300 rounded">
                <label class="block mb-2">Select RAM:</label>
                <input type="text" name="ram" required class="block w-full p-2 border border-gray-300 rounded">
                <label class="block mb-2">Select Storage:</label>
                <input type="text" name="storage" required class="block w-full p-2 border border-gray-300 rounded">
            <?php elseif ($current_step == 5): ?>
                <label class="block mb-2">Select Screen Size:</label>
                <select name="screen_size" required class="block w-full p-2 border border-gray-300 rounded">
                    <option value="10-11">10-11 inch</option>
                    <option value="12-13">12-13 inch</option>
                    <option value="14-15">14-15 inch</option>
                    <option value="above-15">Above 15 inch</option>
                </select>
                <label class="block mb-2">External Graphics Card:</label>
                <select name="graphics_card" required class="block w-full p-2 border border-gray-300 rounded">
                    <option value="available">Graphics Card available</option>
                    <option value="not_available">Graphics Card not available</option>
                </select>
            <?php elseif ($current_step == 6): ?>
                <label class="block mb-2">Condition:</label>
                <select name="condition" required class="block w-full p-2 border border-gray-300 rounded">
                    <option value="working">Working</option>
                    <option value="not_working">Not Working</option>
                </select>
            <?php elseif ($current_step == 7): ?>
                <label class="block mb-2">Age of the Device:</label>
                <select name="age" required class="block w-full p-2 border border-gray-300 rounded">
                    <option value="less_than_1_year">Less than 1 year</option>
                    <option value="1_to_3_years">Between 1 and 3 years</option>
                    <option value="more_than_3_years">More than 3 years</option>
                </select>
            <?php elseif ($current_step == 8): ?>
                <label class="block mb-2">Select Screen Condition:</label>
                <select name="screen_condition" required class="block w-full p-2 border border-gray-300 rounded">
                    <option value="flawless">Flawless</option>
                    <option value="minor_scratches">Minor Scratches</option>
                    <option value="good">Good</option>
                    <option value="average">Average</option>
                    <option value="damaged">Damaged</option>
                </select>
            <?php elseif ($current_step == 9): ?>
                <label class="block mb-2">Select Physical Condition:</label>
                <select name="physical_condition" required class="block w-full p-2 border border-gray-300 rounded">
                    <option value="flawless">Flawless</option>
                    <option value="good">Good</option>
                    <option value="average">Average</option>
                    <option value="below_average">Below Average</option>
                </select>
            <?php elseif ($current_step == 10): ?>
                <h3 class="mb-4">Your Valuation: <?php echo $_SESSION['valuation']; ?></h3>
            <?php endif; ?>
            
            <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Next</button>
        </form>

        <div class="mt-6">
            <h3 class="font-semibold">Preview of Your Selections:</h3>
            <ul>
                <?php
                echo "<li>Brand: " . ($_SESSION['brand'] ?? 'N/A') . "</li>";
                echo "<li>Model: " . ($_SESSION['model'] ?? 'N/A') . "</li>";
                echo "<li>Switch On: " . ($_SESSION['is_on'] ?? 'N/A') . "</li>";
                echo "<li>Processor: " . ($_SESSION['processor'] ?? 'N/A') . "</li>";
                echo "<li>RAM: " . ($_SESSION['ram'] ?? 'N/A') . "</li>";
                echo "<li>Storage: " . ($_SESSION['storage'] ?? 'N/A') . "</li>";
                echo "<li>Screen Size: " . ($_SESSION['screen_size'] ?? 'N/A') . "</li>";
                echo "<li>Graphics Card: " . ($_SESSION['graphics_card'] ?? 'N/A') . "</li>";
                echo "<li>Condition: " . ($_SESSION['condition'] ?? 'N/A') . "</li>";
                echo "<li>Age: " . ($_SESSION['age'] ?? 'N/A') . "</li>";
                echo "<li>Screen Condition: " . ($_SESSION['screen_condition'] ?? 'N/A') . "</li>";
                echo "<li>Physical Condition: " . ($_SESSION['physical_condition'] ?? 'N/A') . "</li>";
                ?>
            </ul>
        </div>
    </div>
</body>
</html>

