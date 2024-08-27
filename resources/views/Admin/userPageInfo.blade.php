@extends('layouts')

@section('style')
    <style>
        .viewBtn {
            height: 29px;
            width: 29px;
            border-radius: 100px;
            background-position: 50% 40%;
            background-color: rgb(137, 137, 255);
            background-repeat: no-repeat;
            background-size: 70%;
            background-image: url('{{ asset('img/icons/view.png') }}');
        }

        .profile-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
            margin-top: 20px;
        }

        .profile-img-container {
            position: relative;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
        }

        .profile-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-img-hover {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
            color: white;
            font-size: 18px;
        }

        .profile-img-container:hover .profile-img-hover {
            opacity: 1;
        }

        .profile-column {
            display: flex;
            flex-direction: column;
        }

        .card {
            margin-top: 20px;
        }

        .change-password-section {
            margin-top: 20px;
        }

        .btn-group-custom {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }
    </style>
@endsection

@section('body')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profile Page</title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

    </head>

    <body>
        <div class="container mt-5">
            <div class="row row-cols-1 row-cols-md-1 row-cols-lg-2 row-cols-sm-1">
                <div class="col-12 col-lg-4 col-md-4 profile-column">
                    <div class="profile-container card p-5">
                        <div class="profile-img-container">
                            <img src="{{ asset($user->imgUrl) }}" alt="Profile Picture" class="profile-img"
                                id="profile-img">
                            <div class="profile-img-hover">Change Profile</div>
                        </div>
                        <h4>{{ $user->Fname }} {{ $user->Lname }}</h4>
                        @php
                            $roleName = 'User';
                            if ($user->role() <= 2) {
                                $roleName = 'Admin';
                            } elseif ($user->role() == 3) {
                                $roleName = 'Supervisor';
                            }
                        @endphp
                        <p><b>Role : </b><button class="btn btn-success p-2 pt-0 pb-0" disabled
                                style="border-radius: 100px;">{{ $roleName }}</button></p>
                    </div>
                </div>
                <div class="col-12 col-lg-8 col-md-8 info-column">
                    <div class="card">
                        <div class="card-header">
                            <h4>Profile Information</h4>
                        </div>
                        <div class="card-body">
                            <div id="updateUserInfoForm">
                                @csrf
                                <div class="form-group mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text"><b>First Name</b></span>
                                        <input type="text" aria-label="First name" class="form-control" id="fname"
                                            name="Fname" value="{{ $user->Fname }}" disabled>
                                        <span class="input-group-text"><b>Last Name</b></span>
                                        <input type="text" aria-label="Last name" class="form-control" id="lname"
                                            name="Lname" value="{{ $user->Lname }}" disabled>
                                    </div>
                                </div>

                                <div class="form-group mb-2">
                                    <label for="tell"><b>Telephone</b></label>
                                    <input type="text" class="form-control" id="tell" name="tell"
                                        value="{{ $user->tell }}" disabled>
                                </div>

                                <div class="form-group mb-2">
                                    <label for="email"><b>Email</b></label>
                                    <input type="email" class="form-control" id="email" value="{{ $user->email }}"
                                        disabled>
                                </div>

                                <div class="form-group mb-2">
                                    <label for="role"><b>Role</b></label>
                                    <select class="form-select" id="role" disabled>
                                        <option value="1" @if ($user->role() == 1 || $user->role() == 2) selected @endif>Admin
                                        </option>
                                        <option value="2" @if ($user->role() == 3) selected @endif>Supervisor
                                        </option>
                                        <option value="3" @if ($user->role() > 3) selected @endif>Normal User
                                        </option>
                                    </select>
                                </div>

                                <input type="hidden" name="type" value="info">
                            </div>

                            <div class="btn-group-custom">
                                <button type="button" class="btn btn-secondary mr-2 m-1" data-bs-toggle="modal"
                                    data-bs-target="#exampleModalPasswordChange">Change Password</button>
                            </div>
                            <!-- Password modal -->
                            <div class="modal fade" id="exampleModalPasswordChange" tabindex="-1"
                                aria-labelledby="exampleModalPasswordChangeLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalPasswordChangeLabel">Change
                                                Password</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="passwordChangeForm"
                                                action="{{ route('admin.user.info.form', ['id' => $user->id]) }}"
                                                method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="newPassword" class="form-label">New Password</label>
                                                    <input type="password" class="form-control" id="newPassword"
                                                        name="new_password" required>
                                                    <div id="passwordError" class="text-danger" style="display:none;">
                                                        Password must be at least 8 characters long.</div>
                                                    @if ($errors->has('new_password'))
                                                        <span class="help-block text-danger">
                                                            <strong>{{ $errors->first('new_password') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="mb-3">
                                                    <label for="confirmPassword" class="form-label">Confirm New
                                                        Password</label>
                                                    <input type="password" class="form-control" id="confirmPassword"
                                                        name="new_password_confirmation" required>
                                                    @if ($errors->has('new_password_confirmation'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('new_password_confirmation') }}</strong>
                                                        </span>
                                                    @endif
                                                    <div id="confirmPasswordError" class="text-danger"
                                                        style="display:none;">Passwords do not match.</div>
                                                </div>
                                                <input type="text" name="type" value="pass" hidden>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary" form="passwordChangeForm">Save
                                                changes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="profile-form" action="/change-profile-picture" method="POST"
                                enctype="multipart/form-data" class="profile-form">
                                <!-- CSRF Token -->
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="file" id="profile-img-input" name="imgUrl" class="profile-img-input"
                                    accept="image/*" onchange="this.form.submit()" style="display:none;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Data visulation --}}
        <div class="alertsContainer container mt-3">
            <div class="row row-cols-1 row-cols-md-1 row-cols-lg-2 row-cols-sm-1">
                <div class="col-12 col-lg-8 col-md-12 profile-column">
                    <!-- Line Chart for Ticket Metrics -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">Ticket Metrics Over Time <b>[RealTime]</b></h5>
                        </div>
                        <div class="card-body">
                            <!-- Dropdown to select time range for line chart -->
                            <div class="mb-3">
                                <label for="lineChartTimeRange" class="form-label">Select Time Range</label>
                                <select id="lineChartTimeRange" class="form-select" onchange="updateLineChart()">
                                    <option value="thisWeek">This Week</option>
                                    <option value="thisMonth">This Month</option>
                                    <option value="thisYear">This Year</option>
                                </select>
                            </div>
                            <!-- Line Chart Container -->
                            <canvas id="ticketLineChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 col-md-12 profile-column">
                    <!-- User Ticket Statistics Card -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">User Ticket Lifecycle Summary <b>[RealTime]</b></h5>
                        </div>
                        <div class="card-body">
                            <!-- Dropdown for Selecting Time Range for donut chart -->
                            <div class="mb-3">
                                <label for="donutChartTimeRange" class="form-label">Select Time Range:</label>
                                <select id="donutChartTimeRange" class="form-select" onchange="updateDonutChart()">
                                    <option value="today">Today</option>
                                    <option value="lastWeek">Last Week</option>
                                    <option value="lastMonth">Last Month</option>
                                    <option value="lastYear">Last Year</option>
                                </select>
                            </div>
                            <!-- Donut Chart Container -->
                            <div>
                                <canvas id="ticketDonutChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-5 col-md-12 profile-column">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">User Activity HeatMap <b>[RealTime]</b></h5>
                        </div>
                        <div class="card-body">
                            <figure>
                                <div id="ticketHeatMapChart"></div>
                            </figure>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7 col-md-12 profile-column">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">Monthly Ticket Statistics <b>[RealTime]</b></h5>
                        </div>
                        <div class="card-body">
                            <canvas id="ticketBarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="alertsContainer container  mt-3">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Ticket Overview: Detailed Logs and User Activity</h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="accordionExample">
                        @foreach ($UserTickets as $item)
                            <div class="accordion-item">
                                <h2 class="accordion-header" style="background-color: rgb(170, 170, 170) !important;">
                                    <div class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$loop->index}}" aria-expanded="false" aria-controls="collapse{{$loop->index}}">
                                        <div class="d-flex justify-between align-items-center">
                                            <button type="button"
                                                class="btn btn-primary viewBtn m-1 mt-0 mb-0"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="View Ticket Details" data-ticketID="{{$item['ticket']->id}}">
                                            </button>
                                            <div>
                                                Ticket #{{$item['ticket']->id}}: {{$item['ticket']->title}}
                                            </div>
                                        </div>
                                    </div>

                                </h2>
                                <div id="collapse{{$loop->index}}" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionExample">
                                    @foreach ($item['logs'] as $log)
                                        <div class="log-header d-flex align-items-center p-2 mb-2" style="background-color: #f8f9fa; border-left: 4px solid #007bff;">
                                            <strong class="text-primary">
                                                {{ $log['logType'] }}
                                            </strong>
                                        </div>
                                        <div class="accordion-body">
                                            @switch($log['logTypeIndex'])
                                                @case(0)
                                                    {{-- Ticket Creation Log --}}
                                                    <p>Ticket was created by <strong>{{ $log['user']['name'] }}</strong> on <strong>{{ $log['date'] }}</strong>.</p>
                                                    @break

                                                @case(1)
                                                    {{-- Transfer Owner Log --}}
                                                    <p>Ticket ownership was transferred on <strong>{{ $log['date'] }}</strong>.</p>

                                                    @if ($log['LogData']->owner->id == $log['LogData']->reserver->id || $log['LogData']->owner->id == 0)
                                                        <p>User <strong>{{ $log['LogData']->reserver->Fname }}</strong> transferred the ticket to themselves.</p>
                                                    @elseif ($log['LogData']->forced == 1)
                                                        <p>Ticket was transferred to <strong>{{ $log['LogData']->reserver->Fname }}</strong> by a supervisor.</p>
                                                    @else
                                                        <p>Old Owner: <strong>{{ $log['LogData']->owner->Fname }}</strong></p>
                                                        <p>New Owner: <strong>{{ $log['LogData']->reserver->Fname }}</strong></p>
                                                    @endif

                                                    @if ($log['LogData']->forced == 0)
                                                        {{-- Only show reservation status if the transfer was not forced --}}
                                                        <p>Reservation Status:
                                                            @if ($log['LogData']->statu === 1)
                                                                <span class="badge bg-success rounded-pill">Accepted</span>
                                                            @elseif ($log['LogData']->statu === 0)
                                                                <span class="badge bg-danger rounded-pill">Declined</span>
                                                            @else
                                                                <span class="badge bg-secondary rounded-pill">No response yet</span>
                                                            @endif
                                                        </p>
                                                        @if (!is_null($log['LogData']->respond_at))
                                                            <p>The user responded at: <strong>{{ $log['LogData']->respond_at }}</strong></p>
                                                        @endif
                                                    @endif
                                                    @break

                                                @case(3)
                                                    {{-- Analysis Log --}}
                                                    <p>Analysis performed by <strong>{{ $log['user']['name'] }}</strong> on <strong>{{ $log['date'] }}</strong>.</p>
                                                    <ul>
                                                        <li>
                                                            <p>Equipment: <strong>{{ $log['LogData']->getEquipement->equipement ?? 'Not Available' }}</strong></p>
                                                        </li>
                                                        <li>
                                                            <p>NSM Status: <strong>{{ $log['LogData']->getNSMStatu() }}</strong></p>
                                                        </li>
                                                        <li>
                                                            <p>Incident Nature: <strong>{{ $log['LogData']->getNatureIncident->val ?? 'Not Available' }}</strong></p>
                                                        </li>
                                                    </ul>
                                                    {{-- Check if report body exists and is not empty --}}
                                                    @if(!empty($log['LogData']->repportBody))
                                                        {{-- Display the report body with a title and styled border --}}
                                                        <div class="report-body-container p-2">
                                                            <h5 class="report-body-title">Report Body:</h5>
                                                            <div class="report-body-content p-2" style="border: 2px solid black;">
                                                                {!! $log['LogData']->repportBody !!}
                                                            </div>
                                                        </div>
                                                    @endif
                                                    {{-- Check if operator information is available --}}
                                                    @if($log['LogData']->operatoreID)
                                                        <div class="operator-info p-2">
                                                            <h5>Operator Information:</h5>
                                                            <ul>
                                                                <li>Operator Ticket: <strong>{{ $log['LogData']->getOperatore->NTicket }}</strong></li>
                                                                <li>Name: <strong>{{ $log['LogData']->getOperatore->name }}</strong></li>
                                                                <li>Email: <strong>{{ $log['LogData']->getOperatore->mail }}</strong></li>
                                                            </ul>
                                                        </div>
                                                    @endif
                                                    @break

                                                @case(4)
                                                    {{-- Recovery Log --}}
                                                    <p>Recovery action performed by <strong>{{ $log['user']['name'] }}</strong> on <strong>{{ $log['date'] }}</strong>.</p>
                                                    <p>Recovery Details: </p>
                                                    <ul>
                                                        <li>
                                                            <p>Solution Nature: <strong>{{ $log['LogData']->getNatureSolution->val ?? 'Not Available' }}</strong></p>
                                                        </li>
                                                        <li>
                                                            <p>Ticket Recovery Date: {{ $log['LogData']->dateRecovery ?? 'Not Available' }}</p>
                                                        </li>
                                                    </ul>
                                                    {{-- Check if report body exists and is not empty --}}
                                                    @if(!empty($log['LogData']->repportBody))
                                                        {{-- Display the report body with a title and styled border --}}
                                                        <div class="report-body-container p-2">
                                                            <h5 class="report-body-title">Report Body:</h5>
                                                            <div class="report-body-content p-2" style="border: 2px solid black;">
                                                                {!! $log['LogData']->repportBody !!}
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @break

                                                @case(5)
                                                    {{-- Add Comments Log --}}
                                                    <p>Comment added by <strong>{{ $log['user']['name'] }}</strong>.</p>
                                                    <p>Comment: {{ $log['LogData']->body }}</p>
                                                    @break

                                                @case(6)
                                                    {{-- Validation Log --}}
                                                    <p>Validation completed by <strong>{{ $log['user']['name'] }}</strong> on <strong>{{ $log['date'] }}</strong>.</p>
                                                    @break

                                                @case(7)
                                                    {{-- Ticket Recovered Log --}}
                                                    <p>Ticket recovered by <strong>{{ $log['user']['name'] }}</strong> on <strong>{{ $log['date'] }}</strong>.</p>
                                                    @break

                                                @case(8)
                                                    {{-- Ticket Closed Log --}}
                                                    <p>Ticket closed by <strong>{{ $log['user']['name'] }}</strong> on <strong>{{ $log['date'] }}</strong>.</p>
                                                    @break

                                                @default
                                                    <p>Unknown log type.</p>
                                            @endswitch
                                        </div>
                                        <hr class="mt-2 mb-2">
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </body>

    </html>
