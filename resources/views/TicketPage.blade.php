@extends('layouts')
@section('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">

    <style>
        .btn-circle {
            width: 27px;
            height: 27px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        /* Custom CSS */
        body {
            background-color: #f0f4fa;
        }

        .card-custom {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header-custom {
            background-color: #f8f9fa;
            border-bottom: none;
            border-radius: 15px 15px 0 0;
            padding: 15px;
        }

        .nav-tabs-custom .nav-link.active {
            background-color: #e9ecef;
            border-color: #dee2e6 #dee2e6 #fff;
            border-radius: 10px 10px 0 0;
        }

        .validation-comments {
            margin-top: 30px;
        }

        .ticket-list {
            margin-top: 30px;
        }

        /* Remove default Bootstrap caret */
        .dropdown-btn::after {
            display: none;
            width: 24px;
            height: 24px;
        }

        /* Remove default padding and margin for tab-content */
        .tab-content {
            margin-top: 0;
            padding-top: 0;
        }

        /* Add padding to individual tab-pane */
        .tab-pane {
            padding: 15px 0;
        }

        /* Specific adjustments for the active tab-pane */
        .tab-pane.show.active {
            margin-top: 0;
            padding-top: 0;
        }

        .makeSpacingDesapear {
            height: 0px !important;
            margin: 0px;
            padding: 0px;
        }

        .profile-img {
            width: 43px;
            height: 43px;
            border-radius: 50%;
            margin-left: 10px;
            margin-right: 10px;
        }
    </style>
    <style>
        /* Background colors based on data-status attribute */
        .status-circle[data-status="open"] {
            background-color: #007bff;
            /* Blue */
        }

        .status-circle[data-status="resolved"] {
            background-color: #ffc107;
            /* Yellow */
        }

        .status-circle[data-status="closed"] {
            background-color: #28a745;
            /* Green */
        }

        .status-circle[data-status="validated"] {
            background-color: #989a9c;
            /* Gray */
        }

        .status-circle[data-status="nvalidated"] {
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
    <style>
        .comment {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eaeaea;
        }
        .comment img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 15px;
        }
        .comment-body {
            flex-grow: 1;
        }
        .comment-name {
            font-weight: bold;
        }
        .comment-date {
            font-size: 0.85rem;
            color: #888;
        }
        .comment-text {
            margin-top: 5px;
            color: #333;
        }
    </style>
@endsection

@section('body')
    <div class="container-fluid mt-4 px-5">
        <div class="card card-custom">
            <div class="card-header card-header-custom mb-2 pb-0">
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <h4 class="">Ticket</h4>
                    <div class="d-flex " id="ticketMainInfoOptionHeader">
                        <div class="m-2 mt-0 mb-0">
                            <h5>
                                <span class="badge rounded-pill status-circle"
                                    data-status="{{ $ticket->getStatusDesign() }}"> {{ $ticket->getStatus() }} </span>
                            </h5>
                        </div>
                        <div class="dropdown
                        ItemShouldDesapairOwnership @if (!$CanEdit && auth()->user()->role() > 3)
                            d-none disabled
                        @endif
                        ">
                            <button
                                class="btn btn-secondary dropdown-toggle dropdown-btn p-0 m-0 d-flex justify-content-center align-items-center"
                                data-toggle="dropdown" id="dropdownMenuButton" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class='bx bx-sm bx-dots-horizontal-rounded p-0 m-0'></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item @if ($ticket->status >= 2) disabled @endif"
                                        href="" data-bs-toggle="modal" data-bs-target="#listUsersModalSelect"
                                        data-ticketId={{$ticket->id}}>Transfer
                                        Ticket</a></li>
                                @if (auth()->user()->role() <= 3)
                                    <li><a class="dropdown-item
                                        @if ($ticket->status >= 2) disabled @endif
                                        ticketTransforToMeAction"
                                            href="#" data-ticket-id="{{$ticket->id}}">
                                            Assign to Myself
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item " href="#" data-bs-toggle="modal" data-bs-target="#modalAddOperatorTicket">
                                            ISP Information[ADD]
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="m-2 p-0">
                                    </li>
                                    <li><a class="dropdown-item
                                        @if ($ticket->status < 2) disabled @endif
                                         "
                                            href="{{ route('ticket.pdf.rapport', ['id' => $ticket->id]) }}">Generate
                                            Rapport</a></li>
                                    <li><a class="dropdown-item
                                                            @if ($ticket->TicketParent != null) disabled @endif"
                                            href="#" data-bs-toggle="modal" data-bs-target="#setParentModal"
                                            data-ticket-id="{{ $ticket->id }}">Set a Parent</a></li>
                                @endif
                            </ul>
                                <!-- Modal Add Operato information-->
                                <div class="modal fade" id="modalAddOperatorTicket" tabindex="-1" aria-labelledby="modalAddOperatorTicketLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="modalAddOperatorTicketLabel">ISP Information</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div>
                                                <form id="operatorForm">
                                                    <div class="mb-3">
                                                        <label for="nticket" class="form-label">NTicket *</label>
                                                        <input type="text" class="form-control" id="operator_nticket"
                                                            placeholder="Enter NTicket"
                                                            value="@if($ticket->hasAnalyseLogs() && $ticket->latestAnalyseLog->operatoreID != null){{ $ticket->latestAnalyseLog->getOperatore->NTicket }}@endif">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="operatorName" class="form-label">Name *</label>
                                                        <input type="text" class="form-control" id="operator_name"
                                                            placeholder="Enter Name"
                                                            value="@if($ticket->hasAnalyseLogs() && $ticket->latestAnalyseLog->operatoreID != null){{ $ticket->latestAnalyseLog->getOperatore->name }}@endif">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="operatorEmail" class="form-label">Email</label>
                                                        <input type="email" class="form-control" id="operator_mail"
                                                            placeholder="Enter Email"
                                                            value="@if($ticket->hasAnalyseLogs() && $ticket->latestAnalyseLog->operatoreID != null){{ $ticket->latestAnalyseLog->getOperatore->mail }}@endif">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="operatorPhone" class="form-label">Phone</label>
                                                        <input type="tel" class="form-control" id="operator_tell"
                                                            placeholder="Enter Phone Number"
                                                            value="@if($ticket->hasAnalyseLogs() && $ticket->latestAnalyseLog->operatoreID != null){{ $ticket->latestAnalyseLog->getOperatore->tell }}@endif">
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" id="RemoveOperatoreFromTheTicket">Remove ISP</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary" id="saveOperatoreTicket">Save changes</button>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                        </div>
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
                                    <div class="modal-body">
                                        <!-- Hidden input for ticket ID -->
                                        <input type="text" id="ticketIdContainerModalTransfer" hidden>

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
                                                        <td><img src="{{ asset($user->imgUrl) }}" alt="Profile Image"
                                                                class="profile-img"></td>
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
                                                <label class="form-check-label" for="userSelectTransferForcedVal">Force
                                                    Transfer</label>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary" id="saveChangesButton">Transfer
                                            Ticket</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Set Parent Modal -->
                        <div class="modal fade" id="setParentModal" tabindex="-1" aria-labelledby="setParentModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="setParentModalLabel">Select Parent Ticket</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <table id="ticketTable" class="table" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Select</th>
                                                    <th>Ticket ID</th>
                                                    <th>Title</th>
                                                    <th>Status</th>
                                                    <th>Created At</th>
                                                    <th>Updated At</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($ticketsOptions as $item)
                                                    <tr data-ticketid="{{ $item->id }}">
                                                        <td><input type="radio" name="ticketSelectParent"
                                                                value="{{ $item->id }}"></td>
                                                        <td>{{ $item->id }}</td>
                                                        <td>{{ $item->title }}</td>
                                                        <td>
                                                            <span class="badge rounded-pill status-circle"
                                                                data-status="{{ $item->getStatusDesign() }}">
                                                                {{ $item->getStatus() }} </span>
                                                        </td>
                                                        <td>{{ $item->created_at }}</td>
                                                        <td>{{ $item->updated_at }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" id="applyParentChange">Apply
                                            Changes</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="card-body mt-0 pt-0">
                <ul class="nav nav-tabs nav-tabs-custom" id="ticketTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab"
                            data-bs-target="#info, #info-body" type="button" role="tab" aria-controls="info"
                            aria-selected="true">Info</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="analyse-tab" data-bs-toggle="tab"
                            data-bs-target="#analyse, #analyse-body" type="button" role="tab"
                            aria-controls="analyse" aria-selected="false">Analyse</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link
                        @if (!($ticket->hasAnalyseLogs() && $ticket->latestAnalyseLog->NSMStatu!=null && $ticket->latestAnalyseLog->equipementID!=null && $ticket->latestAnalyseLog->NSMStatu!=null))
                        disabled
                        @endif

                        " id="recovery-tab" data-bs-toggle="tab"
                            data-bs-target="#recovery, #recovery-body" type="button" role="tab"
                            aria-controls="recovery" aria-selected="false">Recovery</button>
                    </li>
                </ul>
                <div class="tab-content mt-3" id="ticketTabContent">
                    <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                        <div class="container">
                            <div class="row g-3">
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-12">
                                            <p><strong>N Ticket:</strong> {{ $ticket->id }}</p>
                                        </div>
                                        <div class="col-12">
                                            <p><strong>Title:</strong> {{ $ticket->title }}</p>
                                        </div>
                                        <div class="col-12">
                                            <p><strong>Support Notification:</strong> {{ $ticket->NaturNotification }}</p>
                                        </div>
                                        <div class="col-12">
                                            <p><strong>Contact Reclamation:</strong> {{ $ticket->contactReclamation }} </p>
                                        </div>
                                        <div class="col-12">
                                            <p><strong>Owner:</strong>
                                            <div class="d-flex ">
                                                <img src="{{ asset($ticket->currentOwnerRelation->reserver->imgUrl) }}"
                                                    alt="Profile Image" class="profile-img">
                                                {{ $ticket->currentOwnerRelation->reserver->Fname }}
                                                {{ $ticket->currentOwnerRelation->reserver->Lname }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="col-12">
                                                <p><strong>Created On:</strong> {{ $ticket->created_at }}</p>
                                            </div>
                                            <div class="col-12">
                                                <p><strong>Incident Date:</strong> {{ $ticket->DateIncident }}</p>
                                            </div>
                                            <p><strong>Description:</strong> {{ $ticket->desc }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- abcd --}}
                        @if (auth()->user()->role() <= 3 && $ticket->status >= 2)
                            @if (!$ticket->hasValidation())
                                <div class="mt-3">
                                    <button type="button" class="btn btn-primary validation_btn m-1 mt-0 mb-0"
                                        data-status="1">Validate</button>
                                    <button type="button" class="btn btn-secondary validation_btn m-1 mt-0 mb-0"
                                        data-status="0">Not Validate</button>
                                </div>
                            @else
                                <hr class="mt-2 mb-2">
                                <h5 class="m-2 mt-0 mb-1">Validated By : </h5>
                                <div class="container m-0 p-0 d-flex">
                                    <div class="col-12 col-md-4">
                                        <div class="d-flex ">
                                            <img src="{{ asset($ticket->validation->user->imgUrl) }}" alt="Profile Image"
                                                class="profile-img">
                                            {{ $ticket->validation->user->Fname }}
                                            {{ $ticket->validation->user->Lname }}</p>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">

                                    </div>
                                    <div class="col-12 col-md-4">

                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                    <div class="tab-pane fade  mb-0 pb-0" id="analyse" role="tabpanel" aria-labelledby="analyse-tab">
                        <div class="row mt-2">
                            <div class="col-12 col-md-4">
                                <div class="">
                                    <p class="mb-1 pb-1"><strong>Equipement:</strong> </p>
                                    <select class="form-select" aria-label="Default select example"
                                        id="equipement_analyse">
                                        <option value="{{ null }}"></option>
                                        @foreach ($equipements as $item)
                                            <option value="{{ $item->id }}"
                                                @if ($ticket->hasAnalyseLogs()) @if ($ticket->latestAnalyseLog->equipementID != null && $ticket->latestAnalyseLog->equipementID == $item->id)
                                                    selected @endif
                                                @endif >{{ $item->equipement }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="">
                                    <p class="mb-1 pb-1"><strong>NSM Statu:</strong></p>
                                    <select class="form-select" aria-label="Default select example"
                                        id="nsm_statu_analyse">
                                        <option value="{{ null }}"></option>
                                        @foreach ([1 => 'Host DOWN', 2 => 'Host UP', 3 => 'Service Critical', 4 => 'Service OK', 5 => 'Unknown Status'] as $value => $label)
                                            <option value="{{ $value }}"
                                                @if ($ticket->hasAnalyseLogs() && $ticket->latestAnalyseLog->NSMStatu == $value) selected @endif>
                                                {{ $label }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="">
                                    <p class="mb-1 pb-1"><strong>Nature Incident:</strong> </p>
                                    <select class="form-select" aria-label="Default select example"
                                        id="nature_incident_analyse">
                                        <option value="{{ null }}"></option>
                                        @foreach ($problems as $item)
                                            <option value="{{ $item->id }}"
                                                @if ($ticket->hasAnalyseLogs()) @if ($ticket->latestAnalyseLog->naruteIncidentID != null && $ticket->latestAnalyseLog->naruteIncidentID == $item->id)
                                                    selected @endif
                                                @endif>{{ $item->val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row
                        ItemShouldDesapairOwnership @if (!$CanEdit && auth()->user()->role() > 3)
                            d-none disabled
                        @endif mt-3 mb-0 pb-0">
                            <div class="col d-flex justify-content-end">

                                @if ($ticket->status <= 1 || ($ticket->status >= 2 && auth()->user()->role() <= 3))
                                    <button class="btn btn-success" id="save_analyse_changes">
                                        Save Changes
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade mb-0 pb-0" id="recovery" role="tabpanel" aria-labelledby="recovery-tab">
                        <div class="row mt-2">
                            <div class="col-12 col-md-6">
                                <div class="">
                                    <p class="mb-1 pb-1"><strong>
                                            Date Recovery
                                            :</strong> </p>
                                    @php
                                        use Carbon\Carbon;

                                        $dateRecovery =
                                            $ticket->hasRecoveryLogs() && $ticket->latestRecoveryLog->dateRecovery
                                                ? Carbon::parse($ticket->latestRecoveryLog->dateRecovery)->format(
                                                    'Y-m-d\TH:i',
                                                )
                                                : '';
                                    @endphp
                                    <input type="datetime-local" class="form-control" id="date_recovery_recovery"
                                        name="datetime" value="{{ $dateRecovery }}"
                                        data-date-recovery="{{ $dateRecovery }}">
                                </div>

                            </div>
                            <div class="col-12 col-md-6">
                                <div class="">
                                    <p class="mb-1 pb-1"><strong>Nature Solution:</strong></p>
                                    <select class="form-select" aria-label="Default select example"
                                        id="nature_solution_recovery">
                                        <option value="{{ null }}"></option>
                                        @foreach ($solutions as $item)
                                            <option value="{{ $item->id }}"
                                                @if ($ticket->hasRecoveryLogs()) @if ($ticket->latestRecoveryLog->naruteSolutionID != null && $ticket->latestRecoveryLog->naruteSolutionID == $item->id)
                                                selected @endif
                                                @endif>{{ $item->val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row
                        ItemShouldDesapairOwnership @if (!$CanEdit && auth()->user()->role() > 3)
                            d-none disabled
                        @endif mt-4 mb-0 pb-0">
                            <div class="col d-flex justify-content-end">
                                <div class=" d-flex">
                                        <div id="close-button-container" style="display: none;"
                                        @if ($ticket->status == 1 && $ticket->hasRecoveryLogs() && $ticket->latestRecoveryLog->dateRecovery != null)
                                            hidden
                                        @endif>
                                            <button id="close-ticket-btn" class="btn btn-danger m-3 mt-0 mb-0">Close this
                                                Ticket</button>
                                        </div>
                                    @if ($ticket->status <= 1 || ($ticket->status >= 2 && auth()->user()->role() <= 3))
                                        <button class="btn btn-success" id="save_recovery_changes">
                                            Save Changes
                                        </button>

                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade active show " id="info-body" role="tabpanel" aria-labelledby="info-tab">
            @if (auth()->user()->role() <= 3)
                @if ($ticket->hasValidation())
                    <div class="card card-custom validation-comments mt-4">
                        <div class="card-body">
                            <h5 class="card-title">Validation Comments</h5>
                            @if ($ticket->validation->user->id == auth()->user()->id)
                                <div class="mb-3">
                                    <label class="form-label">Add Comment</label>
                                    <textarea id="commentTextarea" class="form-control" rows="3"></textarea>
                                </div>
                                <button type="button" id="submitCommentBtn" class="btn btn-primary">Submit</button>

                            @endif
                            @foreach ($ticket->validation->getCommentsOrdered() as $item)
                                <div class="mt-3">
                                    <hr>
                                    <p><strong>{{ $item->created_at }} : </strong> {{ $item->body }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                {{-- @endif --}}
                <div class="card card-custom ticket-list mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Ticket List</h5>

                        <div style="overflow-x:auto">
                            <table id="ticketTableParents" class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAllTikets"></th>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Aerport</th>
                                        <th>Created On</th>
                                        <th>Incident Date</th>
                                        <th>Parent ID</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $count = $parentTickets->count();
                                    @endphp
                                    @foreach ($parentTickets as $item)
                                        @if (auth()->user()->role() <= 3 ||
                                                (auth()->user()->role() > 3 && $item->currentOwnerRelation->reserver->id == auth()->user()->id))
                                            <!-- Example rows (replace with your data) -->
                                            <tr data-ticketId="{{ $item->id }}" class="clickable-row"
                                                data-status="{{ $item->getStatusDesign() }}"
                                                data-status-org="{{ $item->status }}"
                                                data-aerport="{{ $item->aerport->id }}"
                                                data-created_at="{{ $item->created_at }}"
                                                data-inicident_date="{{ $item->DateIncident }}"
                                                data-cloture_date="{{ $item->DateIncident }}">
                                                <td><input type="checkbox" class="select-row"></td>
                                                <td>{{ $item->id }}</td>
                                                <td>
                                                    <span class="d-inline-block text-truncate" style="max-width: 90px;">
                                                        {{ $item->title }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="d-inline-block text-truncate" style="max-width: 150px;">
                                                        {{ $item->desc }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge rounded-pill status-circle"
                                                        data-status="{{ $item->getStatusDesign() }}">
                                                        {{ $item->getStatus() }} </span>
                                                </td>
                                                <td>{{ $item->aerport->code }}</td>
                                                <td>{{ $item->created_at }}</td>
                                                <td>{{ $item->DateIncident }}</td>
                                                <td>
                                                    Order {{ $count-- }}
                                                </td>
                                                <td>
                                                    <a class="btn btn-primary btn-circle d-flex justify-content-center align-items-center clickable-row-btn"
                                                        href="" data-ticketid="{{$item->id}}">
                                                        <img src="{{ asset('img/icons/view.png') }}" alt=""
                                                            width="60%">
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            {{-- here all the log of the tickets  --}}
            @if (auth()->user()->role() <= 3)
                <div class="card card-custom ticket-list mt-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Ticket Log</h5>
                        <div style="overflow-y: auto;">
                            <div class="accordion" id="accordionExample" style="max-height: 500px; overflow:auto;">
                                @foreach ($TicketLogs as $log)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed pt-2 pb-0" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#corden_ticket_log_{{ $loop->count - $loop->index  }}" data-indexOfItemLog="{{ $loop->count - $loop->index  }}" aria-expanded="true"
                                                aria-controls="corden_ticket_log_{{ $loop->count - $loop->index  }}">
                                                {{-- Show date-time bold + user name + " : " + "log title " --}}
                                                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-2" style="width: 50%">
                                                    <div class="col row m-0 p-0 row-cols-1 col-4" style="width: 30%">
                                                        <div class="col">
                                                            <div class="user-info d-flex align-items-center">
                                                                <img src="{{ asset($log['user']['imgURL']) }}"
                                                                    alt="{{ $log['user']['name'] }}"
                                                                    class="profile-picture">
                                                                <span class="m-2">{{ $log['user']['name'] }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col mt-2">
                                                            <p class="fw-light">{{ $log['date'] }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col d-flex align-items-center" style="overflow:hidden; width:70%;">
                                                        <b>
                                                            [ Act {{ $loop->count - $loop->index  }} ]
                                                            .
                                                            {{ $log['logType'] }}
                                                        </b>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="corden_ticket_log_{{ $loop->count - $loop->index  }}" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body" id="ticketsLogContainer">
                                                {{-- Log content --}}
                                                @switch($log['logTypeIndex'])
                                                    @case(0)
                                                        {{-- Ticket Creation Log --}}
                                                        <p>Ticket was created by <strong>{{ $log['user']['name'] }}</strong> on <strong>{{ $log['date'] }}</strong>.</p>
                                                        @break

                                                    @case(1)
                                                        {{-- Transfer Owner Log --}}
                                                        <p>Ticket ownership was transferred on <strong>{{ $log['date'] }}</strong>.</p>

                                                        @if ($log['LogData']->owner->id == $log['LogData']->reserver->id || $log['LogData']->owner->id == 0)
                                                            <p>User <strong>{{ $log['LogData']->reserver->Fname }}</strong> assigned the ticket to themselves.</p>
                                                        @elseif ($log['LogData']->forced == 1)
                                                            <p>Ticket was transferred to <strong>{{ $log['LogData']->reserver->Fname }}</strong> by a supervisor<b>[forced]</b>.</p>
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

                                                        @if(!empty($log['LogData']->comments))
                                                            <div class="report-body-container p-2">
                                                                <h5 class="report-body-title">Report Comments:</h5>
                                                                <div class="card-body">
                                                                    <!-- List of Comments -->
                                                                    @foreach ($log['LogData']->comments as $comment)
                                                                        <div class="comment">
                                                                            <div class="comment-body">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <div class="comment-name">{{$comment->user->Fname}} {{$comment->user->Lname}}</div>
                                                                                    <div class="comment-date">{{$comment->created_at}}</div>
                                                                                </div>
                                                                                <div class="comment-text">
                                                                                    {{$comment->comment}}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                    <!-- Add more comments as needed -->
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
                                        </div>
                                    </div>
                                @endforeach


                            </div>
                        </div>
                    </div>
                </div>

            @endif
        </div>
        <div class="tab-pane fade makeSpacingDesapear" id="analyse-body" role="tabpanel" aria-labelledby="analyse-tab">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">
                        Final Analysis Report
                    </h5>
                    <div id="editorAnalyse" class="ckeditor">
                        @if ($ticket->hasAnalyseLogs())
                            {!! $ticket->latestAnalyseLog->repportBody !!}
                        @endif
                    </div>
                </div>
                @if ($ticket->status <= 1 || ($ticket->status >= 2 && auth()->user()->role() <= 3))
                    <div class="card-footer
                        ItemShouldDesapairOwnership @if (!$CanEdit && auth()->user()->role() > 3)
                            d-none disabled
                        @endif d-flex justify-content-end">
                        <button id="save_final_analysis_report" class="btn btn-primary">Save Changes</button>
                    </div>
                @endif
            </div>
        </div>
        <div class="tab-pane fade makeSpacingDesapear" id="recovery-body" role="tabpanel"
            aria-labelledby="recovery-tab">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">
                        Final Recovery Report
                    </h5>
                    <div id="editorRecovery" class="ckeditor">
                        @if ($ticket->hasRecoveryLogs())
                            {!! $ticket->latestRecoveryLog->repportBody !!}
                        @endif
                    </div>
                </div>
                @if ($ticket->status <= 1 || ($ticket->status >= 2 && auth()->user()->role() <= 3))
                    <div class="card-footer
                        ItemShouldDesapairOwnership @if (!$CanEdit && auth()->user()->role() > 3)
                            d-none disabled
                        @endif d-flex justify-content-end">
                        <button id="Add_comment_To_recovery_report" class="btn btn-secondary m-2 mt-0 mb-0" data-bs-toggle="modal" data-bs-target="#AddComment2RapportModal">Add Comment</button>
                        <!-- Modal -->
                        <div class="modal fade" id="AddComment2RapportModal" tabindex="-1" aria-labelledby="AddComment2RapportModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addCommentModalLabel">Add New Comment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="commentText" class="form-label">Your Comment</label>
                                        <textarea class="form-control" id="commentRapportText" rows="4" placeholder="Write your comment" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" id="submitCommentRapportFormula">Submit Comment</button>
                                </div>
                            </div>
                            </div>
                        </div>
                        <button id="save_final_recovery_report" class="btn btn-primary">Save Changes</button>
                    </div>
                @endif
            </div>
            {{-- @if (auth()->user()->role() <= 3)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title">Comments</h5>
                    </div>
                    <div class="card-body">
                        <!-- List of Comments -->
                        <div class="comment">
                            <img src="https://via.placeholder.com/45" alt="User Profile">
                            <div class="comment-body">
                                <div class="d-flex justify-content-between">
                                    <div class="comment-name">John Doe</div>
                                    <div class="comment-date">2024-09-20 10:15 AM</div>
                                </div>
                                <div class="comment-text">
                                    This is a comment from John Doe. It’s a very insightful thought on the topic!
                                </div>
                            </div>
                        </div>
                        <div class="comment">
                            <img src="https://via.placeholder.com/45" alt="User Profile">
                            <div class="comment-body">
                                <div class="d-flex justify-content-between">
                                    <div class="comment-name">Jane Smith</div>
                                    <div class="comment-date">2024-09-19 5:45 PM</div>
                                </div>
                                <div class="comment-text">
                                    I totally agree with this! This is a great example of thoughtful input.
                                </div>
                            </div>
                        </div>
                        <div class="comment">
                            <img src="https://via.placeholder.com/45" alt="User Profile">
                            <div class="comment-body">
                                <div class="d-flex justify-content-between">
                                    <div class="comment-name">Mike Johnson</div>
                                    <div class="comment-date">2024-09-18 12:30 PM</div>
                                </div>
                                <div class="comment-text">
                                    Interesting perspective, thanks for sharing your thoughts!
                                </div>
                            </div>
                        </div>
                        <!-- Add more comments as needed -->
                    </div>
                </div>
            @endif --}}
        </div>
    </div>
    <script>
        console.log(
            @json($TicketLogs)
        )
    </script>
@endsection

@section('script2')
    <script defer src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script defer src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script defer src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable for Airport Table
            $('#airportTable').DataTable();
            $('#ticketTable').DataTable();
        });

        $(document).ready(function() {
            $('button[data-bs-toggle="tab"]').on('click', function() {
                var target = $(this).data('bs-target').split(", ");
                $('.tab-pane').removeClass('show active');
                $('.tab-pane').addClass('makeSpacingDesapear');
                $(target[0]).addClass('show active ');
                $(target[1]).addClass('show active ');
                $(target[0]).removeClass('makeSpacingDesapear');
                $(target[1]).removeClass('makeSpacingDesapear');
            });

        });


        // Initialize CKEditor for the element with id 'editorAnalyse'
        ClassicEditor.create(document.querySelector('#editorAnalyse'), {
            ckfinder: {
                uploadUrl: "{{ route('upload.image') }}?_token={{ csrf_token() }}"
            },
            toolbar: [
                'undo',
                'redo',
                '|',
                'heading',
                '|',
                'bold',
                'italic',
                'underline',
                '|',
                'link',
                'uploadImage',
                'ckbox',
                'insertTable',
                'blockQuote',
                'mediaEmbed',
                '|',
                'bulletedList',
                'numberedList',
                '|',
                'outdent',
                'indent',
            ],
            heading: {
                options: [{
                        model: 'paragraph',
                        title: 'Paragraph',
                        class: 'ck-heading_paragraph',
                    },
                    {
                        model: 'heading1',
                        view: 'h1',
                        title: 'Heading 1',
                        class: 'ck-heading_heading1',
                    },
                    {
                        model: 'heading2',
                        view: 'h2',
                        title: 'Heading 2',
                        class: 'ck-heading_heading2',
                    },
                    {
                        model: 'heading3',
                        view: 'h3',
                        title: 'Heading 3',
                        class: 'ck-heading_heading3',
                    },
                    {
                        model: 'heading4',
                        view: 'h4',
                        title: 'Heading 4',
                        class: 'ck-heading_heading4',
                    },
                ],
            },
            image: {
                resizeOptions: [{
                        name: 'resizeImage:original',
                        label: 'Default image width',
                        value: null,
                    },
                    {
                        name: 'resizeImage:50',
                        label: '50% page width',
                        value: '50',
                    },
                    {
                        name: 'resizeImage:75',
                        label: '75% page width',
                        value: '75',
                    },
                ],
                toolbar: [
                    'imageTextAlternative',
                    'toggleImageCaption',
                    '|',
                    'imageStyle:inline',
                    'imageStyle:wrapText',
                    'imageStyle:breakText',
                    '|',
                    'resizeImage',
                ],
            },
            table: {
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'],
            }
        }).then(editor => {
            editorForAnalyse = editor; // Store the editor instance globally
        }).catch(error => {
            console.error(error);
        });



        // Initialize CKEditor for the element with id 'editorRecovery'
        ClassicEditor.create(document.querySelector('#editorRecovery'), {
            ckfinder: {
                uploadUrl: "{{ route('upload.image') }}?_token={{ csrf_token() }}"
            },
            toolbar: [
                'undo',
                'redo',
                '|',
                'heading',
                '|',
                'bold',
                'italic',
                'underline',
                '|',
                'link',
                'uploadImage',
                'ckbox',
                'insertTable',
                'blockQuote',
                'mediaEmbed',
                '|',
                'bulletedList',
                'numberedList',
                '|',
                'outdent',
                'indent',
            ],
            heading: {
                options: [{
                        model: 'paragraph',
                        title: 'Paragraph',
                        class: 'ck-heading_paragraph',
                    },
                    {
                        model: 'heading1',
                        view: 'h1',
                        title: 'Heading 1',
                        class: 'ck-heading_heading1',
                    },
                    {
                        model: 'heading2',
                        view: 'h2',
                        title: 'Heading 2',
                        class: 'ck-heading_heading2',
                    },
                    {
                        model: 'heading3',
                        view: 'h3',
                        title: 'Heading 3',
                        class: 'ck-heading_heading3',
                    },
                    {
                        model: 'heading4',
                        view: 'h4',
                        title: 'Heading 4',
                        class: 'ck-heading_heading4',
                    },
                ],
            },
            image: {
                resizeOptions: [{
                        name: 'resizeImage:original',
                        label: 'Default image width',
                        value: null,
                    },
                    {
                        name: 'resizeImage:50',
                        label: '50% page width',
                        value: '50',
                    },
                    {
                        name: 'resizeImage:75',
                        label: '75% page width',
                        value: '75',
                    },
                ],
                toolbar: [
                    'imageTextAlternative',
                    'toggleImageCaption',
                    '|',
                    'imageStyle:inline',
                    'imageStyle:wrapText',
                    'imageStyle:breakText',
                    '|',
                    'resizeImage',
                ],
            },
            table: {
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'],
            }
        }).then(editor => {
            editorForRecovery = editor; // Store the editor instance globally
        }).catch(error => {
            console.error(error);
        });
    </script>

    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- Ticket Drpdown action [trnsfore ticket] --}}
    <script>
        $(document).ready(function() {
            $('a[data-bs-target="#listUsersModalSelect"]').on('click', function(event) {
                // Prevent the default link action
                event.preventDefault();

                // Get the ticket ID from the data attribute
                var ticketId = $(this).data('ticketid');

                // Set the ticket ID in the hidden input field in the modal
                $('#ticketIdContainerModalTransfer').val(ticketId);
            });
            // When the 'Save Changes' button is clicked, log the selected data and close the modal
            $('#saveChangesButton').on('click', function() {
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
                        // Show alert with each piece of airport data
                        showAlertS('Ticket Assigned successfully!');

                        // -----------------------------------------
                        // only be commeted if i add every 100milisec to refrech the data of the tickets [after]
                        // -----------------------------------------
                        // Reload the page after a short delay (optional)
                        // setTimeout(function() {
                        //     location.reload(); // Reloads the current page
                        // }, 1000);
                      // console.log(response);
                    },
                    error: function(xhr, status, error) {
                        // Handle error responses if needed
                        showAlertD('Failed to assign Ticket. Please try again.');
                    }
                });
                // Close the modal
                $('#listUsersModalSelect').modal('hide');
            });

            $('.ticketTransforToMeAction').on('click', function(event) {
                event.preventDefault(); // Prevent the default action of the anchor tag
                var ticketId = $(this).data('ticket-id'); // Use jQuery to access data attribute

                $.ajax({
                    url: '{{ route('ticket.transform.toMe') }}', // Adjust the URL to your API endpoint for adding airports
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
                      // console.log(response);
                    },
                    error: function(xhr, status, error) {
                        // Handle error responses if needed
                        showAlertD('Failed to assign Ticket. Please try again.');
                    }
                });
            });
        });
    </script>

    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- Ticket analyse & recovery logic --}}
    <script>
        $(document).ready(function() {
            // Attach click event handlers to the buttons
            $('#save_analyse_changes, #save_final_analysis_report, #saveOperatoreTicket').click(function() {

                // Get values from select elements
                var equipementAnalyse = $('#equipement_analyse').val();
                var nsmStatusAnalyse = $('#nsm_statu_analyse').val();
                var natureIncidentAnalyse = $('#nature_incident_analyse').val();

                // Get text from CKEditor
                var finalAnalysisReport = editorForAnalyse ? editorForAnalyse.getData() : null;

                var operatorNTicket = $('#operator_nticket').val();
                var operatorName = $('#operator_name').val();
                var operatorMail = $('#operator_mail').val();
                var operatorTell = $('#operator_tell').val();
                // Alert the values
                // alert("operatorNTicket: " + operatorNTicket + "\n" +
                //     "operatorName: " + operatorName + "\n" +
                //     "operatorMail: " + operatorMail + "\n" +
                //     "operatorTell: " + operatorTell);

                $.ajax({
                    url: '{{ route('ticket.Log.Add') }}', // Adjust the URL to your API endpoint for adding airports
                    method: 'POST',
                    data: {
                        equipementId: equipementAnalyse,
                        nsmStatus: nsmStatusAnalyse,
                        natureIncidentId: natureIncidentAnalyse,
                        body: finalAnalysisReport,
                        ticketId: '{{ $ticket->id }}',
                        type: 'analyse',
                        operatorNTicket: operatorNTicket,
                        operatorName: operatorName,
                        operatorMail: operatorMail,
                        operatorTell: operatorTell,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Show alert with each piece of airport data
                        showAlertS('Analysis log added successfully!');

                        // -----------------------------------------
                        // only be commeted if i add every 100milisec to refrech the data of the tickets [after]
                        // -----------------------------------------
                        // Reload the page after a short delay (optional)
                        setTimeout(function() {
                            location.reload(); // Reloads the current page
                        }, 1000);
                      // console.log(response);
                    },
                    error: function(xhr, status, error) {
                        // Handle error responses if needed
                      // console.log(xhr);
                      // console.log(error);
                        showAlertD('Failed to add analysis log. Please try again.');
                    }
                });
            });

            $('#RemoveOperatoreFromTheTicket').click(function() {
                $.ajax({
                    url: '{{ route('ticket.Log.Add') }}', // Adjust the URL to your API endpoint for adding airports
                    method: 'POST',
                    data: {
                        ticketId: '{{ $ticket->id }}',
                        type: 'remove',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Show alert with each piece of airport data
                        showAlertS('Operatore Removed successfully!');

                        // -----------------------------------------
                        // only be commeted if i add every 100milisec to refrech the data of the tickets [after]
                        // -----------------------------------------
                        // Reload the page after a short delay (optional)
                        setTimeout(function() {
                            location.reload(); // Reloads the current page
                        }, 1000);
                    // console.log(response);
                    },
                    error: function(xhr, status, error) {
                        // Handle error responses if needed
                    // console.log(xhr);
                    // console.log(error);
                        showAlertD('Failed to Remove Operatore. Please try again.');
                    }
                });
            });

            $('#save_recovery_changes, #save_final_recovery_report').click(function() {

                // Get values from select elements
                var dateRecovery = $('#date_recovery_recovery').val();
                var natureSolution = $('#nature_solution_recovery').val();

                // Get text from CKEditor
                var finalRecoveryReport = editorForRecovery ? editorForRecovery.getData() : null;

                // Alert the values
                // console.log("Equipement: " + dateRecovery + "\n" +
                //     "NSM Status: " + natureSolution + "\n" +
                //     "Final Analysis Report: " + finalRecoveryReport);

                $.ajax({
                    url: '{{ route('ticket.Log.Add') }}', // Adjust the URL to your API endpoint for adding airports
                    method: 'POST',
                    data: {
                        dateRecovery: dateRecovery,
                        natureSolution: natureSolution,
                        body: finalRecoveryReport,
                        ticketId: '{{ $ticket->id }}',
                        type: 'recovery',
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
                      // console.log(response);
                    },
                    error: function(xhr, status, error) {
                        // Handle error responses if needed
                        showAlertD('Failed to assign Ticket. Please try again.');
                    }
                });
            });

        });
    </script>

    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- Ticket cloture logic --}}
    <script>
        $(document).ready(function() {
            function checkRecoveryDate() {
                var recoveryDateInput = $('#date_recovery_recovery');
                var closeButtonContainer = $('#close-button-container');
                var closeButton = $('#close-ticket-btn');

                // Get the date from the data attribute
                var recoveryDate = new Date(recoveryDateInput.data('date-recovery'));
                var now = new Date();

                if (recoveryDateInput.data('dateRecovery')) {
                    closeButtonContainer.show();
                    closeButton.prop('disabled', false);
                } else {
                    closeButtonContainer.hide();
                }
            }

            // Run the function initially and set an interval to check every minute
            checkRecoveryDate();
            setInterval(checkRecoveryDate, 10000);

            $('#close-ticket-btn').click(function() {
                // Collect the data from the input and other elements
                var ticketId = '{{ $ticket->id }}'; // Ensure this is available in your

                // Send AJAX request
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
        });
    </script>

    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- Ticket Validation&comment logic --}}
    <script>
        $(document).ready(function() {
            $('.validation_btn').on('click', function() {
                var status = $(this).data('status'); // Get the status from the button's data attribute
                var ticketId =
                    '{{ $ticket->id }}'; // Assuming you have a way to pass the ticket ID dynamically

                // Perform the AJAX request
                $.ajax({
                    url: '{{ route('ticket.validation') }}', // Adjust the URL to your route
                    method: 'POST',
                    data: {
                        ticketId: ticketId,
                        status: status,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        showAlertS('Ticket status updated successfully!');

                        // Optionally, refresh the data or the page
                        setTimeout(function() {
                            location.reload(); // Reloads the current page
                        }, 1000);
                      // console.log(response);
                    },
                    error: function(xhr, status, error) {
                        showAlertD('Failed to update ticket status. Please try again.');
                    }
                });
            });
        });
    </script>
    @if ($ticket->hasValidation())
        @if ($ticket->validation->user->id == auth()->user()->id)
            <script>
                $(document).ready(function() {
                    $('#submitCommentBtn').on('click', function() {
                        // Get the value from the textarea
                        var comment = $('#commentTextarea').val();

                        // Perform the AJAX request
                        $.ajax({
                            url: '{{ route('ticket.add.commet.validation') }}', // Replace with your actual route
                            method: 'POST',
                            data: {
                                comment: comment, // Send the comment value
                                validationID: '{{ $ticket->validation->id }}', // Send the comment value
                                _token: '{{ csrf_token() }}' // Include CSRF token for security
                            },
                            success: function(response) {
                                // Handle success (e.g., show a success message, clear the textarea, etc.)
                                showAlertS('Comment submitted successfully!');
                                $('#commentTextarea').val(''); // Clear the textarea
                            },
                            error: function(xhr, status, error) {
                                // Handle error responses
                                showAlertD('Failed to submit comment. Please try again.');
                            }
                        });
                    });


                });
            </script>
        @endif
    @endif

    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- Ticket set Parent logic --}}
    <script>
        $(document).ready(function() {
            $('#ticketTableParents').DataTable();
        });

        document.addEventListener('DOMContentLoaded', function() {
            var currentTicketId;

            $('#setParentModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                currentTicketId = button.data('ticket-id');
            });

            $('#applyParentChange').on('click', function() {
                var parentTicketId = $('input[name="ticketSelectParent"]:checked').val();
                if (parentTicketId) {
                    // Here you can make an AJAX call to save the changes if needed.
                    // Perform the AJAX request
                    $.ajax({
                        url: '{{ route('ticket.set.parent') }}', // Replace with your actual route
                        method: 'POST',
                        data: {
                            parentTicketId: parentTicketId, // Send the comment value
                            ticketId: '{{ $ticket->id }}', // Send the comment value
                            _token: '{{ csrf_token() }}' // Include CSRF token for security
                        },
                        success: function(response) {
                            // Handle success (e.g., show a success message, clear the textarea, etc.)
                            showAlertS('Set Parent successfully!');
                            // Optionally, refresh the data or the page
                            setTimeout(function() {
                                location.reload(); // Reloads the current page
                            }, 1000);
                          // console.log(response);
                        },
                        error: function(xhr, status, error) {
                            // Handle error responses
                            showAlertD('Failed to Set Parent. Please try again.');
                        }
                    });
                } else {
                    showAlertD('Please select a parent ticket.');
                }
            });
        });
    </script>
    <script>
        $(document).on('click', '.clickable-row-btn', function(event) {
            // Prevent the default anchor behavior
            event.preventDefault();

            // Get the ticket ID from data attribute
            var ticketId = $(this).data("ticketid");

            // Redirect to the desired URL
            window.location.href = "/" + ticketId + "/ticket"; // Change to your desired URL structure
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#submitCommentRapportFormula').click(function() {
                // Get the values from the form
                var comment = $('#commentRapportText').val();

                // Make sure the comment is not empty
                if (comment) {
                    $.ajax({
                        url: '{{route('ticket.add.commet.recovery.repport')}}', // The endpoint to handle the request
                        type: 'POST',
                        data: {
                            comment: comment,
                            ticketId: {{$ticket->id}},
                            _token: '{{ csrf_token() }}' // Add CSRF token for security
                        },
                        success: function(response) {
                            // Handle success (e.g., show a success message, close the modal, etc.)
                            showAlertS('Comment submitted successfully!');
                            $('#commentRapportText').val(''); // Clear the textarea
                            $('#addCommentModal').modal('hide'); // Hide the modal
                        },
                        error: function(xhr) {
                            // Handle errors (e.g., show an error message)
                            showAlertD('Error submitting comment: ' + xhr.responseJSON.message);
                        }
                    });
                } else {
                    showAlertD('Please enter a comment.');
                }
            });
        });
    </script>
    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| --}}
    {{-- Ticket RealTimeUpdate --}}
    <script>
        function fetchTickets() {
            $.ajax({
                url: '{{ route('ticket.homepage.json', ['id' => $ticket->id]) }}',
                method: 'GET',
                success: function(response) {
                    //  get all the data & update content
                    // ...
                  // console.log(response);
                    updateHeaderTicket(response);
                    updateAdminListOfUsers(response);
                    updateTicketTableOptionSetParent(response);

                    ticketPageInfo_Generalinfo(response);
                    // Analyse Update
                    updateAnalyseSection_ListOptions(response);
                    // Recovery update
                    updateRecoverySection(response);

                    updateItemVisibilityDependOnOwnership(response);
                    // Validation log [not important]

                    if (response.role <= 3) {
                        ticketPageInfo_TicketParentOrder(response);
                        ticketPageInfo_TicketLogsUpdates(response);
                    }

                },
                error: function(error) {
                    console.error('Error fetching tickets:', error);
                }
            });
        }

        function updateItemVisibilityDependOnOwnership(response) {
            // Get all elements with the class 'ItemShouldDesapairOwnership'
            const items = document.querySelectorAll('.ItemShouldDesapairOwnership');

            items.forEach(item => {
                if (!response.CanEdit && response.role > 3) {
                    // Remove 'd-none' and 'disabled' classes if isVisible is true
                    item.classList.add('d-none', 'disabled');
                } else {
                    // Add 'd-none' and 'disabled' classes if isVisible is false
                    item.classList.remove('d-none', 'disabled');
                }
            });
            if(response.CanAddOperatore){
                items[2].classList.remove('d-none', 'disabled');
            }
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
                        `<span class="badge rounded-pill text-bg-primary">${user.getRoleAttribute()}</span>`,
                        `${user.email}`,
                        `<span class="badge rounded-pill ${user.latestLoginLog?.isLogged === 1 ? 'text-bg-success' : 'text-bg-danger'}">${user.latestLoginLog?.isLogged === 1 ? 'Online' : 'Offline'}</span>`
                    ]).draw(false).node(); // Get the node before calling draw(false)

                    // Set the attributes on the node
                    $(newRow).attr('data-userid', user.id);
                }
            });

            // Redraw the table to reflect any changes
            table.draw();
        }

        function updateHeaderTicket(response){
            // Get status from response
            var status = response.ticket.status;


            // Update the status badge and text in the header
            var statusBadge = document.querySelector('#ticketMainInfoOptionHeader .status-circle');

            var statusDesign = getStatusDesign(status);
            var statusText = getStatus(status);

            // Update the status badge
            statusBadge.textContent = statusText;

            statusBadge.setAttribute('data-status', statusDesign);

            // Update dropdown options
            var dropdownItems = $('#ticketMainInfoOptionHeader .dropdown .dropdown-menu .dropdown-item');

            // Transfer Ticket
            console.log(dropdownItems)
            if (status >= 2) {
                $(dropdownItems[0]).addClass('disabled');
            } else {
                $(dropdownItems[0]).removeClass('disabled');
            }

            if (response.role <= 3) {
                // Assign to Myself
                if (status < 2) {
                    $(dropdownItems[1]).removeClass('disabled');
                } else {
                    $(dropdownItems[1]).addClass('disabled');
                }

                // Generate Rapport
                if (status >= 2) {
                    $(dropdownItems[3]).removeClass('disabled');
                } else {
                    $(dropdownItems[3]).addClass('disabled');
                }

                // Set a Parent
                if (response.ticket.parent) {
                    $(dropdownItems[4]).addClass('disabled');
                } else {
                    $(dropdownItems[4]).removeClass('disabled');
                }

                // Add Operatore ISP
                if (response.CanAddOperatore) {
                    $(dropdownItems[2]).removeClass('disabled');
                } else {
                    $(dropdownItems[2]).addClass('disabled');
                }
            }
        }

        function updateTicketTableOptionSetParent(response) {
            var tickets = response.ticketsOptions; // Assuming response contains ticketsOptions
            var table = $('#ticketTable').DataTable();
            var responseIds = new Set(tickets.map(ticket => ticket.id));
            var currentRows = [];

            // Iterate over the existing rows in the table
            table.rows().every(function() {
                var row = this.node();
                var rowId = parseInt($(row).attr('data-ticketid'));

                // If the ticket exists in the response, update the row
                if (responseIds.has(rowId)) {
                    var ticketData = tickets.find(ticket => ticket.id === rowId);

                    // Update row data
                    $(row).find('td').eq(1).text(ticketData.id); // Ticket ID
                    $(row).find('td').eq(2).text(ticketData.title); // Title
                    $(row).find('td').eq(3).find('span')
                        .text(getStatus(ticketData.status))
                        .attr('data-status', getStatusDesign(ticketData.status));

                    $(row).find('td').eq(4).text(formatDate(ticketData.created_at)); // Created At
                    $(row).find('td').eq(5).text(formatDate(ticketData.updated_at)); // Updated At

                    currentRows.push(rowId);
                } else {
                    // If the ticket does not exist in the response, remove the row
                    table.row(row).remove().draw();
                }
            });

            // Add new tickets that aren't already in the table
            tickets.forEach(ticket => {
                if (!currentRows.includes(ticket.id)) {
                    var newRow = table.row.add([
                        `<input type="radio" name="ticketSelectParent" value="${ticket.id}">`,
                        ticket.id,
                        ticket.title,
                        `<span class="badge rounded-pill status-circle " data-status="${getStatusDesign(ticket.status)}"">${getStatus(ticket.status)}</span>`,
                        formatDate(ticket.created_at),
                        formatDate(ticket.updated_at)
                    ]).draw(false).node(); // Get the node before calling draw(false)

                    // Set the attributes on the node
                    newRow.setAttribute('data-ticketid', ticket.id);
                }
            });

            // Redraw the table to reflect any changes
            table.draw();
        }

        function ticketPageInfo_Generalinfo(response) {
            // Extract ticket data from the response
            var ticket = response.ticket; // Assuming the response contains a 'ticket' object

            // Update the Title
            $('#info .container .row .col-md-6 .row .col-12 p:contains("Title:")')
                .html(`<strong>Title:</strong> ${ticket.title}`);

            // Update Support Notification
            $('#info .container .row .col-md-6 .row .col-12 p:contains("Support Notification:")')
                .html(`<strong>Support Notification:</strong> ${ticket.NaturNotification}`);

            // Update Contact Reclamation
            $('#info .container .row .col-md-6 .row .col-12 p:contains("Contact Reclamation:")')
                .html(`<strong>Contact Reclamation:</strong> ${ticket.contactReclamation}`);

            // Update Owner Information
            var ownerImg = ticket.current_owner_relation.reserver.imgUrl ?
                `{{ asset('${ticket.current_owner_relation.reserver.imgUrl}') }}` :
                'default-profile.png'; // Default image if not provided

            var ownerName = `${ticket.current_owner_relation.reserver.Fname} ${ticket.current_owner_relation.reserver.Lname}`;
            $('#info .container .row .col-md-6 .row .col-12 p:contains("Owner:") div')
                .html(`
                        <img src="${ownerImg}" alt="Profile Image" class="profile-img">
                        ${ownerName}
                    `);

            // Update Created On
            $('#info .container .row .col-md-6 .row .col-12 p:contains("Created On:")')
                .html(`<strong>Created On:</strong> ${ formatDate(ticket.created_at)}`);

            // Update Incident Date
            $('#info .container .row .col-md-6 .row .col-12 p:contains("Incident Date:")')
                .html(`<strong>Incident Date:</strong> ${ticket.DateIncident}`);

            // Update Description
            $('#info .container .row .col-md-6 .row p:contains("Description:")')
                .html(`<strong>Description:</strong> ${ticket.desc}`);
        }

        function ticketPageInfo_TicketParentOrder(response) {

            var tickets = response.parentTickets;

            var table = $('#ticketTableParents').DataTable();
            var responseIds = new Set(tickets.map(ticket => ticket.id));
            var currentRows = [];

            // Iterate over the existing rows in the table
            table.rows().every(function() {
                var row = this.node();
                var rowId = parseInt($(row).attr('data-ticketId'));

                // If the ticket exists in the response, update the row
                if (responseIds.has(rowId)) {
                    var ticketData = tickets.find(ticket => ticket.id === rowId);

                    $(row).find('td').eq(1).text(ticketData.id); // Ticket ID
                    $(row).find('td').eq(2).find('span').text(ticketData.title); // Title
                    $(row).find('td').eq(3).find('span').text(ticketData.desc); // Description

                    $(row).find('td').eq(4).find('span')  // Status
                        .text(getStatus(ticketData.status))
                        .attr('data-status', getStatusDesign(ticketData.status));

                    $(row).find('td').eq(5).text(ticketData.aerport.code); // Aerport
                    $(row).find('td').eq(6).text(formatDate(ticketData.created_at)); // Created On
                    $(row).find('td').eq(7).text(formatDate(ticketData.DateIncident)); // Incident Date

                    $(row).find('td').eq(8).text('Order ' + ticketData.order); // Order

                    currentRows.push(rowId);
                } else {
                    // If the ticket does not exist in the response, remove the row
                    table.row(row).remove();
                }
            });

            // Add new rows for tickets that are not already in the table
            tickets.forEach(ticket => {
                if (!currentRows.includes(ticket.id)) {
                    table.row.add([
                        '<input type="checkbox" class="select-row">',
                        ticket.id,
                        '<span class="d-inline-block text-truncate" style="max-width: 90px;">' + ticket.title + '</span>',
                        '<span class="d-inline-block text-truncate" style="max-width: 150px;">' + ticket.desc + '</span>',
                        '<span class="badge rounded-pill status-circle" data-status="' + getStatusDesign(ticket.status) + '">' + getStatus(ticket.status) + '</span>',
                        ticket.aerport.code,
                        formatDate(ticket.created_at),
                        formatDate(ticket.DateIncident),
                        'Order ' + ticket.order,
                        `<a class="btn btn-primary btn-circle d-flex justify-content-center align-items-center clickable-row-btn" href="" data-ticketid="${ticket.id}"><img src=" {{ asset('img/icons/view.png') }} " alt="" width="60%"></a>`
                    ]).draw();
                }
            });

            // Redraw the table to reflect any changes
            table.draw();
        }

        // |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
        // |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
        // fucntion only for logs update

        function ticketPageInfo_TicketLogsUpdates(response) {
            // Ensure we have the log data from the response
            if (!response.TicketLogs || response.TicketLogs.length === 0) return;

            // Create a container for new items to be added in sorted order
            var newItems = [];

            // Collect all existing accordion items and their reverse indices
            $('#accordionExample .accordion-item').each(function() {
                var $button = $(this).find('.accordion-button');
                var index = parseInt($button.attr('data-indexOfItemLog'));
                $(this).data('index', index);
            });

            // Process each log in reverse order of their indices
            for (var index = response.TicketLogs.length - 1; index >= 0; index--) {
                var log = response.TicketLogs[index];

                // Calculate the reverse index for the accordion item
                var reverseIndex = response.TicketLogs.length - index;

                // Check if this item already exists
                var existingItem = $('#corden_ticket_log_' + reverseIndex);

                if (existingItem.length) {
                    // Update existing item's content
                    var logHtml = varructLogHtml(log);
                    existingItem.find('.accordion-body').html(logHtml);
                } else {
                    // Create a new accordion item
                    var newAccordionItem = createNewAccordionItem(log, reverseIndex);
                    newItems.push({ index: reverseIndex, item: newAccordionItem });
                }
            }

            // Sort new items by reverse index in descending order
            newItems.sort(function(a, b) {
                return b.index - a.index;
            });

            // Insert new items into the accordion in the correct order
            newItems.forEach(function(newItem) {
                insertAccordionItemInOrder(newItem.item, newItem.index);
            });
        }

        function insertAccordionItemInOrder(newAccordionItem, reverseIndex) {
            // Convert reverse index to a number for comparison
            reverseIndex = parseInt(reverseIndex);

            // Find all existing accordion items
            var accordionItems = $('#accordionExample .accordion-item');

            // Iterate through accordion items to find the correct position
            var inserted = false;
            accordionItems.each(function() {
                var currentItem = $(this);
                var currentIndex = parseInt(currentItem.data('index'));

                if (reverseIndex > currentIndex) {
                    // Insert the new item before the current item if the new index is larger
                    currentItem.before(newAccordionItem);
                    inserted = true;
                    return false; // Break out of the each loop
                }
            });

            // If no position found (i.e., it's the smallest index), append to the end
            if (!inserted) {
                $('#accordionExample').append(newAccordionItem);
            }
        }

        function varructLogHtml(log) {
            var logHtml = '';

            // varruct the log HTML based on logTypeIndex
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
                    // Comments section
                    if (log.LogData.comments && log.LogData.comments.length > 0) {
                        logHtml += `<div class="report-comments-container p-2">
                                        <h5 class="report-comments-title">Report Comments:</h5>
                                        <div class="card-body">`;
                        log.LogData.comments.forEach(comment => {
                            logHtml += `<div class="comment">
                                            <div class="comment-body">
                                                <div class="d-flex justify-content-between">
                                                    <div class="comment-name">${comment.user.Fname} ${comment.user.Lname}</div>
                                                    <div class="comment-date">${formatDate(comment.created_at)}</div>
                                                </div>
                                                <div class="">${comment.comment}</div>
                                            </div>
                                        </div>`;
                        });
                        logHtml += `</div>
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

        function createNewAccordionItem(log, reverseIndex) {
            var logHtml = varructLogHtml(log);

            var newItem = `
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed pt-2 pb-0" type="button" data-bs-toggle="collapse"
                            data-bs-target="#corden_ticket_log_${reverseIndex}" data-indexOfItemLog="${reverseIndex}" aria-expanded="true"
                            aria-controls="corden_ticket_log_${reverseIndex}">
                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-2">
                                <div class="col row m-0 p-0 row-cols-1 col-4" style="max-width: 230px;">
                                    <div class="col">
                                        <div class="user-info d-flex align-items-center">
                                            <img src="${log.user.imgURL}" alt="${log.user.name}" class="profile-picture">
                                            <span class="m-2">${log.user.name}</span>
                                        </div>
                                    </div>
                                    <div class="col mt-2">
                                        <p class="fw-light">${log.date}</p>
                                    </div>
                                </div>
                                <div class="col d-flex align-items-center" style="overflow:hidden;">
                                    <strong>${log.logType}</strong>
                                </div>
                            </div>
                        </button>
                    </h2>
                    <div id="corden_ticket_log_${reverseIndex}" class="accordion-collapse collapse"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body" id="ticketsLogContainer">
                            ${logHtml}
                        </div>
                    </div>
                </div>`;

            return newItem;
        }

        // Empty
        function UpdateTicketValidationConditions(response) {
            // Empty
        }

        // |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
        // |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
        // fucntion only for Analyse Update
        function updateAnalyseSection_ListOptions(response) {
            var { equipements, problems, ticket, role } = response;

            // Update Save Changes Button
            var saveButton = document.getElementById('save_analyse_changes');
            var saveButton2 = document.getElementById('save_final_analysis_report');
            if (ticket.status > 1 && role > 3) {
                saveButton.hidden = true;
                saveButton2.hidden = true;
            } else {
                saveButton.hidden = false;
                saveButton2.hidden = false;
            }

        }

        // |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
        // |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
        // fucntion only for Analyse Update
        function updateRecoverySection(response) {
            var { solutions , ticket, role } = response;

            // // Update Date Recovery input field
            // var dateRecoveryInput = document.getElementById('date_recovery_recovery');
            // if (ticket.latest_recovery_log!=null && ticket.latest_recovery_log.dateRecovery!=null) {
            //     dateRecoveryInput.value = ticket.latest_recovery_log.dateRecovery; // Assuming response.dateRecovery is in 'Y-m-d\TH:i' format
            // }

            // // Add a default empty option
            // var natureSolutionSelect = document.getElementById('nature_solution_recovery');
            // natureSolutionSelect.innerHTML = '<option value=""></option>'; // Clear existing options
            // solutions.forEach(solution => {
            //     var option = document.createElement('option');
            //     option.value = solution.id;
            //     option.textContent = solution.val;
            //     if (ticket.latest_recovery_log && ticket.latest_recovery_log.naruteSolutionID && ticket.latest_recovery_log.get_nature_solution.id == solution.id) {
            //         option.selected = true;
            //     }
            //     natureSolutionSelect.appendChild(option);
            // });

            // Update the visibility of the "Save Changes" button
            var saveButton = document.getElementById('save_recovery_changes');
            if (ticket.status <= 1 || (ticket.status >= 2 && response.role <= 3)) {
                saveButton.hidden = false;
            } else {
                saveButton.hidden = true;
            }

            // Update the visibility of the "Close this Ticket" button container

            var closeButtonContainer = document.getElementById('close-button-container');
            closeButtonContainer.hidden = true;
            if (ticket.status == 1 && ticket.latest_recovery_log!=null && ticket.latest_recovery_log.dateRecovery!=null) {
                closeButtonContainer.hidden = false;
            } else {
                closeButtonContainer.hidden = true;
            }
        }



        // Run the fetchTickets function every 3 seconds
        setInterval(fetchTickets, 3000);
    </script>
@endsection
