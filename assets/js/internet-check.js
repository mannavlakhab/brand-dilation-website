// Create and style the internet error message div
const errorDiv = document.createElement('div');
errorDiv.id = 'internet-error';
errorDiv.style.display = 'none';
errorDiv.style.backgroundColor = 'red';
errorDiv.style.color = 'white';
errorDiv.style.textAlign = 'center';
errorDiv.style.padding = '10px';
errorDiv.style.position = 'fixed';
errorDiv.style.width = '100%';
errorDiv.style.top = '0';
errorDiv.style.fontFamily = 'BD,Montserrat, sans-serif';
errorDiv.style.left = '0';
errorDiv.style.zIndex = '1000';
errorDiv.innerText = 'Internet connection lost. Please check your connection.';
document.body.appendChild(errorDiv);

// Function to update the display of the error message
function updateOnlineStatus() {
    const internetError = document.getElementById('internet-error');
    if (navigator.onLine) {
        internetError.style.display = 'none';
    } else {
        internetError.style.display = 'block';
    }
}

// Event listeners for online and offline status
window.addEventListener('online', updateOnlineStatus);
window.addEventListener('offline', updateOnlineStatus);

// Initial check on page load
updateOnlineStatus();

// Optional: Function to periodically check the connection
function checkInternetConnection() {
    fetch('https://www.google.com', { mode: 'no-cors' })
        .then(() => {
            document.getElementById('internet-error').style.display = 'none';
        })
        .catch(() => {
            document.getElementById('internet-error').style.display = 'block';
        });
}

// Check every second (adjust as needed)
setInterval(checkInternetConnection, 3000);
