<!-- Coding by CodingLab | www.codinglabweb.com -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!----======== CSS ======== -->
    <link rel="stylesheet" href="{{ asset('css/layouts/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/darkModeStyle.css') }}">

    <!----===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">


    <script src="https://cdn.ckeditor.com/ckeditor5/35.3.0/classic/ckeditor.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">



    <title>Dashboard Sidebar Menu</title>
</head>

<style>
    /* Define CSS variables for light mode */
    :root {
        --body-color: #E4E9F7;
        --sidebar-color: #FFF;
        --primary-color: #695CFE;
        --primary-color-light: #F6F5FF;
        --toggle-color: #DDD;
        --toggle-color2: #555; /* Dark grey for toggle switch */
        --text-primary: #707070;
        /* ===== Colors ===== */
        --card-background-color: #fff; /* Light card background */
        --card-text-color: #333; /* Dark card text */
        /* Normal mode colors */
        --donut-chart-bg-color-1: #007bff;
        --donut-chart-bg-color-2: #28a745;
        --donut-chart-bg-color-3: #ffc107;
        --donut-chart-bg-color-4: #dc3545;
        --line-chart-border-color-1: rgb(75, 192, 192);
        --line-chart-border-color-2: rgb(255, 99, 132);
        --line-chart-border-color-3: rgb(54, 162, 235);
        --line-chart-border-color-4: rgb(255, 206, 86);
        --bar-chart-bg-color-1: #007bff;
        --bar-chart-bg-color-2: #28a745;
        --bar-chart-bg-color-3: #ffc107;
        --bar-chart-border-color: #007bff;
        --toggle-color: #ddd;
        --text-color: #707070;
        --background-color: #E4E9F7;
    }

    /* Define CSS variables for dark mode */
    .dark-mode {
        --body-color: #1e1e2f; /* Dark blue background */
        --sidebar-color: #2b2b4f; /* Darker blue for sidebar */
        --primary-color: #483bd6; /* Dark grey for primary elements */
        --primary-color-light: #2b2b4f; /* Darker blue for lighter accents */
        --toggle-color: #555; /* Dark grey for toggle switch */
        --toggle-color2: #DDD;
        --text-primary: #e0e0e0; /* Light text color */
        /* ===== Colors ===== */
        --card-background-color: #33354a; /* Darker blue for card background */
        --card-text-color: #e0e0e0; /* Light card text */
        /* Dark mode colors */
        --donut-chart-bg-color-1: #3a3b3c;
        --donut-chart-bg-color-2: #4caf50;
        --donut-chart-bg-color-3: #ffeb3b;
        --donut-chart-bg-color-4: #f44336;
        --line-chart-border-color-1: rgb(255, 182, 193);
        --line-chart-border-color-2: rgb(255, 105, 180);
        --line-chart-border-color-3: rgb(100, 149, 237);
        --line-chart-border-color-4: rgb(255, 215, 0);
        --bar-chart-bg-color-1: #3a3b3c;
        --bar-chart-bg-color-2: #4caf50;
        --bar-chart-bg-color-3: #ffeb3b;
        --bar-chart-border-color: #3a3b3c;
        --toggle-color: #555;
        --text-color: #e0e0e0;
        --background-color: #1e1e2f;
    }

    /* General styling for the toggle switch */
    .toggle-switch {
        position: absolute;
        right: 0;
        height: 100%;
        min-width: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        cursor: pointer;
    }

    /* Styling for the switch itself */
    .toggle-switch .switch {
        position: relative; /* Ensures the switch is positioned inside the container */
        cursor: pointer; /* Cursor pointer for better UX */
        height: 22px;
        width: 40px;
        border-radius: 25px;
        background-color: var(--toggle-color);
        transition: var(--tran-05);
    }

    /* Styling for the switch knob */
    .toggle-switch .switch::before {
        content: "";
        position: absolute;
        height: 15px;
        width: 15px;
        border-radius: 50%;
        top: 50%;
        left: 5px;
        transform: translateY(-50%); /* Center the knob vertically */
        background: var(--sidebar-color); /* Background color using CSS variable */
        transition: var(--tran-04); /* Transition effect for the knob */
    }

    /* Dark mode styling for the switch background */
    .dark-mode .toggle-switch .switch {
        background-color: #2196F3; /* Dark mode background color */
    }

    /* Dark mode styling for the knob position */
    .dark-mode .toggle-switch .switch::before {
        left: 50%;
        /* transform: translateX(16px); /Move the knob to the right in dark mode */
    }

    /* Apply the variables */
    body {
        background-color: var(--body-color) !important;
        color: var(--text-primary);
    }
    /* .container{
        background-color: var(--body-color) !important;
        color: var(--text-primary);
    } */
    .navbar {
        background-color: var(--navbar-background);
        color: var(--navbar-text);
    }

    .sidebar {
        background-color: var(--sidebar-color);
        color: var(--primary-color);
    }

    /* Example for links in navbar and sidebar */
    .navbar .nav-link, .sidebar .nav-link {
        color: var(--sidebar-color);
    }
