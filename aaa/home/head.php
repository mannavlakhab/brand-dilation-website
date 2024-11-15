
<html>
<head>
    <title>Simple Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 ">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64">
                <div class="flex flex-col flex-grow bg-gray shadow-md  overflow-y-auto">
                    <div class="flex items-center flex-shrink-0 px-4 py-4">
                        <img class="h-8 w-8" src="https://storage.googleapis.com/a1aa/image/o7bJTDe9xw1ZBiMoCzPvY9jekxFuePcmJFsxt7syk2fioFedC.jpg" alt="Gravity Technologies logo" width="32" height="32">
                        <span class="ml-2 text-xl font-semibold">Gravity Technologies</span>
                    </div>
                    <div class="mt-5 flex-1 flex flex-col">
                        <nav class="flex-1 px-2 space-y-1">
                            <a href="../../../aaa/home" class="text-gray-900  group flex items-center px-2 py-2 text-sm font-medium rounded-md bg-gray-100 ">
                                <i class="fas fa-tachometer-alt mr-3"></i>
                                Dashboard
                            </a>
                            <!-- <a href="#" class="text-gray-600  hover:bg-gray-50  hover:text-gray-900  group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <i class="fas fa-users mr-3"></i>
                                Team
                            </a>
                            <a href="#" class="text-gray-600  hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <i class="fas fa-briefcase mr-3"></i>
                                Projects
                            </a>
                            <a href="#" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <i class="fas fa-calendar-alt mr-3"></i>
                                Calendar
                            </a> -->
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <!-- Navbar -->





            <nav class="bg-gray shadow-md">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <button class="md:hidden m-5 text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 " id="mobile-menu-button">
                                <i class="fas fa-bars"></i>
                            </button>
                            <div class="flex-shrink-0 flex items-center">
                                <img class="lg:hidden  h-8 w-8" src="https://storage.googleapis.com/a1aa/image/o7bJTDe9xw1ZBiMoCzPvY9jekxFuePcmJFsxt7syk2fioFedC.jpg" alt="Gravity Technologies logo" width="32" height="32">
                                <h1 class="ml-1 text-black-500 hover:text-gray-900 ">Gravity Technologies</h1>
                            </div>
                        </div>
                        <div class="ml-6 flex items-center">
                            <!-- <button class="bg-white  p-1 rounded-full text-gray-400 hover:text-gray-500  focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <span class="sr-only">View notifications</span>
                                <i class="fas fa-bell"></i>
                            </button> -->
                            <div class="ml-3 relative">
                                <div>
                                    <button onclick="location.href = '../../../aaa/home/profile';" class="max-w-xs bg-white  flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="user-menu-button" aria-haspopup="true" aria-expanded="false">
                                        <span class="sr-only">Open user menu</span>
                                        <img class="h-8 w-8 rounded-full" src="../../../aaa/signup/<?php echo htmlspecialchars($profile_picture)?>" alt="User profile picture" width="32" height="32">
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Mobile Sidebar -->








            <div class="md:hidden hidden" id="mobile-sidebar">
                <div class="fixed inset-0 flex z-40">
                    <div class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
                    <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white ">
                        <div class="absolute top-0 right-0 -mr-12 pt-2">
                            <button class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:bg-gray-600" id="close-mobile-menu">
                                <span class="sr-only">Close sidebar</span>
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div class="flex-shrink-0 flex items-center px-4 py-4">
                            <img class="h-8 w-8" src="https://storage.googleapis.com/a1aa/image/o7bJTDe9xw1ZBiMoCzPvY9jekxFuePcmJFsxt7syk2fioFedC.jpg" alt="Gravity Technologies logo" width="32" height="32">
                            <span class="ml-2 text-xl font-semibold">Gravity Technologies</span>
                        </div>
                        <div class="mt-5 flex-1 h-0 overflow-y-auto">
                            <nav class="px-2 space-y-1">
                                <a href="../../../aaa/home/" class="text-gray-900  group flex items-center px-2 py-2 text-base font-medium rounded-md bg-gray-100">
                                    <i class="fas fa-tachometer-alt mr-3"></i>
                                    Dashboard
                                </a>
                                <!-- <a href="#" class="text-gray-600 hover:bg-gray-50  hover:text-gray-900  group flex items-center px-2 py-2 text-base font-medium rounded-md">
                                    <i class="fas fa-users mr-3"></i>
                                    Team
                                </a>
                                <a href="#" class="text-gray-600 hover:bg-gray-50  hover:text-gray-900  group flex items-center px-2 py-2 text-base font-medium rounded-md">
                                    <i class="fas fa-briefcase mr-3"></i>
                                    Projects
                                </a>
                                <a href="#" class="text-gray-600 hover:bg-gray-50  hover:text-gray-900  group flex items-center px-2 py-2 text-base font-medium rounded-md">
                                    <i class="fas fa-calendar-alt mr-3"></i>
                                    Calendar
                                </a> -->
                            </nav>
                        </div>
                    </div>
                    <div class="flex-shrink-0 w-14"></div>
                </div>
            </div>
<!-- main -->
