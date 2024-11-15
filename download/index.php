<?php
session_start();
include '../db_connect.php'; // Include your database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  // Check if "Remember Me" cookie is set
  if (isset($_COOKIE['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    // Optionally, you can re-validate the user with the database here
  } else {
    // If login is optional, do not redirect, just leave session unset
    // If you want to prompt the user to log in, you can show a message or an optional login button
    // Optionally, store the current page in session for redirection after optional login
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    // You can choose to show a notification or a button to log in here
  }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download The App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../../assets/js/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>

    <script>
    $(function() {
        $('#header').load('../../pages/header.php');

    });
    </script>
    <script>
    $(function() {
        $('#footer').load('../../pages/footer.php');

    });
    </script>

</head>
<body>
<div id="header"></div>

  <section class="pt-24 bg-white">
    <div class="px-12 mx-auto max-w-7xl">
        <div class="w-full mx-auto text-left md:w-11/12 xl:w-9/12 md:text-center">
            <h1 class="mb-8 text-4xl font-extrabold leading-none tracking-normal text-gray-900 md:text-6xl md:tracking-tight">
                <span>Run </span> <span class="block w-full py-2 text-transparent bg-clip-text leading-12 bg-gradient-to-r from-green-400 to-purple-500 lg:inline">Diagnostics </span> <span>on PC/Laptop?</span>
            </h1>
            <p class="px-0 mb-8 text-lg text-gray-600 md:text-xl lg:px-24">
              Ensure your PC, computer, or laptop is running smoothly with our top-rated diagnostics software.
            </p>
            <div class="mb-4 space-x-0 md:space-x-2 md:mb-8">
                <a href="#download" style="border-radius: 10px; color:#fff;" class="inline-flex  items-center justify-center w-full px-6 rounded-3xl py-3 mb-2 text-lg text-white bg-purple-900 rounded-2xl sm:w-auto sm:mb-0">
                    Download Now
                    <svg class="w-4 h-4 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </a>
                <a href="#_" class="inline-flex items-center justify-center w-full px-6 py-3 mb-2 text-lg bg-gray-100 rounded-2xl sm:w-auto sm:mb-0">
                    Learn More
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                </a>
            </div>
        </div>
        <div class="w-full mx-auto mt-20 text-center md:w-10/12">
            <div class="relative z-0 w-full mt-8">
                <div class="relative overflow-hidden">
                   
                    <img src="./MacBook Mockup, Floating (1).png">
                </div>
            </div>
        </div>
    </div>
</section>
          
  <section class="bg-gray-50 py-24">
    <div class="container mx-auto  px-4 flex flex-col md:flex-row items-center">
      <!-- Left Content -->
      <div class="w-full md:w-1/2 space-y-6">
        <h1 class="text-5xl font-bold text-gray-800 leading-tight">
          Comprehensive Diagnostics for Your PC and Laptop
        </h1>
        <p class="text-gray-600 text-xl">Get a complete health check for your PC and laptop. Our diagnostics app provides detailed insights into your system’s hardware and software, ensuring optimal performance.

        </p>
        <a href="#download" class="inline-block px-8 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-500 text-lg">Download For Free</a>
      </div>
  
      <!-- Right Image -->
      <div class="w-80 ml-4 md:w-full mt-12 md:mt-0">
        <img src="./Mockup.png" alt="SaaS Product Image" class="w-full h-auto rounded-lg shadow-lg">
      </div>
    </div>
  </section>
  <section id="features" class="bg-gray-100 py-16">
    <div class="container mx-auto px-4">
  
      <!-- Feature 1 -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center mb-12">
        <div class="order-2 md:order-1">
          <h2 class="text-3xl font-bold mb-4">Feature 1: RAM Checker</h2>
          <p class="text-gray-600 mb-4">
            Feature 1: Powerful Analytics
Get in-depth insights into your data with our powerful analytics tools. Track user behavior, revenue growth, and more, all in one dashboard.
          </p>
          <a href="#" class="text-blue-500 hover:underline">Learn More</a>
        </div>
        <div class="order-1 md:order-2">
          <img src="HP Elitebook Dragonfly (1).png" alt="Analytics">
        </div>
      </div>
  
      <!-- Feature 2 -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center mb-12">
        <div class="order-1">
          <img src="./Free Surface Studio Laptop Mockup (1).png" alt="Collaboration" class="rounded-lg shadow-lg">
        </div>
        <div class="order-2">
          <h2 class="text-3xl font-bold mb-4">Feature 2: Hardware Test</h2>
          <p class="text-gray-600 mb-4">
            Get detailed insights into your PC or laptop's hardware components. Monitor the status of your CPU, GPU, RAM, storage, and more, all from an intuitive dashboard. Ensure your system is running at its best with our comprehensive hardware diagnostics.
          </p>
          <a href="#" class="text-blue-500 hover:underline">Learn More</a>
        </div>
      </div>
  
      <!-- Feature 3 -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center mb-12">
        <div class="order-2 md:order-1">
          <h2 class="text-3xl font-bold mb-4">Feature 3: USB ports check</h2>
          <p class="text-gray-600 mb-4">
            Customize your dashboard and interface to suit your business needs. Our platform is flexible, allowing you to adjust settings and preferences easily.
          </p>
          <a href="#" class="text-blue-500 hover:underline">Learn More</a>
        </div>
        <div class="order-1 md:order-2">
          <img src="./usb.png" alt="Customization" class="rounded-lg shadow-lg">
        </div>
      </div>
  
    </div>
  </section>
  <div class="w-full mx-auto mt-20 text-center md:w-10/12">
    <div class="relative z-0 w-full mt-8">
        <div class="relative overflow-hidden">
           
            <img class="rounded-lg shadow-lg" src="./Front View Laptop Mockup.jpg">
        </div>
    </div>
</div>

<section id="download" class="bg-white py-16">
  <div class="container mx-auto px-4 flex flex-col md:flex-row items-center">

    <!-- App Information and Download Links -->
    <div class="md:w-1/2 mb-8 md:mb-0 md:pr-8">
      <h2 class="text-4xl font-bold mb-4">Download Our App</h2>
      <p class="text-gray-600 mb-6">
        Get our app for the ultimate convenience! Available on windows user only. Stay connected and access your features on the go.
      </p>

      <div class="flex space-x-4">
        <!-- Play Store Button -->
   

        <!-- App Store Button -->
        <a href="./Brand Dilation checker.msi" style="color:#fff" class="bg-black text-white py-3 px-6 rounded-lg inline-flex items-center shadow-lg hover:bg-gray-800 transition duration-300">
         

 <svg class="w-6 h-6 mr-3" viewBox="0 0 20 20" version="1.1" >
    <g id="Page-1" stroke="none" stroke-width="1" fill="#fff" fill-rule="evenodd">
        <g id="Dribbble-Light-Preview" transform="translate(-60.000000, -7439.000000)" fill="#fff">
            <g id="icons" transform="translate(56.000000, 160.000000)">
                <path d="M13.1458647,7289.43426 C13.1508772,7291.43316 13.1568922,7294.82929 13.1619048,7297.46884 C16.7759398,7297.95757 20.3899749,7298.4613 23.997995,7299 C23.997995,7295.84873 24.002005,7292.71146 23.997995,7289.71311 C20.3809524,7289.71311 16.7649123,7289.43426 13.1458647,7289.43426 M4,7289.43526 L4,7296.22153 C6.72581454,7296.58933 9.45162907,7296.94113 12.1724311,7297.34291 C12.1774436,7294.71736 12.1704261,7292.0908 12.1704261,7289.46524 C9.44661654,7289.47024 6.72380952,7289.42627 4,7289.43526 M4,7281.84344 L4,7288.61071 C6.72581454,7288.61771 9.45162907,7288.57673 12.1774436,7288.57973 C12.1754386,7285.96017 12.1754386,7283.34361 12.1724311,7280.72405 C9.44461153,7281.06486 6.71679198,7281.42567 4,7281.84344 M24,7288.47179 C20.3879699,7288.48578 16.7759398,7288.54075 13.1619048,7288.55175 C13.1598997,7285.88921 13.1598997,7283.22967 13.1619048,7280.56914 C16.7689223,7280.01844 20.3839599,7279.50072 23.997995,7279 C24,7282.15826 23.997995,7285.31353 24,7288.47179" id="windows-[#174]">

</path>
            </g>
        </g>
    </g>
</svg>

          Download For Windows
        </a>
      </div>
    </div>





    <!-- App Mockup Image -->
    <div class="md:w-1/2">
      <img src="./MacBook Mockup, Floating.png" alt="App Mockup" class="w-full rounded-lg shadow-lg">
    </div>
  </div>
</section>

<div id="footer"></div>
 
  
                                    
</body>
</html>