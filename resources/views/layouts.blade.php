<!-- Coding by CodingLab | www.codinglabweb.com -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!----======== CSS ======== -->
    <link rel="stylesheet" href="{{ asset('css/layouts/style.css') }}">

    <!----===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">


    <script src="https://cdn.ckeditor.com/ckeditor5/35.3.0/classic/ckeditor.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">



    <title>Dashboard Sidebar Menu</title>
</head>
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
                    @elseif (auth()->user()->role() == 3)
                        {{-- Supervisor menu --}}
                    @elseif (auth()->user()->role() > 3)
                        {{-- Operatore menu --}}
                    @else
                        unkonws menu
                        <li class="nav-link">
                            <a href="#">
                                <i class='bx bx-home-alt icon'></i>
                                <span class="text nav-text">Dashboard</span>
                            </a>
                        </li>

                        <li class="nav-link">
                            <a href="#">
                                <i class='bx bx-bar-chart-alt-2 icon'></i>
                                <span class="text nav-text">Revenue</span>
                            </a>
                        </li>

                        <li class="nav-link">
                            <a href="#">
                                <i class='bx bx-bell icon'></i>
                                <span class="text nav-text">Notifications</span>
                            </a>
                        </li>

                        <li class="nav-link">
                            <a href="#">
                                <i class='bx bx-pie-chart-alt icon'></i>
                                <span class="text nav-text">Analytics</span>
                            </a>
                        </li>

                        <li class="nav-link">
                            <a href="#">
                                <i class='bx bx-heart icon'></i>
                                <span class="text nav-text">Likes</span>
                            </a>
                        </li>

                        <li class="nav-link">
                            <a href="#">
                                <i class='bx bx-wallet icon'></i>
                                <span class="text nav-text">Wallets</span>
                            </a>
                        </li>
                    @endif
                    <li class="nav-link">
                        <a href="{{ route('ticket.index') }}">
                            <i class='bx bx-clipboard icon'></i>
                            <span class="text nav-text">Tickets Management</span>
                        </a>
                    </li>
                </ul>
            </div>

            <header>
                <div class="bottom-content mb-3">
                    <li class="">
                        <a href="{{ route('logout') }}">
                            <i class='bx bx-log-out icon'></i>
                            <span class="text nav-text">Logout</span>
                        </a>
                    </li>
                </div>
                <a href="{{ route('personal-profile') }}" class="profile_c">
                    <div class="image-text" style="background-color: #f0f0f0; border-radius: 17px;">
                        <span class="image">
                            <img src="{{ asset(Auth()->user()->imgUrl) }}" alt="">
                        </span>

                        <div class="text logo-text">
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
                    </div>
                </a>
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
            <div class="text">Dashboard Sidebar</div>
            @yield('body')
        </div>
    </section>

    <script src="{{ asset('js/layouts/script.js') }}"></script>


</body>

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
