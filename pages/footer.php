<?php
session_start();
include '../db_connect.php'; // Include your database connection

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>footer</title>
    <link rel="stylesheet" href="../pages/css/fstyle.css">

</head>

<body>



    <footer id="colophon" class="site-footer">
        <div class="footer-gradient">
        </div>
        <div class="footer-card">
            <div class="footer-card-top">
                <div class="footer-logo">
                    <img src="../assets/img/1x/bd6.png" alt="logo" srcset="">
                </div>
            </div>
            <hr>
            <div class="footer-card-bottom">
                <div class="footer-menus">
                    <div class="product footer-menu-column">
                        <div class="footer-menu-heading">Useful Link</div>
                        <ul class="sub-menu">
                            <li id="menu-item-54711"
                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-54711"><a
                                    href="../index.php">Home</a></li>
                            <li id="menu-item-54710"
                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-54710"><a
                                    href="../services">Tech Services</a></li>
                            <li id="menu-item-55376"
                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-55376"><a
                                    href="../shop.php">Products</a></li>
                            <li id="menu-item-55376"
                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-55376"><a
                                    href="../blog">Blog</a></li>
                                    <li id="menu-item-55376"
                                    class="menu-item menu-item-type-post_type menu-item-object-page menu-item-55376"><a
                                        href="../track_order.php">Track Your Order</a></li>
                                       
                        </ul>
                    </div>
                    <div class="product footer-menu-column">
                        <div class="footer-menu-heading">Profile Link</div>
                        <ul class="sub-menu">
                        
            <?php if(isset($_SESSION['user_id'])): ?>
                <li id="menu-item-54711"
                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-54711"><a
                                    href="../profile">Profile</a></li>
                            <li id="menu-item-54710"
                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-54710"><a
                                    href="../logout.php">Logout</a></li>
                            <li id="menu-item-54710"
                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-54710"><a
                                    href="../change_password.php">Change Password</a></li>
                            <li id="menu-item-54710"
                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-54710"><a
                                    href="../profile?page=orders">My Orders</a></li>
            <?php else: ?>
                <li id="menu-item-55376"
                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-55376"><a
                                    href="../login.php">Login</a></li>
                                    <li id="menu-item-55376"
                                    class="menu-item menu-item-type-post_type menu-item-object-page menu-item-55376"><a
                                        href="../signup.php">Signup</a></li>

            <?php endif; ?>
     
                           
                           
                                        <!-- <li id="menu-item-55376"
                                    class="menu-item menu-item-type-post_type menu-item-object-page menu-item-55376"><a
                                        href="../bd/dashboard.php">My Dashboard</a></li> -->
                        </ul>
                    </div>
                    <div class="company footer-menu-column">
                        <div class="footer-menu-heading">Company</div>
                        <ul class="sub-menu">
                            <li id="menu-item-9090"
                                class="menu-item menu-item-type-custom menu-item-object-custom menu-item-9090"><a
                                    rel="noopener" href="../download/">Diagnostics App</a></li>
                            <li id="menu-item-9090"
                                class="menu-item menu-item-type-custom menu-item-object-custom menu-item-9090"><a
                                    rel="noopener" href="../pages/about-us.html">About Us</a></li>
                            <li id="menu-item-9091"
                                class="menu-item menu-item-type-custom menu-item-object-custom menu-item-9091"><a
                                    target="_blank" rel="noopener" href="../pages/toc.html">Term & Conditions</a></li>
                            <li id="menu-item-54713"
                                class="menu-item menu-item-type-custom menu-item-object-custom menu-item-54713"><a
                                    rel="noopener" href="../pages/help.html">Help</a></li>
                        </ul>
                    </div>
                    <div class="support footer-menu-column">
                        <div class="footer-menu-heading">Support</div>
                        <ul class="sub-menu">
                            <li id="menu-item-54715"
                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-54715"><a
                                    href="../pages/help.html">Help Center</a></li>
                                    <li id="menu-item-54715"
                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-54715"><a
                                    href="../pages/faq.html">Faq ( Frequently Asked Questions )</a></li>
                            <li id="menu-item-9106"
                                class="menu-item menu-item-type-custom menu-item-object-custom menu-item-9106"><a
                                    target="_blank" rel="noopener" href="../pages/toc.html">Term & Conditions</a></li>

                        </ul>
                    </div>
                </div>
                <div class="footer-social">
                    <div class="footer-social-header">
                        Social Media LInks
                    </div>
                    <div class="footer-social-content">
                        <a href="https://www.instagram.com/" rel="noopener noreferrer" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M3.06167 7.24464C3.10844 6.22264 3.26846 5.56351 3.48487 5.00402L3.48778 4.99629C3.70223 4.42695 4.03818 3.91119 4.47224 3.48489L4.47833 3.47891L4.48431 3.47282C4.91096 3.0382 5.42691 2.70258 5.99575 2.4887L6.00556 2.48495C6.56378 2.26786 7.22162 2.10843 8.24447 2.06167M3.06167 7.24464C3.0125 8.33659 2.99997 8.67508 2.99997 11.5063C2.99997 14.3381 3.01181 14.6758 3.06164 15.768M3.06167 7.24464L3.06167 7.52008M3.48867 18.0168C3.70255 18.5856 4.03817 19.1015 4.47279 19.5282L4.47887 19.5342L4.48484 19.5402C4.91116 19.9743 5.42694 20.3103 5.99628 20.5247L6.00478 20.5279C6.56351 20.7446 7.22167 20.9041 8.24447 20.9509M3.48867 18.0168L3.48492 18.0069C3.26783 17.4487 3.1084 16.7909 3.06164 15.768M3.48867 18.0168L3.47585 17.9492M3.06164 15.768L3.07839 15.8562M3.06164 15.768L3.06164 15.4919M3.47585 17.9492L3.07839 15.8562M3.47585 17.9492C3.30704 17.5033 3.13322 16.881 3.07839 15.8562M3.47585 17.9492C3.48177 17.9649 3.48768 17.9803 3.49359 17.9955C3.70766 18.5726 4.04685 19.0952 4.48679 19.5256C4.91708 19.9655 5.43944 20.3046 6.01636 20.5187C6.47934 20.699 7.13172 20.8875 8.24431 20.9385C9.3671 20.9896 9.71399 21 12.5062 21C15.2985 21 15.6457 20.9896 16.7685 20.9385C17.8824 20.8874 18.534 20.6979 18.9954 20.519C19.5726 20.305 20.0953 19.9657 20.5257 19.5256C20.9655 19.0953 21.3046 18.573 21.5187 17.9961C21.699 17.5331 21.8875 16.8808 21.9384 15.7682C21.9895 14.6454 22 14.2978 22 11.5063C22 8.71472 21.9895 8.36684 21.9384 7.24405C21.8871 6.12427 21.6959 5.47168 21.5161 5.00992C21.2811 4.40322 20.9831 3.94437 20.525 3.48627C20.0678 3.02999 19.6102 2.73179 19.003 2.49654C18.5396 2.31537 17.8866 2.12531 16.7685 2.07406C16.6712 2.06964 16.5798 2.06552 16.4921 2.06168M3.07839 15.8562C3.07684 15.8273 3.07539 15.7981 3.07403 15.7685C3.06961 15.6712 3.06548 15.5797 3.06164 15.4919M8.24447 2.06167C9.33668 2.01184 9.67505 2 12.5062 2C15.3374 2 15.6756 2.01252 16.7675 2.06168M8.24447 2.06167L8.52062 2.06167M16.7675 2.06168L16.4921 2.06168M16.7675 2.06168C17.7897 2.10844 18.4489 2.26844 19.0085 2.48487L19.0162 2.48781C19.5855 2.70226 20.1013 3.03821 20.5276 3.47227L20.5335 3.4783L20.5396 3.48422C20.9737 3.91055 21.3096 4.42646 21.5239 4.99596L21.5275 5.00559C21.7446 5.56381 21.9041 6.22165 21.9508 7.2445M8.52062 2.06167L16.4921 2.06168M8.52062 2.06167C9.44548 2.02123 9.95666 2.01253 12.5062 2.01253C15.056 2.01253 15.5671 2.02124 16.4921 2.06168M8.52062 2.06167C8.43284 2.06551 8.34134 2.06964 8.24402 2.07406C7.13004 2.12512 6.47843 2.31464 6.01708 2.49358C5.43767 2.70837 4.91328 3.04936 4.48192 3.49186C4.0281 3.94756 3.73105 4.40422 3.49655 5.0094C3.31536 5.4728 3.12527 6.12614 3.07402 7.24434C3.06961 7.34135 3.06549 7.43257 3.06167 7.52008M21.9508 15.768C21.9041 16.7908 21.7446 17.449 21.5279 18.0077L21.5247 18.0162C21.3102 18.5856 20.9743 19.1013 20.5402 19.5276L20.5341 19.5336L20.5282 19.5397C20.1015 19.9743 19.5856 20.3099 19.0167 20.5238L19.0069 20.5276C18.4487 20.7447 17.7908 20.9041 16.768 20.9509M3.06164 15.4919C3.0212 14.567 3.0125 14.0558 3.0125 11.5063C3.0125 8.95591 3.0212 8.44544 3.06167 7.52008M3.06164 15.4919L3.06167 7.52008M10.8155 15.5881C11.3515 15.8101 11.926 15.9244 12.5062 15.9244C13.678 15.9244 14.8018 15.4589 15.6304 14.6304C16.4589 13.8018 16.9244 12.678 16.9244 11.5063C16.9244 10.3345 16.4589 9.21072 15.6304 8.38215C14.8018 7.55359 13.678 7.0881 12.5062 7.0881C11.926 7.0881 11.3515 7.20238 10.8155 7.42442C10.2794 7.64645 9.79239 7.97189 9.38213 8.38215C8.97187 8.79242 8.64643 9.27947 8.42439 9.81551C8.20236 10.3515 8.08808 10.9261 8.08808 11.5063C8.08808 12.0865 8.20236 12.661 8.42439 13.197C8.64643 13.7331 8.97187 14.2201 9.38213 14.6304C9.79239 15.0406 10.2794 15.3661 10.8155 15.5881ZM9.37229 8.37231C10.2035 7.54113 11.3308 7.07418 12.5062 7.07418C13.6817 7.07418 14.809 7.54113 15.6402 8.37231C16.4714 9.20349 16.9383 10.3308 16.9383 11.5063C16.9383 12.6817 16.4714 13.809 15.6402 14.6402C14.809 15.4714 13.6817 15.9383 12.5062 15.9383C11.3308 15.9383 10.2035 15.4714 9.37229 14.6402C8.54111 13.809 8.07416 12.6817 8.07416 11.5063C8.07416 10.3308 8.54111 9.20349 9.37229 8.37231ZM19.434 6.04229C19.434 6.37873 19.3003 6.70139 19.0625 6.93929C18.8246 7.17719 18.5019 7.31084 18.1655 7.31084C17.829 7.31084 17.5064 7.17719 17.2685 6.93929C17.0306 6.70139 16.8969 6.37873 16.8969 6.04229C16.8969 5.70585 17.0306 5.38319 17.2685 5.1453C17.5064 4.9074 17.829 4.77375 18.1655 4.77375C18.5019 4.77375 18.8246 4.9074 19.0625 5.1453C19.3003 5.38319 19.434 5.70585 19.434 6.04229Z"
                                stroke="#5D666D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                        </a>
                        <a href="https://www.facebook.com/" rel="noopener noreferrer" target="_blank">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 1H9.81818C8.85375 1 7.9288 1.36875 7.24691 2.02513C6.56493 2.6815 6.18182 3.57174 6.18182 4.5V6.6H4V9.4H6.18182V15H9.09091V9.4H11.2727L12 6.6H9.09091V4.5C9.09091 4.31435 9.16756 4.1363 9.30393 4.00502C9.44029 3.87375 9.62531 3.8 9.81818 3.8H12V1Z"
                                    stroke="#5D666D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                </path>
                            </svg>
                        </a>
                        <a href="https://twitter.com/" rel="noopener noreferrer" target="_blank">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 15L7 9M15 1L9 7M9 7L4.5 1H1L7 9M9 7L15 15H11.5L7 9" stroke="#5D666D"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </a>
                        <a href="https://www.youtube.com/" rel="noopener noreferrer" target="_blank">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M14.7074 4.56153C14.5464 3.94685 14.0721 3.46282 13.4697 3.29855C12.3779 3 8 3 8 3C8 3 3.62208 3 2.53029 3.29855C1.92795 3.46282 1.45353 3.94685 1.29255 4.56153C1 5.67559 1 8 1 8C1 8 1 10.3244 1.29255 11.4385C1.45353 12.0531 1.92795 12.5372 2.53029 12.7015C3.62208 13 8 13 8 13C8 13 12.3779 13 13.4697 12.7015C14.0721 12.5372 14.5464 12.0531 14.7074 11.4385C15 10.3244 15 8 15 8C15 8 15 5.67559 14.7074 4.56153Z"
                                    stroke="#5D666D" stroke-width="2" stroke-miterlimit="10"></path>
                                <path d="M6 10L10 8.0001L6 6V10Z" stroke="#5D666D" stroke-width="2"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-copyright">
                <b>Copyright © 2024-25 Brand Dilation •</b> <span style="white-space: nowrap">Powered by Man
                    Navlakha<br><br><br><span>
                    </span></span>
            </div>
        </div>
    </footer>




</body>

</html>