@endsection
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



@section('script2')
    <script>
        $(document).ready(function() {
            $('.profile-img-container').on('click', function() {
                $('#profile-img-input').click();
            });

            $('#profile-img-input').change(function(event) {
                var file = event.target.files[0]; // Get the selected file

                // Ensure it's an image file
                if (file.type.startsWith('image/')) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#profile-img').attr('src', e.target.result); // Display the image preview
                    }
                    reader.readAsDataURL(file); // Read the image file as a data URL


                    var formData = new FormData();
                    formData.append('profile_image', file);
                    formData.append('type', "pic");
                    formData.append('_token', '{{ csrf_token() }}');

                    $.ajax({
                        url: "{{ route('admin.user.info.form', ['id' => $user->id]) }}",
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            showAlertS('Image uploaded successfully!');
                            // Optionally update UI or handle response as needed
                        },
                        error: function(xhr, status, error) {
                            showAlertD('Error uploading image: ' + error);
                            console.error(xhr.responseText); // Log the error for debugging
                        }
                    });
                } else {
                    showAlertD('Please select an image file.');
                }
            });
        });
    </script>

    <script>
        document.getElementById('passwordChangeForm').addEventListener('submit', function(event) {
            var newPassword = document.getElementById('newPassword').value;
            var confirmPassword = document.getElementById('confirmPassword').value;
            var passwordError = document.getElementById('passwordError');
            var confirmPasswordError = document.getElementById('confirmPasswordError');
            var isValid = true;

            // Reset error messages
            passwordError.style.display = 'none';
            confirmPasswordError.style.display = 'none';

            if (newPassword.length < 8) {
                passwordError.style.display = 'block';
                isValid = false;
            }

            if (newPassword !== confirmPassword) {
                confirmPasswordError.style.display = 'block';
                isValid = false;
            }

            if (!isValid) {
                event.preventDefault();
            }
        });
    </script>

    <script>
        // Attach click event handler to all elements with class 'viewBtn'
        $(document).on('click','.viewBtn', function() {
            // Get the user ID from the data attribute
            var ticketId = $(this).data('ticketid');

            // Construct the URL for the route
            var url = '/' + ticketId + '/ticket';

            // Redirect to the constructed URL
            window.location.href = url;
        });
    </script>

    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- the chart 001 for data of TicketNbr001 --}}
    <script>
        var ticketDataDonut = @json($lifeCyleOfTickets);

        // Initialize donut chart with default data (e.g., "today")
        let donutChart;

        function initDonutChart() {
            const ctx = document.getElementById('ticketDonutChart').getContext('2d');
            donutChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Created', 'Closed', 'Recovered', 'Worked On'],
                    datasets: [{
                        label: 'Ticket Statistics',
                        data: Object.values(ticketDataDonut.today),
                        backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545'],
                        borderColor: ['#fff', '#fff', '#fff', '#fff'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });
        }

        // Function to update donut chart data based on dropdown selection
        function updateDonutChart() {
            const selectedTimeRange = document.getElementById('donutChartTimeRange').value;
            donutChart.data.datasets[0].data = Object.values(ticketDataDonut[selectedTimeRange]);
            donutChart.update();
        }

        // Initialize the donut chart on page load
        document.addEventListener('DOMContentLoaded', function() {
            initDonutChart();
        });
    </script>


    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- the chart 002 for data of TicketNbr002 --}}
    <script>
        var ticketDataLine = @json($AvergaeTimeOfTickets);

        // Initialize line chart with default data (e.g., "lastWeek")
        let lineChart;

        function initLineChart() {
            const ctx = document.getElementById('ticketLineChart').getContext('2d');
            lineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ticketDataLine.thisWeek.labels,
                    datasets: [{
                            label: 'Average Time to Close a Ticket (Hours)',
                            data: ticketDataLine.thisWeek.avgCloseTime,
                            fill: false,
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1
                        },
                        {
                            label: 'Average Time of Ticket Creation After Accident (Hours)',
                            data: ticketDataLine.thisWeek.avgCreationTime,
                            fill: false,
                            borderColor: 'rgb(255, 99, 132)',
                            tension: 0.1
                        },
                        {
                            label: 'Average Lifespan of Tickets from Creation to Closing (Hours)',
                            data: ticketDataLine.thisWeek.avgLifespan,
                            fill: false,
                            borderColor: 'rgb(54, 162, 235)',
                            tension: 0.1
                        },
                        {
                            label: 'Average Duration of Ticket from Incident to Recovery (Hours)',
                            data: ticketDataLine.thisWeek.avgRecoveryDuration,
                            fill: false,
                            borderColor: 'rgb(255, 206, 86)',
                            tension: 0.1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Time'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Hours'
                            },
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });
        }

        // Function to update line chart data based on dropdown selection
        function updateLineChart() {
            const selectedTimeRange = document.getElementById('lineChartTimeRange').value;
            lineChart.data.labels = ticketDataLine[selectedTimeRange].labels;
            lineChart.data.datasets[0].data = ticketDataLine[selectedTimeRange].avgCloseTime;
            lineChart.data.datasets[1].data = ticketDataLine[selectedTimeRange].avgCreationTime;
            lineChart.data.datasets[2].data = ticketDataLine[selectedTimeRange].avgLifespan;
            lineChart.data.datasets[3].data = ticketDataLine[selectedTimeRange].avgRecoveryDuration;
            lineChart.update();
        }

        // Initialize the line chart on page load
        document.addEventListener('DOMContentLoaded', function() {
            initLineChart();
        });
    </script>


    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- the chart 004 for data of TicketNbr004 --}}
    <script>
        var ticketDataBar = @json($MonthlyTicketStats);
        let barChart;

        function initBarChart() {
            const ctx = document.getElementById('ticketBarChart').getContext('2d');
            barChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ticketDataBar.months,
                    datasets: [
                        {
                            label: 'Created',
                            data: ticketDataBar.created,
                            backgroundColor: '#007bff',
                            borderColor: '#007bff',
                            borderWidth: 1
                        },
                        {
                            label: 'Closed',
                            data: ticketDataBar.closed,
                            backgroundColor: '#28a745',
                            borderColor: '#28a745',
                            borderWidth: 1
                        },
                        {
                            label: 'Recovered',
                            data: ticketDataBar.recovered,
                            backgroundColor: '#ffc107',
                            borderColor: '#ffc107',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Tickets'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                }
            });
        }

        // Function to update bar chart data based on new data
        function updateBarChart(newData) {
            // Update the global ticketDataBar variable with new data
            ticketDataBar = newData;

            // Update the chart data
            barChart.data.labels = ticketDataBar.months;
            barChart.data.datasets[0].data = ticketDataBar.created;
            barChart.data.datasets[1].data = ticketDataBar.closed;
            barChart.data.datasets[2].data = ticketDataBar.recovered;

            // Update the chart to reflect the new data
            barChart.update();
        }

        // Initialize the bar chart on page load
        document.addEventListener('DOMContentLoaded', function() {
            initBarChart();
        });
    </script>


    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- the chart 003 for data of TicketNbr003 --}}
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/heatmap.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script>
        // Add a helper function for substr in Highcharts templating
        Highcharts.Templating = Highcharts.Templating || {};
        Highcharts.Templating.helpers = Highcharts.Templating.helpers || {};
        Highcharts.Templating.helpers.substr = (s, from, length) => s.substr(from, length);

        // Initialize the heatmap chart
        let heatMapChart = Highcharts.chart('ticketHeatMapChart', {
            chart: {
                type: 'heatmap',
                marginTop: 50,
                marginBottom: 100,
                plotBorderWidth: 1,
                height: 500 // Control the total height of the chart
            },

            title: {
                text: 'User Activity per Hour per Weekday',
                style: {
                    fontSize: '1em'
                }
            },

            xAxis: {
                categories: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']
            },

            yAxis: {
                categories: [
                    '00:00', '01:00', '02:00', '03:00',
                    '04:00', '05:00', '06:00', '07:00',
                    '08:00', '09:00', '10:00', '11:00',
                    '12:00', '13:00', '14:00', '15:00',
                    '16:00', '17:00', '18:00', '19:00',
                    '20:00', '21:00', '22:00', '23:00'
                ],
                title: null,
                reversed: true
            },

            accessibility: {
                point: {
                    descriptionFormat: '{index}. {series.xAxis.categories[x]} sales {series.yAxis.categories[y]}, {value}.'
                }
            },

            colorAxis: {
                min: 0,
                minColor: '#FFFFFF',
                maxColor: Highcharts.getOptions().colors[0]
            },

            legend: {
                align: 'left',
                layout: 'vertical',
                margin: 0,
                verticalAlign: 'top',
                y: 20,
                symbolHeight: 400
            },

            tooltip: {
                formatter: function() {
                    return '<b>' + this.series.xAxis.categories[this.point.x] + '</b> had <br>' +
                        '<b>' + this.point.value + '</b> events';
                }
            },

            series: [{
                name: 'Activity per hour',
                borderWidth: 1,
                data: @json($ActivityHeatMap),
                dataLabels: {
                    enabled: true,
                    color: '#000000'
                }
            }],

            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 800
                    },
                    chartOptions: {
                        yAxis: {
                            labels: {
                                formatter: function() {
                                    return Highcharts.Templating.helpers.substr(this.value, 0, 5);
                                }
                            }
                        }
                    }
                }]
            }

        });

        // Function to update heatmap chart data
        function updateHeatMapData(newData) {
            // Update the chart with new data
            heatMapChart.series[0].setData(newData, true); // 'true' for redraw
        }
    </script>



    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- Real time update of page user --}}
    <script>
        // 123
        // Define the function to fetch tickets
        // Initialize an object to store Choices instances

        function fetchTickets() {
            $.ajax({
                url: '{{ route('admin.user.info.json',['id'=> $user->id ]) }}', // The Laravel route URL
                method: 'GET',
                success: function(response) {
                    //  get all the data & update content
                    // ...
                    // console.log(response);
                    ticketDataDonut = response.lifeCyleOfTickets;
                    updateDonutChart();

                    ticketDataLine = response.AvergaeTimeOfTickets;
                    updateLineChart();

                    updateBarChart(response.MonthlyTicketStats);

                    updateHeatMapData(response.ActivityHeatMap);
                    // updateAccordion(response);
                },
                error: function(error) {
                    console.error('Error fetching tickets:', error);
                }
            });
        }

        function getNSMStatus(nsmStatus) {
            switch (nsmStatus) {
                case 1:
                    return 'Host DOWN';
                case 2:
                    return 'Host UP';
                case 3:
                    return 'Service Critical';
                case 4:
                    return 'Service OK';
                case 5:
                    return 'Unknown Status';
                default:
                    return 'Status not defined';
            }
        }

        function getStatus(status) {
            switch (status) {
                case 0:
                    return 'Open';
                case 1:
                    return 'Recovered';
                case 2:
                    return 'Cloture';
                case 3:
                    return 'Valid';
                default:
                    return 'Not Valid';
            }
        }

        function getStatusDesign(status) {
            switch (status) {
                case 0:
                    return 'open';
                case 1:
                    return 'resolved';
                case 2:
                    return 'closed';
                case 3:
                    return 'validated';
                default:
                    return 'nvalidated';
            }
        }

        function formatDate(inputDate) {
            // Parse the input date string
            var date = new Date(inputDate);

            // Extract year, month, day, hours, minutes, and seconds
            var year = date.getFullYear();
            var month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed
            var day = String(date.getDate()).padStart(2, '0');
            var hours = String(date.getHours()).padStart(2, '0');
            var minutes = String(date.getMinutes()).padStart(2, '0');
            var seconds = String(date.getSeconds()).padStart(2, '0');

            // varruct and return formatted date string
            return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        }

        function updateAccordion(response) {
            // Get the accordion container
            const accordionContainer = document.getElementById('accordionExample');

            // Get all current accordion items
            const existingItems = accordionContainer.querySelectorAll('.accordion-item');
            const existingTickets = {};

            // Map existing tickets by ID
            existingItems.forEach(item => {
                const ticketID = item.dataset.ticketid;
                existingTickets[ticketID] = item;
            });

            // Track new items to add
            const newItems = [];

            // Iterate over the new data and create/update items
            response.UserTickets.forEach((item, index) => {
                const ticketID = item.ticket.id;

                // Create HTML for the current ticket
                const accordionItemHTML = `
                    <div class="accordion-item" data-ticketid="${ticketID}">
                        <h2 class="accordion-header" style="background-color: rgb(170, 170, 170) !important;">
                            <div class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${index}" aria-expanded="false" aria-controls="collapse${index}">
                                <div class="d-flex justify-between align-items-center">
                                    <button type="button"
                                        class="btn btn-primary viewBtn m-1 mt-0 mb-0"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="View Ticket Details" data-ticketid="${ticketID}">
                                    </button>
                                    <div>
                                        Ticket #${ticketID}: ${item.ticket.title}
                                    </div>
                                </div>
                            </div>
                        </h2>
                        <div id="collapse${index}" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                            ${item.logs.map(log => `
                                <div class="log-header d-flex align-items-center p-2 mb-2" style="background-color: #f8f9fa; border-left: 4px solid #007bff;">
                                    <strong class="text-primary">
                                        ${log.logType}
                                    </strong>
                                </div>
                                <div class="accordion-body">
                                    ${logContent(log)}
                                </div>
                                <hr class="mt-2 mb-2">
                            `).join('')}
                        </div>
                    </div>
                `;

                // Check if the ticket already exists
                if (existingTickets[ticketID]) {
                    // Update existing ticket
                    existingTickets[ticketID].outerHTML = accordionItemHTML;
                    // Remove from existingTickets to avoid reprocessing
                    delete existingTickets[ticketID];
                } else {
                    // Add new ticket
                    newItems.push(accordionItemHTML);
                }
            });

            // Remove tickets that are no longer in the response
            Object.values(existingTickets).forEach(item => item.remove());

            // Add new items to the container
            if (newItems.length) {
                accordionContainer.insertAdjacentHTML('beforeend', newItems.join(''));
            }

            // Reinitialize the tooltips if needed
            bootstrap.Tooltip.getInstance(document.querySelectorAll('[data-bs-toggle="tooltip"]')).forEach(tooltip => tooltip.dispose());
            bootstrap.Tooltip.init(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        }

        // Helper function to generate log content based on log type
        function logContent(log) {
            let logHtml = '';
            switch (log.logTypeIndex) {
                case 0:
                    logHtml = `<p>Ticket was created by <strong>${log.user.name}</strong> on <strong>${log.date}</strong>.</p>`;
                    break;
                case 1:
                    logHtml = `<p>Ticket ownership was transferred on <strong>${log.date}</strong>.</p>`;
                    if (log.LogData.owner.id == log.LogData.reserver.id || log.LogData.owner.id == 0) {
                        logHtml += `<p>User <strong>${log.LogData.reserver.Fname}</strong> transferred the ticket to themselves.</p>`;
                    } else if (log.LogData.forced == 1) {
                        logHtml += `<p>Ticket was transferred to <strong>${log.LogData.reserver.Fname}</strong> by a supervisor.</p>`;
                    } else {
                        logHtml += `<p>Old Owner: <strong>${log.LogData.owner.Fname}</strong></p>`;
                        logHtml += `<p>New Owner: <strong>${log.LogData.reserver.Fname}</strong></p>`;
                    }
                    if (log.LogData.forced == 0) {
                        logHtml += `<p>Reservation Status: ${log.LogData.statu === 1 ? '<span class="badge bg-success rounded-pill">Accepted</span>' : log.LogData.statu === 0 ? '<span class="badge bg-danger rounded-pill">Declined</span>' : '<span class="badge bg-secondary rounded-pill">No response yet</span>'}</p>`;
                        if (log.LogData.respond_at) {
                            logHtml += `<p>The user responded at: <strong>${log.LogData.respond_at}</strong></p>`;
                        }
                    }
                    break;
                case 3:
                    logHtml = `<p>Analysis performed by <strong>${log.user.name}</strong> on <strong>${log.date}</strong>.</p>`;
                    logHtml += `<ul>
                                    <li><p>Equipment: <strong>${(log.LogData.get_equipement ? log.LogData.get_equipement.equipement : 'Not Available')}</strong></p></li>
                                    <li><p>NSM Status: <strong>${getNSMStatus(log.LogData.NSMStatu)}</strong></p></li>
                                    <li><p>Incident Nature: <strong>${(log.LogData.get_nature_incident != null ? log.LogData.get_nature_incident.val : 'Not Available')}</strong></p></li>
                                </ul>`;
                    if (log.LogData.repportBody) {
                        logHtml += `<div class="report-body-container p-2">
                                        <h5 class="report-body-title">Report Body:</h5>
                                        <div class="report-body-content p-2" style="border: 2px solid black;">
                                            ${log.LogData.repportBody}
                                        </div>
                                    </div>`;
                    }
                    if (log.LogData.operatoreID) {
                        logHtml += `<div class="operator-info p-2">
                                        <h5>Operator Information:</h5>
                                        <ul>
                                            <li>Operator Ticket: <strong>${log.LogData.get_operatore.NTicket}</strong></li>
                                            <li>Name: <strong>${log.LogData.get_operatore.name}</strong></li>
                                            <li>Email: <strong>${log.LogData.get_operatore.mail}</strong></li>
                                        </ul>
                                    </div>`;
                    }
                    break;
                case 4:
                    logHtml = `<p>Recovery action performed by <strong>${log.user.name}</strong> on <strong>${log.date}</strong>.</p>
                                <p>Recovery Details: </p>
                                <ul>
                                    <li><p>Solution Nature: <strong>${(log.LogData.get_nature_solution != null ? log.LogData.get_nature_solution.val : 'Not Available')}</strong></p></li>
                                    <li><p>Ticket Recovery Date: ${log.LogData.dateRecovery || 'Not Available'}</p></li>
                                </ul>`;
                    if (log.LogData.repportBody) {
                        logHtml += `<div class="report-body-container p-2">
                                        <h5 class="report-body-title">Report Body:</h5>
                                        <div class="report-body-content p-2" style="border: 2px solid black;">
                                            ${log.LogData.repportBody}
                                        </div>
                                    </div>`;
                    }
                    break;
                case 5:
                    logHtml = `<p>Comment added by <strong>${log.user.name}</strong>.</p>
                                <p>Comment: ${log.LogData.body}</p>`;
                    break;
                case 6:
                    logHtml = `<p>Validation completed by <strong>${log.user.name}</strong> on <strong>${log.date}</strong>.</p>`;
                    break;
                case 7:
                    logHtml = `<p>Ticket recovered by <strong>${log.user.name}</strong> on <strong>${log.date}</strong>.</p>`;
                    break;
                case 8:
                    logHtml = `<p>Ticket closed by <strong>${log.user.name}</strong> on <strong>${log.date}</strong>.</p>`;
                    break;
                default:
                    logHtml = `<p>Unknown log type.</p>`;
                    break;
            }
            return logHtml;
        }



        // Run the fetchTickets function every 3 seconds
        setInterval(fetchTickets, 3000);
    </script>
@endsection

@section('script1')
@endsection
