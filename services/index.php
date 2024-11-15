<?php
session_start();
include '../db_connect.php'; // Include your database connection
// Initialize variables for user details
$user_name = '';
$user_phone = '';

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Fetch user details
    $user_id = $_SESSION['user_id'];
    $user_query = $conn->prepare("SELECT first_name, phone_number FROM users WHERE user_id = ?");
    $user_query->bind_param("i", $user_id);
    $user_query->execute();
    $user_result = $user_query->get_result();
    
    if ($user_row = $user_result->fetch_assoc()) {
        $user_name = $user_row['first_name'];
        $user_phone = $user_row['phone_number'];
    }
    
    $user_query->close();
}

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


// Fetch services under the selected category
$query = "SELECT * FROM  service_categories";
$ser = mysqli_query($conn, $query);

// Fetch services under the selected category
$query = "SELECT * FROM  services";
$list = mysqli_query($conn, $query);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Dilation - Expert Computer Solutions</title>
    <link rel="stylesheet" href="../assets/css/services_page.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <!--=============== file loader ===============-->
    <link rel="shortcut icon" href="../assets/img/bd.png" type="image/x-icon">
    <!--=============== header ===============-->
    <script src="../assets/js/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>

    <script>
    $(function() {
        $('#header').load('../pages/header.php');

    });
    </script>
    <!--=============== footer ===============-->
    <script>
    $(function() {
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

    <!-- Hero Section -->
    <section id="hero">
        <div class="hero-content">
            <h1 class="title-font" >Your One-Stop Shop for Computers & Expert Service</h1>
            <p>Buy, Repair, Maintain - We've Got You Covered</p>
            <button class="title-font" onclick="scrollToSection('services')">Explore Services</button>
        </div>
    </section>






    <div  class="new_to_con">
 <style>
  :root {
  --color-text: navy;
  --color-bg: papayawhip;
  --color-bg-accent: #ecdcc0;
  --size: clamp(10rem, 1rem + 40vmin, 30rem);
  --gap: calc(var(--size) / 14);
  --duration: 60s;
  --scroll-start: 0;
  --scroll-end: calc(-100% - var(--gap));
}

@media (prefers-color-scheme: dark) {
  :root {
    --color-text: papayawhip;
    --color-bg: #e8e8e8;
    --color-bg-accent: #2626a0;
  }
}

* {
  box-sizing: border-box;
}

.new_to_con {
  display: grid;
  align-content: center;
  overflow: hidden;
  gap: var(--gap);
  width: 100%;
  font-family: system-ui, sans-serif;
  font-size: 1rem;
  line-height: 1.5;
  color: var(--color-text);
  background-color: #e8e8e8;
}

.marquee {
  display: flex;
  overflow: hidden;
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
          user-select: none;
  gap: var(--gap);
  -webkit-mask-image: linear-gradient(
    var(--mask-direction, to right),
    hsl(0 0% 0% / 0),
    hsl(0 0% 0% / 1) 20%,
    hsl(0 0% 0% / 1) 80%,
    hsl(0 0% 0% / 0)
  );
          mask-image: linear-gradient(
    var(--mask-direction, to right),
    hsl(0 0% 0% / 0),
    hsl(0 0% 0% / 1) 20%,
    hsl(0 0% 0% / 1) 80%,
    hsl(0 0% 0% / 0)
  );
}

.marquee__group {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: space-around;
  gap: var(--gap);
  min-width: 100%;
  -webkit-animation: scroll-x var(--duration) linear infinite;
          animation: scroll-x var(--duration) linear infinite;
}

@media (prefers-reduced-motion: reduce) {
  .marquee__group {
    -webkit-animation-play-state: paused;
            animation-play-state: paused;
  }
}

.marquee--vertical {
  --mask-direction: to bottom;
}

.marquee--vertical,
.marquee--vertical .marquee__group {
  flex-direction: column;
}

.marquee--vertical .marquee__group {
  -webkit-animation-name: scroll-y;
          animation-name: scroll-y;
}

.marquee--reverse .marquee__group {
  animation-direction: reverse;
  -webkit-animation-delay: -3s;
          animation-delay: -3s;
}

@-webkit-keyframes scroll-x {
  from {
    transform: translateX(var(--scroll-start));
  }
  to {
    transform: translateX(var(--scroll-end));
  }
}

@keyframes scroll-x {
  from {
    transform: translateX(var(--scroll-start));
  }
  to {
    transform: translateX(var(--scroll-end));
  }
}

@-webkit-keyframes scroll-y {
  from {
    transform: translateY(var(--scroll-start));
  }
  to {
    transform: translateY(var(--scroll-end));
  }
}

@keyframes scroll-y {
  from {
    transform: translateY(var(--scroll-start));
  }
  to {
    transform: translateY(var(--scroll-end));
  }
}

/* Element styles */
.marquee svg {
  display: grid;
  place-items: center;
  width: var(--size);
  fill: #000;
  background: #FFF;
  aspect-ratio: 16/9;
  padding: calc(var(--size) / 10);
  border-radius: 0.5rem;
}

.marquee--vertical svg {
  aspect-ratio: 1;
  width: calc(var(--size) / 1.5);
  padding: calc(var(--size) / 6);
}

/* Parent wrapper */
.wrapper {
  display: flex;
  flex-direction: column;
  gap: var(--gap);
  margin: auto;
  max-width: 100vw;
}

.wrapper--vertical {
  flex-direction: row;
  height: 100vh;
}

/* Toggle direction button */
.toggle {
  --size: 3rem;
  position: relative;
  position: fixed;
  top: 1rem;
  left: 1rem;
  width: var(--size);
  height: var(--size);
  font: inherit;
  text-align: center;
  cursor: pointer;
  outline: none;
  border: none;
  border-radius: 50%;
  color: inherit;
  background-color: var(--color-bg-accent);
  z-index: 1;
}

.toggle:focus-visible {
  box-shadow: 0 0 0 2px var(--color-text);
}

.toggle span {
  position: absolute;
  display: inline-block;
  top: 50%;
  left: calc(100% + 0.4em);
  width: -webkit-fit-content;
  width: -moz-fit-content;
  width: fit-content;
  white-space: nowrap;
  transform: translateY(-50%);
  -webkit-animation: fade 400ms 4s ease-out forwards;
          animation: fade 400ms 4s ease-out forwards;
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
          user-select: none;
}

.toggle svg {
  --size: 1.5rem;
  position: absolute;
  top: 50%;
  left: 50%;
  width: var(--size);
  height: var(--size);
  fill: currentcolor;
  transform: translate(-50%, -50%);
  transition: transform 300ms cubic-bezier(0.25, 1, 0.5, 1);
}

.toggle--vertical svg {
  transform: translate(-50%, -50%) rotate(-90deg);
}

@-webkit-keyframes fade {
  to {
    opacity: 0;
    visibility: hidden;
  }
}

@keyframes fade {
  to {
    opacity: 0;
    visibility: hidden;
  }
}

@font-face {
  font-family: 'bd title';
  /* src: url('../assets/font/bd title.woff2') format('woff2'); */
  src: url('../assets/font/pp.woff2') format('woff2');
  
}

.wrapper h1{
    font-family: bd title !important;
    text-transform: capitalize;
   font-weight: 600;
   color: #3c0e40;
   text-align: center;
   line-height: 1.1;
   font-size: xx-large;

}
  </style>
  
 
<!-- partial:index.partial.html -->
    <!-- <button class="toggle" id="direction-toggle">
    <span>Toggle scroll axis</span>
    <svg aria-hidden="true" viewBox="0 0 512 512" width="100" title="arrows-alt-h">
        <path d="M377.941 169.941V216H134.059v-46.059c0-21.382-25.851-32.09-40.971-16.971L7.029 239.029c-9.373 9.373-9.373 24.568 0 33.941l86.059 86.059c15.119 15.119 40.971 4.411 40.971-16.971V296h243.882v46.059c0 21.382 25.851 32.09 40.971 16.971l86.059-86.059c9.373-9.373 9.373-24.568 0-33.941l-86.059-86.059c-15.119-15.12-40.971-4.412-40.971 16.97z" />
    </svg>
    </button> -->
<article style="margin-top:6%" class="wrapper">

<h1>Top services brands</h1>
  <div class="marquee">
    <div class="marquee__group">
      <a href="../shop.php?search=&brand%5B%5D=hp" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#hp" /></svg></a>
      <a href="../shop.php?search=&brand%5B%5D=microsoft" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#microsoft" /></svg></a>
      <a href="../shop.php?search=&brand%5B%5D=lenovo" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#lenovo" /></svg></a>
      <a href="../shop.php?search=&brand%5B%5D=msi" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#msi" /></svg></a>
      <a href="../shop.php?search=&brand%5B%5D=dell" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#dell" /></svg></a>
      <a href="../shop.php?search=intel" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#intel" /></svg></a>
      <a href="../shop.php?search=&brand%5B%5D=apple" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#apple" /></svg></a>
      <a href="../shop.php?search=&brand%5B%5D=asus" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#asus" /></svg></a>
      <a href="../shop.php?search=amd" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#amd" /></svg></a>
      <a href="../shop.php?search=&brand%5B%5D=acer" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#acer" /></svg></a>
    </div>

    <div aria-hidden="true" class="marquee__group">
    <a href="../shop.php?search=&brand%5B%5D=lenovo" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#lenovo" /></svg></a>
        <a href="../shop.php?search=&brand%5B%5D=dell" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#dell" /></svg></a>
        <a href="../shop.php?search=&brand%5B%5D=hp" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#hp" /></svg></a>
        <a href="../shop.php?search=intel" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#intel" /></svg></a>
        <a href="../shop.php?search=&brand%5B%5D=apple" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#apple" /></svg></a>
        <a href="../shop.php?search=&brand%5B%5D=asus" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#asus" /></svg></a>
        <a href="../shop.php?search=&brand%5B%5D=microsoft" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#microsoft" /></svg></a>
      <a href="../shop.php?search=&brand%5B%5D=acer" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#acer" /></svg></a>
      <a href="../shop.php?search=amd" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#amd" /></svg></a>
    </div>
  </div>

  <div class="marquee marquee--reverse">
    <div class="marquee__group">
    <a href="../shop.php?search=&brand%5B%5D=lenovo" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#lenovo" /></svg></a>
        <a href="../shop.php?search=&brand%5B%5D=dell" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#dell" /></svg></a>
        <a href="../shop.php?search=&brand%5B%5D=hp" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#hp" /></svg></a>
        <a href="../shop.php?search=intel" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#intel" /></svg></a>
        <a href="../shop.php?search=&brand%5B%5D=apple" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#apple" /></svg></a>
        <a href="../shop.php?search=&brand%5B%5D=asus" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#asus" /></svg></a>
        <a href="../shop.php?search=&brand%5B%5D=microsoft" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#microsoft" /></svg></a>
      <a href="../shop.php?search=&brand%5B%5D=acer" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#acer" /></svg></a>
      <a href="../shop.php?search=amd" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#amd" /></svg></a>
    </div>

    <div aria-hidden="true" class="marquee__group">
    <a href="../shop.php?search=&brand%5B%5D=lenovo" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#lenovo" /></svg></a>
        <a href="../shop.php?search=&brand%5B%5D=dell" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#dell" /></svg></a>
        <a href="../shop.php?search=&brand%5B%5D=hp" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#hp" /></svg></a>
        <a href="../shop.php?search=intel" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#intel" /></svg></a>
        <a href="../shop.php?search=&brand%5B%5D=apple" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#apple" /></svg></a>
        <a href="../shop.php?search=&brand%5B%5D=asus" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#asus" /></svg></a>
        <a href="../shop.php?search=&brand%5B%5D=microsoft" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#microsoft" /></svg></a>
      <a href="../shop.php?search=&brand%5B%5D=acer" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#acer" /></svg></a>
      <a href="../shop.php?search=amd" target="_blank" rel="noopener noreferrer"><svg><use xlink:href="#amd" /></svg></a>
    </div>
  </div>
</article>


<svg style="display: none" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <symbol id="hp" viewBox="0 0 24 24">
      <path d="M8.4210347,0.00016035599 L5,15.127022 L7.13818677,15.127022 L10.5590611,0.00016035599 L8.4210347,0.00016035599 Z M17.4142797,8.87313837 L15.9176772,15.0979976 L18.0557037,15.0979976 L19.5523061,8.87313837 L17.4142797,8.87313837 Z M13.7794905,8.87313837 L10.3586161,24 L12.4966425,24 L15.9176772,8.87313837 L13.7794905,8.87313837 Z M10.131552,8.87313837 L8.63478923,15.0979976 L10.7728157,15.0979976 L12.2694181,8.87313837 L10.131552,8.87313837 Z"/>
    </symbol>
    <symbol id="microsoft" viewBox="0 0 16 16">
    <path d="M1 1h6.5v6.5H1V1zM8.5 1H15v6.5H8.5V1zM1 8.5h6.5V15H1V8.5zM8.5 8.5H15V15H8.5V8.5z"/>
    </symbol>

    <symbol id="dell" viewBox="-19 5 80 24">
      <path d="M23.953 19.469v-7.036h1.625v5.604h2.854v1.427h-4.479zM10.906 15.083l3.974-3.057c0.375 0.302 0.745 0.609 1.12 0.917l-3.771 2.854 0.818 0.714 3.766-2.958c0.375 0.307 0.75 0.615 1.125 0.922-1.25 0.99-2.51 1.974-3.771 2.953l0.813 0.714 3.776-3.057-0.005-2.651h1.63v5.604h2.958v1.427h-4.484v-2.646c-1.328 1.016-2.651 2.036-3.974 3.052l-3.974-3.052c-0.193 0.969-0.87 1.813-1.75 2.255-0.38 0.198-0.797 0.323-1.219 0.37-0.245 0.031-0.49 0.021-0.734 0.021h-2.516v-7.031h2.901c0.755 0.010 1.49 0.271 2.083 0.745 0.604 0.479 1.036 1.161 1.234 1.906zM6.219 13.859v4.281h1.271c0.542-0.021 1.047-0.276 1.38-0.698 0.573-0.719 0.667-1.708 0.245-2.521-0.266-0.51-0.74-0.885-1.302-1.021-0.224-0.052-0.453-0.042-0.677-0.042zM15.76 0h0.448c2.516 0.031 4.984 0.661 7.208 1.828 2.172 1.146 4.052 2.766 5.5 4.745 1.958 2.667 3.036 5.88 3.083 9.188v0.479c-0.052 3.984-1.589 7.807-4.313 10.714-2.969 3.167-7.104 4.99-11.443 5.047h-0.484c-1.891-0.026-3.76-0.391-5.526-1.073-2.151-0.839-4.094-2.13-5.698-3.781-1.719-1.771-3.010-3.906-3.771-6.25-0.484-1.505-0.74-3.073-0.766-4.656v-0.479c0.052-3.036 0.974-6 2.656-8.526 1.573-2.375 3.734-4.292 6.281-5.563 2.12-1.063 4.453-1.635 6.823-1.672zM15.557 1.641c-3.13 0.089-6.141 1.203-8.573 3.167-1.995 1.604-3.521 3.708-4.427 6.099-1.318 3.505-1.224 7.385 0.255 10.823 1.026 2.354 2.656 4.385 4.729 5.896 1.885 1.375 4.073 2.266 6.38 2.594 1.943 0.281 3.943 0.167 5.839-0.344 2.594-0.703 4.938-2.115 6.766-4.083 1.828-1.964 3.068-4.406 3.578-7.042 0.401-2.068 0.344-4.198-0.161-6.24-1.193-4.792-4.771-8.635-9.464-10.172-1.589-0.516-3.255-0.755-4.922-0.698z"/>
    </symbol>
    <symbol id="msi" viewBox="0 0 174 40">
    <path d="M189.4,81.7l11.2-30.8a101.5,101.5,0,0,0-16.7,1.6L173.3,81.7Zm-1.9-39.2-2.1,6a95.7,95.7,0,0,1,16.7-1.7l2.2-5.9a102.3,102.3,0,0,0-16.8,1.6M129.2,74.4a87.6,87.6,0,0,0,15.5,1.4h0c5.5,0,9.4-1.2,9.8-4.9.2-1.8-1.4-3-4.5-4.7s-6.4-2.9-9.4-4.9a7.5,7.5,0,0,1-3.3-8.6c1.2-3.9,4-6.2,8.2-8.4s8.8-3.4,18.4-3.4a91.9,91.9,0,0,1,17.5,1.6l-2,5.7a89.3,89.3,0,0,0-15.5-1.4c-5.5,0-9.4,1.2-9.9,4.9-.2,1.7,1.5,3,4.5,4.7s6.5,2.9,9.5,4.9a7.5,7.5,0,0,1,3.3,8.6c-1.2,3.9-4,6.2-8.2,8.3s-8.8,3.5-18.4,3.5h0A91.9,91.9,0,0,1,127.2,80Zm-24.3,7.3L114.3,56c3.1-8.6-8.3-5.5-10.9-3.5s-5.6,5.1-7,8.8L89,81.7H72.9L82.2,56c3.1-8.6-8.3-5.5-10.8-3.5s-5.7,5.1-7,8.8L57,81.7H40.8L55.7,40.9c5.1,0,7.9,1.1,10.2,3.4a7.3,7.3,0,0,1,1.7,3.1,55.4,55.4,0,0,1,7.5-3.9,31.9,31.9,0,0,1,12.6-2.6c5.1,0,7.9,1.1,10.2,3.4a6.3,6.3,0,0,1,1.7,3.1,55.4,55.4,0,0,1,7.5-3.9,32.3,32.3,0,0,1,12.6-2.6c5.2,0,8,1.1,10.3,3.4s2.3,6.5,1.4,8.8L121,81.7Z" transform="translate(-40.8 -40.8)"/>
    </symbol>

    <symbol id="lenovo" viewBox="0 4 24 24">
      <path xmlns="http://www.w3.org/2000/svg" d="M27.005 15.229c-0.63-0.005-1.13 0.526-1.089 1.151-0.021 0.63 0.479 1.151 1.104 1.156 0.63 0.005 1.125-0.526 1.089-1.156 0.021-0.625-0.479-1.146-1.104-1.151zM18.323 15.229c-0.625-0.005-1.13 0.526-1.083 1.151-0.021 0.63 0.474 1.151 1.104 1.156 0.625 0.005 1.125-0.526 1.083-1.156 0.021-0.625-0.474-1.146-1.104-1.151zM8.979 15.156c-0.339-0.010-0.661 0.141-0.87 0.411-0.203 0.286-0.286 0.635-0.229 0.979l1.969-0.813c-0.146-0.349-0.49-0.578-0.87-0.578zM0 10.667v10.667h32v-10.667zM6.677 18.438h-3.708v-5.333h1.146v4.297h2.563zM9.099 17.609c0.432 0.005 0.854-0.146 1.177-0.438l0.714 0.547c-0.51 0.505-1.193 0.786-1.911 0.786-1.224 0.12-2.297-0.823-2.333-2.052-0.036-1.234 0.979-2.234 2.214-2.188 0.609-0.031 1.203 0.214 1.62 0.667 0.271 0.328 0.443 0.724 0.495 1.146l-2.885 1.203c0.245 0.224 0.573 0.344 0.911 0.328zM15.609 18.438h-1.13v-2.339c-0.016-0.5-0.443-0.891-0.948-0.865-0.5-0.031-0.927 0.365-0.932 0.865v2.339h-1.125v-4.109h1.13v0.589c0.318-0.411 0.813-0.651 1.333-0.656 0.927-0.052 1.698 0.703 1.667 1.63zM18.255 18.505c-1.87-0.078-2.734-2.359-1.38-3.656 1.349-1.292 3.594-0.339 3.594 1.531-0.005 1.208-1.010 2.172-2.214 2.125zM21.984 18.432l-1.688-4.104h1.286l1.021 2.802 1.021-2.802h1.286l-1.693 4.104zM26.932 18.505c-1.865-0.078-2.729-2.359-1.38-3.656 1.354-1.292 3.594-0.339 3.594 1.531-0.005 1.208-1.005 2.172-2.214 2.125zM29.599 17.948h-0.188v0.49h-0.109v-0.49h-0.182v-0.104h0.479zM30.323 18.438h-0.109v-0.422l-0.182 0.286h-0.016l-0.182-0.286v0.422h-0.109v-0.594h0.12l0.177 0.281 0.177-0.281h0.12z"/>
    </symbol>

    <symbol id="apple"   viewBox="-20 5 80 24">
      <path xmlns="http://www.w3.org/2000/svg" d="M9.438 31.401c-0.63-0.422-1.193-0.938-1.656-1.536-0.516-0.615-0.984-1.266-1.422-1.938-1.021-1.495-1.818-3.125-2.375-4.849-0.667-2-0.99-3.917-0.99-5.792 0-2.094 0.453-3.922 1.339-5.458 0.651-1.198 1.625-2.203 2.797-2.906 1.135-0.708 2.453-1.094 3.786-1.12 0.469 0 0.974 0.068 1.51 0.198 0.385 0.109 0.854 0.281 1.427 0.495 0.729 0.281 1.13 0.453 1.266 0.495 0.427 0.156 0.786 0.224 1.068 0.224 0.214 0 0.516-0.068 0.859-0.172 0.193-0.068 0.557-0.188 1.078-0.411 0.516-0.188 0.922-0.349 1.245-0.469 0.495-0.146 0.974-0.281 1.401-0.349 0.521-0.078 1.036-0.104 1.531-0.063 0.948 0.063 1.813 0.266 2.589 0.557 1.359 0.547 2.458 1.401 3.276 2.615-0.349 0.214-0.667 0.458-0.969 0.734-0.651 0.573-1.198 1.25-1.641 2.005-0.573 1.026-0.865 2.188-0.859 3.359 0.021 1.443 0.391 2.714 1.12 3.813 0.521 0.802 1.208 1.484 2.047 2.047 0.417 0.281 0.776 0.474 1.12 0.604-0.161 0.5-0.333 0.984-0.536 1.464-0.464 1.078-1.016 2.109-1.667 3.083-0.578 0.839-1.031 1.464-1.375 1.88-0.536 0.635-1.052 1.12-1.573 1.458-0.573 0.38-1.25 0.583-1.938 0.583-0.469 0.021-0.932-0.042-1.38-0.167-0.385-0.13-0.766-0.271-1.141-0.432-0.391-0.177-0.792-0.333-1.203-0.453-0.51-0.135-1.031-0.198-1.552-0.198-0.536 0-1.057 0.068-1.547 0.193-0.417 0.12-0.818 0.26-1.214 0.432-0.557 0.234-0.927 0.391-1.141 0.458-0.427 0.125-0.87 0.203-1.318 0.229-0.693 0-1.339-0.198-1.979-0.599zM18.578 6.786c-0.906 0.453-1.771 0.646-2.63 0.583-0.135-0.865 0-1.75 0.359-2.719 0.318-0.828 0.745-1.573 1.333-2.24 0.609-0.693 1.344-1.266 2.172-1.677 0.88-0.453 1.719-0.698 2.521-0.734 0.104 0.906 0 1.797-0.333 2.76-0.307 0.854-0.76 1.641-1.333 2.344-0.583 0.693-1.302 1.266-2.115 1.682z"/>
    </symbol>

    <symbol id="acer" viewBox="0 0 24 24">
      <path xmlns="http://www.w3.org/2000/svg" d="M23.943 9.364c-.085-.113-.17-.198-.595-.226-.113 0-.453-.029-1.048-.029-1.56 0-2.636.482-3.175 1.417.142-.935-.765-1.417-2.749-1.417-2.324 0-3.798.935-4.393 2.834-.226.709-.226 1.276-.056 1.73h-.567c-.425.027-.992.056-1.36.056-.85 0-1.39-.142-1.588-.425-.17-.255-.17-.737.057-1.446.368-1.162 1.247-1.672 2.664-1.672.737 0 1.445.085 1.445.085.085 0 .142-.113.142-.198l-.028-.085-.057-.397c-.028-.255-.227-.397-.567-.453-.311-.029-.567-.029-.907-.029h-.028c-1.842 0-3.146.624-3.854 1.814.255-1.219-.596-1.814-2.551-1.814-1.105 0-1.9.029-2.353.085-.368.057-.595.199-.68.454l-.17.51c-.028.085.029.142.142.142.085 0 .425-.057.992-.086a24.816 24.816 0 0 1 1.672-.085c1.077 0 1.559.284 1.389.822-.029.114-.114.199-.255.227-1.02.17-1.842.284-2.438.369-1.7.226-2.692.736-2.947 1.587-.369 1.162.538 1.728 2.72 1.728 1.078 0 2.013-.056 2.75-.198.425-.085.652-.17.737-.453l.396-1.304c-.028 1.304.85 1.955 2.721 1.955.794 0 1.559-.028 1.927-.085.369-.056.567-.141.652-.425l.085-.396c.397.623 1.276.935 2.608.935 1.417 0 2.239-.029 2.465-.114a.523.523 0 0 0 .369-.311l.028-.085.17-.539c.029-.085-.028-.142-.142-.142l-.906.057c-.596.029-1.077.057-1.418.057-.651 0-1.076-.057-1.332-.142-.368-.142-.538-.397-.51-.822l2.863-.368c1.275-.17 2.154-.567 2.579-1.19l-.992 3.315c-.028.057 0 .114.028.142.029.028.085.057.199.057h1.19c.198 0 .283-.114.312-.199l1.048-3.656c.142-.481.567-.708 1.36-.708.71 0 1.22 0 1.56.028h.028c.057 0 .17-.028.255-.17l.17-.51c0-.085 0-.17-.057-.227zM4.841 13.73c-.368.057-.907.085-1.587.085-1.219 0-1.729-.255-1.587-.737.113-.34.425-.567.935-.624l2.75-.368zm12.669-2.95c-.114.369-.652.624-1.616.766l-2.295.311.056-.198c.199-.624.454-1.02.794-1.247.34-.227.907-.34 1.7-.34 1.05.028 1.503.255 1.36.708z"/>
    </symbol>

    <symbol id="asus" viewBox="0 0 24 24">
      <path xmlns="http://www.w3.org/2000/svg" d="M23.904 10.788V9.522h-4.656c-.972 0-1.41.6-1.482 1.182v.018-1.2h-1.368v1.266h1.362zm-6.144.456-1.368-.078v1.458c0 .456-.228.594-1.02.594H14.28c-.654 0-.93-.186-.93-.594v-1.596l-1.386-.102v1.812h-.03c-.078-.528-.276-1.14-1.596-1.23L6 11.22c0 .666.474 1.062 1.218 1.14l3.024.306c.24.018.414.09.414.288 0 .216-.18.24-.456.24H5.946V11.22l-1.386-.09v3.348h5.646c1.26 0 1.662-.654 1.722-1.2h.03c.156.864.912 1.2 2.19 1.2h1.41c1.494 0 2.202-.456 2.202-1.524zm4.398.258-4.338-.258c0 .666.438 1.11 1.182 1.17l3.09.24c.24.018.384.078.384.276 0 .186-.168.258-.516.258h-4.212v1.29h4.302c1.356 0 1.95-.474 1.95-1.554 0-.972-.534-1.338-1.842-1.422zm-10.194-1.98h1.386v1.266h-1.386zM3.798 11.07l-1.506-.15L0 14.478h1.686zm7.914-1.548h-4.23c-.984 0-1.416.612-1.518 1.2v-1.2H3.618c-.33 0-.486.102-.642.33l-.648.936h9.384z"/>
    </symbol>

    <symbol id="intel" viewBox="0 0 24 24">
      <path xmlns="http://www.w3.org/2000/svg" d="M20.42 7.345v9.18h1.651v-9.18zM0 7.475v1.737h1.737V7.474zm9.78.352v6.053c0 .513.044.945.13 1.292.087.34.235.618.44.828.203.21.475.359.803.451.334.093.754.136 1.255.136h.216v-1.533c-.24 0-.445-.012-.593-.037a.672.672 0 0 1-.39-.173.693.693 0 0 1-.173-.377 4.002 4.002 0 0 1-.037-.606v-2.182h1.193v-1.416h-1.193V7.827zm-3.505 2.312c-.396 0-.76.08-1.082.241-.327.161-.6.384-.822.668l-.087.117v-.902H2.658v6.256h1.639v-3.214c.018-.588.16-1.02.433-1.299.29-.297.642-.445 1.044-.445.476 0 .841.149 1.082.433.235.284.359.686.359 1.2v3.324h1.663V12.97c.006-.89-.229-1.595-.686-2.09-.458-.495-1.1-.742-1.917-.742zm10.065.006a3.252 3.252 0 0 0-2.306.946c-.29.29-.525.637-.692 1.033a3.145 3.145 0 0 0-.254 1.273c0 .452.08.878.241 1.274.161.395.39.742.674 1.032.284.29.637.526 1.045.693.408.173.86.26 1.342.26 1.397 0 2.262-.637 2.782-1.23l-1.187-.904c-.248.297-.841.699-1.583.699-.464 0-.847-.105-1.138-.321a1.588 1.588 0 0 1-.593-.872l-.019-.056h4.915v-.587c0-.451-.08-.872-.235-1.267a3.393 3.393 0 0 0-.661-1.033 3.013 3.013 0 0 0-1.02-.692 3.345 3.345 0 0 0-1.311-.248zm-16.297.118v6.256h1.651v-6.256zm16.278 1.286c1.132 0 1.664.797 1.664 1.255l-3.32.006c0-.458.525-1.255 1.656-1.261zm7.073 3.814a.606.606 0 0 0-.606.606.606.606 0 0 0 .606.606.606.606 0 0 0 .606-.606.606.606 0 0 0-.606-.606zm-.008.105a.5.5 0 0 1 .002 0 .5.5 0 0 1 .5.501.5.5 0 0 1-.5.5.5.5 0 0 1-.5-.5.5.5 0 0 1 .498-.5zm-.233.155v.699h.13v-.285h.093l.173.285h.136l-.18-.297a.191.191 0 0 0 .118-.056c.03-.03.05-.074.05-.136 0-.068-.02-.117-.063-.154-.037-.038-.105-.056-.185-.056zm.13.099h.154c.019 0 .037.006.056.012a.064.064 0 0 1 .037.031c.013.013.012.031.012.056a.124.124 0 0 1-.012.055.164.164 0 0 1-.037.031c-.019.006-.037.013-.056.013h-.154z"/>
    </symbol>

    <symbol id="amd" viewBox="0 0 24 24">
      <path xmlns="http://www.w3.org/2000/svg" d="m18.324 9.137 1.559 1.56h2.556v2.557L24 14.814V9.137zM2 9.52l-2 4.96h1.309l.37-.982H3.9l.408.982h1.338L3.432 9.52zm4.209 0v4.955h1.238v-3.092l1.338 1.562h.188l1.338-1.556v3.091h1.238V9.52H10.47l-1.592 1.845L7.287 9.52zm6.283 0v4.96h2.057c1.979 0 2.88-1.046 2.88-2.472 0-1.36-.937-2.488-2.747-2.488zm1.237.91h.792c1.17 0 1.63.711 1.63 1.57 0 .728-.372 1.572-1.616 1.572h-.806zm-10.985.273.791 1.932H2.008zm17.137.307-1.604 1.603v2.25h2.246l1.604-1.607h-2.246z"/>
    </symbol>
  </defs>
</svg>

      
   
<script>
    /*
 JS to toggle scroll axis styles
*/
const control = document.getElementById("direction-toggle");
const marquees = document.querySelectorAll(".marquee");
const wrapper = document.querySelector(".wrapper");

control.addEventListener("click", () => {
  control.classList.toggle("toggle--vertical");
  wrapper.classList.toggle("wrapper--vertical");
  [...marquees].forEach((marquee) =>
    marquee.classList.toggle("marquee--vertical")
  );
});
</script>
<br><br>
</div>

<section  class=" p-6">

<div class="bg-white shadow-md rounded-lg p-6 max-w-md mx-auto">
        <p class="text-xl font-semibold text-gray-800 mb-4">Please note</p>
        <ul class="list-disc pl-5 text-gray-700 space-y-2">
            <li>Repair Costs will be provided after diagnosis</li>
            <li>Visitation Charge will be adjusted in the repair cost</li>
        </ul>
    </div>

    </section>


    <!-- Service Overview Section -->
    <section id="services">
        <h2 class="section-title title-font">Discover Our Services</h2>
        <div class="service-container">
            <div class="service-item">
                <div class="icon sales-icon"></div>
                <h3 class="title-font">Top-Tier Computer Sales</h3>
                <p>Explore a vast selection of new and certified pre-owned computers, including custom builds, all at
                    unbeatable prices.</p>
            </div>
            <div class="service-item">
                <div class="icon repair-icon"></div>
                <h3 class="title-font">Professional Computer Repair</h3>
                <p>Our expert technicians solve hardware and software issues swiftly, backed by a service warranty for
                    peace of mind.</p>
            </div>
            <div class="service-item">
                <div class="icon maintenance-icon"></div>
                <h3 class="title-font">Comprehensive Maintenance</h3>
                <p>Keep your systems in peak condition with regular tune-ups, diagnostics, and tailored maintenance
                    plans.</p>
            </div>
            <div class="service-item">
                <div class="icon corporate-icon"></div>
                <h3 class="title-font">Custom Corporate Solutions</h3>
                <p>Delivering end-to-end IT services for businesses, from infrastructure setup to ongoing support and
                    upgrades.</p>
            </div>
        </div>
    </section>



  <main class="container mx-auto px-4 py-8">
<h2 class="section-title title-font">Explore Service categories</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6">
            <?php while($row = mysqli_fetch_assoc($ser)) { ?>
            <div class="detail-box rounded-lg shadow-md p-6">
                <img src="<?= $row['img'] ?>" alt="<?= $row['category_name'] ?>" class="w-full rounded-lg mb-4">
                <h3 class="text-xl font-bold text-center text-gray-800"><?= $row['category_name'] ?></h3>
                <div onclick="location.href = './home/home.php?service_category_id=<?= $row['id'] ?>';" class="bg-blue-500 hover:bg-blue-700 cursor-pointer	 text-center text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Explore</div>
            </div>
            <?php } ?>
        </div>
    </main>


  <main class="container mx-auto px-4 py-8">
<h2 class="section-title title-font">Select your Services</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6">
            <?php while($row = mysqli_fetch_assoc($list)) { ?>
            <div class="detail-box rounded-lg shadow-md p-6">
                <img src="<?= $row['img'] ?>" alt="<?= $row['service_name'] ?>" class="w-full border-solid border-2 border-gray-300 rounded-lg mb-4">
                <h3 class="text-xl font-bold text-center text-gray-800"><?= $row['service_name'] ?></h3>
                <div onclick="location.href = './home/add_to_cart.php?service_id=<?= $row['id'] ?>';" class="bg-blue-500 hover:bg-blue-700 cursor-pointer	 text-center text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Add to Cart</div>
            </div>
            <?php } ?>
        </div>
    </main>








    <!-- Service Details Section -->
    <section id="details">
        <h2 class="section-title title-font">In-Depth Service Details</h2>

        <div class="detail-container grid grid-cols-2 gap-6">
            <!-- Computer Sales Details -->
            <div class="detail-box">
                <h3 class="title-font">Comprehensive Computer Sales</h3>
                <p>Choose from an extensive collection of computers designed to fit every budget and performance need.
                    From cutting-edge gaming systems to economical office setups, each purchase is backed by a warranty
                    and professional guidance from our experts.</p>
            </div>

            <!-- Computer Repair Details -->
            <div class="detail-box">
                <h3 class="title-font">Expert Computer Repair</h3>
                <p>Our certified technicians are skilled in diagnosing and repairing a broad spectrum of hardware and
                    software issues. We ensure quick service with guaranteed satisfaction, offering post-repair support
                    to keep your systems running smoothly.</p>
            </div>

            <!-- Maintenance Services Details -->
            <div class="detail-box">
                <h3 class="title-font">Reliable Maintenance Services</h3>
                <p>Regular maintenance is key to extending the life of your computers. Our service plans include
                    detailed inspections, system optimizations, and preventive measures to avoid costly breakdowns.</p>
            </div>

            <!-- Corporate Solutions Details -->
            <div class="detail-box">
                <h3 class="title-font">Tailored Corporate Solutions</h3>
                <p>We deliver custom IT solutions tailored to your business needs, ranging from infrastructure setup to
                    ongoing support. Our corporate services include bulk hardware procurement, system integration, and
                    dedicated IT management.</p>
            </div>
        </div>
    </section>


<!-- 
    <div class="container mx-auto p-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <?php
        // SQL query to fetch categories
        $sql = "SELECT * FROM service_categories";
        $service_categories = $conn->query($sql);

        // Loop through each category and display
        if ($service_categories->num_rows > 0) {
            while($srowsbyc = $service_categories->fetch_assoc()) {
                ?>
                <a href="#" class="category-link" data-category-id="<?php echo $srowsbyc['id']; ?>">
                    <div class="bg-white rounded-lg shadow-md p-4">
                        <img src="<?php echo $srowsbyc['img']; ?>" alt="<?php echo $srowsbyc['category_name']; ?>" class="w-full rounded-lg">
                        <h2 class="text-xl font-bold mt-2"><?php echo $srowsbyc['category_name']; ?></h2>
                    </div>
                </a>
                <?php
            }
        } else {
            echo "No categories found.";
        }
        ?>
    </div> -->


  <!-- Modal Structure -->
<!-- <div id="category-modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Services</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-body">
                
                <div class="loading-spinner" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript code to fetch services for each category
    $(document).ready(function() {
        $('.category-link').on('click', function(e) {
            e.preventDefault();
            var categoryId = $(this).data('category-id');
            $.ajax({
                type: 'POST',
                url: 'fetch_services.php',
                data: {category_id: categoryId},
                beforeSend: function() {
                    $('#modal-body .loading-spinner').show();
                },
                success: function(data) {
                    $('#modal-body').html(data);
                    $('#modal-body .loading-spinner').hide();
                    $('#category-modal').modal('show');
                }
            });
        });
    });
</script>
  
</div> --> 


    <h2 class="section-title title-font">Rate Card</h2>

    <div class="container mx-auto">

        <!-- Section 1 -->
        <details class="mb-6 shadow-md rounded-lg">
            <summary class="bg-gray-800 text-white p-4 rounded-t-lg cursor-pointer flex justify-between items-center">
                <span class="font-semibold">General Sevices </span>
                <div class="flex items-center">
                    <span class="font-semibold mr-2">Price</span>
                    <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </summary>
            <div class="bg-white p-4 border-t border-gray-200 rounded-b-lg">
                <div class="flex justify-between py-2">
                    <span>Laubor Charge</span>
                    <span>₹150/-</span>
                </div>
                <div class="flex justify-between py-2">
                    <span>Visit at home/site</span>
                    <span>₹150/-</span>
                </div>
                <div class="flex justify-between py-2">
                    <span>Technical Support (Online)</span>
                    <span>FREE</span>
                </div>
                <div class="flex justify-between py-2">
                    <span>Delivery Charges</span>
                    <span>₹50.00/- Per K/M</span>
                </div>
            </div>
        </details>

    </div>
    <script>
        document.querySelectorAll('details').forEach((detail) => {
            detail.addEventListener('toggle', (event) => {
                const svg = event.target.querySelector('summary svg');
                if (event.target.open) {
                    svg.classList.add('rotate-180');
                } else {
                    svg.classList.remove('rotate-180');
                }
            });
        });
    </script>



    <!-- Book a Service Section -->
    <section id="book-service">
        <h2 class="title-font">Book a Service</h2>
        <div class="booking-container">
            <div class="booking-info">
                <h3 class="title-font">Easy Booking for All Our Services</h3>
                <p>Whether you need a new computer, a quick repair, regular maintenance, or a corporate solution, we're
                    here to help. Book your service online, and let us take care of the rest.</p>
            </div>
            <form class="booking-form" action="process_booking.php" method="post">
                <!-- Autocomplete fields if user is logged in -->
                <?php if (isset($_SESSION['user_id'])): ?>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_name); ?>" required>

                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user_phone); ?>"
                    required>
                <?php else: ?>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>

                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" required>
                <?php endif; ?>

                <label for="service-type">Select Service:</label>
                <select id="service-type" name="service-type" required>
                    <option value="computer-sales">Computer Sales</option>
                    <option value="computer-repair">Computer Repair</option>
                    <option value="maintenance">Maintenance Services</option>
                    <option value="corporate-solutions">Corporate Solutions</option>
                </select>

                <label for="date">Preferred Date:</label>
                <input type="date" id="date" name="date" required>

                <label for="time">Preferred Time:</label>
                <input type="time" id="time" name="time" required>

                <label for="details">Additional Details:</label>
                <textarea id="details" name="details" placeholder="Provide any additional details here..."></textarea>

                <button class="title-font" type="submit">Book Now</button>
            </form>

        </div>
    </section>





  

    <!-- Testimonials Section -->
    <section id="testimonials">
        <h2 class="section-title title-font">What Our Customers Are Saying</h2>
        <div class="testimonial-container">
            <div class="testimonial-box">
                <p>"Amazing customer service and top-quality products. Highly recommended!" - Sarah K.</p>
            </div>
            <div class="testimonial-box">
                <p>"Quick and reliable repair services. My go-to computer shop for all my needs." - Michael R.</p>
            </div>
            <div class="testimonial-box">
                <p>"They saved my business with their expert corporate IT solutions. Truly exceptional!" - Linda W.</p>
            </div>
            <div class="testimonial-box">
                <p>"Affordable prices and knowledgeable staff. I wouldn't shop anywhere else." - David T.</p>
            </div>
        </div>
    </section>





    <!-- FAQ Section -->
    <section id="faq">
        <h2 class="section-title title-font">Frequently Asked Questions</h2>
        <p>Have questions? Find answers to the most common inquiries by exploring our <a href="../pages/faq.html">FAQ
                page</a>.</p>
    </section>


    <!-- Call-to-Action Section -->
    <section id="contact">
        <h2 class="section-title title-font">Get in Touch</h2>
        <div class="contact-container">
        <form action="../pages/help.php" method="post" class="contact-form">
    <input type="text" name="name" placeholder="Your Name" required>
    <input type="email" name="email" placeholder="Your Email" required>
    <textarea name="message" placeholder="Your Message" required></textarea>
    <button class="title-font" type="submit">Send Message</button>
</form>


        </div>
    </section>


    <script>
    function scrollToSection(sectionId) {
        document.getElementById(sectionId).scrollIntoView({
            behavior: 'smooth'
        });
    }






    // Optional: Enable smooth scrolling behavior for testimonials container
    document.querySelector('#testimonials .testimonial-container').addEventListener('wheel', function(event) {
        if (event.deltaY > 0) {
            this.scrollLeft += 100;
        } else {
            this.scrollLeft -= 100;
        }
        event.preventDefault();
    });
    </script>
    <!--=============== HEADER ===============-->
    <div id="footer"></div>

</body>

</html>