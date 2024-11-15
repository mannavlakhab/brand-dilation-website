// Apply formatting using document.execCommand
function format(command) {
    document.execCommand(command, false, null);
  }
  
  // Handle saving content
  document.getElementById('saveBtn').addEventListener('click', function () {
    const content = document.getElementById('editor').innerHTML;
    
    fetch('api/save.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ content }),
    })
    .then(response => response.json())
    .then(data => {
      alert('Content saved successfully!');
    })
    .catch(error => {
      console.error('Error:', error);
    });
  });
  