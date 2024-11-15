
function toggleWhitelist(productId, action) {
    const url = '../../whitelist_action.php';
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('action', action);

    fetch(url, {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        console.log(data); // Log the response to check what's returned
        if (data.success) {
            // Update the button appearance
            const button = document.querySelector(`button[onclick*='${productId}']`);
            const icon = button.querySelector('.material-icons');
            if (action === 'add') {
                button.classList.add('has_liked');
                button.setAttribute('aria-label', 'remove');
                icon.textContent = 'favorite'; // Change icon to filled heart
                button.onclick = () => toggleWhitelist(productId, 'remove');
            } else {
                button.classList.remove('has_liked');
                button.setAttribute('aria-label', 'like');
                icon.textContent = 'favorite_border'; // Change icon to outlined heart
                button.onclick = () => toggleWhitelist(productId, 'add');
            }
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}