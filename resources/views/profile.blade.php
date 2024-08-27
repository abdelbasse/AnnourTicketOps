@extends('layouts')

@section('style')
    <style>
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
                            <img src="{{ auth()->user()->imgUrl }}" alt="Profile Picture" class="profile-img"
                                id="profile-img">
                            <div class="profile-img-hover">Change Profile</div>
                        </div>
                        <h4>{{ auth()->user()->Fname }} {{ auth()->user()->Lname }}</h4>
                        @php
                            $roleName = 'User';
                            if (Auth()->user()->role() <= 2) {
                                $roleName = 'Admin';
                            } elseif (Auth()->user()->role() == 3) {
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
                                            name="Fname" value="{{ auth()->user()->Fname }}">
                                        <span class="input-group-text"><b>Last Name</b></span>
                                        <input type="text" aria-label="Last name" class="form-control" id="lname"
                                            name="Lname" value="{{ auth()->user()->Lname }}">
                                    </div>
                                </div>

                                <div class="form-group mb-2">
                                    <label for="tell"><b>Telephone</b></label>
                                    <input type="text" class="form-control" id="tell" name="tell"
                                        value="{{ auth()->user()->tell }}" required>
                                </div>

                                <div class="form-group mb-2">
                                    <label for="email"><b>Email</b></label>
                                    <input type="email" class="form-control" id="email"
                                        value="{{ auth()->user()->email }}" disabled>
                                </div>

                                <div class="form-group mb-2">
                                    <label for="role"><b>Role</b></label>
                                    <select class="form-select" id="role" disabled>
                                        <option value="1" @if (auth()->user()->role() == 1 || auth()->user()->role() == 2) selected @endif>Admin
                                        </option>
                                        <option value="2" @if (auth()->user()->role() == 3) selected @endif>Supervisor
                                        </option>
                                        <option value="3" @if (auth()->user()->role() > 3) selected @endif>Normal User
                                        </option>
                                    </select>
                                </div>

                                <input type="hidden" name="type" value="info">
                            </div>

                            <div class="btn-group-custom">
                                <button type="button" class="btn btn-secondary mr-2 m-1" data-bs-toggle="modal"
                                    data-bs-target="#exampleModalPasswordChange">Change Password</button>
                                <button type="button" id="submitUserInfoButton" class="btn btn-success m-1">Save
                                    Changes</button>
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
                                                action="{{ route('personal-profile-changeInfo') }}" method="POST">
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

        <div class="alertsContainer">

        </div>
    </body>

    </html>
@endsection
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
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
                        url: "{{ route('personal-profile-changeInfo') }}",
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#submitUserInfoButton').click(function(e) {
                // Submit the AJAX request
                $.ajax({
                    url: "{{ route('personal-profile-changeInfo') }}",
                    method: 'POST',
                    data: {
                        type: "info",
                        Fname: $('#fname').val(),
                        Lname: $('#lname').val(),
                        tell: $('#tell').val(),
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        showAlertS("Information Updated Successfully!");
                    },
                    error: function(xhr, status, error) {
                        showAlertD("Error: Something went wrong!");
                        console.error(xhr.responseText); // Log the error for debugging
                    },
                });
            });
        });
    </script>
@endsection

@section('script1')
@endsection
