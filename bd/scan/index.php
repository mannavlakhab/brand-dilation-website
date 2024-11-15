<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Scanner</title>
    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
</head>

<body>
    <h1>Scan Barcode</h1>
    <div id="interactive" class="viewport"></div>
    <p>Scanned Code: <span id="scanned-code"></span></p>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: document.querySelector('#interactive') // Element where camera feed will show
            },
            decoder: {
                readers: ["code_128_reader", "ean_reader", "ean_8_reader"]
            }
        }, function(err) {
            if (err) {
                console.error("Error initializing Quagga:", err);
                alert("Camera initialization failed. Please check camera permissions and HTTPS.");
                return;
            }
            Quagga.start();
            alert("Camera started successfully.");
        });

        Quagga.onDetected(function(result) {
            const code = result.codeResult.code;
            document.getElementById('scanned-code').textContent = code;

            // Send barcode data to PHP for validation
            fetch(`validate_barcode.php?barcode=${code}`)
                .then(response => response.json())
                .then(data => {
                    if (data.valid) {
                        alert('Barcode validated successfully!');
                    } else {
                        alert('Invalid barcode.');
                    }
                })
                .catch(error => console.error('Error:', error));

            Quagga.stop(); // Stop scanner after successful scan
        });
    });
    </script>

<h2>Upload Barcode Image</h2>
<form id="uploadForm" enctype="multipart/form-data">
    <input type="file" id="barcodeImage" name="barcodeImage" accept="image/*">
    <button type="button" onclick="uploadImage()">Upload Image</button>
    <p id="upload-result"></p>
</form>

<script>
function uploadImage() {
    const fileInput = document.getElementById('barcodeImage');
    if (fileInput.files.length === 0) {
        alert("Please select an image file.");
        return;
    }

    const formData = new FormData();
    formData.append("barcodeImage", fileInput.files[0]);

    fetch('upload_barcode.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('upload-result').textContent = data.message;
        })
        .catch(error => console.error('Error:', error));
}
</script>

</body>

</html>