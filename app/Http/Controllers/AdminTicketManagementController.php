<?php

namespace App\Http\Controllers;

use App\Models\Aerport;
use App\Models\Equipement;
use App\Models\NatureIncident;
use App\Models\NatureSolution;
use App\Models\Ticket;
use App\Models\TicketOwnership;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminTicketManagementController extends Controller
{
    public function fetchTickets()
    {
        // get list of Tickets
        $listOfTickets = Ticket::all();

        // get list of airports
        $listOfAirport = Aerport::all();

        // get list of equipemnt
        $listOfEquipements = Equipement::all();

        // get list of problems
        $listOfProblems = NatureIncident::all();

        // get list of solutions
        $listOfSolutions = NatureSolution::all();

        $ListOfUsersSuper = User::whereIn('role', [3])
            ->with('latestLoginLog')
            ->get();

        $ListOfUsersnormal = User::whereIn('role', [4])
            ->with('latestLoginLog')
            ->get();


            // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
        $listTransferedTickets = User::find(auth()->user()->id)->getPendingTickets();
        $listTransferedTicketsCount = $listTransferedTickets->count();

        // Get pending closed tickets
        $listPendingCloture = Ticket::where('status', 1)->get();

        // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
        // Count tickets based on user role
        $countPendingClosed = 0;
        $currentUser = auth()->user();
        // Determine the user role
        $userRole = $currentUser->role();

        if ($userRole <= 3) {
            $countPendingClosed = $listPendingCloture->count();
        } else {
            foreach ($listPendingCloture as $item) {
                if ($item->currentOwnerRelation->reserver->id == $currentUser->id) {
                    $countPendingClosed += 1;
                }
            }
        }

        $authUserId = auth()->user()->id; // Get the authenticated user's ID

        // -------------------------
        $JSONlistOfTickets = Ticket::with([
            'latestRecoveryLog.getNatureSolution',
            'latestAnalyseLog.getEquipement',
            'latestAnalyseLog.getNatureIncident',
            'latestAnalyseLog.getNatureIncident',
            'latestAnalyseLog.user',
            'latestAnalyseLog',
            'aerport',
            'currentOwnerRelation.reserver',
        ])->get();
        // -------------------------
        $JSONlistTransferedTickets = Ticket::with([
            'latestRecoveryLog.getNatureSolution',
            'latestAnalyseLog.getEquipement',
            'latestAnalyseLog.getNatureIncident',
            'latestAnalyseLog.getNatureIncident',
            'latestAnalyseLog.user',
            'latestAnalyseLog',
            'aerport',
            'currentOwnerRelation.reserver',
        ])->get();

        // Add custom attributes to the tickets
        $JSONlistTransferedTickets->transform(function ($ticket) {
            $ticket->latestNullStatusOwnership = $ticket->getLatestNullStatusOwnership();
            $ticket->latestNullStatusOwnershipGet = $ticket->getLatestNullStatusOwnershipGet();
            return $ticket;
        });


        // -------------------------
        $JSONlistPendingCloture = Ticket::with([
            'latestRecoveryLog.getNatureSolution',
            'latestAnalyseLog.getEquipement',
            'latestAnalyseLog.getNatureIncident',
            'latestAnalyseLog.user',
            'aerport',
            'currentOwnerRelation.reserver',
        ])->where('status', 1)
        ->get();
        // -------------------------

        return response()->json([
            'airports' => $listOfAirport,
            'equipements' => $listOfEquipements,
            'ListEquipements' => $listOfEquipements,
            'solutions' => $listOfSolutions,
            'problems' => $listOfProblems,
            'tickets' => $listOfTickets,
            'users' => $ListOfUsersnormal,
            'transeferTicketCount' => $listTransferedTicketsCount,
            'TicketsPendingClotureCount' => $countPendingClosed,

            'userID'=> $authUserId,

            'JSONtickets' => $JSONlistOfTickets,
            'JSONtranseferTicket' => $JSONlistTransferedTickets,
            'JSONTicketsPendingCloture' => $JSONlistPendingCloture,

            'role'=>$userRole,

            'getNbrOwnedticket' => User::find(auth()->user()->id)->getAssignedTickets()->count(),
        ]);
    }

    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||

    //
    public function index()
    {
        // get list of airports
        $listOfAirport = Aerport::all();
        // get list of equipemnt
        $listOfEquipements = Equipement::all();
        // get list of problems
        $listOfProblems = NatureIncident::all();
        // get list of solutions
        $listOfSolutions = NatureSolution::all();
        // get list of Tickets
        $listOfTickets = Ticket::all();

        $ListOfUsersSuper = User::whereIn('role', [3])
            ->with('latestLoginLog')
            ->get();

        $ListOfUsersnormal = User::whereIn('role', [4])
            ->with('latestLoginLog')
            ->get();

        $listTransferedTickets = User::find(auth()->user()->id)->getPendingTickets();

        $listPendingCloture = Ticket::where('status', 1)->get();

        return view('ticketManagement')->with([
            'airports' => $listOfAirport,
            'equipements' => $listOfEquipements,
            'solutions' => $listOfSolutions,
            'problems' => $listOfProblems,
            'tickets' => $listOfTickets,
            'users' => $ListOfUsersnormal,
            'transeferTicket' => $listTransferedTickets,
            'TicketsPendingCloture' => $listPendingCloture,

            'getNbrOwnedticket' => User::find(auth()->user()->id)->getOwnedTickets(),
        ]);
    }

    public function delete(Request $req)
    {
        // Validate the incoming request data
        $req->validate([
            'id' => 'required|integer',
            'type' => 'required|string',
        ]);

        $itemId = $req->id;
        $itemType = $req->type;

        switch ($itemType) {
            case 'airport':
                $item = Aerport::find($itemId);
                if ($item) {
                    $item->delete();
                    return response()->json(['success' => true, 'message' => 'Airport deleted successfully.']);
                }
                break;

            case 'equipment':
                $item = Equipement::find($itemId);
                if ($item) {
                    $item->delete();
                    return response()->json(['success' => true, 'message' => 'Equipment deleted successfully.']);
                }
                break;

            case 'problem':
                $item = NatureIncident::find($itemId);
                if ($item) {
                    $item->delete();
                    return response()->json(['success' => true, 'message' => 'Incident deleted successfully.']);
                }
                break;

            case 'solution':
                $item = NatureSolution::find($itemId);
                if ($item) {
                    $item->delete();
                    return response()->json(['success' => true, 'message' => 'Solution deleted successfully.']);
                }
                break;

            default:
                return response()->json(['success' => false, 'message' => 'Invalid item type.'], 400);
        }

        return response()->json(['success' => false, 'message' => 'Item not found.'], 404);
    }

    public function edit(Request $req)
    {
        if ($req->type == 'EAir') {
            // Validate the incoming request data
            $req->validate([
                'code' => 'required|max:255',
                'location' => 'string|max:255',
                'address' => 'string|max:255', // Add validation for address if needed
            ]);

            // Find the airport by ID
            $airport = Aerport::find($req->id);

            if (!$airport) {
                return response()->json(['message' => 'Airport not found'], 404);
            }

            // Update the airport attributes
            $airport->update([
                'code' => $req->code,
                'location' => $req->location,
                'address' => $req->address, // Add address update
            ]);

            // Optionally, you can return a response indicating success or failure
            return response()->json(['message' => 'Airport updated successfully', 'airport' => $airport]);
        } else if ($req->type == 'EProblem') {
            // Validate the incoming request data
            $req->validate([
                'Ptype' => 'required|max:255', // Assuming 'type' is the name field
                'description' => 'string|max:255',
            ]);

            // Find the problem type by ID
            $problemType = NatureIncident::find($req->id);

            if (!$problemType) {
                return response()->json(['message' => 'Problem Type not found'], 404);
            }

            // Update the problem type attributes
            $problemType->update([
                'val' => $req->Ptype, // Assuming 'val' is the type field
                'desc' => $req->description,
            ]);

            // Optionally, you can return a response indicating success or failure
            return response()->json(['message' => 'Problem Type updated successfully', 'problemType' => $problemType]);
        } else if ($req->type == 'ESolution') {
            // Validate the incoming request data
            $req->validate([
                'Stype' => 'required|string|max:255', // Assuming 'type' is the name field
                'description' => 'string|max:255',
            ]);

            // Find the solution type by ID
            $solutionType = NatureSolution::find($req->id);

            if (!$solutionType) {
                return response()->json(['message' => 'Solution Type not found'], 404);
            }

            // Update the solution type attributes
            $solutionType->update([
                'val' => $req->Stype, // Assuming 'val' is the type field
                'desc' => $req->description,
            ]);

            // Optionally, you can return a response indicating success or failure
            return response()->json(['message' => 'Solution Type updated successfully', 'solutionType' => $solutionType]);
        } else if ($req->type == 'EEqu') {
            // Validate the incoming request data
            $req->validate([
                'name' => 'required|string|max:255',
            ]);

            // Find the solution type by ID
            $equipement = Equipement::find($req->id);

            if (!$equipement) {
                return response()->json(['message' => 'Solution Type not found'], 404);
            }

            // Update the solution type attributes
            $equipement->update([
                'equipement' => $req->name
            ]);

            // Optionally, you can return a response indicating success or failure
            return response()->json(['message' => 'Solution Type updated successfully', 'solutionType' => $equipement]);
        }

        return response()->json(['success' => true]);
    }

    public function addNew(Request $req)
    {
        // Determine the type of item being added
        $type = $req->type;
        if ($type == 'NAir') {
            // Validate the request data
            $req->validate([
                'code' => 'required',
                'location' => 'required',
                'address' => 'required',
            ]);

            // Create a new airport instance using create method
            Aerport::create([
                'code' => $req->code,
                'location' => $req->location,
                'address' => $req->address
            ]);

            return response()->json(['success' => true, 'message' => 'Airport added successfully']);
        } elseif ($type == 'NEqu') {
            // Validate the request data
            $req->validate([
                'equipement' => 'required|unique:equipements,equipement',
            ]);

            // Create a new equipment instance using create method
            Equipement::create([
                'equipement' => $req->equipement
            ]);

            return response()->json(['success' => true, 'message' => 'Equipment added successfully']);
        } elseif ($type == 'NProblem') {
            // Validate the request data
            $req->validate([
                'problemType' => 'required|unique:nature_incidents,val'
            ]);

            // Create a new problem type instance using create method
            NatureIncident::create([
                'val' => $req->problemType,
                'desc' => $req->problemDescription
            ]);

            return response()->json(['success' => true, 'message' => 'Problem type added successfully']);
        } elseif ($type == 'NSolution') {
            // Validate the request data
            $req->validate([
                'solutionType' => 'required|unique:nature_solutions,val'
            ]);

            // Create a new solution type instance using create method
            NatureSolution::create([
                'val' => $req->solutionType,
                'desc' => $req->solutionDescription
            ]);

            return response()->json(['success' => true, 'message' => 'Solution type added successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid type provided']);
        }
    }

    public function newTicket(Request $req)
    {
        // Validate request data
        $req->validate([
            'title' => 'required|string|max:255',
            'airport' => 'required|integer|exists:aerports,id',
            'dateIncident' => 'required',
            'ContactReclamation' => 'required|string|max:255',
            'SupportNotification' => 'required|string|max:255',
        ]);

        $dateIncident = \Carbon\Carbon::parse($req->dateIncident)->format('Y-m-d H:i:s');

        // Create a new ticket
        $ticket = Ticket::create([
            'title' => $req->title,
            'desc' => $req->desc != null ? $req->desc : '',
            'AerportID' => $req->airport,
            'DateIncident' => $dateIncident,
            'contactReclamation' => $req->ContactReclamation,
            'NaturNotification' => $req->SupportNotification,
            'NTicket' => '234532',
            'status' => 0,
            'creatorID' => auth()->user()->id
        ]);

        TicketOwnership::create([
            'ticketID' => $ticket->id,
            'ownerID' => 0,
            'reseverID' => auth()->user()->id,
            'statu' => true,
            'respond_at' => Carbon::now('Africa/Casablanca'),
            'forced' => true
        ]);

        // Return success response
        return response()->json(['success' => true, 'message' => 'Ticket created successfully!']);
    }

    public function tranformTicket(Request $req)
    {
        // Validate the incoming request data
        $req->validate([
            'UserId' => 'required|exists:users,id', // UserId must be present, an integer, and exist in the users table
            'ticketId' => 'required|exists:tickets,id', // ticketId must be present, an integer, and exist in the tickets table
            'isForced' => 'required', // isForced must be present and a boolean
        ]);
        $isForce = ($req->isForced == "true");

        $ticketIds = explode(',', $req->ticketId);

        // Loop through each ticket ID
        foreach ($ticketIds as $ticketId) {
            // Validate if the ticket ID exists in the tickets table
            if (!Ticket::where('id', $ticketId)->exists()) {
                return response()->json(['error' => 'Invalid ticket ID: ' . $ticketId], 422);
            }

            // Create a new TicketOwnership record for each ticket
            TicketOwnership::create([
                'ticketID' => $ticketId,
                'ownerID' => auth()->user()->id,
                'reseverID' => $req->UserId,
                'forced' => $isForce,
                'statu' => $isForce ? true : null,
                'respond_at' => $isForce ? Carbon::now('Africa/Casablanca') : null,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Ticket created successfully!', 'req' => $req->all()]);
    }

    public function tranformTicketToMe(Request $req)
    {
        // Validate the incoming request data
        $req->validate([
            'ticketId' => 'required|exists:tickets,id'
        ]);
        TicketOwnership::create([
            'ticketID' => $req->ticketId,
            'ownerID' => 0,
            'reseverID' => auth()->user()->id,
            'statu' => true,
            'respond_at' => Carbon::now('Africa/Casablanca'),
            'forced' => true
        ]);

        return response()->json(['success' => true, 'message' => 'Ticket created successfully!', 'req' => $req->all()]);
    }

    public function tranformTicketRespond(Request $req)
    {
        $ticket = Ticket::find($req->Id);
        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found.',
            ], 404);
        }

        $ownership = $ticket->getLatestNullStatusOwnershipGet();

        if ($ticket->getLatestNullStatusOwnership()) {
            // Update the ownership record
            TicketOwnership::find($ownership->id)->update([
                'statu' => $req->status,
                'respond_at' => Carbon::now('Africa/Casablanca')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ownership status updated successfully.',
                'data' => $ownership
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No ownership found with null status.',
                'data' => $ownership
            ], 404); // Optional: Set the HTTP status code to 404 (Not Found)
        }
    }
}
