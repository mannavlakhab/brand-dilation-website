<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare an SQL statement
    $stmt = $conn->prepare("SELECT id, password, role FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $row['password'])) { // Use password_verify to compare hashed passwords
            $_SESSION['id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            
            // // Log login time
            // $loginTime = date("Y-m-d H:i:s");
            // $_SESSION['login_time'] = $loginTime;
            // $logStmt = $conn->prepare("INSERT INTO admin_user_activity (id, login_time) VALUES (?, ?)");
            // $logStmt->bind_param("is", $row['id'], $loginTime);
            // $logStmt->execute();
            if ($row['role'] == 'delivery') {
                header("Location: delivery/db.php");
                exit; // Ensure no further code execution after redirection
            }elseif ($row['role'] == '') {
                header("Location: delivery/");
                exit; // Ensure no further code execution after redirection
            
            }else {
                header("Location: home.php");
                exit; // Ensure no further code execution after redirection
            }
        } else {
            echo "Invalid password";
        }
    } else {
        echo "Invalid username or password";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login </title>
  <link rel="stylesheet" href="./assets/font.css">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.15/tailwind.min.css">
</head>
<body>
<div id="__next" bis_skin_checked="1">
    <div style="z-index: 1;" class="absolute z-0 top-0 opacity-75  inset-x-0 flex justify-center overflow-hidden pointer-events-none" bis_skin_checked="1">
        <div class="w-[108rem] flex-none flex justify-end" bis_skin_checked="1">
            <picture>
                <source srcset="./assets/img/docs@30.8b9a76a2.avif" type="image/avif">
                <img src="./assets/img/docs@tinypng.d9e4dcdc.png" alt="" class="w-[90rem] flex-none max-w-none" decoding="async">
            </picture>
        </div>
    </div>
</div>
  <section class="flex flex-col md:flex-row items-center mb-8">
    <div class="mt-10  w-full md:max-w-md lg:max-w-full md:mx-auto md:mx-0 md:w-1/2 xl:w-1/3 px-6 lg:px-16 xl:px-12
          flex items-center justify-center">
  
      <div class="w-full h-100 z-20 ">
        <img src="../assets/img/64-bd-t.png" alt="LOGO" class="w-16 mx-auto">
        <h1 class="text-xl md:text-2xl font-bold leading-tight mt-12">Log in to BRAND DILATION</h1>
  
        <form class="mt-6" action="#" method="POST">
          <div>
            <label class="block text-gray-700">Username</label>
            <input type="text" id="username" name="username" required placeholder="Enter username" class="w-full px-4 py-3 rounded-lg bg-gray-200 mt-2 border focus:border-blue-500 focus:bg-white focus:outline-none" autofocus autocomplete required>
          </div>
  
          <div class="mt-4">
            <label class="block text-gray-700">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter Password" class="w-full px-4 py-3 rounded-lg bg-gray-200 mt-2 border focus:border-blue-500
                  focus:bg-white focus:outline-none" required>
          </div>
  
          <div class="text-right mt-2">
            <a href="#./forget.php" class="text-sm font-semibold text-gray-700 hover:text-blue-700 focus:text-blue-700">Forgot Password?</a>
          </div>
  
          <button type="submit" class="w-full block bg-blue-500 hover:bg-blue-400 focus:bg-blue-400 text-white font-semibold rounded-lg
                px-4 py-3 mt-6">Log In</button>
        </form>
  
        <hr class="my-6 border-gray-300 w-full">
  
        
        <p class="mt-8">
            I doesn't have an account?    </p>
            <br>
            <button type="button" onclick="window.location.href='./signup.php'"  class="w-full block bg-white hover:bg-gray-100 focus:bg-gray-100 text-gray-900 font-semibold rounded-lg px-4 py-3 border border-gray-300">
              <div class="flex items-center justify-center">
                <!-- <svg class="w-6 h-6" viewBox="0 0 48 48"><defs><path id="a" d="M44.5 20H24v8.5h11.8C34.7 33.9 30.1 37 24 37c-7.2 0-13-5.8-13-13s5.8-13 13-13c3.1 0 5.9 1.1 8.1 2.9l6.4-6.4C34.6 4.1 29.6 2 24 2 11.8 2 2 11.8 2 24s9.8 22 22 22c11 0 21-8 21-22 0-1.3-.2-2.7-.5-4z"/></defs><clipPath id="b"><use xlink:href="#a" overflow="visible"/></clipPath><path clip-path="url(#b)" fill="#FBBC05" d="M0 37V11l17 13z"/><path clip-path="url(#b)" fill="#EA4335" d="M0 11l17 13 7-6.1L48 14V0H0z"/><path clip-path="url(#b)" fill="#34A853" d="M0 37l30-23 7.9 1L48 0v48H0z"/><path clip-path="url(#b)" fill="#4285F4" d="M48 48L17 24l-4-3 35-10z"/></svg> -->
                <svg class="w-8 h-8"  viewBox="0 0 24.00 24.00" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="0.00024000000000000003" transform="matrix(1, 0, 0, 1, 0, 0)"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.096"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12.0002 8C11.1718 8 10.5002 8.67157 10.5002 9.5C10.5002 10.3284 11.1718 11 12.0002 11C12.8286 11 13.5002 10.3284 13.5002 9.5C13.5002 8.67157 12.8286 8 12.0002 8ZM8.50018 9.5C8.50018 7.567 10.0672 6 12.0002 6C13.9332 6 15.5002 7.567 15.5002 9.5C15.5002 11.433 13.9332 13 12.0002 13C10.0672 13 8.50018 11.433 8.50018 9.5Z" fill="#ff6666"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M6 18C6.00478 18.1073 6.00044 18.0205 6.00044 18.0205L6.00048 18.0222L6.00056 18.0259L6.0008 18.0341L6.00157 18.054C6.00225 18.0688 6.00326 18.0866 6.00478 18.1073C6.00783 18.1486 6.01294 18.2014 6.02151 18.2642C6.03862 18.3895 6.06978 18.5561 6.12678 18.7504C6.2409 19.1392 6.4606 19.6447 6.88176 20.1444C7.75153 21.1765 9.31667 22 12.0001 22C14.4911 22 16.0226 21.2914 16.9304 20.3527C17.8205 19.4323 17.9691 18.436 17.9945 18.123C18.014 17.8823 17.955 17.6735 17.8751 17.5137C17.1048 15.9732 15.5302 15 13.8078 15H10.2363C8.48712 15 6.88806 15.9883 6.1058 17.5528L6 18ZM8.04522 18.185L8.04584 18.1871C8.09681 18.3608 8.20021 18.6053 8.4111 18.8556C8.80544 19.3235 9.74023 20 12.0001 20C14.1057 20 15.0583 19.4116 15.4927 18.9623C15.7742 18.6713 15.9006 18.3745 15.9569 18.1796C15.4928 17.45 14.6845 17 13.8078 17H10.2363C9.34631 17 8.52487 17.4513 8.04522 18.185Z" fill="#ff6666"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4ZM2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12Z" fill="#3c0e40"></path> </g></svg>
                <!-- <svg  class="w-8 h-8" width="64px" height="64px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2Z" fill="#3c0e40"></path> <path d="M12.0001 6C10.3433 6 9.00012 7.34315 9.00012 9C9.00012 10.6569 10.3433 12 12.0001 12C13.657 12 15.0001 10.6569 15.0001 9C15.0001 7.34315 13.657 6 12.0001 6Z" fill="#ff6666"></path> <path d="M17.8948 16.5528C18.0356 16.8343 18.0356 17.1657 17.8948 17.4473C17.9033 17.4297 17.8941 17.4487 17.8941 17.4487L17.8933 17.4502L17.8918 17.4532L17.8883 17.46L17.8801 17.4756C17.874 17.4871 17.8667 17.5004 17.8582 17.5155C17.841 17.5458 17.8187 17.5832 17.7907 17.6267C17.7348 17.7138 17.6559 17.8254 17.5498 17.9527C17.337 18.208 17.0164 18.5245 16.555 18.8321C15.623 19.4534 14.1752 20 12.0002 20C8.31507 20 6.76562 18.4304 6.26665 17.7115C5.96476 17.2765 5.99819 16.7683 6.18079 16.4031C6.91718 14.9303 8.42247 14 10.0691 14H13.7643C15.5135 14 17.1125 14.9883 17.8948 16.5528Z" fill="#ff6666"></path> </g></svg> -->
                
                <span class="ml-4 ">
                  Create an account
                  </span>
              </div>
            </button>
            <br>
            <button type="button" onclick="window.location.href='../index.php'"  class="w-full block bg-white hover:bg-gray-100 focus:bg-gray-100 text-gray-900 font-semibold rounded-lg px-4 py-3 border border-gray-300">
              <div class="flex items-center justify-center">
                <!-- <svg class="w-6 h-6" viewBox="0 0 48 48"><defs><path id="a" d="M44.5 20H24v8.5h11.8C34.7 33.9 30.1 37 24 37c-7.2 0-13-5.8-13-13s5.8-13 13-13c3.1 0 5.9 1.1 8.1 2.9l6.4-6.4C34.6 4.1 29.6 2 24 2 11.8 2 2 11.8 2 24s9.8 22 22 22c11 0 21-8 21-22 0-1.3-.2-2.7-.5-4z"/></defs><clipPath id="b"><use xlink:href="#a" overflow="visible"/></clipPath><path clip-path="url(#b)" fill="#FBBC05" d="M0 37V11l17 13z"/><path clip-path="url(#b)" fill="#EA4335" d="M0 11l17 13 7-6.1L48 14V0H0z"/><path clip-path="url(#b)" fill="#34A853" d="M0 37l30-23 7.9 1L48 0v48H0z"/><path clip-path="url(#b)" fill="#4285F4" d="M48 48L17 24l-4-3 35-10z"/></svg> -->
                <svg class="w-8 h-8"  viewBox="0 -3 34 34" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="1.1560000000000001"></g><g id="SVGRepo_iconCarrier"> <!-- Uploaded to: SVG Repo, www.svgrepo.com, Generator: SVG Repo Mixer Tools --> <title>website</title> <desc>Created with Sketch.</desc> <defs> </defs> <g id="Vivid.JS" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <g id="Vivid-Icons" transform="translate(-44.000000, -491.000000)"> <g id="Icons" transform="translate(37.000000, 169.000000)"> <g id="website" transform="translate(0.000000, 312.000000)"> <g transform="translate(7.000000, 10.000000)"> <rect id="Rectangle-path" fill="#ff6666" fill-rule="nonzero" x="0" y="0" width="34" height="28"> </rect> <path d="M0,0 L34,0 L34,3 L0,3 L0,0 Z M4,7 L30,7 L30,12 L4,12 L4,7 Z M4,16 L12,16 L12,24 L4,24 L4,16 Z M15,16 L30,16 L30,19 L15,19 L15,16 Z M15,21 L23,21 L23,24 L15,24 L15,21 Z" id="Shape" fill="#3c0e40"> </path> </g> </g> </g> </g> </g> </g></svg>
                <!-- <svg  class="w-8 h-8" width="64px" height="64px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2Z" fill="#3c0e40"></path> <path d="M12.0001 6C10.3433 6 9.00012 7.34315 9.00012 9C9.00012 10.6569 10.3433 12 12.0001 12C13.657 12 15.0001 10.6569 15.0001 9C15.0001 7.34315 13.657 6 12.0001 6Z" fill="#ff6666"></path> <path d="M17.8948 16.5528C18.0356 16.8343 18.0356 17.1657 17.8948 17.4473C17.9033 17.4297 17.8941 17.4487 17.8941 17.4487L17.8933 17.4502L17.8918 17.4532L17.8883 17.46L17.8801 17.4756C17.874 17.4871 17.8667 17.5004 17.8582 17.5155C17.841 17.5458 17.8187 17.5832 17.7907 17.6267C17.7348 17.7138 17.6559 17.8254 17.5498 17.9527C17.337 18.208 17.0164 18.5245 16.555 18.8321C15.623 19.4534 14.1752 20 12.0002 20C8.31507 20 6.76562 18.4304 6.26665 17.7115C5.96476 17.2765 5.99819 16.7683 6.18079 16.4031C6.91718 14.9303 8.42247 14 10.0691 14H13.7643C15.5135 14 17.1125 14.9883 17.8948 16.5528Z" fill="#ff6666"></path> </g></svg> -->
                
                <span class="ml-4 ">
                Go to Website
                  </span>
              </div>
            </button>
     

      </div>

    </div>
  
  </section>
  

</body>
</html>