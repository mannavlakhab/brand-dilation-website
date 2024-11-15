<?php
$user_agent = $_SERVER['HTTP_USER_AGENT'];

if (preg_match('/(android|iphone|ipad|tablet|mobile)/i', $user_agent)) {
    // Redirect to a different page or display a different layout
   echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Mobile Page</title>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css'>
</head>
<body>
    
    <main class='container mx-auto p-4'>
        <section class='bg-white p-4 mb-4 shadow-md'>
       <svg width='185px' height='185px' viewBox='-19 -19 228.00 228.00' fill='none' xmlns='http://www.w3.org/2000/svg' stroke='#ffffff' stroke-width='0.0019'><g id='SVGRepo_bgCarrier' stroke-width='0' transform='translate(18.049999999999997,18.049999999999997), scale(0.81)'></g><g id='SVGRepo_tracerCarrier' stroke-linecap='round' stroke-linejoin='round' stroke='#CCCCCC' stroke-width='0.76'></g><g id='SVGRepo_iconCarrier'> <path fill-rule='evenodd' clip-rule='evenodd' d='M38.155 140.475L48.988 62.1108L92.869 67.0568L111.437 91.0118L103.396 148.121L38.155 140.475ZM84.013 94.0018L88.827 71.8068L54.046 68.3068L44.192 135.457L98.335 142.084L104.877 96.8088L84.013 94.0018ZM59.771 123.595C59.394 123.099 56.05 120.299 55.421 119.433C64.32 109.522 86.05 109.645 92.085 122.757C91.08 123.128 86.59 125.072 85.71 125.567C83.192 118.25 68.445 115.942 59.771 123.595ZM76.503 96.4988L72.837 99.2588L67.322 92.6168L59.815 96.6468L56.786 91.5778L63.615 88.1508L59.089 82.6988L64.589 79.0188L68.979 85.4578L76.798 81.5328L79.154 86.2638L72.107 90.0468L76.503 96.4988Z' fill='#3c0e40'></path> </g></svg>
            <h1 class='text-2xl font-bold mb-2'>Sorry, It's seems your server is closed</h1>
            <p class='text-gray-600'>Please Tryagin after some time</p>
        </section>
    </main>
 
</body>
</html>";
    exit;
}
?>

<!-- HTML content -->
<div id="desktop-content">
    <!-- Desktop content here -->
</div>

<!-- JavaScript code -->
<script>
// Hide content on mobile and tablet devices
if (window.matchMedia('(max-width: 768px)').matches) {
    document.getElementById('desktop-content').style.display = 'none';
}
</script>

<!-- CSS code -->
<style>
/* Hide content on mobile and tablet devices */
@media (max-width: 768px) {
    #desktop-content {
        display: none;
    }
}
</style>