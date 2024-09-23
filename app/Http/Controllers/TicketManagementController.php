<?php

namespace App\Http\Controllers;

use App\Models\AnalyseLog;
use App\Models\Comment;
use App\Models\Equipement;
use App\Models\NatureIncident;
use App\Models\NatureSolution;
use App\Models\OperatorTicket;
use App\Models\RecoveryLog;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Validation;
use App\Models\RepportComment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\Response;

class TicketManagementController extends Controller
{

    public function fetchJSONRealTimeData($id)
    {
        $ticket = Ticket::with([
            'latestRecoveryLog.getNatureSolution',
            'latestAnalyseLog.getOperatore',
            'latestAnalyseLog.getEquipement',
            'latestAnalyseLog.getNatureIncident',
            'latestAnalyseLog.user',
            'latestAnalyseLog',
            'aerport',
            'parent',
            'currentOwnerRelation.reserver',
        ])->find($id);

        $tickets = Ticket::with([
            'aerport',
        ])->where('TicketParent', null)->where('id', '!=', $id)
            ->get();

        $canEdit = false;
        if(auth()->user()->id == $ticket->currentOwnerRelation->id){
            $canEdit = true;
        }


        $parentTickets = collect([]);
        $currentTicket = $ticket;

        while ($currentTicket && $currentTicket->parent) {
            // Load the parent ticket with aerport relation
            $parent = $currentTicket->parent()->with('aerport')->first();
            if ($parent) {
                $parentTickets->push($parent);
                $currentTicket = $parent;
            } else {
                break; // Exit loop if no parent found
            }
        }

        // Assign order in reverse, starting from the length of the collection
        $totalParents = $parentTickets->count();
        foreach ($parentTickets as $index => $parentTicket) {
            $parentTicket->order = $totalParents - $index;
        }

        $listOfEquipements = Equipement::all();
        // get list of problems
        $listOfProblems = NatureIncident::all();

        $listOfSolutions = NatureSolution::all();

        $ListOfUsersnormal = User::whereIn('role', [4])
            ->with('latestLoginLog')
            ->get();

        $AnalyseLogs = $ticket->analyseLogs()->with([
            'user',
            'getEquipement',
            'getOperatore',
            'getNatureIncident',
        ])->get();

        $RecoveryLogs = $ticket->recoveryLogs()->with([
            'getNatureSolution',
            'user',
        ])->get();

        $transferLogs = $ticket->ticketOwnerShip()->with([
            'reserver',
            'owner',
        ])->get();

        $ValidationLogs = ($ticket->hasValidation()==true)? $ticket->validation->comments : [];

        // Prepare the list of logs with structure
        $logs = [];

        // Add Analysis Logs
        foreach ($AnalyseLogs as $log) {
            // Create a unique key based on user, equipment, incident nature, NSM status, rapportBody, and operatoreID
            $uniqueKey = ($log->getEquipement->equipement ?? 'Not Available') . '-' .
                        $log->getNSMStatu() . '-' .
                        ($log->getNatureIncident ? $log->getNatureIncident->val : '') . '-' .
                        ($log->repportBody ?? '') . '-' .
                        ($log->operatoreID ?? '');

            // Check if this unique key is already in the $logs array
            $isDuplicate = false;
            foreach ($logs as $existingLog) {
                $existingKey = ($existingLog['LogData']->getEquipement->equipement ?? 'Not Available') . '-' .
                            $existingLog['LogData']->getNSMStatu() . '-' .
                            ($existingLog['LogData']->getNatureIncident ? $existingLog['LogData']->getNatureIncident->val : '') . '-' .
                            ($existingLog['LogData']->repportBody ?? '') . '-' .
                            ($existingLog['LogData']->operatoreID ?? '');

                if ($uniqueKey === $existingKey) {
                    $isDuplicate = true;
                    break;
                }
            }

            // Add the log to the $logs array only if it's not a duplicate
            if (!$isDuplicate) {
                $logs[] = [
                    'user' => [
                        'id' => $log->user->id, // Include user ID
                        'name' => $log->user->Fname,
                        'imgURL' => asset($log->user->imgUrl),
                    ],
                    'logTypeIndex' => 3,
                    'logType' => 'Analysis Log',
                    'date' => Carbon::parse($log->created_at)->format('Y-m-d H:i:s'),
                    'LogData' => $log,
                ];
            }
        }

        // Add Recovery Logs
        foreach ($RecoveryLogs as $log) {
            // Add Recovery Log
            $recoveryLog = [
                'user' => [
                    'name' => $log->user->Fname,
                    'imgURL' => asset($log->user->imgUrl),
                ],
                'logTypeIndex' => 4,
                'logType' => 'Recovery Log',
                'date' => Carbon::parse($log->created_at)->format('Y-m-d H:i:s'),
                'LogData' => $log,
            ];

            // Check if Recovery Log is unique based on natureSolutionID, repportBody, and dateRecovery
            $recoveryUniqueKey = ($log->repportBody ?? '') . '-' .
                                 ($log->naruteSolutionID ?? '');

            $exists = false;
            foreach ($logs as $existingLog) {
                if ($existingLog['logType'] === 'Recovery Log' &&
                    $existingLog['LogData']->repportBody === $log->repportBody &&
                    $existingLog['LogData']->naruteSolutionID === $log->naruteSolutionID &&
                    $existingLog['LogData']->dateRecovery === $log->dateRecovery) {
                    $exists = true;
                    break;
                }
            }

            // Add the unique Recovery Log
            if (!$exists) {
                $logs[] = $recoveryLog;

                // Add Ticket Recovered Log if dateRecovery is not null
                if ($log->dateRecovery !== null) {
                    $newRecoveryLog = [
                        'user' => [
                            'name' => $ticket->getOwnerAtDateTime($log->dateRecovery)->Fname,
                            'imgURL' => asset($ticket->getOwnerAtDateTime($log->dateRecovery)->imgUrl),
                        ],
                        'logTypeIndex' => 7,
                        'logType' => 'Recovery Log [Recovery]',
                        'date' => Carbon::parse($log->created_at,'Africa/Casablanca')->format('Y-m-d H:i:s'),
                        'LogData' => $log,
                    ];

                    // Check if Ticket Recovered Log is unique based on user and date
                    $exists = false;
                    foreach ($logs as $existingLog) {
                        if ($existingLog['logType'] === 'Recovery Log [Recovery]' &&
                            $existingLog['date'] === $newRecoveryLog['date'] &&
                            $existingLog['user']['name'] === $newRecoveryLog['user']['name']) {
                            $exists = true;
                            break;
                        }
                    }

                    // Add the unique Ticket Recovered Log
                    if (!$exists) {
                        $logs[] = $newRecoveryLog;
                    }
                }
            }

            // Add Cloture Log if applicable
            if ($log->clotureDate !== null) {
                $newClotureLog = [
                    'user' => [
                        'name' => $ticket->getOwnerAtDateTime($log->clotureDate)->Fname,
                        'imgURL' => asset($ticket->getOwnerAtDateTime($log->clotureDate)->imgUrl),
                    ],
                    'logTypeIndex' => 8,
                    'logType' => 'Cloture Log',
                    'date' => Carbon::parse($log->clotureDate,'Africa/Casablanca')->format('Y-m-d H:i:s'),
                    'LogData' => $log,
                ];

                // Check if Cloture Log is unique based on user and date
                $exists = false;
                foreach ($logs as $existingLog) {
                    if ($existingLog['logType'] === 'Cloture Log' &&
                        $existingLog['date'] === $newClotureLog['date'] &&
                        $existingLog['user']['name'] === $newClotureLog['user']['name']) {
                        $exists = true;
                        break;
                    }
                }

                // Add the unique Cloture Log
                if (!$exists) {
                    $logs[] = $newClotureLog;
                }
            }
        }


        // Add Transfer Logs
        $loopOne = true;
        foreach ($transferLogs as $log) {
            if($loopOne){
                $loopOne = false;
                continue;
            }
            $logs[] = [
                'user' => [
                    'name' => $log->owner->Fname,
                    'imgURL' => asset($log->owner->imgUrl),
                ],
                'logTypeIndex' => 1,
                'logType' => 'Transfer Owner',
                'date' => Carbon::parse($log->created_at)->format('Y-m-d H:i:s'),
                'LogData' => $log,
            ];
            // if ($log->owner->id != 0) {
            //     $logs[] = [
            //         'user' => [
            //             'name' => $log->reserver->Fname,
            //             'imgURL' => $log->reserver->imgUrl,
            //         ],
            //         'logTypeIndex' => 2,
            //         'logType' => 'Transfer Resever Stat',
            //         'date' => $log->respond_at,
            //         'LogData' => $log,
            //     ];
            // }
        }

        // Add Validation Logs
        foreach ($ValidationLogs as $log) {
            $logs[] = [
                'user' => [
                    'name' => $ticket->validation->user->Fname,
                    'imgURL' => asset($ticket->validation->user->profile_image),
                ],
                'logTypeIndex' => 5,
                'logType' => 'Add Comments',
                'date' => Carbon::parse($log->created_at)->format('Y-m-d H:i:s'),
                'LogData' => $log,
            ];
        }

        // Add Validation Log if it exists
        if ($ticket->hasValidation()) {
            $logs[] = [
                'user' => [
                    'name' => $ticket->validation->user->Fname,
                    'imgURL' => asset($ticket->validation->user->profile_image),
                ],
                'logTypeIndex' => 6,
                'logType' => 'Validation',
                'date' => Carbon::parse($ticket->validation->created_at)->format('Y-m-d H:i:s'),
                'LogData' => $ticket->validation,
            ];
        }

        if($ticket->DateCloture!=null){
            $logs[] = [
                'user' => [
                    'name' => $ticket->getOwnerAtDateTime($ticket->DateCloture)->Fname,
                    'imgURL' => asset($ticket->getOwnerAtDateTime($ticket->DateCloture)->imgUrl),
                ],
                'logTypeIndex' => 8,
                'logType' => 'Ticket Cloture',
                'date' => Carbon::parse($ticket->DateCloture)->format('Y-m-d H:i:s'),
                'LogData' => '',
            ];
        }

        // Sort logs by date
        usort($logs, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });


        // Add Ticket Creation Log
        $logs[] = [
            'user' => [
                'name' => $ticket->user->Fname,
                'imgURL' => asset($ticket->user->imgUrl),
            ],
            'logTypeIndex' => 0,
            'logType' => 'Ticket Creation',
            'date' => Carbon::parse($ticket->created_at)->format('Y-m-d H:i:s'),
            'LogData' => '',
        ];


        $authUserId = auth()->user()->id; // Get the authenticated user's ID

        $currentUser = auth()->user();
        // Determine the user role
        $userRole = $currentUser->role();

        return response()->json([
            'parentTickets' => $parentTickets,
            'ticket' => $ticket,
            'ticketsOptions' => $tickets,
            'equipements' => $listOfEquipements,
            'problems' => $listOfProblems,
            'solutions' => $listOfSolutions,
            'users' => $ListOfUsersnormal,

            'TicketLogs' => $logs,

            'userID'=> $authUserId,

            'role'=>$userRole,


            'CanEdit' => $canEdit,
        ]);
    }

    //
    public function index($id)
    {
        $ticket = Ticket::find($id);
        $canEdit = false;
        if(auth()->user()->id == $ticket->currentOwnerRelation->id){
            $canEdit = true;
        }
        $tickets = Ticket::with([
            'aerport',
        ])->where('TicketParent', null)->where('id', '!=', $id)
            ->get();


        $parentTickets = collect([]);
        $currentTicket = $ticket;

        while ($currentTicket && $currentTicket->parent) {
            // Load the parent ticket with aerport relation
            $parent = $currentTicket->parent()->with('aerport')->first();
            if ($parent) {
                $parentTickets->push($parent);
                $currentTicket = $parent;
            } else {
                break; // Exit loop if no parent found
            }
        }

        $listOfEquipements = Equipement::all();
        // get list of problems
        $listOfProblems = NatureIncident::all();

        $listOfSolutions = NatureSolution::all();

        $ListOfUsersnormal = User::whereIn('role', [4])
            ->with('latestLoginLog')
            ->get();

        $AnalyseLogs = $ticket->analyseLogs()->with([
            'user',
            'getEquipement',
            'getNatureIncident',
            'getOperatore',
        ])->get();

        $RecoveryLogs = $ticket->recoveryLogs()->with([
            'getNatureSolution',
            'user',
        ])->get();

        $transferLogs = $ticket->ticketOwnerShip()->with([
            'reserver',
            'owner',
        ])->get();

        $ValidationLogs = ($ticket->hasValidation()==true)? $ticket->validation->comments : [];

        // Prepare the list of logs with structure
        $logs = [];

        // Add Analysis Logs
        foreach ($AnalyseLogs as $log) {
            // Create a unique key based on user, equipment, incident nature, NSM status, rapportBody, and operatoreID
            $uniqueKey = ($log->getEquipement->equipement ?? 'Not Available') . '-' .
                        $log->getNSMStatu() . '-' .
                        ($log->getNatureIncident ? $log->getNatureIncident->val : '') . '-' .
                        ($log->repportBody ?? '') . '-' .
                        ($log->operatoreID ?? '');

            // Check if this unique key is already in the $logs array
            $isDuplicate = false;
            foreach ($logs as $existingLog) {
                $existingKey = ($existingLog['LogData']->getEquipement->equipement ?? 'Not Available') . '-' .
                            $existingLog['LogData']->getNSMStatu() . '-' .
                            ($existingLog['LogData']->getNatureIncident ? $existingLog['LogData']->getNatureIncident->val : '') . '-' .
                            ($existingLog['LogData']->repportBody ?? '') . '-' .
                            ($existingLog['LogData']->operatoreID ?? '');

                if ($uniqueKey === $existingKey) {
                    $isDuplicate = true;
                    break;
                }
            }

            // Add the log to the $logs array only if it's not a duplicate
            if (!$isDuplicate) {
                $logs[] = [
                    'user' => [
                        'id' => $log->user->id, // Include user ID
                        'name' => $log->user->Fname,
                        'imgURL' => asset($log->user->imgUrl),
                    ],
                    'logTypeIndex' => 3,
                    'logType' => 'Analysis Log',
                    'date' => Carbon::parse($log->created_at)->format('Y-m-d H:i:s'),
                    'LogData' => $log,
                ];
            }
        }

        // Add Recovery Logs
        foreach ($RecoveryLogs as $log) {
            // Add Recovery Log
            $recoveryLog = [
                'user' => [
                    'name' => $log->user->Fname,
                    'imgURL' => asset($log->user->imgUrl),
                ],
                'logTypeIndex' => 4,
                'logType' => 'Recovery Log',
                'date' => Carbon::parse($log->created_at)->format('Y-m-d H:i:s'),
                'LogData' => $log,
            ];

            // Check if Recovery Log is unique based on natureSolutionID, repportBody, and dateRecovery
            $recoveryUniqueKey = ($log->repportBody ?? '') . '-' .
                                 ($log->naruteSolutionID ?? '');

            $exists = false;
            foreach ($logs as $existingLog) {
                if ($existingLog['logType'] === 'Recovery Log' &&
                    $existingLog['LogData']->repportBody === $log->repportBody &&
                    $existingLog['LogData']->naruteSolutionID === $log->naruteSolutionID &&
                    $existingLog['LogData']->dateRecovery === $log->dateRecovery) {
                    $exists = true;
                    break;
                }
            }

            // Add the unique Recovery Log
            if (!$exists) {
                $logs[] = $recoveryLog;

                // Add Ticket Recovered Log if dateRecovery is not null
                if ($log->dateRecovery !== null) {
                    $newRecoveryLog = [
                        'user' => [
                            'name' => $ticket->getOwnerAtDateTime($log->dateRecovery)->Fname,
                            'imgURL' => asset($ticket->getOwnerAtDateTime($log->dateRecovery)->imgUrl),
                        ],
                        'logTypeIndex' => 7,
                        'logType' => 'Recovery Log [Recovery]',
                        'date' => Carbon::parse($log->created_at,'Africa/Casablanca')->format('Y-m-d H:i:s'),
                        'LogData' => $log,
                    ];

                    // Check if Ticket Recovered Log is unique based on user and date
                    $exists = false;
                    foreach ($logs as $existingLog) {
                        if ($existingLog['logType'] === 'Recovery Log [Recovery]' &&
                            $existingLog['date'] === $newRecoveryLog['date'] &&
                            $existingLog['user']['name'] === $newRecoveryLog['user']['name']) {
                            $exists = true;
                            break;
                        }
                    }

                    // Add the unique Ticket Recovered Log
                    if (!$exists) {
                        $logs[] = $newRecoveryLog;
                    }
                }
            }

            // Add Cloture Log if applicable
            if ($log->clotureDate !== null) {
                $newClotureLog = [
                    'user' => [
                        'name' => $ticket->getOwnerAtDateTime($log->clotureDate)->Fname,
                        'imgURL' => asset($ticket->getOwnerAtDateTime($log->clotureDate)->imgUrl),
                    ],
                    'logTypeIndex' => 8,
                    'logType' => 'Cloture Log',
                    'date' => Carbon::parse($log->clotureDate,'Africa/Casablanca')->format('Y-m-d H:i:s'),
                    'LogData' => $log,
                ];

                // Check if Cloture Log is unique based on user and date
                $exists = false;
                foreach ($logs as $existingLog) {
                    if ($existingLog['logType'] === 'Cloture Log' &&
                        $existingLog['date'] === $newClotureLog['date'] &&
                        $existingLog['user']['name'] === $newClotureLog['user']['name']) {
                        $exists = true;
                        break;
                    }
                }

                // Add the unique Cloture Log
                if (!$exists) {
                    $logs[] = $newClotureLog;
                }
            }
        }


        // Add Transfer Logs
        $loopOne = true;
        foreach ($transferLogs as $log) {
            if($loopOne){
                $loopOne = false;
                continue;
            }
            $logs[] = [
                'user' => [
                    'name' => $log->owner->Fname,
                    'imgURL' => asset($log->owner->imgUrl),
                ],
                'logTypeIndex' => 1,
                'logType' => 'Transfer Owner',
                'date' => Carbon::parse($log->created_at)->format('Y-m-d H:i:s'),
                'LogData' => $log,
            ];
            // if ($log->owner->id != 0) {
            //     $logs[] = [
            //         'user' => [
            //             'name' => $log->reserver->Fname,
            //             'imgURL' => $log->reserver->imgUrl,
            //         ],
            //         'logTypeIndex' => 2,
            //         'logType' => 'Transfer Resever Stat',
            //         'date' => $log->respond_at,
            //         'LogData' => $log,
            //     ];
            // }
        }

        // Add Validation Logs
        foreach ($ValidationLogs as $log) {
            $logs[] = [
                'user' => [
                    'name' => $ticket->validation->user->Fname,
                    'imgURL' => asset($ticket->validation->user->profile_image),
                ],
                'logTypeIndex' => 5,
                'logType' => 'Add Comments',
                'date' => Carbon::parse($log->created_at)->format('Y-m-d H:i:s'),
                'LogData' => $log,
            ];
        }

        // Add Validation Log if it exists
        if ($ticket->hasValidation()) {
            $logs[] = [
                'user' => [
                    'name' => $ticket->validation->user->Fname,
                    'imgURL' => asset($ticket->validation->user->profile_image),
                ],
                'logTypeIndex' => 6,
                'logType' => 'Validation',
                'date' => Carbon::parse($ticket->validation->created_at)->format('Y-m-d H:i:s'),
                'LogData' => $ticket->validation,
            ];
        }

        if($ticket->DateCloture!=null){
            $logs[] = [
                'user' => [
                    'name' => $ticket->getOwnerAtDateTime($ticket->DateCloture)->Fname,
                    'imgURL' => asset($ticket->getOwnerAtDateTime($ticket->DateCloture)->imgUrl),
                ],
                'logTypeIndex' => 8,
                'logType' => 'Ticket Cloture',
                'date' => Carbon::parse($ticket->DateCloture)->format('Y-m-d H:i:s'),
                'LogData' => '',
            ];
        }

        // Sort logs by date
        usort($logs, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });


        // Add Ticket Creation Log
        $logs[] = [
            'user' => [
                'name' => $ticket->user->Fname,
                'imgURL' => asset($ticket->user->imgUrl),
            ],
            'logTypeIndex' => 0,
            'logType' => 'Ticket Creation',
            'date' => Carbon::parse($ticket->created_at)->format('Y-m-d H:i:s'),
            'LogData' => '',
        ];

        return view('TicketPage')->with([
            'parentTickets' => $parentTickets,
            'ticket' => $ticket,
            'ticketsOptions' => $tickets,
            'equipements' => $listOfEquipements,
            'problems' => $listOfProblems,
            'solutions' => $listOfSolutions,
            'users' => $ListOfUsersnormal,

            'TicketLogs' => $logs,

            'CanEdit' => $canEdit,
        ]);
    }

    public function AddTicketLog(Request $req)
    {
        if ($req->type === 'analyse') {
            $operatoreId = null;
            if (!empty($req->operatorNTicket) || !empty($req->operatorName)) {
                // Check if an operator with the same NTicket exists
                $existingOperator = OperatorTicket::where('NTicket', $req->operatorNTicket)
                    ->first();

                if ($existingOperator) {
                    // If the NTicket exists, check if other fields match
                    if (
                        $existingOperator->name === $req->operatorName
                    ) {
                        // If everything matches, use the existing operator
                        $operatoreId = $existingOperator->id;
                    } else {
                        // If the NTicket matches but other fields differ, create a new operator
                        $operatorTicket = OperatorTicket::create([
                            'NTicket' => $req->operatorNTicket,
                            'name' => $req->operatorName,
                            'mail' => $req->operatorMail != null ? $req->operatorMail  : '',
                            'tell' => $req->operatorTell != null ? $req->operatorTell  : '',
                        ]);

                        $operatoreId = $operatorTicket->id;
                    }
                } else {
                    // If the NTicket doesn't exist, create a new operator
                    $operatorTicket = OperatorTicket::create([
                        'NTicket' => $req->operatorNTicket,
                        'name' => $req->operatorName,
                        'mail' => $req->operatorMail,
                        'tell' => $req->operatorTell,
                    ]);

                    $operatoreId = $operatorTicket->id;
                }
            }

            // Create a new AnalyseLog record
            $analyseLog = AnalyseLog::create([
                'TicketID' => $req->ticketId,
                'UserID' => auth()->user()->id, // Get the ID of the currently logged-in user
                'NSMStatu' => $req->nsmStatus,
                'naruteIncidentID' => $req->natureIncidentId,
                'equipementID' => $req->equipementId,
                'operatoreID' => $operatoreId, // Set this to a valid operator ID if applicable
                'repportBody' => $req->body,
            ]);

            // Return a success response
            return response()->json(['message' => 'Analysis log added successfully!', 'data' => $analyseLog], 200);
        } else if ($req->type === 'recovery') {
            if (!empty($req->natureSolution) && !empty($req->dateRecovery)) {
                // Check and update ticket status if less than 3
                $ticket = Ticket::find($req->ticketId);
                if ($ticket && $ticket->status < 1) {
                    $ticket->status = 1;
                    $ticket->save();
                }
            }
            // Create a new AnalyseLog record
            $recoveryLog = RecoveryLog::create([
                'TicketID' => $req->ticketId,
                'UserID' => auth()->user()->id, // Get the ID of the currently logged-in user
                'naruteSolutionID' => $req->natureSolution,
                'dateRecovery' => $req->dateRecovery,
                'repportBody' => $req->body,
            ]);

            // Return a success response
            return response()->json(['message' => 'Recovery log added successfully!', 'data' => $recoveryLog], 200);
        }

        // If the type is not 'analyse', handle other types or return an error
        return response()->json(['message' => 'Invalid log type.'], 400);
    }

    public function clotureTicket(Request $req)
    {
        // Validate the request
        $validated = $req->validate([
            'ticketId' => 'required|integer|exists:tickets,id'
        ]);

        // Retrieve the ticket by its ID
        $ticket = Ticket::find($validated['ticketId']);

        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found.'], 404);
        }

        // Update the closure date
        $ticket->DateCloture = Carbon::now('Africa/Casablanca');
        if ($ticket && $ticket->status < 2) {
            $ticket->status = 2;
        }
        $ticket->save();

        return response()->json(['message' => 'Ticket closed successfully!']);
    }

    public function ValidationTicket(Request $req)
    {
        // Find the ticket by ID
        $ticket = Ticket::find($req->ticketId);

        Validation::create([
            'TicketID' => $req->ticketId,
            'statu' => $req->status == '1' ? true : false,
            'userID' => auth()->user()->id,
        ]);
        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        // Update the status of the ticket
        $ticket->status = $req->status == '1' ? 3 : 4;
        $ticket->save();

        return response()->json(['message' => 'Ticket status updated successfully'], 200);
    }

    public function addComment(Request $req)
    {
        $comment = Comment::create([
            'ValidationID' => $req->validationID,
            'body' => $req->comment,
        ]);
        return response()->json(['message' => 'Comment Added successfully'], 200);
    }

    public function addCommentRapport(Request $req)
    {
        $ticket = Ticket::findOrFail($req->ticketId);
        // Check if the ticket has a recovery log
        if ($ticket->hasRecoveyLog()) {
            $idRecoveryLog = $ticket->latestRecoveryLog->id;

            // Create a new comment
            RepportComment::create([
                'RecoveryIdLog' => $idRecoveryLog, // Use $idRecoveryLog here
                'comment' => $req->comment,
                'userID' => auth()->user()->id,
            ]);

            return response()->json(['message' => 'Comment submitted successfully!']);
        } else {
            return response()->json(['message' => 'No recovery log found for this ticket.'], 404);
        }
    }

    public function setParent(Request $req)
    {
        // Validate the incoming data
        $req->validate([
            'ticketId' => 'required|exists:tickets,id',
            'parentTicketId' => 'required|exists:tickets,id',
        ]);

        // Find the ticket by ID
        $ticket = Ticket::find($req->ticketId);

        if ($ticket) {
            // Update the TicketParent column with the selected parent ticket ID
            $ticket->TicketParent = $req->parentTicketId;
            $ticket->save();

            return response()->json(['success' => true, 'message' => 'Parent ticket updated successfully!']);
        }

        return response()->json(['success' => false, 'message' => 'Ticket not found!'], 404);
    }

    public function getRapport($id)
    {
        $ticket = Ticket::find($id);
        $reportBody = $ticket->latestRecoveryLog->repportBody;

        // Replace image URLs with public path
        $reportBody = str_replace(
            asset('img/Rapports/'),
            public_path('img/Rapports/'),
            $reportBody
        );

        $pdf = PDF::loadView('report', ['ticket' => $ticket, 'reportBody' => $reportBody]);
        return $pdf->download('ticket_report_' . $id . '.pdf');
    }
}
