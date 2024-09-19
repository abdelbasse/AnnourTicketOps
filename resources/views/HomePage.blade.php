@extends('layouts')
@section('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
@endsection

<style>
    .CARD_header_bgcolor{
        background-color: var(--primary-color) !important;
        /* color: var(--primary-color-light) !important; */
    }
</style>
@section('body')
    {{-- Data visulation --}}
    <div class="alertsContainer container-fluid px-5 mt-3">
        <div class="row row-cols-1 row-cols-md-1 row-cols-lg-2 row-cols-sm-1">

            <div class="col-12 col-lg-6 col-md-12 profile-column mt-4">
                <div class="row" style="height: 100%;">
                    <div class="col-12 col-lg-6 col-md-12 mb-3 mb-lg-0">
                        <div class="card shadow-sm d-flex flex-row align-items-center"
                            style="max-height: 70%; background-color: var(--sidebar-color); color: var(--text-primary);">
                            <div class="ms-auto p-3" style="max-width:30%;">
                                <img src="https://cdn-icons-png.flaticon.com/128/7411/7411135.png" alt=""
                                    width="100%">
                            </div>
                            <div class="p-3" style="width: 70%;">
                                <h5 class="card-title">Total Tickets Created</h5>
                                <p class="card-text display-6 fw-bold" id="totalTicketsCreated">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 col-md-12 mb-3 mb-lg-0">
                        <div class="card shadow-sm d-flex flex-row align-items-center"
                            style="max-height: 70%; background-color: var(--sidebar-color); color: var(--text-primary);">
                            <div class="ms-auto p-3" style="max-width:30%;">
                                <img src="https://cdn-icons-png.flaticon.com/128/12822/12822540.png" alt=""
                                    width="100%">
                            </div>
                            <div class="p-3" style="width: 70%;">
                                <h5 class="card-title">Tickets Not Closed</h5>
                                <p class="card-text display-6 fw-bold" id="ticketsNotClosed">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-12 col-md-12 mb-3 mb-lg-0">
                        <div class="card shadow-sm d-flex flex-row align-items-center"
                            style="max-height: 120%; background-color: var(--sidebar-color); color: var(--text-primary);">
                            <div class="p-3">
                                <h5 class="card-title">Tickets Not Closed</h5>
                                <p class="card-text display-6 fw-bold">1234</p>
                            </div>
                            <div class="ms-auto p-3">
                                <i class="bi bi-file-earmark-plus" style="font-size: 2.5rem; color: #0d6efd;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6 col-md-12 profile-column mt-4">
                <div class="card shadow-sm" style="background-color: var(--sidebar-color); color: var(--text-primary);">
                    <div class="card-header CARD_header_bgcolor text-white">
                        <h5 class="card-title mb-0">Monthly Ticket Statistics <b>[RealTime]</b></h5>
                    </div>
                    <div class="card-body">
                        <canvas id="ticketBarChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- ----------------- --}}


            {{-- heat map mororco --}}
            <div class="col-12 col-lg-5 col-md-12 profile-column mt-4">
                <!-- Top Users Activity Card -->
                <div class="card shadow-sm" style="background-color: var(--sidebar-color); color: var(--text-primary);">
                    <div class="card-header CARD_header_bgcolor text-white">
                        <h5 class="card-title mb-0">Top Users Activity<b>[RealTime]</b></h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">This card displays the top 5 users based on activity. You can filter the
                            activity by selecting a time range from the dropdown below.</p>
                        <label for="userActivityTimeRange" class="form-label">Select Time Range</label>
                        <select id="userActivityTimeRange" class="form-select" onchange="updateUserActivityList()">
                            <option value="thisWeek">This Week</option>
                            <option value="thisMonth">This Month</option>
                            <option value="thisYear">This Year</option>
                        </select>
                        <div id="topUsersList" class="mt-3">
                            <!-- User items will be dynamically added here -->
                            <!-- Example user -->
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://via.placeholder.com/50" alt="User Image" class="rounded-circle me-3"
                                    width="50">
                                <div>
                                    <h6 class="mb-1">John Doe</h6>
                                    <p class="text-muted mb-0">johndoe@example.com</p>
                                </div>
                            </div>
                            <hr>

                            <!-- Add more users similarly -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-7 col-md-12 profile-column mt-4">
                <!-- Airport Ticket Statistics Card -->
                <div class="card shadow-sm" style="background-color: var(--sidebar-color); color: var(--text-primary);">
                    <div class="card-header CARD_header_bgcolor text-white">
                        <h5 class="card-title mb-0">Airport Ticket Statistics <b>[RealTime]</b></h5>
                    </div>
                    <div class="card-body">
                        <label for="airportTimeRange" class="form-label">Select Time Range</label>
                        <select id="airportTimeRange" class="form-select" onchange="updateBarChartAirport()">
                            <option value="thisWeek">This Week</option>
                            <option value="thisMonth">This Month</option>
                            <option value="thisYear">This Year</option>
                        </select>
                        <canvas id="airportBarChart" width="400" height="200" class="mt-3"></canvas>
                    </div>
                </div>
            </div>

            {{-- ----------------- --}}

            <div class="col-12 col-lg-8 col-md-12 profile-column mt-4">
                <!-- Line Chart for Ticket Metrics -->
                <div class="card shadow-sm" style="background-color: var(--sidebar-color); color: var(--text-primary);">
                    <div class="card-header CARD_header_bgcolor text-white">
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

            <div class="col-12 col-lg-4 col-md-12 profile-column mt-4">
                <!-- User Ticket Statistics Card -->
                <div class="card shadow-sm" style="background-color: var(--sidebar-color); color: var(--text-primary);">
                    <div class="card-header CARD_header_bgcolor text-white">
                        <h5 class="card-title mb-0">Ticket Lifecycle Summary <b>[RealTime]</b></h5>
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

            {{-- ----------------- --}}

            <div class="col-12 col-lg-12 col-md-12 profile-column mt-4">
                <!-- User Ticket Statistics Card -->
                <div class="card shadow-sm" style="background-color: var(--sidebar-color); color: var(--text-primary);">
                    <div class="card-header CARD_header_bgcolor text-white">
                        <h5 class="card-title mb-0">Problem Nature Statistics<b>[RealTime]</b></h5>
                    </div>
                    <div class="card-body" style="overflow: auto;">
                        <table id="problemTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Problem Name</th>
                                    <th>Tickets Today</th>
                                    <th>Tickets This Week</th>
                                    <th>Tickets This Month</th>
                                    <th>Tickets This Year</th>
                                    <th>Total Tickets</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-12 col-md-12 profile-column mt-4">
                <!-- User Ticket Statistics Card -->
                <div class="card shadow-sm" style="background-color: var(--sidebar-color); color: var(--text-primary);">
                    <div class="card-header CARD_header_bgcolor text-white">
                        <h5 class="card-title mb-0">Solution Nature Statistics<b>[RealTime]</b></h5>
                    </div>
                    <div class="card-body" style="overflow: auto;">
                        <table id="solutionTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Solution Name</th>
                                    <th>Tickets Today</th>
                                    <th>Tickets This Week</th>
                                    <th>Tickets This Month</th>
                                    <th>Tickets This Year</th>
                                    <th>Total Tickets</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@section('script2')
    <script defer src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script defer src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#problemTable').DataTable();
            $('#solutionTable').DataTable();
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
                            position: 'top',
                            labels: {
                                color: '#4385ff' // Text color for legend labels
                            }
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
                                text: 'Time',
                                color: '#4385ff'
                            },
                            ticks: {
                                color: '#4385ff' // Color for x-axis ticks
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Hours',
                                color: '#4385ff'
                            },
                            beginAtZero: true,
                            ticks: {
                                color: '#4385ff' // Color for x-axis ticks
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: '#4385ff' // Text color for legend labels
                            }
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
    {{-- the chart 003 for data of TicketNbr003 --}}
    <script>
        var ticketDataBar = @json($MonthlyTicketStats);
        let barChart;

        function initBarChart() {
            const ctx = document.getElementById('ticketBarChart').getContext('2d');
            barChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ticketDataBar.months,
                    datasets: [{
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
                            stacked: true,
                            color: '#4385ff',
                            ticks: {
                                color: '#4385ff' // Color for x-axis ticks
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Tickets',
                                color: '#4385ff'
                            },
                            ticks: {
                                color: '#4385ff' // Color for x-axis ticks
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: '#4385ff' // Text color for legend labels
                            }
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
    {{-- the chart 004 for data of TicketNbr004 --}}
    <script>
        // Example data structure for airport ticket statistics
        var ticketDataBarData = @json($AerportTicketRealtion);

        let barChartAirport;

        function initBarChartAirport() {
            const ctx = document.getElementById('airportBarChart').getContext('2d');
            barChartAirport = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ticketDataBar.labels, // Default to thisWeek
                    datasets: [{
                        label: 'Number of Tickets',
                        data: ticketDataBarData.thisWeek,
                        backgroundColor: '#007bff',
                        borderColor: '#007bff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Airport Locations',
                                color: '#4385ff'
                            },
                            ticks: {
                                color: '#4385ff' // Color for x-axis ticks
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Tickets',
                                color: '#4385ff'
                            },
                            ticks: {
                                color: '#4385ff' // Color for x-axis ticks
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: '#4385ff' // Text color for legend labels
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                }
            });
        }

        // Function to update bar chart data based on selected time range
        function updateBarChartAirport() {
            var selectedTimeRange = document.getElementById('airportTimeRange').value;
            // Update the bar chart data based on selected time range
            barChartAirport.data.labels = ticketDataBarData.labels;
            barChartAirport.data.datasets[0].data = ticketDataBarData[selectedTimeRange];

            // Update the chart to reflect the new data
            barChartAirport.update();
        }

        // Initialize the bar chart on page load
        document.addEventListener('DOMContentLoaded', function() {
            initBarChartAirport();
        });
    </script>

    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- the chart 005 for data of TicketNbr004 --}}
    <script>
        // Sample user data for different time ranges
        var userData = @json($top5Users);

        function updateUserActivityList() {
            const selectedRange = document.getElementById('userActivityTimeRange').value;
            const topUsersList = document.getElementById('topUsersList');

            // Clear the current list
            topUsersList.innerHTML = '';

            // Get the user data for the selected range
            const users = userData[selectedRange] || [];

            // Generate HTML for each user
            users.forEach(user => {
                const userItem = document.createElement('div');
                userItem.className = 'd-flex align-items-center mb-3';
                userItem.innerHTML = `
                    <img src="${user.imgSrc}" alt="User Image" class="rounded-circle me-3" width="50">
                    <div>
                        <h6 class="mb-1" style="color:var(--text-primary);">${user.name}</h6>
                        <p class="mb-0" style="color:var(--toggle-color2);">${user.email}</p>
                    </div>
                    <hr>
                `;
                topUsersList.appendChild(userItem);
            });
        }

        // Initialize with default selection
        document.addEventListener('DOMContentLoaded', () => {
            updateUserActivityList();
        });
    </script>



    <script>
        // 123
        // Define the function to fetch tickets
        // Initialize an object to store Choices instances

        function fetchTickets() {
            $.ajax({
                url: '{{ route('home.ajax') }}', // The Laravel route URL
                method: 'GET',
                success: function(response) {
                    //  get all the data & update content
                    // ...
                    // console.log(response);
                    // -----------------------------------------------------
                    // -----------------------------------------------------
                    ticketDataDonut = response.lifeCyleOfTickets;
                    updateDonutChart();

                    ticketDataLine = response.AvergaeTimeOfTickets;
                    updateLineChart();

                    ticketDataBarData = response.AerportTicketRealtion;
                    updateBarChartAirport();

                    userData = response.top5Users;
                  // console.log(userData);
                    updateUserActivityList()

                    updateBarChart(response.MonthlyTicketStats);
                    // -----------------------------------------------------
                    // -----------------------------------------------------

                    updateProblemStatisticsTable(response);
                    updateSolutionStatisticsTable(response);

                    updateNbrOfTicketTopCard(response);

                    // updateAccordion(response);
                },
                error: function(error) {
                    console.error('Error fetching tickets:', error);
                }
            });
        }

        function updateProblemStatisticsTable(response) {
            // Assuming 'ProblemStatistics' is the key in the response
            var problemStatistics = response.ProblemStatistics;

            // Clear the DataTable
            var dataTable = $('#problemTable').DataTable(); // Replace 'incidentTable' with your actual table ID
            dataTable.clear();

            // Populate the DataTable with new data
            problemStatistics.forEach(stat => {
                dataTable.row.add([
                    stat.problem_name,
                    stat.tickets_today,
                    stat.tickets_this_week,
                    stat.tickets_this_month,
                    stat.tickets_this_year,
                    stat.total_tickets
                ]);
            });

            // Redraw the table
            dataTable.draw();
        }

        function updateSolutionStatisticsTable(response) {
            // Assuming 'ProblemStatistics' is the key in the response
            var solutionStatistics = response.SolutionStatistics;

            // Clear the DataTable
            var dataTable = $('#solutionTable').DataTable(); // Replace 'incidentTable' with your actual table ID
            dataTable.clear();

            // Populate the DataTable with new data
            solutionStatistics.forEach(stat => {
                dataTable.row.add([
                    stat.solution_name,
                    stat.tickets_today,
                    stat.tickets_this_week,
                    stat.tickets_this_month,
                    stat.tickets_this_year,
                    stat.total_tickets
                ]);
            });

            // Redraw the table
            dataTable.draw();
        }

        function updateNbrOfTicketTopCard(response) {
            // Assuming the response contains 'total_tickets' and 'open_tickets'
            var totalTickets = response.HeaderInfoNbrTotalTickets.total_tickets;
            var openTickets = response.HeaderInfoNbrTotalTickets.open_tickets;

            // Update the first card (Total Tickets Created)
            document.querySelector('#totalTicketsCreated').textContent = totalTickets == null ? '0' : totalTickets;

            // Update the second card (Tickets Not Closed)
            document.querySelector('#ticketsNotClosed').textContent = openTickets == null ? '0' : openTickets;
        }


        // Run the fetchTickets function every 3 seconds
        setInterval(fetchTickets, 3000);
    </script>
@endsection
