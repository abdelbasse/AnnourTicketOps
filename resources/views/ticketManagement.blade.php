@extends('layouts')

@section('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <style>
        /* Remove default Bootstrap caret */
        .dropdown-btn::after {
            display: none;
            width: 24px;
            height: 24px;
        }

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

        .editBtn {
            height: 29px;
            width: 29px;
            border-radius: 100px;
            background-position: 57% 50%;
            background-color: rgb(232, 210, 46);
            background-repeat: no-repeat;
            background-size: 70%;
            background-image: url('{{ asset('img/icons/write.png') }}');
        }

        .profile-img {
            width: 43px;
            height: 43px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .card {
            margin-bottom: 20px;
        }

        tr.clickable-row {
            cursor: pointer;
        }
    </style>
    <style>
        /* Common styling for the status circle */
        .status-circle {
            /* display: inline-block;
                                                                                                                                                                                                                            width: 20px;
                                                                                                                                                                                                                            height: 20px;
                                                                                                                                                                                                                            line-height: 20px;
                                                                                                                                                                                                                            text-align: center;
                                                                                                                                                                                                                            border-radius: 50%;
                                                                                                                                                                                                                            font-weight: bold; */
            color: #fff;
            /* White text color for contrast */
        }

        /* Background colors based on data-status attribute */
        tr[data-status="open"] .status-circle {
            background-color: #007bff;
            /* Blue */
        }

        tr[data-status="resolved"] .status-circle {
            background-color: #ffc107;
            /* Yellow */
        }

        tr[data-status="closed"] .status-circle {
            background-color: #28a745;
            /* Green */
        }

        tr[data-status="validated"] .status-circle {
            background-color: #989a9c;
            /* Gray */
        }

        tr[data-status="nvalidated"] .status-circle {
            background-color: #dc3545;
            /* Red */
        }

        .profile-picture {
            min-width: 40px;
            max-width: 40px;
            /* Set the desired size */
            min-height: 40px;
            max-height: 40px;
            /* Set the desired size */
            border-radius: 50%;
            border: 2px solid black;
            /* Black border */
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            /* Shadow effect */
            object-fit: cover;
            /* Ensure the image covers the entire circle */
        }
    </style>
@endsection

@section('body')
    <div class="container-fluid mt-5 px-5">
        {{-- menu --}}
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item position-relative" role="presentation">
                <a class="nav-link active" id="user-tickets-tab" data-bs-toggle="tab" href="#user-tickets" role="tab"
                    aria-controls="user-tickets" aria-selected="true">
                    @if (auth()->user()->role() >= 3)
                        My Tickets
                    @elseif (auth()->user()->role() <= 2)
                        All Tickets
                    @endif
                </a>
                @if (auth()->user()->role() >= 3)
                    <span
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger
                    @if ($transeferTicket->count() != 0) d-none @endif"
                        id="List-of-tickets-tab-Badge">
                        999993
                    </span>
                @endif
            </li>
            <li class="nav-item position-relative" role="presentation">
                <a class="nav-link" id="transferred-tickets-tab" data-bs-toggle="tab" href="#transferred-tickets"
                    role="tab" aria-controls="transferred-tickets" aria-selected="false">Transferred Tickets</a>

                <span
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger
                @if ($transeferTicket->count() == 0) d-none @endif"
                    id="transferred-tickets-tab-Badge">
                    {{ $transeferTicket->count() }}
                </span>
            </li>
            <li class="nav-item position-relative" role="presentation">
                <a class="nav-link" id="cloture-tickets-tab" data-bs-toggle="tab" href="#cloture-tickets" role="tab"
                    aria-controls="cloture-tickets" aria-selected="false">Pending Closed Tickets</a>
                @php
                    $count = 0;
                    foreach ($TicketsPendingCloture as $item) {
                        if (
                            auth()->user()->role() <= 3 ||
                            (auth()->user()->role() > 3 &&
                                $item->currentOwnerRelation->reserver->id == auth()->user()->id)
                        ) {
                            $count += 1;
                        }
                    }
                @endphp


                <span
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger @if ($count == 0) d-none @endif"
                    id="cloture-tickets-tab-Badge">
                    {{ $count }}
                </span>
            </li>
        </ul>

        <style>
            .offcanvas-end {
                width: 400px; /* Adjust the width as needed */
            }
        </style>

        {{-- Fitrage card --}}

        {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
        <!-- Tickets Table -->
        <div class="card mt-0">
            <div class="card-body">
                <div class="row m-0 p-0 row-cols-1">
                    <div class="col p-2 pt-0 pb-0">
                        <div class="row m-1 mt-0 mb-0">
                            <div class="col m-0 p-0 align-content-center">
                                <h3>Tickets List</h3>
                                {{-- 2342234 --}}
                            </div>
                            <div class="col m-0 p-0 d-flex justify-content-end align-content-center">
                                <div class="justify-content-center align-content-center mt-0 mb-0 m-2">
                                    <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">Filter</button>
                                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
                                        <div class="offcanvas-header">
                                            <h5 class="offcanvas-title" id="offcanvasRightLabel">Filtration Options</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                        </div>
                                        <div class="offcanvas-body">
                                            <form id="filterForm">
                                                <div class="accordion" id="dateAccordion">
                                                    <!-- Created Date Filter -->
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingCreatedDate">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#collapseCreatedDate" aria-expanded="false"
                                                                aria-controls="collapseCreatedDate">
                                                                Created Date
                                                            </button>
                                                        </h2>
                                                        <div id="collapseCreatedDate" class="accordion-collapse collapse"
                                                            aria-labelledby="headingCreatedDate" data-bs-parent="#dateAccordion">
                                                            <div class="accordion-body">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <label for="createdDateStart" class="form-label">Start Date & Time</label>
                                                                        <input type="datetime-local" id="createdDateStart" name="createdDateStart"
                                                                            class="form-control">
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <label for="createdDateEnd" class="form-label">End Date & Time</label>
                                                                        <input type="datetime-local" id="createdDateEnd" name="createdDateEnd"
                                                                            class="form-control">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Incident Date Filter -->
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingIncidentDate">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#collapseIncidentDate" aria-expanded="false"
                                                                aria-controls="collapseIncidentDate">
                                                                Incident Date
                                                            </button>
                                                        </h2>
                                                        <div id="collapseIncidentDate" class="accordion-collapse collapse"
                                                            aria-labelledby="headingIncidentDate" data-bs-parent="#dateAccordion">
                                                            <div class="accordion-body">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <label for="incidentDateStart" class="form-label">Start Date & Time</label>
                                                                        <input type="datetime-local" id="incidentDateStart" name="incidentDateStart"
                                                                            class="form-control">
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <label for="incidentDateEnd" class="form-label">End Date & Time</label>
                                                                        <input type="datetime-local" id="incidentDateEnd" name="incidentDateEnd"
                                                                            class="form-control">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Closure Date Filter -->
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingClosureDate">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#collapseClosureDate" aria-expanded="false"
                                                                aria-controls="collapseClosureDate">
                                                                Cloture Date
                                                            </button>
                                                        </h2>
                                                        <div id="collapseClosureDate" class="accordion-collapse collapse"
                                                            aria-labelledby="headingClosureDate" data-bs-parent="#dateAccordion">
                                                            <div class="accordion-body">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <label for="closureDateStart" class="form-label">Start Date & Time</label>
                                                                        <input type="datetime-local" id="closureDateStart" name="closureDateStart"
                                                                            class="form-control">
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <label for="closureDateEnd" class="form-label">End Date & Time</label>
                                                                        <input type="datetime-local" id="closureDateEnd" name="closureDateEnd"
                                                                            class="form-control">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Other Filters -->
                                                <div class="row mt-3">
                                                    <div class="col-md-12">
                                                        <b><label for="equipmentFilter" class="form-label">Equipment</label></b>
                                                        <select id="equipmentFilter" name="equipmentFilter" class="form-select" multiple>
                                                            @foreach ($equipements as $item)
                                                                <option value="{{ $item->id }}">{{ $item->equipement }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <b><label for="airportFilter" class="form-label">Airport</label></b>
                                                        <select id="airportFilter" name="airportFilter" class="form-select" multiple>
                                                            @foreach ($airports as $item)
                                                                <option value="{{ $item->id }}">{{ $item->code }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <b><label for="natureAccidentFilter" class="form-label">Nature of Accident</label></b>
                                                        <select id="natureAccidentFilter" name="natureAccidentFilter" class="form-select" multiple>
                                                            @foreach ($problems as $item)
                                                                <option value="{{ $item->id }}">{{ $item->val }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <b><label for="natureSolutionFilter" class="form-label">Nature of Solution</label></b>
                                                        <select id="natureSolutionFilter" name="natureSolutionFilter" class="form-select" multiple>
                                                            @foreach ($solutions as $item)
                                                                <option value="{{ $item->id }}">{{ $item->val }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-md-12" style="width: 100%;">
                                                        <b><label for="statusFilter" class="form-label">Ticket Status</label></b>
                                                        <div id="statusFilter" class="d-flex flex-wrap" style="width: 100%;">
                                                            <div class="form-check m-2 mt- mb-2">
                                                                <input class="form-check-input border" type="checkbox" value="0"
                                                                    id="statusOpen">
                                                                <label class="form-check-label" for="statusOpen">Open</label>
                                                            </div>
                                                            <div class="form-check m-2 mt- mb-2">
                                                                <input class="form-check-input border" type="checkbox" value="1"
                                                                    id="statusRecovered">
                                                                <label class="form-check-label" for="statusRecovered">Recovered</label>
                                                            </div>
                                                            <div class="form-check m-2 mt- mb-2">
                                                                <input class="form-check-input border" type="checkbox" value="2"
                                                                    id="statusClosed">
                                                                <label class="form-check-label" for="statusClosed">Cloture</label>
                                                            </div>
                                                            @if (auth()->user()->role() <= 3)
                                                                <div class="form-check m-2 mt- mb-2">
                                                                    <input class="form-check-input border" type="checkbox" value="3"
                                                                        id="statusValidated">
                                                                    <label class="form-check-label" for="statusValidated">Validated</label>
                                                                </div>
                                                                <div class="form-check m-2 mt- mb-2">
                                                                    <input class="form-check-input border" type="checkbox" value="4"
                                                                        id="statusNotValidated">
                                                                    <label class="form-check-label" for="statusNotValidated">Not Validated</label>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-md-12 text-right d-flex justify-content-end">
                                                        <div class="btn-container ">
                                                            <button type="button" id="applyFilters" class="btn btn-primary m-2 mt-0 mb-0">Apply
                                                                Filters</button>
                                                            <button type="button" id="clearFilters" class="btn btn-secondary m-2 mt-0 mb-0">Clear
                                                                Filters</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end align-items-center">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <!-- Button trigger modal Add new ticket -->
                                                <a class="dropdown-item" href="#" id="newTicketBtn"
                                                    data-bs-toggle="modal" data-bs-target="#AddNewTicketModal">
                                                    New Ticket
                                                </a>
                                            </li>
                                            @if (auth()->user()->role() <= 3)
                                                <li>
                                                    <a class="dropdown-item" href="#" id="exportALll">Export
                                                        All</a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>

                                    <!-- Modal Add new Ticket -->
                                    <div class="modal fade" id="AddNewTicketModal" tabindex="-1"
                                        aria-labelledby="AddNewTicketModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="AddNewTicketModalLabel">New Ticket
                                                    </h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="title" class="form-label">Title <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="title"
                                                            name="title" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="description" class="form-label">Description</label>
                                                        <textarea class="form-control" id="description" name="description"></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="airport" class="form-label">Airport <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-select select2" id="airport" name="airport"
                                                            required>
                                                            @foreach ($airports as $item)
                                                                <option value="{{ $item->id }}">{{ $item->code }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="datetime" class="form-label">Date of Incident <span
                                                                class="text-danger">*</span></label>
                                                        <input type="datetime-local" class="form-control" id="datetime"
                                                            name="datetime" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary"
                                                        id="openSecondModalBtn">Next</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Add additional data -->
                                    <div class="modal fade" id="AddAdditionalDataModal" tabindex="-1"
                                        aria-labelledby="AddAdditionalDataModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="AddAdditionalDataModalLabel">
                                                        Additional
                                                        Data</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="SupportNotification" class="form-label">Support De
                                                            Notification</label>
                                                        <input type="text" class="form-control"
                                                            id="SupportNotification" name="SupportNotification" value="Platforme Supervison">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="ContactReclamation" class="form-label">Contact
                                                            Reclamation</label>
                                                        <input type="text" class="form-control"
                                                            id="ContactReclamation" name="ContactReclamation" value="Operatore NOC-AT">
                                                    </div>
                                                    <!-- Add more fields as needed -->
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary"
                                                        id="saveAllDataBtn">Save all changes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="user-tickets" role="tabpanel"
                            aria-labelledby="user-tickets-tab" tabindex="0">
                            <div class="">
                                <hr>
                                <div style="overflow-x:auto">
                                    <table id="ticketTable" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="selectAllTikets"></th>
                                                <th>#</th>
                                                <th>Title</th>
                                                <th>Description</th>
                                                <th>Owner</th>
                                                <th>Status</th>
                                                <th>Aerport</th>
                                                <th>Created On</th>
                                                <th>Incident Date</th>
                                                <th>Options</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tickets as $item)
                                                @if (auth()->user()->role() <= 3 ||
                                                        (auth()->user()->role() > 3 && $item->currentOwnerRelation->reserver->id == auth()->user()->id))
                                                    <!-- Example rows (replace with your data) -->
                                                    <tr data-ticketId="{{ $item->id }}" class="clickable-row"
                                                        data-status="{{ $item->getStatusDesign() }}"
                                                        data-status-org="{{ $item->status }}"
                                                        data-aerport="{{ $item->aerport->id }}"
                                                        data-equipement="{{ $item->hasAnalyseLogs() ? $item->latestAnalyseLog->equipementID : '' }}"
                                                        data-nIncident="{{ $item->hasAnalyseLogs() ? $item->latestAnalyseLog->naruteIncidentID : '' }}"
                                                        data-nSolution="{{ $item->hasRecoveryLogs() ? $item->latestRecoveryLog->naruteSolutionID : '' }}"
                                                        data-created_at="{{ $item->created_at }}"
                                                        data-inicident_date="{{ $item->DateIncident }}"
                                                        data-cloture_date="{{ $item->DateCloture }}">
                                                        <td><input type="checkbox" class="select-row"></td>
                                                        <td>{{ $item->id }}</td>
                                                        <td>
                                                            <span class="d-inline-block text-truncate"
                                                                style="max-width: 90px;">
                                                                {{ $item->title }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="d-inline-block text-truncate"
                                                                style="max-width: 150px;">
                                                                {{ $item->desc }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="user-info">
                                                                <img src="{{ $item->currentOwnerRelation->reserver->imgUrl }}"
                                                                    alt="{{ $item->currentOwnerRelation->reserver->Fname }}"
                                                                    class="profile-picture">
                                                                <span>{{ $item->currentOwnerRelation->reserver->Fname }}</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <p class="badge rounded-pill status-circle">
                                                                {{ $item->getStatus() }}
                                                            </p>
                                                        </td>
                                                        <td>{{ $item->aerport->code }}</td>
                                                        <td>{{ $item->created_at }}</td>
                                                        <td>{{ $item->DateIncident }}</td>
                                                        <td>
                                                            <div class="d-flex justify-content-center">
                                                                <div class="dropdown">
                                                                    <button
                                                                        class="btn btn-secondary dropdown-toggle dropdown-btn p-0 m-0 d-flex justify-content-center align-items-center"
                                                                        data-toggle="dropdown" id="dropdownMenuButton"
                                                                        type="button" data-bs-toggle="dropdown"
                                                                        aria-expanded="false">
                                                                        <i
                                                                            class='bx bx-sm bx-dots-horizontal-rounded p-0 m-0'></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        <li><a class="dropdown-item
                                                                            @if ($item->getLatestNullStatusOwnership()) disabled @endif
                                                                            "
                                                                                href="" data-bs-toggle="modal"
                                                                                data-bs-target="#listUsersModalSelect"
                                                                                data-ticketId={{ $item->id }}>Transfer
                                                                                Ticket</a></li>
                                                                        @if (auth()->user()->role() <= 3)
                                                                            <li><a class="dropdown-item
                                                                             ticketTransforToMeAction"
                                                                                    href="#"
                                                                                    data-ticket-id="{{ $item->id }}">
                                                                                    Assign to Myself
                                                                                </a>
                                                                            </li>
                                                                            <li><a class="dropdown-item"
                                                                                    href="{{route('home')}}/ticket/Rapport/{{ $item->id }}">Generate
                                                                                    Rapport</a></li>
                                                                        @endif
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <!-- Modal To Select a user to transfer -->
                                    <div class="modal fade" id="listUsersModalSelect" tabindex="-1"
                                        aria-labelledby="listUsersModalSelectLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="listUsersModalSelectLabel">Select
                                                        User for Ticket Transfer</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body" style="overflow-x:auto">
                                                    <!-- Hidden input for ticket ID -->
                                                    <input type="text" id="ticketIdContainerModalTransfer" hidden>
                                                    <input type="text" id="transferType" hidden>

                                                    <!-- Table for listing users with radio buttons -->
                                                    <table id="userTable" class="table" style="width: 100%;">
                                                        <thead>
                                                            <tr>
                                                                <th>Select</th>
                                                                <th>Avatar</th>
                                                                <th>Name</th>
                                                                <th>Role</th>
                                                                <th>Email</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($users as $user)
                                                                <tr data-userid="{{ $user->id }}">
                                                                    <td><input type="radio" name="userSelectTransfer"
                                                                            value="{{ $user->id }}"></td>
                                                                    <td><img src="{{ asset($user->imgUrl) }}"
                                                                            alt="Profile Image" class="profile-img"></td>
                                                                    <td>{{ $user->Fname }} {{ $user->Lname }}</td>
                                                                    <td><span
                                                                            class="badge rounded-pill text-bg-primary ">{{ $user->getRoleAttribute() }}</span>
                                                                    </td>
                                                                    <td>{{ $user->email }}</td>
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
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>

                                                    <!-- Checkbox for forced transfer (for supervisors) -->
                                                    @if (auth()->user()->role() <= 3)
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input"
                                                                id="userSelectTransferForcedVal" value="Forced">
                                                            <label class="form-check-label"
                                                                for="userSelectTransferForcedVal">Force Transfer</label>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary"
                                                        id="saveChangesButton">Transfer Ticket</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="button" class="btn btn-primary mt-0 mb-0 m-2" data-bs-toggle="modal"
                                        data-bs-target="#listUsersModalSelect"
                                        id="TransferSelectedTicketsTo">Transfer
                                        Selected</button>
                                    @if (auth()->user()->role() <= 3)
                                        <button type="button" class="btn btn-success mt-0 mb-0"
                                            id="exportSelected">Export
                                            Selected</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade m-0 p-0" id="transferred-tickets" role="tabpanel"
                            aria-labelledby="transferred-tickets-tab" tabindex="0">
                            <div class="">
                                <hr>
                                <div style="overflow-x:auto">
                                    <table id="ticketTableTransfered" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="selectAllTiketsTransfered"></th>
                                                <th>#</th>
                                                <th>Title</th>
                                                <th>Description</th>
                                                <th>Owner</th>
                                                <th>Status</th>
                                                <th>Aerport</th>
                                                <th>Created On</th>
                                                <th>Incident Date</th>
                                                <th>Options</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transeferTicket as $item)
                                                <!-- Example rows (replace with your data) -->
                                                <tr data-ticketId="{{ $item->id }}" class="clickable-row"
                                                    data-status="{{ $item->getStatusDesign() }}"
                                                    data-status-org="{{ $item->status }}"
                                                    data-aerport="{{ $item->aerport->id }}"
                                                    data-equipement="@if ($item->hasAnalyseLogs()) {{ $item->latestAnalyseLog->equipementID }} @endif"
                                                    data-nIncident="@if ($item->hasAnalyseLogs()) {{ $item->latestAnalyseLog->naruteIncidentID }} @endif"
                                                    data-nSolution="@if ($item->hasRecoveryLogs()) {{ $item->latestRecoveryLog->naruteSolutionID }} @endif"
                                                    data-created_at="{{ $item->created_at }}"
                                                    data-inicident_date="{{ $item->DateIncident }}"
                                                    data-cloture_date="{{ $item->DateCloture }}">
                                                    <td><input type="checkbox" class="select-row"></td>
                                                    <td>{{ $item->id }}</td>
                                                    <td>
                                                        <span class="d-inline-block text-truncate"
                                                            style="max-width: 90px;">
                                                            {{ $item->title }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="d-inline-block text-truncate"
                                                            style="max-width: 150px;">
                                                            {{ $item->desc }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="user-info">
                                                            <img src="{{ $item->currentOwnerRelation->reserver->imgUrl }}"
                                                                alt="{{ $item->currentOwnerRelation->reserver->Fname }}"
                                                                class="profile-picture">
                                                            <span>{{ $item->currentOwnerRelation->reserver->Fname }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <p class="badge rounded-pill status-circle">
                                                            {{ $item->getStatus() }}
                                                        </p>
                                                    </td>
                                                    <td>{{ $item->aerport->code }}</td>
                                                    <td>{{ $item->created_at }}</td>
                                                    <td>{{ $item->DateIncident }}</td>
                                                    <td>
                                                        <div
                                                            class="btn-container d-flex justify-content-center align-items-center">
                                                            <button class="btn btn-success p-1 m-1 TransferTicektOption"
                                                                data-ticket-id={{ $item->id }} data-statu="true"
                                                                style="width: 38px; height: 38px;">
                                                                <i class='bx bx-sm bx-check'></i>
                                                            </button>
                                                            <button class="btn btn-danger p-1 m-1 TransferTicektOption"
                                                                data-ticket-id={{ $item->id }} data-statu="false"
                                                                style="width: 38px; height: 38px;">
                                                                <i class='bx bx-sm bx-x'></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade m-0 p-0" id="cloture-tickets" role="tabpanel"
                            aria-labelledby="cloture-tickets-tab" tabindex="0">
                            <div class="">
                                <hr>
                                {{-- 099 --}}
                                <div style="overflow-x:auto">
                                    <table id="ticketTablePendingCloture" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Title</th>
                                                <th>Description</th>
                                                <th>Owner</th>
                                                <th>Aerport</th>
                                                <th>Incident Date</th>
                                                <th>Recovery Date</th>
                                                <th>Time Remaining</th>
                                                <th>Options</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($TicketsPendingCloture as $item)
                                                @if (auth()->user()->role() <= 3 ||
                                                        (auth()->user()->role() > 3 && $item->currentOwnerRelation->reserver->id == auth()->user()->id))
                                                    <!-- Example rows (replace with your data) -->
                                                    <tr data-ticketId="{{ $item->id }}" class="clickable-row"
                                                        data-status="{{ $item->getStatusDesign() }}"
                                                        data-status-org="{{ $item->status }}"
                                                        data-aerport="{{ $item->aerport->id }}"
                                                        data-equipement="{{ $item->hasAnalyseLogs() ? $item->latestAnalyseLog->equipementID : '' }}"
                                                        data-nIncident="{{ $item->hasAnalyseLogs() ? $item->latestAnalyseLog->naruteIncidentID : '' }}"
                                                        data-nSolution="{{ $item->hasRecoveryLogs() ? $item->latestRecoveryLog->naruteSolutionID : '' }}"
                                                        data-created_at="{{ $item->created_at }}"
                                                        data-inicident_date="{{ $item->DateIncident }}"
                                                        data-cloture_date="{{ $item->DateCloture }}">
                                                        <td>{{ $item->id }}</td>
                                                        <td>
                                                            <span class="d-inline-block text-truncate"
                                                                style="max-width: 90px;">
                                                                {{ $item->title }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="d-inline-block text-truncate"
                                                                style="max-width: 150px;">
                                                                {{ $item->desc }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="user-info">
                                                                <img src="{{ $item->currentOwnerRelation->reserver->imgUrl }}"
                                                                    alt="{{ $item->currentOwnerRelation->reserver->Fname }}"
                                                                    class="profile-picture">
                                                                <span>{{ $item->currentOwnerRelation->reserver->Fname }}</span>
                                                            </div>
                                                        </td>
                                                        <td>{{ $item->aerport->code }}</td>
                                                        <td>{{ $item->DateIncident }}</td>
                                                        <td>{{ $item->latestRecoveryLog->dateRecovery }}</td>
                                                        <td class="time-remaining"></td>
                                                        <td>
                                                            <div
                                                                class="btn-container d-flex justify-content-center align-items-center">
                                                                <button class="btn btn-danger p-1 m-1 ClotureTicketOption"
                                                                    data-ticket-id={{ $item->id }}
                                                                    style="width: 128px; height: 35px;">
                                                                    <p>Cloture Ticket</p>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}

    @if (auth()->user()->role() <= 2)
        <div class="container-fluid px-5">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title">Aerport List</h5>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#modalAddAirport">Add Aerport</a></li>
                                        <li><a class="dropdown-item" href="#" id="exportAirports">Export Excel</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" style="overflow-x:auto">
                            <table id="airportTable" class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Location</th>
                                        <th>Address</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($airports as $airport)
                                        <tr data-type="airport" data-id="{{ $airport->id }}">
                                            <td>{{ $airport->code }}</td>
                                            <td>{{ $airport->location }}</td>
                                            <td>{{ $airport->address }}</td>
                                            <td>
                                                <button type="button" class="btn btn-primary editBtn m-1 mt-0 mb-0"
                                                    data-bs-toggle="modal" data-bs-target="#editAirportModal"></button>
                                                <button type="button"
                                                    class="btn btn-danger deleteBtn m-0 mt-0 mb-0"></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Add New Airport Modal -->
                    <div class="modal fade" id="modalAddAirport" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="modalAddAirportLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalAddAirportLabel">Add New Airport</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="addAirportForm">
                                        <div class="mb-3">
                                            <label for="airportCode" class="form-label">Airport IATA Code</label>
                                            <input type="text" class="form-control" id="airportCode" name="code"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="airportLocation" class="form-label">Location</label>
                                            <input type="text" class="form-control" id="airportLocation"
                                                name="location" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="airportAddress" class="form-label">Address</label>
                                            <input type="text" class="form-control" id="airportAddress"
                                                name="address" required>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="submitAirportBtn">Add</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Airport Modal -->
                    <div class="modal fade" id="editAirportModal" tabindex="-1" aria-labelledby="editAirportModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editAirportModalLabel">Edit Airport</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="editAirportModal">
                                        <input type="hidden" id="editAirportId" name="id" value="">
                                        <div class="mb-3">
                                            <label for="editAirportCode" class="form-label">Airport IATA Code</label>
                                            <input type="text" class="form-control" id="editAirportCode"
                                                name="code" value="">
                                        </div>
                                        <div class="mb-3">
                                            <label for="editAirportLocation" class="form-label">Location</label>
                                            <input type="text" class="form-control" id="editAirportLocation"
                                                name="location" value="">
                                        </div>
                                        <div class="mb-3">
                                            <label for="editAirportAddress" class="form-label">Address</label>
                                            <input type="text" class="form-control" id="editAirportAddress"
                                                name="address" value="">
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="saveAirportChanges">Save
                                        changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title">Equipment</h5>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#modalAddEquipment">Add New Equipment</a></li>
                                        <li><a class="dropdown-item" href="#" id="exportEquipments">Export
                                                Excel</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" style="overflow-x:auto">
                            <table id="equipmentTable" class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Equipment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($equipements as $item)
                                        <tr data-type="equipment" data-id="{{ $item->id }}">
                                            <td>{{ $item->equipement }}</td>
                                            <td>
                                                <button type="button" class="btn btn-primary editBtn m-1 mt-0 mb-0"
                                                    data-bs-toggle="modal" data-bs-target="#editEquipmentModal"></button>
                                                <button type="button"
                                                    class="btn btn-danger deleteBtn m-0 mt-0 mb-0"></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Add New Equipment Modal -->
                    <div class="modal fade" id="modalAddEquipment" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="modalAddEquipmentLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalAddEquipmentLabel">Add New Equipment</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="addEquipmentForm">
                                        <div class="mb-3">
                                            <label for="equipmentName" class="form-label">Equipment Name</label>
                                            <input type="text" class="form-control" id="equipmentName"
                                                name="equipement" required>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="submitEquipmentBtn">Add</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Equipment Modal -->
                    <div class="modal fade" id="editEquipmentModal" tabindex="-1"
                        aria-labelledby="editEquipmentModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editEquipmentModalLabel">Edit Equipment</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="editEquipmentForm">
                                        <input type="hidden" id="editEquipmentId" name="id" value="">
                                        <div class="mb-3">
                                            <label for="editEquipmentName" class="form-label">Equipment Name</label>
                                            <input type="text" class="form-control" id="editEquipmentName"
                                                name="equipement" value="">
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="saveEquipmentChanges">Save
                                        changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Problem Type Card -->
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title">Problem Type</h5>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#modalAddProblemType">
                                                Add New Problem Type
                                            </a></li>
                                        <li><a class="dropdown-item" href="#" id="exportProblemTypes">Export
                                                Excel</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" style="overflow-x:auto">
                            <table id="problemTypeTable" class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Problem Type</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($problems as $item)
                                        <!-- Problem Type data will be populated here -->
                                        <tr data-type="problem" data-id="{{ $item->id }}">
                                            <td>
                                                <p class="d-inline-block text-truncate" style="max-width: 250px;">
                                                    {{ $item->val }}
                                                </p>
                                            </td>
                                            <td>
                                                <p class="d-inline-block text-truncate" style="max-width: 250px;">
                                                    {{ $item->desc }}
                                                </p>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-primary editBtn m-1 mt-0 mb-0"
                                                    data-bs-toggle="modal" data-bs-target="#editProblemModal">
                                                </button>
                                                <button type="button" class="btn btn-danger deleteBtn m-0 mt-0 mb-0"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-title="Tooltip on top">
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Add New Problem Type Modal -->
                    <div class="modal fade" id="modalAddProblemType" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="modalAddProblemTypeLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalAddProblemTypeLabel">Add New Problem Type</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Form for adding new problem type -->
                                    <form id="addProblemTypeForm">
                                        <div class="mb-3">
                                            <label for="problemType" class="form-label">Problem Type</label>
                                            <input type="text" class="form-control" id="problemType"
                                                name="problemType">
                                        </div>
                                        <div class="mb-3">
                                            <label for="problemDescription" class="form-label">Description</label>
                                            <textarea class="form-control" id="problemDescription" name="problemDescription" rows="3"></textarea>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="submitProblemTypeBtn">Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Edit Problem Type Modal -->
                    <div class="modal fade" id="editProblemModal" tabindex="-1"
                        aria-labelledby="editProblemModalLabel33" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editProblemModalLabel33">Edit Problem Type</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Form for editing Problem Type -->
                                    <form id="editProblemForm">
                                        <input type="hidden" id="problemId" name="problemId" value="">
                                        <div class="mb-3">
                                            <label for="problemType" class="form-label">Problem Type</label>
                                            <input type="text" class="form-control" id="problemType"
                                                name="problemType" value="Delayed Flight">
                                        </div>
                                        <div class="mb-3">
                                            <label for="problemDescription" class="form-label">Description</label>
                                            <textarea class="form-control" id="problemDescription" name="problemDescription" rows="3">.</textarea>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="saveProblemChanges">Save
                                        changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Solution Type Card -->
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title">Solution Type</h5>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#modalAddSolutionType">
                                                Add New Solution Type
                                            </a></li>
                                        <li><a class="dropdown-item" href="#" id="exportSolutionTypes">Export
                                                Excel</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" style="overflow-x:auto">
                            <table id="solutionTypeTable" class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Solution Type</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($solutions as $item)
                                        <!-- Solution Type data will be populated here -->
                                        <tr data-type="solution" data-id="{{ $item->id }}">
                                            <td>
                                                <p class="d-inline-block text-truncate" style="max-width: 250px;">
                                                    {{ $item->val }}
                                                </p>
                                            </td>
                                            <td>
                                                <p class="d-inline-block text-truncate" style="max-width: 250px;">
                                                    {{ $item->desc }}
                                                </p>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-primary editBtn m-1 mt-0 mb-0"
                                                    data-bs-toggle="modal" data-bs-target="#editSolutionModal">

                                                </button>
                                                <button type="button" class="btn btn-danger deleteBtn m-0 mt-0 mb-0"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-title="Delete">

                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Add New Solution Type Modal -->
                    <div class="modal fade" id="modalAddSolutionType" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="modalAddSolutionTypeLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalAddSolutionTypeLabel">Add New Solution Type</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Form for adding new solution type -->
                                    <form id="addSolutionTypeForm">
                                        <div class="mb-3">
                                            <label for="addSolutionType" class="form-label">Solution Type</label>
                                            <input type="text" class="form-control" id="addSolutionType"
                                                name="solutionType">
                                        </div>
                                        <div class="mb-3">
                                            <label for="addSolutionDescription" class="form-label">Description</label>
                                            <textarea class="form-control" id="addSolutionDescription" name="solutionDescription" rows="3"></textarea>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="button" id="submitSolutionTypeBtn"
                                        class="btn btn-primary">Add</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Solution Type Modal -->
                    <div class="modal fade" id="editSolutionModal" tabindex="-1"
                        aria-labelledby="editSolutionModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editSolutionModalLabel">Edit Solution Type</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Form for editing Solution Type -->
                                    <form id="editSolutionForm">
                                        <input type="hidden" id="solutionId" name="solutionId" value="">
                                        <div class="mb-3">
                                            <label for="editSolutionType" class="form-label">Solution Type</label>
                                            <input type="text" class="form-control" id="solutionType"
                                                name="solutionType">
                                        </div>
                                        <div class="mb-3">
                                            <label for="editSolutionDescription" class="form-label">Description</label>
                                            <textarea class="form-control" id="solutionDescription" name="solutionDescription" rows="3"></textarea>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="saveSolutionChanges">Save
                                        changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    </div>
@endsection

@section('script2')
    <script defer src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script defer src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script defer src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script>
        function parseFormData(serializedString) {
            var data = {};
            serializedString.split('&').forEach(function(item) {
                var parts = item.split('=');
                data[decodeURIComponent(parts[0])] = decodeURIComponent(parts[1]);
            });
            return data;
        }
        $(document).ready(function() {
            // Initialize DataTable for Airport Table
            $('#airportTable').DataTable();
            // Initialize DataTable for Equipment Table
            $('#problemTypeTable').DataTable();
            // Initialize DataTable for Equipment Table
            $('#solutionTypeTable').DataTable();
            // Initialize DataTable for Equipment Table
            $('#equipmentTable').DataTable();


            // Initialize DataTable for Ticket Table
            $('#ticketTable').DataTable();
            $('#ticketTableTransfered').DataTable();
            $('#ticketTablePendingCloture').DataTable();




            // Handle Add Airport Form Submission
            $('#submitAirportBtn').on('click', function() {
                var formData = $('#addAirportForm').serialize();
                var parsedData = parseFormData(formData);
                // console.log(parsedData);
                // // AJAX call to add airport
                $.ajax({
                    url: '{{ route('admin.ticket.Add') }}', // Adjust the URL to your API endpoint for adding airports
                    method: 'POST',
                    data: {
                        code: parsedData['code'],
                        location: parsedData['location'],
                        address: parsedData['address'], // Added address field
                        type: 'NAir',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Close the modal
                        $('#modalAddAirport').modal('hide');
                        // Show alert with each piece of airport data
                        showAlertS('New Airport Added!');


                        // Reload the page after a short delay (optional)
                        // setTimeout(function() {
                        //     location.reload(); // Reloads the current page
                        // }, 1000);
                    },
                    error: function(xhr, status, error) {
                        // Handle error responses if needed
                        console.error('Error adding airport:', error);
                        showAlertD('Failed to add new airport. Please try again.');
                    }
                });
            });

            // Handle Add Equipment Form Submission
            $('#submitEquipmentBtn').on('click', function() {
                var formData = $('#addEquipmentForm').serialize();
                var parsedData = parseFormData(formData);
              ////  console.log(parsedData);

                // AJAX call to add equipment
                $.ajax({
                    url: '{{ route('admin.ticket.Add') }}', // Adjust the URL to your API endpoint for adding equipment
                    method: 'POST',
                    data: {
                        equipement: parsedData['equipement'],
                        type: 'NEqu',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Close the modal
                        $('#modalAddEquipment').modal('hide');
                        // Show success alert
                        showAlertS('New Equipment Added!');

                        // Reload the page after a short delay (optional)
                        // setTimeout(function() {
                        //     location.reload(); // Reloads the current page
                        // }, 1000);
                    },
                    error: function(xhr, status, error) {
                        // Handle error responses if needed
                        console.error('Error adding equipment:', error);
                        showAlertD('Failed to add new equipment. Please try again.');
                    }
                });
            });

            // Handle Add Problem Type Form Submission
            $('#submitProblemTypeBtn').on('click', function() {
                var formData = $('#addProblemTypeForm').serialize();
                var parsedData = parseFormData(formData);
              ////  console.log(parsedData);

                // AJAX call to add problem type
                $.ajax({
                    url: '{{ route('admin.ticket.Add') }}', // Adjust the URL to your API endpoint for adding problem types
                    method: 'POST',
                    data: {
                        problemType: parsedData['problemType'],
                        problemDescription: parsedData['problemDescription'],
                        type: 'NProblem',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Close the modal
                        $('#modalAddProblemType').modal('hide');
                        // Show success alert
                        showAlertS('New Problem Type Added!');

                        // Reload the page after a short delay (optional)
                        // setTimeout(function() {
                        //     location.reload(); // Reloads the current page
                        // }, 1000);
                    },
                    error: function(xhr, status, error) {
                        // Handle error responses if needed
                        console.error('Error adding problem type:', error);
                        showAlertD('Failed to add new problem type. Please try again.');
                    }
                });
            });

            // Handle Add Solution Type Form Submission
            $('#submitSolutionTypeBtn').on('click', function() {
                var formData = $('#addSolutionTypeForm').serialize();
                var parsedData = parseFormData(formData);
              ////  console.log("----->>> " + parsedData['solutionType'] + " , " + parsedData[
                    'solutionDescription']);

                // AJAX call to add solution type
                $.ajax({
                    url: '{{ route('admin.ticket.Add') }}', // Adjust the URL to your API endpoint for adding problem types
                    method: 'POST',
                    data: {
                        solutionType: parsedData['solutionType'],
                        solutionDescription: parsedData['solutionDescription'],
                        type: 'NSolution',
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        // Close the modal
                        $('#modalAddsolutionType').modal('hide');
                        // Show success alert
                        showAlertS('New Solution Type Added!');

                        // Reload the page after a short delay (optional)
                        // setTimeout(function() {
                        //     location.reload(); // Reloads the current page
                        // }, 1000);
                    },
                    error: function(xhr, status, error) {
                        // Handle error responses if needed
                        console.error('Error adding solution type:', error);
                        showAlertD('Failed to add new solution type. Please try again.');
                    }
                });
            });

            // Handle Select All Checkboxes for Equipment
            $('#selectAllTikets').change(function() {
                var checkboxes = document.querySelectorAll('#ticketTable tbody tr .select-row');
                checkboxes.forEach(function(checkbox) {
                    // Only check/uncheck checkboxes in visible rows
                    if ($(checkbox).closest('tr').is(':visible')) {
                        checkbox.checked = this.checked;
                    } else {
                        checkbox.checked = false; // Ensure checkboxes in hidden rows are unchecked
                    }
                }, this);
            });

            // Handle Select All Checkboxes for Equipment
            $('#selectAllTiketsTransfered').change(function() {
                var checkboxes = document.querySelectorAll('#ticketTableTransfered tbody tr .select-row');
                checkboxes.forEach(function(checkbox) {
                    // Only check/uncheck checkboxes in visible rows
                    if ($(checkbox).closest('tr').is(':visible')) {
                        checkbox.checked = this.checked;
                    } else {
                        checkbox.checked = false; // Ensure checkboxes in hidden rows are unchecked
                    }
                }, this);
            });

            // Handle Select All Checkboxes for Equipment
            $('#selectAllTiketsPendingClosed').change(function() {
                var checkboxes = document.querySelectorAll(
                    '#ticketTablePendingCloture tbody tr .select-row');
                checkboxes.forEach(function(checkbox) {
                    // Only check/uncheck checkboxes in visible rows
                    if ($(checkbox).closest('tr').is(':visible')) {
                        checkbox.checked = this.checked;
                    } else {
                        checkbox.checked = false; // Ensure checkboxes in hidden rows are unchecked
                    }
                }, this);
            });


            // Show alert message (temporary function)
            function showAlert(message) {
                Swal.fire({
                    icon: 'info',
                    title: message,
                    showConfirmButton: false,
                    timer: 1500
                });
            }
            // Function to confirm deletion
        });
    </script>
    <script>
        // Function to confirm deletion
        function confirmDelete(button) {
            var row = $(this).closest('tr');
            var itemType = row.data('type');
            var itemId = row.data('id');
          ////  console.log(row);

            swal({
                    title: "Are you sure?",
                    text: `Do you want to delete this ${itemType}?`,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        // Show alert with deleted item info
                        swal(`Deleted! ID: ${itemId}, Type: ${itemType}`, {
                            icon: "success",
                        });
                        // Perform deletion action here
                        // Example: send AJAX request to delete item with itemId
                    } else {
                        swal("Deletion canceled.");
                    }
                });
        }

        $(document).on('click', '.deleteBtn', function() {
            var row = $(this).closest('tr');
            var itemType = row.data('type');
            var itemId = row.data('id');

          ////  console.log("here the user id " + itemId + " , type : " + itemType);
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
                    $.ajax({
                        url: "{{ route('admin.ticket.delete') }}",
                        method: 'POST',
                        data: {
                            id: itemId,
                            type: itemType,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            showAlertS('Item Deleted successfully!');
                            // Optionally update UI or handle response as needed
                          ////  console.log(response);

                            row.remove();
                            Swal.fire(
                                'Deleted!',
                                'The Item has been deleted.',
                                'success'
                            );
                        },
                        error: function(xhr, status, error) {
                          //  console.log(error);
                            showAlertD('Error Deleting item: ' + error);
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire(
                        'Cancelled',
                        'The Item is safe :)',
                        'error'
                    );
                }
            });
        });
    </script>
    <script>
        $(document).on('click', '.editBtn', function() {
            var row = $(this).closest('tr');
            var itemType = row.data('type');
            var itemId = row.data('id');

            // alert("id of " + itemType + " is : " + itemId);
            if (itemType === "airport") {
                var airportCode = row.find('td:eq(0)').text()
                    .trim(); // Get airport IATA Code from table cell
                var airportLocation = row.find('td:eq(1)').text()
                    .trim(); // Get airport location from table cell
                var airportAddress = row.find('td:eq(2)').text()
                    .trim(); // Get airport address from table cell

                // Populate modal fields
                $('#editAirportModal #editAirportId').val(itemId);
                $('#editAirportModal #editAirportCode').val(airportCode);
                $('#editAirportModal #editAirportLocation').val(airportLocation);
                $('#editAirportModal #editAirportAddress').val(airportAddress);

                // Show the modal
                $('#editAirportModal').modal('show');
            } else if (itemType === "equipment") {
                var equipmentName = row.find('td:eq(0)').text()
                    .trim(); // Get equipment name from table cell

                // Populate modal fields
                $('#editEquipmentModal #editEquipmentId').val(itemId);
                $('#editEquipmentModal #editEquipmentName').val(equipmentName);

                // Show the modal
                $('#editEquipmentModal').modal('show');
            } else if (itemType === "problem") {
                var problemType = row.find('td:eq(0)').text()
                    .trim(); // Get problem type from table cell
                var problemDescription = row.find('td:eq(1)').text()
                    .trim(); // Get problem description from table cell

                // Populate modal fields
                $('#editProblemModal #problemId').val(itemId);
                $('#editProblemModal #problemType').val(problemType);
                $('#editProblemModal #problemDescription').val(problemDescription);

                // Show the modal
                $('#editProblemModal').modal('show');
            } else if (itemType === "solution") {
                var solutionType = row.find('td:eq(0)').text()
                    .trim(); // Get solution type from table cell
                var solutionDescription = row.find('td:eq(1)').text()
                    .trim(); // Get solution description from table cell

                // Populate modal fields
                $('#editSolutionModal #solutionId').val(itemId);
                $('#editSolutionModal #solutionType').val(solutionType);
                $('#editSolutionModal #solutionDescription').val(solutionDescription);

                // Show the modal
                $('#editSolutionModal').modal('show');
            }
        });

        // Function to handle save changes button click for Airport
        $(document).on('click', '#saveAirportChanges', function() {
            var itemId = $('#editAirportModal #editAirportId').val();
            var airportCode = $('#editAirportModal #editAirportCode').val();
            var airportLocation = $('#editAirportModal #editAirportLocation').val();
            var airportAddress = $('#editAirportModal #editAirportAddress')
                .val(); // Added for address field

            // Perform validation if needed
            $.ajax({
                url: "{{ route('admin.ticket.Edit') }}",
                method: 'POST',
                data: {
                    id: itemId,
                    code: airportCode,
                    location: airportLocation,
                    address: airportAddress, // Added for address field
                    type: 'EAir',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showAlertS('Airport Updated successfully!');
                    // Optionally update UI or handle response as needed
                  //  console.log(response);
                    Swal.fire(
                        'Edited!',
                        'The Airport has been Updated.',
                        'success'
                    );
                },
                error: function(xhr, status, error) {
                  //  console.log(error);
                    showAlertD('Error updating airport: ' + error);
                }
            });

            // Close the modal
            $('#editAirportModal').modal('hide');
        });


        $(document).on('click', '#saveEquipmentChanges', function() {
            var itemId = $('#editEquipmentModal #editEquipmentId').val();
            var equipmentName = $('#editEquipmentModal #editEquipmentName').val();

            // Perform validation if needed
            $.ajax({
                url: "{{ route('admin.ticket.Edit') }}", // Adjust the route to your controller method for editing Equipment
                method: 'POST',
                data: {
                    id: itemId,
                    name: equipmentName,
                    type: 'EEqu', // Adjusted type for equipment
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showAlertS('Equipment Updated successfully!');
                    // Optionally update UI or handle response as needed
                  //  console.log(response);
                    Swal.fire(
                        'Edited!',
                        'The Equipment has been Updated.',
                        'success'
                    );
                },
                error: function(xhr, status, error) {
                  //  console.log(error);
                    showAlertD('Error updating equipment: ' + error);
                }
            });

            // Close the modal
            $('#editEquipmentModal').modal('hide');
        });

        // Function to handle save changes button click for Problem Type
        $(document).on('click', '#saveProblemChanges', function() {
            var problemId = $('#editProblemModal #problemId').val();
            var problemType = $('#editProblemModal #problemType').val();
            var problemDescription = $('#editProblemModal #problemDescription').val();

            // Perform validation if needed
            $.ajax({
                url: "{{ route('admin.ticket.Edit') }}", // Replace with your route
                method: 'POST',
                data: {
                    id: problemId,
                    Ptype: problemType,
                    description: problemDescription,
                    type: 'EProblem', // Adjusted type for equipment
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showAlertS('Problem Type Updated successfully!');
                    // Optionally update UI or handle response as needed
                  //  console.log(response);
                    Swal.fire(
                        'Edited!',
                        'The Problem Type has been Updated.',
                        'success'
                    );
                },
                error: function(xhr, status, error) {
                  //  console.log(error);
                    showAlertD('Error updating problem type: ' + error);
                }
            });

            // Close the modal
            $('#editProblemModal').modal('hide');
        });

        // Function to handle save changes button click for Solution Type
        $(document).on('click', '#saveSolutionChanges', function() {
            var solutionId = $('#editSolutionModal #solutionId').val();
            var solutionType = $('#editSolutionModal #solutionType').val();
            var solutionDescription = $('#editSolutionModal #solutionDescription').val();

            // Perform validation if needed
            $.ajax({
                url: "{{ route('admin.ticket.Edit') }}", // Replace with your route
                method: 'POST',
                data: {
                    id: solutionId,
                    Stype: solutionType,
                    description: solutionDescription,
                    type: 'ESolution', // Adjusted type for equipment
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showAlertS('Solution Type Updated successfully!');
                    // Optionally update UI or handle response as needed
                  //  console.log(response);
                    Swal.fire(
                        'Edited!',
                        'The Solution Type has been Updated.',
                        'success'
                    );
                },
                error: function(xhr, status, error) {
                  //  console.log(error);
                    showAlertD('Error updating solution type: ' + error);
                }
            });

            // Close the modal
            $('#editSolutionModal').modal('hide');
        });

        $(document).ready(function() {
            // Function to handle edit button click and show ID in modal
            // $('.editBtn').on('click', function() {
            //     var row = $(this).closest('tr');
            //     var itemType = row.data('type');
            //     var itemId = row.data('id');

            //     if (itemType === "airport") {
            //         var airportCode = row.find('td:eq(0)').text()
            //             .trim(); // Get airport IATA Code from table cell
            //         var airportLocation = row.find('td:eq(1)').text()
            //             .trim(); // Get airport location from table cell
            //         var airportAddress = row.find('td:eq(2)').text()
            //             .trim(); // Get airport address from table cell

            //         // Populate modal fields
            //         $('#editAirportModal #editAirportId').val(itemId);
            //         $('#editAirportModal #editAirportCode').val(airportCode);
            //         $('#editAirportModal #editAirportLocation').val(airportLocation);
            //         $('#editAirportModal #editAirportAddress').val(airportAddress);

            //         // Show the modal
            //         $('#editAirportModal').modal('show');
            //     } else if (itemType === "equipment") {
            //         var equipmentName = row.find('td:eq(0)').text()
            //             .trim(); // Get equipment name from table cell

            //         // Populate modal fields
            //         $('#editEquipmentModal #editEquipmentId').val(itemId);
            //         $('#editEquipmentModal #editEquipmentName').val(equipmentName);

            //         // Show the modal
            //         $('#editEquipmentModal').modal('show');
            //     } else if (itemType === "problem") {
            //         var problemType = row.find('td:eq(0)').text()
            //             .trim(); // Get problem type from table cell
            //         var problemDescription = row.find('td:eq(1)').text()
            //             .trim(); // Get problem description from table cell

            //         // Populate modal fields
            //         $('#editProblemModal #problemId').val(itemId);
            //         $('#editProblemModal #problemType').val(problemType);
            //         $('#editProblemModal #problemDescription').val(problemDescription);

            //         // Show the modal
            //         $('#editProblemModal').modal('show');
            //     } else if (itemType === "solution") {
            //         var solutionType = row.find('td:eq(0)').text()
            //             .trim(); // Get solution type from table cell
            //         var solutionDescription = row.find('td:eq(1)').text()
            //             .trim(); // Get solution description from table cell

            //         // Populate modal fields
            //         $('#editSolutionModal #solutionId').val(itemId);
            //         $('#editSolutionModal #solutionType').val(solutionType);
            //         $('#editSolutionModal #solutionDescription').val(solutionDescription);

            //         // Show the modal
            //         $('#editSolutionModal').modal('show');
            //     }
            // });

            // Function to handle save changes button click for Airport
            $('#saveAirportChanges').on('click', function() {
                var itemId = $('#editAirportModal #editAirportId').val();
                var airportCode = $('#editAirportModal #editAirportCode').val();
                var airportLocation = $('#editAirportModal #editAirportLocation').val();
                var airportAddress = $('#editAirportModal #editAirportAddress')
                    .val(); // Added for address field

                // Perform validation if needed
                $.ajax({
                    url: "{{ route('admin.ticket.Edit') }}",
                    method: 'POST',
                    data: {
                        id: itemId,
                        code: airportCode,
                        location: airportLocation,
                        address: airportAddress, // Added for address field
                        type: 'EAir',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        showAlertS('Airport Updated successfully!');
                        // Optionally update UI or handle response as needed
                      //  console.log(response);
                        Swal.fire(
                            'Edited!',
                            'The Airport has been Updated.',
                            'success'
                        );
                    },
                    error: function(xhr, status, error) {
                      //  console.log(error);
                        showAlertD('Error updating airport: ' + error);
                    }
                });

                // Close the modal
                $('#editAirportModal').modal('hide');
            });

            // Function to handle save changes button click for Equipment

        });
    </script>

    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}

    <script>
        $(document).on('click', '.clickable-row', function(event) {
            // Check if the click was not on a checkbox or inside the dropdown menu
            if (!$(event.target).closest('input[type="checkbox"]').length &&
                !$(event.target).closest('.dropdown-btn').length &&
                !$(event.target).closest('.dropdown-menu').length &&
                !$(event.target).closest('.ClotureTicketOption').length &&
                !$(event.target).closest('.TransferTicektOption').length) {
                var ticketId = $(this).data("ticketid");
                window.location.href = "/" + ticketId +
                    "/ticket"; // Change to your desired URL structure
            }
        });
    </script>


    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- for addistional stuff like dcurent date on click to add new ticket --}}

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var newTicketBtn = document.getElementById('newTicketBtn');
            const dateTimeInput = document.getElementById('datetime');

            newTicketBtn.addEventListener('click', function() {
                const now = new Date();
                const formattedDateTime = now.toISOString().slice(0, 16);
                dateTimeInput.value = formattedDateTime;
            });
        });
    </script>


    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- for modale Ticket logic --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const openSecondModalBtn = document.getElementById('openSecondModalBtn');
            const saveAllDataBtn = document.getElementById('saveAllDataBtn');

            openSecondModalBtn.addEventListener('click', function() {
                // Close the first modal
                const firstModal = bootstrap.Modal.getInstance(document.getElementById(
                    'AddNewTicketModal'));
                firstModal.hide();

                // Open the second modal
                const secondModal = new bootstrap.Modal(document.getElementById('AddAdditionalDataModal'));
                secondModal.show();
            });

            saveAllDataBtn.addEventListener('click', function() {
                // Collect data from the first modal
                const title = document.getElementById('title').value;
                const description = document.getElementById('description').value;
                const airport = document.getElementById('airport').value;
                const datetime = document.getElementById('datetime').value;

                // Collect data from the second modal
                const SupportNotification = document.getElementById('SupportNotification').value;
                const ContactReclamation = document.getElementById('ContactReclamation').value;

                // Log the collected data to the console
              //  console.log({
                    title,
                    description,
                    airport,
                    datetime,
                    ContactReclamation,
                    SupportNotification
                });

                $.ajax({
                    url: '{{ route('ticket.Add.Ticket') }}', // Adjust the URL to your API endpoint for adding airports
                    method: 'POST',
                    data: {
                        title: title,
                        desc: description,
                        airport: airport,
                        dateIncident: datetime,
                        ContactReclamation: ContactReclamation,
                        SupportNotification: SupportNotification,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Show alert with each piece of airport data
                        showAlertS('New Ticket Added successfully!');

                        // -----------------------------------------
                        // only be commeted if i add every 100milisec to refrech the data of the tickets [after]
                        // -----------------------------------------
                        // Reload the page after a short delay (optional)
                        // setTimeout(function() {
                        //     location.reload(); // Reloads the current page
                        // }, 1000);
                    },
                    error: function(xhr, status, error) {
                        // Handle error responses if needed
                        console.error('Error adding airport:', error);
                        showAlertD('Failed to add new Ticket. Please try again.');
                    }
                });

                // Close the second modal
                const secondModal = bootstrap.Modal.getInstance(document.getElementById(
                    'AddAdditionalDataModal'));
                secondModal.hide();
            });
        });
    </script>

    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- for Fitrage logic --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const equipmentFilter = new Choices('#equipmentFilter', {
                removeItemButton: true,
                searchEnabled: true
            });

            const airportFilter = new Choices('#airportFilter', {
                removeItemButton: true,
                searchEnabled: true
            });

            const natureAccidentFilter = new Choices('#natureAccidentFilter', {
                removeItemButton: true,
                searchEnabled: true
            });

            const natureSolutionFilter = new Choices('#natureSolutionFilter', {
                removeItemButton: true,
                searchEnabled: true
            });

            document.getElementById('applyFilters').addEventListener('click', function() {
                const createdDateStart = convertDate(document.getElementById('createdDateStart').value);
                const createdDateEnd = convertDate(document.getElementById('createdDateEnd').value);
                const incidentDateStart = convertDate(document.getElementById('incidentDateStart').value);
                const incidentDateEnd = convertDate(document.getElementById('incidentDateEnd').value);
                const closureDateStart = convertDate(document.getElementById('closureDateStart').value);
                const closureDateEnd = convertDate(document.getElementById('closureDateEnd').value);

                const equipmentSelected = equipmentFilter.getValue().map(item => item.value);
                const airportSelected = airportFilter.getValue().map(item => item.value);
                const natureAccidentSelected = natureAccidentFilter.getValue().map(item => item.value);
                const natureSolutionSelected = natureSolutionFilter.getValue().map(item => item.value);

                // Get selected checkbox values
                const statusCheckboxes = document.querySelectorAll('#statusFilter .form-check-input');
                const statusSelected = Array.from(statusCheckboxes)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value);


                function convertDate(dateString) {
                    if (!dateString) return null;
                    const date = new Date(dateString);
                    return date.toISOString().replace('T', ' ').split('.')[
                        0]; // Convert to "YYYY-MM-DD HH:MM:SS" format
                }
                // console.log('Filter Data:');
                // console.log('Created Date Start:', createdDateStart);
                // console.log('Created Date End:', createdDateEnd);
                // console.log('Incident Date Start:', incidentDateStart);
                // console.log('Incident Date End:', incidentDateEnd);
                // console.log('Closure Date Start:', closureDateStart);
                // console.log('Closure Date End:', closureDateEnd);
                // console.log('Equipment Selected:', equipmentSelected);
                // console.log('Airport Selected:', airportSelected);
                // console.log('Nature of Accident Selected:', natureAccidentSelected);
                // console.log('Nature of Solution Selected:', natureSolutionSelected);
                // console.log('Status Selected:', statusSelected);

                const rows = document.querySelectorAll(
                    '#ticketTable tbody tr , #ticketTablePendingCloture tbody tr , #ticketTableTransfered tbody tr'
                );

                // console.log(rows)

                rows.forEach(row => {
                    const rowStatus = row.getAttribute('data-status-org');
                    const rowAirport = row.getAttribute('data-aerport');
                    const rowCreatedDate = row.getAttribute('data-created_at');
                    const rowIncidentDate = row.getAttribute('data-inicident_date');
                    const rowClosureDate = row.getAttribute('data-cloture_date');
                    const rowEquipment = row.getAttribute('data-equipement');
                    const rowNatureAccident = row.getAttribute('data-nIncident');
                    const rowNatureSolution = row.getAttribute('data-nSolution');


                  //  console.log(
                        // `Status: ${rowStatus}, ` +
                        // `Airport: ${rowAirport}, ` +
                        // `Created Date: ${rowCreatedDate}, ` +
                        // `Incident Date: ${rowIncidentDate}, ` +
                        // `Closure Date: ${rowClosureDate}, ` //+
                        // `Nature of Accident: ${rowNatureAccident}, ` +
                        // `Nature of Solution: ${rowNatureSolution}, ` +
                        // `Equipment: ${rowEquipment}`
                    );


                    let showRow = true;

                    // Status filter with OR logic
                    if (statusSelected.length > 0 && !statusSelected.includes(rowStatus)) {
                        showRow = false;
                    }

                    // Airport filter with AND logic
                    if (airportSelected.length > 0 && !airportSelected.includes(rowAirport)) {
                        showRow = false;
                    }

                    if (natureAccidentSelected.length > 0 && (!rowNatureAccident || !
                            natureAccidentSelected.includes(rowNatureAccident))) {
                        showRow = false;
                    }

                    if (natureSolutionSelected.length > 0 && (!rowNatureSolution || !
                            natureSolutionSelected.includes(rowNatureSolution))) {
                        showRow = false;
                    }

                    if (equipmentSelected.length > 0 && (!rowEquipment || !equipmentSelected
                            .includes(rowEquipment))) {
                        showRow = false;
                    }

                    // Created Date filter
                    if (createdDateStart && new Date(rowCreatedDate) < new Date(createdDateStart)) {
                        showRow = false;
                    }
                    if (createdDateEnd && new Date(rowCreatedDate) > new Date(createdDateEnd)) {
                        showRow = false;
                    }

                    // Incident Date filter
                    if (incidentDateStart && new Date(rowIncidentDate) < new Date(
                            incidentDateStart)) {
                        showRow = false;
                    }
                    if (incidentDateEnd && new Date(rowIncidentDate) > new Date(incidentDateEnd)) {
                        showRow = false;
                    }

                    // Closure Date filter
                    if (closureDateStart && new Date(rowClosureDate) < new Date(closureDateStart)) {
                        showRow = false;
                    }
                    if (closureDateEnd && new Date(rowClosureDate) > new Date(closureDateEnd)) {
                        showRow = false;
                    }

                    // More date filters for incidentDate and closureDate as needed

                    // Show or hide row based on the filters
                    row.style.display = showRow ? '' : 'none';
                });
            });

            document.getElementById('clearFilters').addEventListener('click', function() {
                document.getElementById('createdDateStart').value = '';
                document.getElementById('createdDateEnd').value = '';
                document.getElementById('incidentDateStart').value = '';
                document.getElementById('incidentDateEnd').value = '';
                document.getElementById('closureDateStart').value = '';
                document.getElementById('closureDateEnd').value = '';

                // Clear checkboxes
                const checkboxes = document.querySelectorAll('#statusFilter input[type="checkbox"]');
                checkboxes.forEach(checkbox => checkbox.checked = false);
                equipmentFilter.removeActiveItems();
                airportFilter.removeActiveItems();
                natureAccidentFilter.removeActiveItems();
                natureSolutionFilter.removeActiveItems();
                // not working cause it selete some option i have so to keep them i seletct one by one to remove them
                // document.getElementById('filterForm').reset();

                document.getElementById('applyFilters').click();
            });
        });
    </script>

    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- for Export stuff logic --}}
    <script>
        // Use delegation to handle clicks on the "Export Selected" button
        $(document).on('click', '#exportSelected', function() {
            // Initialize an array to store the IDs
            var selectedIds = [];

            // Find all checked checkboxes in the table
            $('#ticketTable .select-row:checked').each(function() {
                // Get the row of the checked checkbox
                var row = $(this).closest('tr');

                // Extract the ticket ID from the row's data attribute
                var ticketId = row.data('ticketid');

                // Add the ticket ID to the array
                selectedIds.push(ticketId);
            });

            // Log the selected IDs to the console
          //  console.log('Selected Ticket IDs:', selectedIds);

            if (selectedIds.length > 0) {
                // Send the selected IDs to the backend via AJAX
                $.ajax({
                    url: '{{ route('export.tickets.list') }}',
                    method: 'POST',
                    data: {
                        ids: selectedIds,
                        _token: '{{ csrf_token() }}', // Laravel CSRF token
                    },
                    success: function(response) {
                        // Log success message and file URL
                      //  console.log(response.message);
                      //  console.log('Download URL:', response.fileUrl);

                        // Optionally, automatically download the file
                        window.location.href = response.fileUrl;

                        // Or show an alert to the user with the download link
                        showAlertS('Export successful! Download your file <a href="' + response
                            .fileUrl + '" target="_blank">here</a>.');
                    },
                    error: function(xhr) {
                        // Handle error
                        showAlertD('Export failed:', xhr.responseText);
                    }
                });
            } else {
                showAlertD('No tickets selected for export.');
            }
        });
    </script>



    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- for user data tables transfer --}}

    <script>
        $(document).ready(function(){
            $('#userTable').DataTable();
        });
        // When the 'Transfer Ticket' button is clicked, capture the ticket ID
        $(document).on('click', 'a[data-bs-target="#listUsersModalSelect"]', function(event) {
            // Prevent the default link action
            event.preventDefault();

            // Get the ticket ID from the data attribute
            var ticketId = $(this).data('ticketid');

            // Set the ticket ID in the hidden input field in the modal
            $('#ticketIdContainerModalTransfer').val(ticketId);
        });

        $(document).on('click', '#TransferSelectedTicketsTo', function(event) {
            // Prevent the default link action
            event.preventDefault();

            // Initialize an array to store the IDs
            var selectedIds = [];

            // Find all checked checkboxes in the table
            $('#ticketTable .select-row:checked').each(function() {
                // Get the row of the checked checkbox
                var row = $(this).closest('tr');

                // Extract the ticket ID from the row's data attribute
                var ticketId = row.data('ticketid');

                // Add the ticket ID to the array
                selectedIds.push(ticketId);
            });

            // Set the selected ticket IDs in the hidden input field in the modal
            $('#ticketIdContainerModalTransfer').val(selectedIds.join(','));
        });

        $(document).on('click', '.ticketTransforToMeAction', function(event) {
            event.preventDefault(); // Prevent the default action of the anchor tag
            var ticketId = $(this).data('ticket-id'); // Use jQuery to access data attribute

            $.ajax({
                url: '{{ route('ticket.transform.toMe') }}', // Adjust the URL to consolyour API endpoint for adding airports
                method: 'POST',
                data: {
                    ticketId: ticketId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Show alert with each piece of airport data
                    showAlertS('Ticket Assigned successfully!');

                    // -----------------------------------------
                    // only be commeted if i add every 100milisec to refrech the data of the tickets [after]
                    // -----------------------------------------
                    // Reload the page after a short delay (optional)
                    setTimeout(function() {
                        location.reload(); // Reloads the current page
                    }, 1000);
                  //  console.log(response);
                },
                error: function(xhr, status, error) {
                    // Handle error responses if needed
                    showAlertD('Failed to assign Ticket. Please try again.');
                }
            });
        });


        // When the 'Save Changes' button is clicked, log the selected data and close the modal
        $(document).on('click', '#saveChangesButton', function() {
            // Get the selected user ID
            var selectedUserId = $('input[name="userSelectTransfer"]:checked').val();

            // Get the ticket ID from the hidden input field
            var ticketId = $('#ticketIdContainerModalTransfer').val();

            // Check if the forced transfer checkbox is checked
            var isForced = $('#userSelectTransferForcedVal').is(':checked');

            // Log the selected user ID, ticket ID, and forced transfer status
            // console.log('Selected User ID:', selectedUserId);
            // console.log('Ticket ID:', ticketId);
            // console.log('Forced Transfer:', isForced);
            $.ajax({
                url: '{{ route('ticket.transform') }}', // Adjust the URL to your API endpoint for adding airports
                method: 'POST',
                data: {
                    UserId: selectedUserId,
                    ticketId: ticketId,
                    isForced: isForced,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#ticketIdContainerModalTransfer').val('');
                    // Show alert with each piece of airport data
                    showAlertS('Ticket Assigned successfully!');

                    // -----------------------------------------
                    // only be commeted if i add every 100milisec to refrech the data of the tickets [after]
                    // -----------------------------------------
                    // Reload the page after a short delay (optional)
                    // setTimeout(function() {
                    //     location.reload(); // Reloads the current page
                    // }, 1000);
                  //  console.log(response);
                },
                error: function(xhr, status, error) {
                    $('#ticketIdContainerModalTransfer').val('');
                    // Handle error responses if needed
                    showAlertD('Failed to assign Ticket. Please try again.');
                }
            });
            // Close the modal
            $('#listUsersModalSelect').modal('hide');
        });


        $(document).on('click', '.TransferTicektOption', function() {
            var ticketId = $(this).data('ticket-id');
            var status = $(this).data('statu');
            var tableId = $(this).closest('table').attr('id'); // Get the table ID

          //  console.log('Ticket ID:', ticketId, ' || Status:', status);

            $.ajax({
                url: '{{ route('ticket.transform.Respondes') }}', // Ensure this URL matches your route definition
                method: 'POST',
                data: {
                    Id: ticketId,
                    status: (status ? 1 : 0),
                    _token: '{{ csrf_token() }}' // Ensure CSRF token is included
                },
                success: function(response) {
                    // Show alert with each piece of airport data
                  //  console.log(response);
                    showAlertS('Ownership status updated successfully.');

                    // Remove the row from the specific table
                    $('#' + tableId + ' tr[data-ticketId="' + ticketId + '"]').remove();
                },
                error: function(xhr, status, error) {
                    // Handle error responses if needed
                    showAlertD('No ownership found with null status.');
                }
            });
        });
    </script>

    <script>
        function calculateTimeSpan(recoveryDate) {
            const now = new Date();
            const recovery = new Date(recoveryDate);

            // Add 24 hours to the recovery date
            recovery.setHours(recovery.getHours() + 24);

            // Calculate the time difference in milliseconds
            const diffMs = recovery - now;

            // Convert milliseconds to hours, minutes, and seconds
            const hours = Math.floor(diffMs / (1000 * 60 * 60));
            const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diffMs % (1000 * 60)) / 1000);

            return `${hours}h ${minutes}min ${seconds}s`;
        }

        function updateTableTimeSpans() {
            $('#ticketTablePendingCloture tbody tr').each(function() {
                const recoveryDate = $(this).find('td:nth-child(7)').text();
                const timeRemainingCell = $(this).find('.time-remaining');
                timeRemainingCell.text(calculateTimeSpan(recoveryDate));
            });
        }

        // Initial call to update the time spans
        updateTableTimeSpans();

        // Update the time spans every 5 seconds
        setInterval(updateTableTimeSpans, 5000);
    </script>

    {{-- ABCD cloture logic stuff --}}
    <script>
        // Attach click event handler to the button with class "ClotureTicketOption"
        $(document).on('click', '.ClotureTicketOption', function() {
            // Get the ticket ID from the data attribute
            var ticketId = $(this).data('ticket-id');

            // Show an alert with the ticket ID
            // alert('Ticket ID: ' + ticketId);

            $.ajax({
                url: '{{ route('ticket.cloture') }}', // Adjust the URL to your route for closing the ticket
                method: 'POST',
                data: {
                    ticketId: ticketId,
                    _token: '{{ csrf_token() }}' // CSRF token for security
                },
                success: function(response) {
                    // Handle the successful response
                    showAlertS('Ticket closed successfully!');
                    // Optionally show a success message or redirect
                    // Reload the page after a short delay (optional)
                    setTimeout(function() {
                        location.reload(); // Reloads the current page
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    showAlertD('Failed to close the ticket:', error);
                    // Optionally show an error message
                }
            });

        });
    </script>


    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- for Data Update RealTime --}}
    <script>
        // 123
        // Define the function to fetch tickets
        // Initialize an object to store Choices instances

        function fetchTickets() {
            $.ajax({
                url: '{{ route('ticket.fetch') }}', // The Laravel route URL
                method: 'GET',
                success: function(response) {
                    //  get all the data & update content
                    // ...
                    // console.log(response)
                    updateBadgeCounts(response);

                    updateSelectableElementsForm(response);
                    if (response.role <= 3) {
                        updateAdminListOfOptionsAerport(response);
                        updateAdminListOfOptionsEquipments(response);
                        updateAdminListOfOptionsProblems(response);
                        updateAdminListOfOptionsSolutions(response);
                    }
                    updateAdminListOfUsers(response);

                    updateTicketTable(response);
                    updateTicketTableTransfered(response);
                    updatePendingClotureTable(response);
                },
                error: function(error) {
                    console.error('Error fetching tickets:', error);
                }
            });
        }

        function updateBadgeCounts(response) {
            const transferredCount = response.transeferTicketCount;
            if (transferredCount > 0) {
                $('#transferred-tickets-tab-Badge').text(transferredCount).removeClass('d-none');
            } else {
                $('#transferred-tickets-tab-Badge').addClass('d-none');
            }

            // Calculate and update the count of pending closed tickets
            let pendingClotureCount = response.TicketsPendingClotureCount;

            if (pendingClotureCount > 0) {
                $('#cloture-tickets-tab-Badge').text(pendingClotureCount).removeClass('d-none');
            } else {
                $('#cloture-tickets-tab-Badge').addClass('d-none');
            }
        }

        function updateSelectableElementsForm(response) {
            // const airportSelect = document.querySelector('#airport');

            // // Clear existing options
            // airportSelect.innerHTML = '';

            // // Add new options
            // response.airports.forEach(item => {
            //     const option = document.createElement('option');
            //     option.value = item.id;
            //     option.textContent = item.code;
            //     airportSelect.appendChild(option);
            // });

            // // Optionally, reinitialize your select2 or Choices.js here if needed
            // // For Select2:
            // $(airportSelect).trigger('change');
        }

        function updateAdminListOfOptionsAerport(response) {
            // Get the list of airports from the response
            const airports = response.airports;

            // Initialize or get the DataTable instance
            const table = $('#airportTable').DataTable();

            // Create a Set of IDs from the response for quick lookup
            const responseIds = new Set(airports.map(airport => airport.id));

            // Track which rows should remain in the table
            const currentRows = [];

            // Update existing rows and remove rows not in the response
            table.rows().every(function() {
                const row = this.node();
                const rowId = parseInt($(row).attr('data-id'));

                if (responseIds.has(rowId)) {
                    // Find the airport data from the response
                    const airportData = airports.find(airport => airport.id === rowId);

                    // Update the row with the new data
                    $(row).find('td').eq(0).text(airportData.code);
                    $(row).find('td').eq(1).text(airportData.location);
                    $(row).find('td').eq(2).text(airportData.address);

                    // Track this row as valid
                    currentRows.push(rowId);
                } else {
                    // Remove the row if it's not in the response
                    table.row(row).remove().draw();
                }
            });

            // Add new rows for airports not currently in the table
            airports.forEach(airport => {
                if (!currentRows.includes(airport.id)) {
                    // Create a new row
                    table.row.add([
                        airport.code,
                        airport.location,
                        airport.address,
                        `<button type="button" class="btn btn-primary editBtn m-1 mt-0 mb-0" data-bs-toggle="modal" data-bs-target="#editAirportModal"></button>
                        <button type="button" class="btn btn-danger deleteBtn m-0 mt-0 mb-0"></button>`
                    ]).draw(false).node().setAttribute('data-id', airport.id).setAttribute('data-type',
                        'airport');
                }
            });

            // Draw the table to reflect changes
            table.draw();
        }

        function updateAdminListOfOptionsEquipments(response) {
            // Get the list of equipments from the response
            const equipments = response.equipements;

            // Initialize or get the DataTable instance
            const table = $('#equipmentTable').DataTable();

            // Create a Set of IDs from the response for quick lookup
            const responseIds = new Set(equipments.map(equipment => equipment.id));

            // Track which rows should remain in the table
            const currentRows = [];

            // Update existing rows and remove rows not in the response
            table.rows().every(function() {
                const row = this.node();
                const rowId = parseInt($(row).attr('data-id'));

                if (responseIds.has(rowId)) {
                    // Find the equipment data from the response
                    const equipmentData = equipments.find(equipment => equipment.id === rowId);

                    // Update the row with the new data
                    $(row).find('td').eq(0).text(equipmentData.equipement);

                    // Track this row as valid
                    currentRows.push(rowId);
                } else {
                    // Remove the row if it's not in the response
                    table.row(row).remove().draw();
                }
            });

            // Add new rows for equipments not currently in the table
            equipments.forEach(equipment => {
                if (!currentRows.includes(equipment.id)) {
                    // Create a new row
                    table.row.add([
                        equipment.equipement,
                        `<button type="button" class="btn btn-primary editBtn m-1 mt-0 mb-0" data-bs-toggle="modal" data-bs-target="#editEquipmentModal"></button>
                        <button type="button" class="btn btn-danger deleteBtn m-0 mt-0 mb-0"></button>`
                    ]).draw(false).node().setAttribute('data-id', equipment.id).setAttribute('data-type',
                        'equipment');
                }
            });

            // Draw the table to reflect changes
            table.draw();
        }


        function updateAdminListOfOptionsProblems(response) {
            const problems = response.problems;
            const table = $('#problemTypeTable').DataTable();
            const responseIds = new Set(problems.map(problem => problem.id));
            const currentRows = [];

            table.rows().every(function() {
                const row = this.node();
                const rowId = parseInt($(row).attr('data-id'));

                if (responseIds.has(rowId)) {
                    const problemData = problems.find(problem => problem.id === rowId);
                    $(row).find('td').eq(0).find('p').text(problemData.val);
                    $(row).find('td').eq(1).find('p').text(problemData.desc);
                    currentRows.push(rowId);
                } else {
                    table.row(row).remove().draw();
                }
            });

            problems.forEach(problem => {
                if (!currentRows.includes(problem.id)) {
                    // Add the row and get the node element
                    const newRow = table.row.add([
                        `<p class="d-inline-block text-truncate" style="max-width: 250px;">${problem.val}</p>`,
                        `<p class="d-inline-block text-truncate" style="max-width: 250px;">${problem.desc}</p>`,
                        `<button type="button" class="btn btn-primary editBtn m-1 mt-0 mb-0" data-bs-toggle="modal" data-bs-target="#editProblemModal"></button>
                        <button type="button" class="btn btn-danger deleteBtn m-0 mt-0 mb-0" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Tooltip on top"></button>`
                    ]).draw(false).node(); // Get the node before calling draw(false)

                    // Set the attributes on the node
                    newRow.setAttribute('data-id', problem.id);
                    newRow.setAttribute('data-type', "problem");
                }
            });

            table.draw();
        }

        function updateAdminListOfOptionsSolutions(response) {
            const solutions = response.solutions;
            const table = $('#solutionTypeTable').DataTable();
            const responseIds = new Set(solutions.map(solution => solution.id));
            const currentRows = [];

            table.rows().every(function() {
                const row = this.node();
                const rowId = parseInt($(row).attr('data-id'));

                if (responseIds.has(rowId)) {
                    const solutionData = solutions.find(solution => solution.id === rowId);
                    $(row).find('td').eq(0).find('p').text(solutionData.val);
                    $(row).find('td').eq(1).find('p').text(solutionData.desc);
                    currentRows.push(rowId);
                } else {
                    table.row(row).remove().draw();
                }
            });

            solutions.forEach(solution => {
                if (!currentRows.includes(solution.id)) {
                    // Add the row and get the node element
                    const newRow = table.row.add([
                        `<p class="d-inline-block text-truncate" style="max-width: 250px;">${solution.val}</p>`,
                        `<p class="d-inline-block text-truncate" style="max-width: 250px;">${solution.desc}</p>`,
                        `<button type="button" class="btn btn-primary editBtn m-1 mt-0 mb-0" data-bs-toggle="modal" data-bs-target="#editSolutionModal"></button>
                        <button type="button" class="btn btn-danger deleteBtn m-0 mt-0 mb-0" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete"></button>`
                    ]).draw(false).node(); // Get the node before calling draw(false)

                    // Set the attributes on the node
                    newRow.setAttribute('data-id', solution.id);
                    newRow.setAttribute('data-type', "solution");
                }
            });


            table.draw();
        }

        function formatDate(inputDate) {
            // Remove the fractional seconds and 'Z' if present
            const cleanedInputDate = inputDate.split('.')[0].replace('Z', '');

            // Parse the date string without time zone adjustments
            const date = new Date(cleanedInputDate);

            // Extract year, month, day, hours, minutes, and seconds
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');

            // Construct and return formatted date string
            return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        }

        function updateAdminListOfUsers(response) {
            var users = response.users;
            var table = $('#userTable').DataTable();
            var responseIds = new Set(users.map(user => user.id));
            var currentRows = [];

            // Iterate over the existing rows in the table
            table.rows().every(function() {
                var row = this.node();
                var rowId = parseInt($(row).attr('data-userid'));

                // If the user exists in the response, update the row
                if (responseIds.has(rowId)) {
                    var userData = users.find(user => user.id === rowId);
                    $(row).find('td').eq(2).text(`${userData.Fname} ${userData.Lname}`);
                    $(row).find('td').eq(3).find('span').text(userData.role).removeClass().addClass(
                        'badge rounded-pill text-bg-primary');
                    $(row).find('td').eq(4).text(userData.email);

                    // Update the status based on latestLoginLog
                    let statusBadgeClass = 'text-bg-danger';
                    let statusText = 'Offline';
                    if (userData.latest_login_log && userData.latest_login_log.isLogged === 1) {
                        statusBadgeClass = 'text-bg-success';
                        statusText = 'Online';
                    }
                    $(row).find('td').eq(5).find('span')
                        .removeClass()
                        .addClass(`badge rounded-pill ${statusBadgeClass}`)
                        .text(statusText);

                    currentRows.push(rowId);
                } else {
                    // If the user does not exist in the response, remove the row
                    table.row(row).remove().draw();
                }
            });

            // Add new users that aren't already in the table
            users.forEach(user => {
                if (!currentRows.includes(user.id)) {
                    var newRow = table.row.add([
                        `<input type="radio" name="userSelectTransfer" value="${user.id}">`,
                        `<img src="${user.imgUrl}" alt="Profile Image" class="profile-img">`,
                        `${user.Fname} ${user.Lname}`,
                        `<span class="badge rounded-pill text-bg-primary">${user.role}</span>`,
                        `${user.email}`,
                        `<span class="badge rounded-pill ${user.latest_login_log?.isLogged === 1 ? 'text-bg-success' : 'text-bg-danger'}">${user.latest_login_log?.isLogged === 1 ? 'Online' : 'Offline'}</span>`
                    ]).draw(false).node(); // Get the node before calling draw(false)

                    // Set the attributes on the node
                    newRow.setAttribute('data-userid', user.id);
                }
            });

            // Redraw the table to reflect any changes
            table.draw();
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

        const BASE_URL = '{{ asset('/') }}';

        function updateTicketTable(response) {
            var ticketsTmp = response.JSONtickets;
            var table = $('#ticketTable').DataTable();
            var responseIds = new Set(ticketsTmp.map(ticket => ticket.id));
            var currentRows = [];
            var tickets = [];

            if (response.role > 3) {
                tickets = ticketsTmp.filter(ticket =>
                    ticket.current_owner_relation.reseverID === response.userID
                );
            } else {
                tickets = ticketsTmp;
            }
            // console.log(tickets);

            // Iterate over existing rows to update or remove them
            table.rows().every(function() {
                var row = this.node();
                var rowId = parseInt($(row).attr('data-ticketId'), 10);

                if (responseIds.has(rowId)) {
                    var ticketData = tickets.find(ticket => ticket.id === rowId);


                  //  console.log(ticketData);

                    // Ensure ticketData is defined
                    if (ticketData) {
                        // Update existing rows with new data
                        $(row).find('td').eq(1).text(ticketData.id);
                        $(row).find('td').eq(2).find('span').text(ticketData.title);
                        $(row).find('td').eq(3).find('span').text(ticketData.desc);
                        $(row).find('td').eq(4).find('.user-info span').text(ticketData.current_owner_relation
                            .reserver.Fname || 'N/A');
                        $(row).find('td').eq(5).find('p').text(getStatus(ticketData.status));
                        $(row).find('td').eq(6).text(ticketData.aerport.code || 'N/A');
                        $(row).find('td').eq(7).text(formatDate(ticketData.created_at));
                        $(row).find('td').eq(8).text(ticketData.DateIncident);

                        $(row).attr('data-status', getStatusDesign(ticketData.status));
                        $(row).attr('data-status-org', ticketData.status);
                        $(row).attr('data-aerport', ticketData.aerport.id);
                        $(row).attr('data-equipement', ticketData.latest_analyse_log ? ticketData.latest_analyse_log
                            .equipementID : '');
                        $(row).attr('data-nIncident', ticketData.latest_analyse_log ? ticketData.latest_analyse_log
                            .naruteIncidentID : '');
                        $(row).attr('data-nSolution', ticketData.latest_recovery_log ? ticketData.latest_recovery_log
                            .naruteSolutionID : '');
                        $(row).attr('data-created_at', ticketData.created_at);
                        $(row).attr('data-incident_date', ticketData.DateIncident);
                        $(row).attr('data-cloture_date', ticketData.DateCloture);

                        currentRows.push(rowId);
                    } else {
                        // Remove rows not in the new data set
                        table.row(row).remove().draw();
                    }
                } else {
                    // Remove rows not in the new data set
                    table.row(row).remove().draw();
                }
            });

            // Add new rows
            tickets.forEach(ticket => {
                if (!currentRows.includes(ticket.id)) {
                    // Determine dropdown options based on role
                    var dropdownOptions =
                        `
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#listUsersModalSelect" data-ticketId="${ticket.id}">Transfer Ticket</a></li>`;

                    if (response.role <= 3) {
                        dropdownOptions += `
                        <li><a class="dropdown-item ticketTransforToMeAction" href="#" data-ticket-id="${ticket.id}">Assign to Myself</a></li>
                        <li><a class="dropdown-item" href="{{route('home')}}/ticket/Rapport/${ticket.id}">Generate Rapport</a></li>`;
                    }

                    const newRow = table.row.add([
                        `<input type="checkbox" class="select-row">`,
                        ticket.id,
                        `<span class="d-inline-block text-truncate" style="max-width: 90px;">${ticket.title}</span>`,
                        `<span class="d-inline-block text-truncate" style="max-width: 150px;">${ticket.desc}</span>`,
                        `<div class="user-info">
                            <img src="${BASE_URL + ticket.current_owner_relation.reserver.imgUrl}" alt="${ticket.current_owner_relation.reserver.Fname || 'No Name'}" class="profile-picture">
                            <span>${ticket.current_owner_relation.reserver.Fname || 'N/A'}</span>
                        </div>`,
                        `<p class="badge rounded-pill status-circle">${getStatusDesign(ticket.status)}</p>`,
                        ticket.aerport.code || 'N/A',
                        formatDate(ticket.created_at),
                        formatDate(ticket.DateIncident),
                        `<div class="d-flex justify-content-center">
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle dropdown-btn p-0 m-0 d-flex justify-content-center align-items-center" data-toggle="dropdown" id="dropdownMenuButton" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class='bx bx-sm bx-dots-horizontal-rounded p-0 m-0'></i>
                                </button>
                                <ul class="dropdown-menu">
                                    ${dropdownOptions}
                                </ul>
                            </div>
                        </div>`
                    ]).draw(false).node();

                    // Set data attributes for the new row
                    $(newRow).attr('data-ticketId', ticket.id);
                    $(newRow).attr('data-status', getStatusDesign(ticket.status));
                    $(newRow).attr('data-status-org', ticket.status);
                    $(newRow).attr('data-aerport', ticket.aerport.id);
                    $(newRow).attr('data-equipement', ticket.latest_analyse_log ? ticket.latest_analyse_log
                        .equipementID : '');
                    $(newRow).attr('data-nIncident', ticket.latest_analyse_log ? ticket.latest_analyse_log
                        .naruteIncidentID : '');
                    $(newRow).attr('data-nSolution', ticket.latest_recovery_log ? ticket.latest_recovery_log
                        .naruteSolutionID : '');
                    $(newRow).attr('data-created_at', ticket.created_at);
                    $(newRow).attr('data-incident_date', ticket.DateIncident);
                    $(newRow).attr('data-cloture_date', ticket.DateCloture);

                    // Add the "clickable-row" class to the new row
                    $(newRow).addClass('clickable-row');
                }
            });

            table.draw();
        }

        function updateTicketTableTransfered(response) {
            var ticketsTmp = response.JSONtranseferTicket;
            var table = $('#ticketTableTransfered').DataTable();
            var responseIds = new Set(ticketsTmp.map(ticket => ticket.id));
            var currentRows = [];
            var tickets = [];

            // Filter tickets based on role
            if (response.role > 3) {
                tickets = ticketsTmp.filter(ticket => {
                    // Check if the ticket has the latest null ownership
                    const hasLatestNullStatusOwnership = ticket.latestNullStatusOwnership;

                    // If there is a latest null status ownership
                    if (hasLatestNullStatusOwnership) {
                        // Get the latest null status ownership details
                        const latestNullStatusOwnershipDetails = ticket.latestNullStatusOwnershipGet;

                        // Check if the reservID matches the current userID
                        return latestNullStatusOwnershipDetails.reseverID === response.userID;
                    }

                    // If no latest null status ownership, filter out the ticket
                    return false;
                });
            } else {
                tickets = [];
            }

            // console.log(tickets);

            // Iterate over existing rows to update or remove them
            table.rows().every(function() {
                var row = this.node();
                var rowId = parseInt($(row).attr('data-ticketId'), 10);

                if (responseIds.has(rowId)) {
                    var ticketData = tickets.find(ticket => ticket.id === rowId);

                    // Ensure ticketData is defined
                    if (ticketData) {
                        // Update existing rows with new data
                        $(row).find('td').eq(1).text(ticketData.id);
                        $(row).find('td').eq(2).find('span').text(ticketData.title);
                        $(row).find('td').eq(3).find('span').text(ticketData.desc);

                        // Check if current owner is present
                        if (ticketData.current_owner_relation && ticketData.current_owner_relation.reserver) {
                            $(row).find('td').eq(4).html(`
                                <div class="user-info">
                                    <img src="${BASE_URL + ticketData.current_owner_relation.reserver.imgUrl}"
                                        alt="${ticketData.current_owner_relation.reserver.Fname || 'No Name'}"
                                        class="profile-picture">
                                    <span>${ticketData.current_owner_relation.reserver.Fname || 'N/A'}</span>
                                </div>
                            `);
                        } else {
                            $(row).find('td').eq(4).html(`
                                <div class="user-info">
                                    <span>No Owner Assigned</span>
                                </div>
                            `);
                        }

                        $(row).find('td').eq(5).find('p').text(getStatus(ticketData.status));
                        $(row).find('td').eq(6).text(ticketData.aerport.code || 'N/A');
                        $(row).find('td').eq(7).text(formatDate(ticketData.created_at));
                        $(row).find('td').eq(8).text((ticketData.DateIncident));

                        $(row).attr('data-status', getStatusDesign(ticketData.status));
                        $(row).attr('data-status-org', ticketData.status);
                        $(row).attr('data-aerport', ticketData.aerport.id);
                        $(row).attr('data-equipement', ticketData.latest_analyse_log ? ticketData.latest_analyse_log.equipementID : '');
                        $(row).attr('data-nIncident', ticketData.latest_analyse_log ? ticketData.latest_analyse_log.naruteIncidentID : '');
                        $(row).attr('data-nSolution', ticketData.latest_recovery_log ? ticketData.latest_recovery_log.naruteSolutionID : '');
                        $(row).attr('data-created_at', ticketData.created_at);
                        $(row).attr('data-incident_date', ticketData.DateIncident);
                        $(row).attr('data-cloture_date', ticketData.DateCloture);

                        currentRows.push(rowId);
                    } else {
                        // Remove rows not in the new data set
                        table.row(row).remove().draw();
                    }
                } else {
                    // Remove rows not in the new data set
                    table.row(row).remove().draw();
                }
            });

            // Add new rows
            tickets.forEach(ticket => {
                if (!currentRows.includes(ticket.id)) {
                    // Determine dropdown options based on role
                    var buttonOptions =
                        `
                        <button class="btn btn-success p-1 m-1 TransferTicektOption"
                            data-ticket-id="${ticket.id}"
                            data-statu="true"
                            style="width: 38px; height: 38px;">
                            <i class='bx bx-sm bx-check'></i>
                        </button>
                        <button class="btn btn-danger p-1 m-1 TransferTicektOption"
                            data-ticket-id="${ticket.id}"
                            data-statu="false"
                            style="width: 38px; height: 38px;">
                            <i class='bx bx-sm bx-x'></i>
                        </button>`;

                    const newRow = table.row.add([
                        `<input type="checkbox" class="select-row">`,
                        ticket.id,
                        `<span class="d-inline-block text-truncate" style="max-width: 90px;">${ticket.title}</span>`,
                        `<span class="d-inline-block text-truncate" style="max-width: 150px;">${ticket.desc}</span>`,
                        `<div class="user-info">
                            <img src="${BASE_URL + (ticket.current_owner_relation?.reserver?.imgUrl || 'default.jpg')}"
                                alt="${ticket.current_owner_relation?.reserver?.Fname || 'No Name'}"
                                class="profile-picture">
                            <span>${ticket.current_owner_relation?.reserver?.Fname || 'N/A'}</span>
                        </div>`,
                        `<p class="badge rounded-pill status-circle">${getStatusDesign(ticket.status)}</p>`,
                        ticket.aerport.code || 'N/A',
                        formatDate(ticket.created_at),
                        formatDate(ticket.DateIncident),
                        `<div class="d-flex justify-content-center align-items-center">
                            ${buttonOptions}
                        </div>`
                    ]).draw(false).node();

                    // Set data attributes for the new row
                    $(newRow).attr('data-ticketId', ticket.id);
                    $(newRow).attr('data-status', getStatusDesign(ticket.status));
                    $(newRow).attr('data-status-org', ticket.status);
                    $(newRow).attr('data-aerport', ticket.aerport.id);
                    $(newRow).attr('data-equipement', ticket.latest_analyse_log ? ticket.latest_analyse_log.equipementID : '');
                    $(newRow).attr('data-nIncident', ticket.latest_analyse_log ? ticket.latest_analyse_log.naruteIncidentID : '');
                    $(newRow).attr('data-nSolution', ticket.latest_recovery_log ? ticket.latest_recovery_log.naruteSolutionID : '');
                    $(newRow).attr('data-created_at', ticket.created_at);
                    $(newRow).attr('data-incident_date', ticket.DateIncident);
                    $(newRow).attr('data-cloture_date', ticket.DateCloture);

                    // Add the "clickable-row" class to the new row
                    $(newRow).addClass('clickable-row');
                }
            });

            table.draw();
        }

        function updatePendingClotureTable(response) {
            var ticketsTmp = response.JSONTicketsPendingCloture;
            var table = $('#ticketTablePendingCloture').DataTable();
            var responseIds = new Set(ticketsTmp.map(ticket => ticket.id));
            var currentRows = [];
            var tickets = [];

            if (response.role > 3) {
                tickets = ticketsTmp.filter(ticket =>
                    ticket.current_owner_relation.reseverID === response.userID
                );
            } else {
                tickets = ticketsTmp;
            }

            // Iterate over existing rows to update or remove them
            table.rows().every(function() {
                var row = this.node();
                var rowId = parseInt($(row).attr('data-ticketId'), 10);

                if (responseIds.has(rowId)) {
                    var ticketData = tickets.find(ticket => ticket.id === rowId);

                    // Ensure ticketData is defined
                    if (ticketData) {
                        // Update existing rows with new data
                        $(row).find('td').eq(0).text(ticketData.id);
                        $(row).find('td').eq(1).find('span').text(ticketData.title);
                        $(row).find('td').eq(2).find('span').text(ticketData.desc);
                        $(row).find('td').eq(3).find('.user-info span').text(ticketData.current_owner_relation.reserver.Fname);
                        $(row).find('td').eq(4).text(ticketData.aerport.code);
                        $(row).find('td').eq(5).text(ticketData.DateIncident);
                        $(row).find('td').eq(6).text(ticketData.latest_recovery_log.dateRecovery || 'N/A');

                        $(row).attr('data-status', getStatusDesign(ticketData.status));
                        $(row).attr('data-status-org', ticketData.status);
                        $(row).attr('data-aerport', ticketData.aerport.id);
                        $(row).attr('data-equipement', ticketData.latest_analyse_log ? ticketData.latest_analyse_log.equipementID : '');
                        $(row).attr('data-nIncident', ticketData.latest_analyse_log ? ticketData.latest_analyse_log.naruteIncidentID : '');
                        $(row).attr('data-nSolution', ticketData.latest_recovery_log ? ticketData.latest_recovery_log.naruteSolutionID : '');
                        $(row).attr('data-created_at', ticketData.created_at);
                        $(row).attr('data-incident_date', ticketData.DateIncident);
                        $(row).attr('data-cloture_date', ticketData.DateCloture);

                        currentRows.push(rowId);
                    } else {
                        // Remove rows not in the new data set
                        table.row(row).remove().draw();
                    }
                } else {
                    // Remove rows not in the new data set
                    table.row(row).remove().draw();
                }
            });

            // // Add new rows
            tickets.forEach(ticket => {
                if (!currentRows.includes(ticket.id)) {
                    const newRow = table.row.add([
                        ticket.id,
                        `<span class="d-inline-block text-truncate" style="max-width: 90px;">${ticket.title}</span>`,
                        `<span class="d-inline-block text-truncate" style="max-width: 150px;">${ticket.desc}</span>`,
                        `<div class="user-info">
                            <img src="${BASE_URL + ticket.current_owner_relation.reserver.imgUrl}" alt="${ticket.current_owner_relation.reserver.Fname || 'No Name'}" class="profile-picture">
                            <span>${ticket.current_owner_relation.reserver.Fname || 'N/A'}</span>
                        </div>`,
                        ticket.aerport.code || 'N/A',
                        ticket.DateIncident || 'N/A',
                        ticket.latest_recovery_log ? ticket.latest_recovery_log.dateRecovery : 'N/A',
                        `<td class="time-remaining"></td>`, // Time remaining logic needs to be implemented separately
                        `<div class="btn-container d-flex justify-content-center align-items-center">
                            <button class="btn btn-danger p-1 m-1 ClotureTicketOption" data-ticket-id="${ticket.id}" style="width: 128px; height: 35px;">
                                <p>Cloture Ticket</p>
                            </button>
                        </div>`
                    ]).draw(false).node();

                    // Set data attributes for the new row
                    $(newRow).attr('data-ticketId', ticket.id);
                    $(newRow).attr('data-status', getStatusDesign(ticket.status));
                    $(newRow).attr('data-status-org', ticket.status);
                    $(newRow).attr('data-aerport', ticket.aerport.id);
                    $(newRow).attr('data-equipement', ticket.latest_analyse_log ? ticket.latest_analyse_log.equipementID : '');
                    $(newRow).attr('data-nIncident', ticket.latest_analyse_log ? ticket.latest_analyse_log.naruteIncidentID : '');
                    $(newRow).attr('data-nSolution', ticket.latest_recovery_log ? ticket.latest_recovery_log.naruteSolutionID : '');
                    $(newRow).attr('data-created_at', ticket.created_at);
                    $(newRow).attr('data-incident_date', ticket.DateIncident);
                    $(newRow).attr('data-cloture_date', ticket.DateCloture);

                    // Add the "clickable-row" class to the new row
                    $(newRow).addClass('clickable-row');
                }
            });

            table.draw();
            updateTableTimeSpans();
        }


        // Run the fetchTickets function every 3 seconds
        setInterval(fetchTickets, 3000);
    </script>
@endsection