</style>
<style>
    #alertsContainer {
        z-index: 999;
        max-height: 300px;
        overflow: hidden;
        position: fixed;
        top: 80px;
        right: 10px;
        display: flex;
        flex-direction: column-reverse;
        align-items: flex-end;
    }

    .profile_c {
        /* Remove default underline from links */
        text-decoration: none;
    }
</style>

@yield('style')

<body>
    <nav class="sidebar close">
        <header>
            <div class="image-text">
                <span class="image">
                    <img src="{{ asset('img/layouts/logo.png') }}" alt="">
                </span>

                <div class="text logo-text">
                    <span class="name">Codinglab</span>
                </div>
            </div>

            <i class='bx bx-chevron-right toggle'></i>
        </header>

        <div class="menu-bar">
            <div class="menu">
                <ul class="menu-links p-0">
                    {{-- Admin menu --}}
                    <li class="nav-link">
                        <a href="{{ route('home') }}">
                            <i class='bx bx-home-alt icon'></i>
                            <span class="text nav-text">Dashboard</span>
                        </a>
                    </li>
                    @if (auth()->user()->role() <= 2)
                        <li class="nav-link">
                            <a href="{{ route('admin.allUsersList') }}">
                                <i class='bx bx-user icon'></i>
                                <span class="text nav-text">Users Management</span>
                            </a>
                        </li>
                        <li class="nav-link">
                            <a href="{{ route('fileM.index') }}">
                                <i class='bx bx-folder icon'></i>
                                <span class="text nav-text">Files Management</span>
                            </a>
                        </li>
                    @elseif (auth()->user()->role() == 3)
                        <li class="nav-link">
                            <a href="{{ route('fileM.index') }}">
                                <i class='bx bx-folder icon'></i>
                                <span class="text nav-text">Files Management</span>
                            </a>
                        </li>
                        {{-- Supervisor menu --}}
                    @elseif (auth()->user()->role() > 3)
                        {{-- Operatore menu --}}
                    @endif
                    <li class="nav-link">
                        <a href="{{ route('ticket.index') }}">
                            <i class='bx bx-clipboard icon'></i>
                            <span class="text nav-text">Tickets Management</span>
                        </a>
                    </li>
                </ul>
            </div>

            <header class="bottom-content">
                <div class="bottom-content mb-3">
                    <hr>
                    <li class="">
                        <a href="{{ route('logout') }}">
                            <i class='bx bx-log-out icon'></i>
                            <span class="text nav-text">Logout</span>
                        </a>
                    </li>
                    <li class="mode" id="themeToggle">
                        <div class="sun_moon_container">
                            <i class="bx bx-moon icon moon" style="position: absolute;"></i>
                            <i class="bx bx-sun icon sun" style="position: absolute;"></i>
                        </div>
                        <span class="mode-text text">Dark mode</span>
                        <div class="toggle-switch">
                            <span class="switch"></span>
                        </div>
                    </li>
                </div>
            </header>
        </div>

    </nav>

    <div class="alert container">
        <div id="alertsContainer">

        </div>
        <script>
            setInterval(clearAlertsContainer, 10000);

            function clearAlertsContainer() {
                // Find the alerts container element
                var alertsContainer = document.getElementById('alertsContainer');

                // Clear the content of the alerts container
                alertsContainer.innerHTML = '';
            }
        </script>
        <script>
            var alertsContainer = document.getElementById('alertsContainer');

            function showAlertS(message) {
                alertsContainer.innerHTML += `<div class="alert d-flex justify-content-between alert-success bg-success text-white alert-dismissible" style="opacity:0.65;" role="alert">
                                                        <svg class="bi flex-shrink-0 me-2" role="img" style="width:20px; height:20px;" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                                                <div>${message}</div>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>`;

            }

            function showAlertD(message) {
                alertsContainer.innerHTML += `<div class="alert d-flex justify-content-between alert-danger bg-danger text-white  alert-dismissible" style="opacity:0.65;" role="alert">
                    <svg class="bi flex-shrink-0 me-1" role="img" style="width:20px; height:20px;" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
            <div>
            <div>${message}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
            }

            function initializeCKEditor(elementId) {
                ClassicEditor
                    .create(document.querySelector('#' + elementId), {
                        ckfinder: {
                            uploadUrl: '/upload.image'
                        },
                        toolbar: [
                            'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote',
                            'undo', 'redo', 'ckfinder', 'imageUpload', 'insertTable', 'tableColumn', 'tableRow',
                            'mergeTableCells'
                        ],
                        image: {
                            toolbar: ['imageTextAlternative', 'imageStyle:full', 'imageStyle:side']
                        },
                        table: {
                            contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
                        }
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        </script>
    </div>
    <section class="home" style="height: 100%">
        <div class="container_layout pb-5">
            @yield('script1')
                <div class="d-flex justify-content-between shadow" style=" max-hight:45px;">
                    <div class="text">
                        Dashboard Sidebar
                    </div>
                    <header class="d-flex justify-content-end">
                        <a href="{{ route('personal-profile') }}" class="profile_c">
                            <div class="image-text d-flex justify-content-between align-items-center m-3 mt-0 mb-0" style="border-radius: 17px;">
                                <div class="text logo-text" style="text-align: right; padding-right:10px;">
                                    <span class="name">{{ Auth()->user()->Fname }} {{ Auth()->user()->Lname }}</span>
                                    @php
                                        $roleName = 'User';
                                        if (Auth()->user()->role() <= 2) {
                                            $roleName = 'Admin';
                                        } elseif (Auth()->user()->role() == 3) {
                                            $roleName = 'Supervisor';
                                        }
                                    @endphp
                                    <span class="profession">{{ $roleName }}</span>
                                </div>
                                <span class="image">
                                    <img src="{{ asset(Auth()->user()->imgUrl) }}" alt="" style="max-height: 55px; max-width:55px;">
                                </span>

                            </div>
                        </a>
                    </header>
                </div>
            @yield('body')
        </div>
    </section>

    <script src="{{ asset('js/layouts/script.js') }}"></script>


</body>
<script>
    function updateIcons(theme) {
        var moonIcon = document.querySelector('.moon');
        var sunIcon = document.querySelector('.sun');

        if (theme === 'dark') {
            moonIcon.style.opacity = '1'; // Show moon icon
            sunIcon.style.opacity = '0';   // Hide sun icon
        } else {
            moonIcon.style.opacity = '0';  // Hide moon icon
            sunIcon.style.opacity = '1';  // Show sun icon
        }
    }
    // Function to set the theme based on user preference
    function setTheme(theme) {
        if (theme === 'dark') {
            document.body.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark');
        } else {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('theme', 'light');
        }
        updateIcons(theme);
        updateModeText();
    }

    // Function to toggle between dark and light mode
    function toggleTheme() {
        const currentTheme = localStorage.getItem('theme');
        setTheme(currentTheme === 'dark' ? 'light' : 'dark');
    }

    // Function to update the mode text based on the current theme
    function updateModeText() {
        const modeText = document.querySelector('.mode-text');
        const isDarkMode = document.body.classList.contains('dark-mode');
        modeText.innerText = isDarkMode ? 'Light mode' : 'Dark mode';
    }

    // Set the theme on page load based on user preference
    document.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            setTheme(savedTheme);
        } else {
            // Default to light mode if no preference is saved
            setTheme('light');
        }
    });

    // Attach event listener to the toggle button
    document.getElementById('themeToggle').addEventListener('click', toggleTheme);
</script>


</html>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
    integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous">
</script>

@yield('script2')
