@extends('layouts')

@section('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
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

        .deleteBtn {
            height: 29px;
            width: 29px;
            border-radius: 100px;
            background-position: 57% 50%;
            background-color: rgb(255, 72, 72);
            background-repeat: no-repeat;
            background-size: 85%;
            background-image: url('{{ asset('img/icons/trash.png') }}');
        }
    </style>
    <style>
        .profile-img {
            width: 43px;
            height: 43px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .profile-card {
            text-align: center;
            margin-bottom: 20px;
        }

        td {
            align-content: center;
        }
    </style>
@endsection

@section('body')
    <!-- Your table goes here -->
    <div class="container mt-5">
        <div class="">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1"
                        type="button" role="tab" aria-controls="tab1" aria-selected="true">All Users</button>
                </li>
                {{-- <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button"
                        role="tab" aria-controls="tab2" aria-selected="false">Operators</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab3-tab" data-bs-toggle="tab" data-bs-target="#tab3" type="button"
                        role="tab" aria-controls="tab3" aria-selected="false">Supervisors</button>
                </li>
                @if (auth()->user()->role == 1)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab4-tab" data-bs-toggle="tab" data-bs-target="#tab4" type="button"
                            role="tab" aria-controls="tab4" aria-selected="false">Admins</button>
                    </li>
                @endif --}}
            </ul>
        </div>
        <div class="card ">
            <div class=" ">
                <!-- Tab panes -->
                <div class="tab-content mt-2 p-3" id="myTabContent">
                    <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                        <div class="row m-0 p-0 row-cols-1">
                            <div class="col p-2 pt-0 pb-0">
                                <div class="row m-1 mt-0 mb-0">
                                    <div class="col m-0 p-0">
                                        <h4>List of All Users</h4>
                                        <p>This is example of the <a href="{{ asset('downloads/ExampleImportUsers.xlsx') }}"
                                                download>
                                                Excel file</a> , is importent to respect the structue of this example!</p>
                                    </div>
                                    <div class="col m-0 p-0 d-flex justify-content-end">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="" data-bs-toggle="modal"
                                                        data-bs-target="#modalAddNewUser">Add User</a></li>
                                                <li>
                                                    <label for="fileUpload" class="dropdown-item" style="cursor: pointer;">
                                                        Import Excel
                                                        <input type="file" id="fileUpload" style="display: none;"
                                                            accept=".xls,.xlsx">
                                                    </label>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li><a class="dropdown-item" href="" id="exportExcel">Export
                                                        Excel</a></li>
                                            </ul>
                                        </div>
                                        <!-- Add new User Modal -->
                                        <div class="modal fade" id="modalAddNewUser" data-bs-backdrop="static"
                                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalAddNewUserLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="modalAddNewUserLabel">Add New User
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form id="addUserForm">
                                                            <div class="row mb-3">
                                                                <div class="col">
                                                                    <label for="firstName" class="form-label">First
                                                                        Name</label>
                                                                    <input type="text" class="form-control"
                                                                        id="FName" name="FName" required>
                                                                </div>
                                                                <div class="col">
                                                                    <label for="lastName" class="form-label">Last
                                                                        Name</label>
                                                                    <input type="text" class="form-control"
                                                                        id="LName" name="LName" required>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="email" class="form-label">Email
                                                                    address</label>
                                                                <input type="email" class="form-control" id="email"
                                                                    name="email" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="phone" class="form-label">Phone
                                                                    Number</label>
                                                                <input type="tel" class="form-control" id="phone"
                                                                    name="tell" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="role" class="form-label">Role</label>
                                                                <select class="form-select" id="role"
                                                                    name="role">
                                                                    <option value="4" selected>User</option>
                                                                    <option value="2">Admin</option>
                                                                    <option value="3">Supervisor</option>
                                                                </select>
                                                            </div>
                                                            <input type="hidden" id="type" name="type"
                                                                value="user" hidden>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-primary"
                                                            id="submitBtn">Add</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="">
                                    <hr>
                                    <table id="example" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="selectAll"></th>
                                                <th style="width: 60px !importent;"> {{-- here should be the profile pic --}} </th>
                                                <th>Full Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Statu</th>
                                                <th>Options</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $user)
                                                <tr data-userId="{{ $user->id }}">
                                                    <td><input type="checkbox" class="selectRow"></td>
                                                    <td><img src="{{ asset($user->imgUrl) }}" alt="Profile Image"
                                                            class="profile-img"></td>
                                                    <td>{{ $user->Fname }} {{ $user->Lname }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td><span class="badge rounded-pill text-bg-primary ">
                                                            @if ($user->role > 3)
                                                                User
                                                            @elseif($user->role == 3)
                                                                Supervisor
                                                            @elseif($user->role <= 2)
                                                                Admin
                                                            @endif
                                                        </span></td>
                                                    <td>
                                                        @if ($user->latestLoginLog)
                                                            @if ($user->latestLoginLog->isLogged)
                                                                <span
                                                                    class="badge rounded-pill text-bg-success">Online</span>
                                                            @else
                                                                <span
                                                                    class="badge rounded-pill text-bg-danger">Offline</span>
                                                            @endif
                                                        @else
                                                            <span class="badge rounded-pill text-bg-danger">Offline</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button type="button"
                                                            class="btn btn-primary viewBtn m-1 mt-0 mb-0"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            data-bs-title="Tooltip on top" data-userId="{{$user->id}}">
                                                        </button>
                                                        <button type="button"
                                                            class="btn btn-danger deleteBtn m-0 mt-0 mb-0"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            data-bs-title="Tooltip on top">
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @if (auth()->user()->id == 1)
                                                @foreach ($admins as $user)
                                                    <tr data-userId="{{ $user->id }}">
                                                        <td><input type="checkbox" class="selectRow"></td>
                                                        <td><img src="{{ asset($user->imgUrl) }}" alt="Profile Image"
                                                                class="profile-img"></td>
                                                        <td>{{ $user->Fname }} {{ $user->Lname }}</td>
                                                        <td>{{ $user->email }}</td>
                                                        <td><span class="badge rounded-pill text-bg-primary ">
                                                                @if ($user->role > 3)
                                                                    User
                                                                @elseif($user->role == 3)
                                                                    Supervisor
                                                                @elseif($user->role <= 2)
                                                                    Admin
                                                                @endif
                                                            </span></td>
                                                        <td>
                                                            @if ($user->latestLoginLog)
                                                                @if ($user->latestLoginLog->isLogged)
                                                                    <span
                                                                        class="badge rounded-pill text-bg-success">Online</span>
                                                                @else
                                                                    <span
                                                                        class="badge rounded-pill text-bg-danger">Offline</span>
                                                                @endif
                                                            @else
                                                                <span
                                                                    class="badge rounded-pill text-bg-danger">Offline</span>
                                                            @endif
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-primary viewBtn m-1 mt-0 mb-0"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-title="Tooltip on top" data-userId="{{$user->id}}">
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-danger deleteBtn m-0 mt-0 mb-0"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-title="Tooltip on top">
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th><input type="checkbox" id="selectAllFooter"></th>
                                                <th> {{-- here should be the profile pic --}} </th>
                                                <th>Full Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Statu</th>
                                                <th>Options</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <div class="d-flex justify-content-end mt-3">
                                        <button type="button" class="btn btn-danger m-3 mt-0 mb-0"
                                            id="deleteSelected">Delete
                                            Selected</button>
                                        <button type="button" class="btn btn-success mt-0 mb-0"
                                            id="exportSelected">Export
                                            Selected</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--  --}}
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script2')
    <script defer src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script>
        $(document).ready(function() {
            $('.deleteBtn').click(function() {
                var row = $(this).closest('tr');
                var userId = row.data('userid');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        console.log("here the user id " + userId);
                        $.ajax({
                            url: "{{ route('admin.allUsersList.form') }}",
                            method: 'POST',
                            data: {
                                userId: userId,
                                type: 'Duser',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                showAlertS('User Deleted successfully!');
                                // Optionally update UI or handle response as needed
                                console.log(response);

                                row.remove();
                                Swal.fire(
                                    'Deleted!',
                                    'The user has been deleted.',
                                    'success'
                                );
                            },
                            error: function(xhr, status, error) {
                                console.log(error);
                                showAlertD('Error Deleting user: ' + error);
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire(
                            'Cancelled',
                            'The user is safe :)',
                            'error'
                        );
                    }
                });
            });
        });
    </script>
    <script>
        document.getElementById('selectAll').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('.selectRow');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = this.checked;
            }, this);
        });

        document.getElementById('selectAllFooter').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('.selectRow');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = this.checked;
            }, this);
        });

        document.getElementById('deleteSelected').addEventListener('click', function() {
            var checkboxes = document.querySelectorAll('.selectRow:checked');
            if (checkboxes.length > 0) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete them!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        let selectedIds = [];
                        checkboxes.forEach(function(checkbox) {
                            let userId = checkbox.closest('tr').getAttribute('data-userId');
                            selectedIds.push(userId);
                            checkbox.closest('tr').remove();
                        });
                        // here the user's id !!!!!!
                        console.log(selectedIds);
                        $.ajax({
                            url: "{{ route('admin.allUsersList.form') }}",
                            method: 'POST',
                            data: {
                                usersIds: selectedIds,
                                type: 'Dusers',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    showAlertS('Selected users have been deleted.');

                                    // Reload the page after a short delay (optional)
                                    setTimeout(function() {
                                        location.reload(); // Reloads the current page
                                    }, 1000);
                                } else {
                                    showAlertD('Failed to delete selected users.');
                                }
                            },
                            error: function(xhr, status, error) {
                                showAlertD('Failed to delete selected users: ' + error)
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire(
                            'Cancelled',
                            'Selected users are safe :)',
                            'error'
                        )
                    }
                })
            } else {
                Swal.fire(
                    'No Selection',
                    'Please select users to delete.',
                    'info'
                )
            }
        });

        document.getElementById('exportSelected').addEventListener('click', function() {
            // var checkboxes = document.querySelectorAll('.selectRow:checked');
            // var data = [];
            // checkboxes.forEach(function(checkbox) {
            //     var row = checkbox.closest('tr');
            //     var rowData = {
            //         name: row.cells[1].innerText,
            //         position: row.cells[2].innerText,
            //         office: row.cells[3].innerText,
            //         age: row.cells[4].innerText,
            //         startDate: row.cells[5].innerText
            //     };
            //     data.push(rowData);
            // });

            // var json = JSON.stringify(data);
            // var blob = new Blob([json], {
            //     type: "application/json"
            // });
            // var url = URL.createObjectURL(blob);
            // var a = document.createElement('a');
            // a.href = url;
            // a.download = 'selected_data.json';
            // a.click();
            // URL.revokeObjectURL(url);
            showAlertS("Export user in success");
        });

        $('#submitBtn').click(function() {
            var formData = $('#addUserForm').serialize();

            function parseFormData(serializedString) {
                var data = {};
                serializedString.split('&').forEach(function(item) {
                    var parts = item.split('=');
                    data[decodeURIComponent(parts[0])] = decodeURIComponent(parts[1]);
                });
                return data;
            }

            var parsedData = parseFormData(formData);

            // Debugging: Log the parsedData object to check if all fields are present
            console.log(parsedData);

            $.ajax({
                url: "{{ route('admin.allUsersList.form') }}",
                method: 'POST',
                data: {
                    Fname: parsedData['FName'], // Use 'FName' as the key based on your form data
                    Lname: parsedData['LName'], // Use 'LName' as the key based on your form data
                    email: parsedData['email'],
                    tell: parsedData['tell'], // Use 'tell' as the key based on your form data
                    role: parsedData['role'],
                    type: 'Cuser',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showAlertS('User added successfully!');
                    console.log(response);

                    // Reload the page after a short delay (optional)
                    setTimeout(function() {
                        location.reload(); // Reloads the current page
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    console.log(error);
                    showAlertD('Error adding user: ' + error);
                }
            });
        });

        $(document).ready(function() {
            $('#fileUpload').change(function() {
                var formData = new FormData();
                var file = this.files[0]; // Get the selected file

                formData.append('excelFile', file);
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('type', 'ImportExcel');
                $.ajax({
                    url: "{{ route('admin.allUsersList.form') }}", // Replace with your Laravel route for importing Excel
                    method: 'POST',
                    data: formData,
                    processData: false, // Important: prevent jQuery from processing the data
                    contentType: false, // Important: tell jQuery not to set contentType
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        showAlertS('Excel file imported successfully!');
                        console.log(response);
                        // Optionally update UI or handle response as needed
                    },
                    error: function(xhr, status, error) {
                        showAlertD('Error importing Excel file: ' + error);
                        console.log(error);
                    }
                });
            });
        });
    </script>
    <!-- DataTables JS -->
    <script defer src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script defer src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>

    <!-- Custom JS -->
    <script defer>
        document.addEventListener('DOMContentLoaded', function() {
            $('#example').DataTable();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fetch and update users' data every 3 seconds (3000 ms)
            setInterval(fetchAndUpdateUsersData, 3000);

            function fetchAndUpdateUsersData() {
                $.ajax({
                    url: "{{ route('admin.allUsersList.json') }}", // Replace with your Laravel route for importing Excel
                    method: 'GET',
                    success: function(response) {
                        updateUsersTable(response.users);
                    },
                    error: function(xhr, status, error) {
                        console.log(error);
                    }
                });
            }

            function updateUsersTable(users) {
                users.forEach(user => {
                    updateUserInTable(user);
                });
            }

            function updateUserInTable(user) {
                // Find the row with the user's ID and update it
                const userRow = document.querySelector(`tr[data-userId="${user.id}"]`);
                if (userRow) {
                    userRow.querySelector('td:nth-child(2)').innerHTML =
                        `<img src="{{ asset('') }}${user.imgUrl}" alt="Profile Image" class="profile-img">`;
                    userRow.querySelector('td:nth-child(3)').innerText = `${user.Fname} ${user.Lname}`;
                    userRow.querySelector('td:nth-child(4)').innerText = user.email;
                    userRow.querySelector('td:nth-child(5)').innerHTML = `<span class="badge rounded-pill text-bg-primary ">${user.role}</span>`;
                    const statusBadge = (user.latest_login_log.isLogged)? 'success' : 'danger';
                    const statusText = (user.latest_login_log.isLogged)? 'Online' : 'Offline';
                    userRow.querySelector('td:nth-child(6)').innerHTML =
                        `<span class="badge rounded-pill text-bg-${statusBadge}">${statusText}</span>`;

                } else {
                    // Optionally, append a new row if the user is not found
                    const usersTableBody = document.querySelector('#example tbody');
                    const newRow = document.createElement('tr');
                    newRow.setAttribute('data-userId', user.id);
                    newRow.innerHTML = `
                <td><input type="checkbox" class="selectRow"></td>
                <td><img src="{{ asset('') }}${user.imgUrl}" alt="Profile Image" class="profile-img"></td>
                <td>${user.Fname} ${user.Lname}</td>
                <td>${user.email}</td>
                <td><span class="badge rounded-pill text-bg-primary ">${user.role}</span></td>
                <td><span class="badge rounded-pill text-bg-${statusBadge}">${statusText}</span></td>
                <td>
                    <button type="button" class="btn btn-primary viewBtn m-1 mt-0 mb-0" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Tooltip on top" data-userId="${user.id}"></button>
                    <button type="button" class="btn btn-danger deleteBtn m-0 mt-0 mb-0" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Tooltip on top"></button>
                </td>
            `;
                    usersTableBody.appendChild(newRow);
                }
            }
        });
    </script>

    <script>
        // Attach click event handler to all elements with class 'viewBtn'
        $(document).on('click','.viewBtn', function() {
            // Get the user ID from the data attribute
            var userId = $(this).data('userid');

            // Construct the URL for the route
            var url = '/user/' + userId + '/info';

            // Redirect to the constructed URL
            window.location.href = url;
        });
    </script>
@endsection
