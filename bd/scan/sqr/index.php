<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
    <!-- Include HTML5 QR Code library with defer -->
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode/minified/html5-qrcode.min.js" defer></script>
</head>
<body>
    <h1>QR Code Scanner</h1>
    <div id="qr-reader" style="width: 300px; height: 300px;"></div>
    <p id="qr-result">Scan a QR code to see the result here</p>

    <script defer>
        // Ensure the script runs after the library loads
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof Html5Qrcode === 'undefined') {
                console.error("Html5Qrcode library failed to load.");
                return;
            }

            function onScanSuccess(decodedText, decodedResult) {
                document.getElementById('qr-result').innerText = `Scanned QR Code: ${decodedText}`;
            }

            function onScanFailure(error) {
                console.warn(`QR scanning error: ${error}`);
            }

            const html5QrCode = new Html5Qrcode("qr-reader");
            html5QrCode.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                onScanSuccess,
                onScanFailure
            );
        });
    </script>
</body>
</html>
