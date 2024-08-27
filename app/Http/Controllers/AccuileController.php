<?php

namespace App\Http\Controllers;

use App\Models\Aerport;
use App\Models\NatureIncident;
use App\Models\NatureSolution;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class AccuileController extends Controller
{
    //
    public function index(){
        $ListOfTickets = $this->getUserTicketLogs();
        return view('HomePage')->with([
            'ListOfTickets' => $ListOfTickets,
            'lifeCyleOfTickets' => $this->getTicketStats($ListOfTickets),
            'MonthlyTicketStats' => $this->getMonthlyTicketStats($ListOfTickets),
            'AvergaeTimeOfTickets' => $this->getTicketDataLine($ListOfTickets),
            'ProblemStatistics' => $this->getProblemStatistics(),
            'SolutionStatistics' => $this->getSolutionStatistics(),
            'HeaderInfoNbrTotalTickets' => $this->getTicketStatistics(),
            'AerportTicketRealtion' => $this->getTicketDataBar(),
            'top5Users'=> $this->getUserActivityData(),
        ]);
    }

    public function fetch(){
        $ListOfTickets = $this->getUserTicketLogs();
        return response()->json([
            'ListOfTickets' => $ListOfTickets,
            'lifeCyleOfTickets' => $this->getTicketStats($ListOfTickets),
            'MonthlyTicketStats' => $this->getMonthlyTicketStats($ListOfTickets),
            'AvergaeTimeOfTickets' => $this->getTicketDataLine($ListOfTickets),
            'ProblemStatistics' => $this->getProblemStatistics(),
            'SolutionStatistics' => $this->getSolutionStatistics(),
            'HeaderInfoNbrTotalTickets' => $this->getTicketStatistics(),
            'AerportTicketRealtion' => $this->getTicketDataBar(),
            'top5Users'=> $this->getUserActivityData(),
        ]);
    }

    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // functions for tickets logs
    private function GetInfoOfticket($ticketID){
        $ticket = Ticket::find($ticketID);

        if (!$ticket) {
            return null;
        }
        // ->where('creatorID', $userId)

        // $user = User::find($userId);

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
                        'email' => $log->user->email, // Include useremailD
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
                    'id' => $log->user->id,
                    'email' => $log->user->email,
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
                    $existingLog['LogData']->naruteSolutionID === $log->naruteSolutionID) {
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
                            'id' => $ticket->getOwnerAtDateTime($log->dateRecovery)->id,
                            'email' => $ticket->getOwnerAtDateTime($log->dateRecovery)->email,
                            'name' => $ticket->getOwnerAtDateTime($log->dateRecovery)->Fname,
                            'imgURL' => asset($ticket->getOwnerAtDateTime($log->dateRecovery)->imgUrl),
                        ],
                        'logTypeIndex' => 7,
                        'logType' => 'Recovery Log [Recovery]',
                        'date' => Carbon::parse($log->dateRecovery)->format('Y-m-d H:i:s'),
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
                        'id' => $ticket->getOwnerAtDateTime($log->clotureDate)->id,
                        'email' => $ticket->getOwnerAtDateTime($log->clotureDate)->email,
                        'name' => $ticket->getOwnerAtDateTime($log->clotureDate)->Fname,
                        'imgURL' => asset($ticket->getOwnerAtDateTime($log->clotureDate)->imgUrl),
                    ],
                    'logTypeIndex' => 8,
                    'logType' => 'Cloture Log',
                    'date' => Carbon::parse($log->clotureDate)->format('Y-m-d H:i:s'),
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
        foreach ($transferLogs as $log) {
            $logs[] = [
                'user' => [
                    'id' => $log->owner->id,
                    'email' => $log->owner->email,
                    'name' => $log->owner->Fname,
                    'imgURL' => asset($log->owner->imgUrl),
                ],
                'logTypeIndex' => 1,
                'logType' => 'Transfer Owner',
                'date' => Carbon::parse($log->created_at)->format('Y-m-d H:i:s'),
                'LogData' => $log,
            ];
        }

        // Add Validation Logs
        foreach ($ValidationLogs as $log) {
            $logs[] = [
                'user' => [
                    'id' => $ticket->validation->user->id,
                    'email' => $ticket->validation->user->email,
                    'name' => $ticket->validation->user->Fname,
                    'imgURL' => asset($ticket->validation->user->imgUrl),
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
                    'id' => $ticket->validation->user->id,
                    'email' => $ticket->validation->user->email,
                    'name' => $ticket->validation->user->Fname,
                    'imgURL' => asset($ticket->validation->user->imgUrl),
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
                    'id' => $ticket->getOwnerAtDateTime($ticket->DateCloture)->id,
                    'email' => $ticket->getOwnerAtDateTime($ticket->DateCloture)->email,
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
                'id' => $ticket->user->id,
                'email' => $ticket->user->email,
                'name' => $ticket->user->Fname,
                'imgURL' => asset($ticket->user->imgUrl),
            ],
            'logTypeIndex' => 0,
            'logType' => 'Ticket Creation',
            'date' => Carbon ::parse($ticket->created_at)->format('Y-m-d H:i:s'),
            'LogData' => '',
        ];

        return $logs;

    }

    private function getUserTicketLogs()
    {
        // Retrieve all tickets associated with the user
        $tickets = Ticket::with([
            'latestRecoveryLog.getNatureSolution',
            'latestAnalyseLog.getOperatore',
            'latestAnalyseLog.getEquipement',
            'latestAnalyseLog.getNatureIncident',
            'latestAnalyseLog.user',
            'latestAnalyseLog',
            'aerport',
            'parent',
            'currentOwnerRelation.reserver',
        ])->get();

        $userTicketsLogs = [];

        foreach ($tickets as $ticket) {
            // Call the GetInfoOfticket function for each ticket
            $logs = $this->GetInfoOfticket( $ticket->id);
            // If logs are not empty, add the ticket to the result
            if (!empty($logs)) {
                $userTicketsLogs[] = [
                    'ticket' => $ticket,
                    'logs' => $logs
                ];
            }
        }

        return $userTicketsLogs;
    }

    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // fucntion for the  nbr of ticket created ect...
    private function getTicketStats($userTicketsLogs) {
        $stats = [
            'today' => ['created' => 0, 'closed' => 0, 'recovered' => 0, 'workedOn' => 0],
            'lastWeek' => ['created' => 0, 'closed' => 0, 'recovered' => 0, 'workedOn' => 0],
            'lastMonth' => ['created' => 0, 'closed' => 0, 'recovered' => 0, 'workedOn' => 0],
            'lastYear' => ['created' => 0, 'closed' => 0, 'recovered' => 0, 'workedOn' => 0],
        ];

        $now = Carbon::now();

        foreach ($userTicketsLogs as $ticketLog) {
            $ticket = $ticketLog['ticket'];
            $logs = $ticketLog['logs'];

            $hasWorkedonit_today = false;
            $hasWorkedonit_last_week = false;
            $hasWorkedonit_last_month = false;
            $hasWorkedonit_last_year = false;
            foreach ($logs as $log) {
                $logDate = Carbon::parse($log['date']);

                if ($logDate->isToday()) {
                    $this->incrementStats($log, $stats['today'],$hasWorkedonit_today);
                }

                if ($logDate->greaterThanOrEqualTo($now->copy()->subWeek()) && $logDate->lessThanOrEqualTo($now)) {
                    $this->incrementStats($log, $stats['lastWeek'],$hasWorkedonit_last_week);
                }

                if ($logDate->greaterThanOrEqualTo($now->copy()->subMonth()) && $logDate->lessThanOrEqualTo($now)) {
                    $this->incrementStats($log, $stats['lastMonth'],$hasWorkedonit_last_month);
                }

                if ($logDate->greaterThanOrEqualTo($now->copy()->subYear()) && $logDate->lessThanOrEqualTo($now)) {
                    $this->incrementStats($log, $stats['lastYear'],$hasWorkedonit_last_year);
                }
            }
        }

        return $stats;
    }

    private function incrementStats($log, &$statPeriod ,&$hasWorkedOn) {
        switch ($log['logTypeIndex']) {
            case 0: // Ticket Creation
                $statPeriod['created']++;
                if(!$hasWorkedOn){
                    $statPeriod['workedOn']++;
                    $hasWorkedOn = true;
                }
                break;
            case 3: // Analysis Log (worked on)
            case 4: // Recovery Log (worked on)
            case 1: // Transfer Owner (worked on)
                if(!$hasWorkedOn){
                    $statPeriod['workedOn']++;
                    $hasWorkedOn = true;
                }
                break;
            case 7: // Recovery Log [Recovery]
                $statPeriod['recovered']++;
                if(!$hasWorkedOn){
                    $statPeriod['workedOn']++;
                    $hasWorkedOn = true;
                }
                break;
            case 8: // Cloture Log (closed)
                $statPeriod['closed']++;
                if(!$hasWorkedOn){
                    $statPeriod['workedOn']++;
                    $hasWorkedOn = true;
                }
                break;
            default :
                // $hasWorkedOn = false;
                break;
        }
    }

    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // fucntion for the  nbr of ticket created ect... of the current year
    private function getMonthlyTicketStats($userTicketsLogs) {
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $stats = [
            'months' => $months,
            'created' => array_fill(0, 12, 0),
            'closed' => array_fill(0, 12, 0),
            'recovered' => array_fill(0, 12, 0),
        ];

        foreach ($userTicketsLogs as $ticketLog) {
            $logs = $ticketLog['logs'];

            foreach ($logs as $log) {
                $logDate = Carbon::parse($log['date']);
                $month = $logDate->month - 1; // month is 1-based, array is 0-based

                switch ($log['logTypeIndex']) {
                    case 0: // Ticket Creation
                        $stats['created'][$month]++;
                        break;
                    case 8: // Cloture Log (closed)
                        $stats['closed'][$month]++;
                        break;
                    case 7: // Recovery Log [Recovery]
                        $stats['recovered'][$month]++;
                        break;
                    default:
                        // Handle other log types if necessary
                        break;
                }
            }
        }

        return $stats;
    }

    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // fucntion for the average time of ticket in h ....
    function getTicketDataLine($userTicketsLogs)
    {
        // Initialize data array
        $ticketDataLine = [
            'thisWeek' => [
                'labels' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                'avgCreationTime' => array_fill(0, 7, 0),
                'avgCloseTime' => array_fill(0, 7, 0),
                'avgIncidentDuration' => array_fill(0, 7, 0),
                'avgLifespan' => array_fill(0, 7, 0),
            ],
            'thisMonth' => [
                'labels' => array_map(fn($i) => 'Week ' . ($i + 1), range(0, 3)), // Example for 4 weeks
                'avgCreationTime' => array_fill(0, 4, 0),
                'avgCloseTime' => array_fill(0, 4, 0),
                'avgIncidentDuration' => array_fill(0, 4, 0),
                'avgLifespan' => array_fill(0, 4, 0),
            ],
            'thisYear' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'avgCreationTime' => array_fill(0, 12, 0),
                'avgCloseTime' => array_fill(0, 12, 0),
                'avgIncidentDuration' => array_fill(0, 12, 0),
                'avgLifespan' => array_fill(0, 12, 0),
            ],
        ];

        // Helper arrays to accumulate data
        $counters = [
            'thisWeek' => array_fill(0, 7, 0),
            'thisMonth' => array_fill(0, 4, 0),
            'thisYear' => array_fill(0, 12, 0),
        ];

        // Current date
        $now = Carbon::now();

        // Determine the start of the current week, month, and year
        $startOfWeek = $now->startOfWeek();
        $startOfMonth = $now->startOfMonth();
        $startOfYear = $now->startOfYear();

        // Iterate through user tickets logs
        foreach ($userTicketsLogs as $item) {
            $ticket = $item['ticket'];

            // Convert dates to Carbon instances
            $createdAt = Carbon::parse($ticket->created_at);
            $dateIncident = $ticket->DateIncident ? Carbon::parse($ticket->DateIncident) : null;
            $dateCloture = $ticket->DateCloture ? Carbon::parse($ticket->DateCloture) : null;
            $dateRecovery = $ticket->hasRecoveryLogs() && $ticket->latestRecoveryLog->dateRecovery ? Carbon::parse($ticket->latestRecoveryLog->dateRecovery) : null;

            // Skip calculations if any required date is null
            if (!$createdAt || !$dateIncident || !$dateRecovery || !$dateCloture) {
                continue;
            }

            // Calculate times
            $creationTime = $dateIncident->diffInHours($createdAt);
            $closeTime = $dateCloture->diffInHours($dateRecovery);
            $incidentDuration = $dateIncident->diffInHours($dateRecovery);
            $lifespan = $dateCloture->diffInHours($createdAt);

            // Determine day of the week, week of the month, and month of the year
            $dayOfWeek = $createdAt->dayOfWeekIso - 1; // 0 = Monday, 6 = Sunday
            $weekOfMonth = (int)ceil($createdAt->day / 7) - 1; // 0-indexed week
            $monthOfYear = $createdAt->month - 1; // 0 = January, 11 = December

            // Accumulate values for this week
            if ($createdAt->greaterThanOrEqualTo($startOfWeek)) {
                $ticketDataLine['thisWeek']['avgCreationTime'][$dayOfWeek] += $creationTime;
                $ticketDataLine['thisWeek']['avgCloseTime'][$dayOfWeek] += $closeTime;
                $ticketDataLine['thisWeek']['avgIncidentDuration'][$dayOfWeek] += $incidentDuration;
                $ticketDataLine['thisWeek']['avgLifespan'][$dayOfWeek] += $lifespan;
                $counters['thisWeek'][$dayOfWeek]++;
            }

            // Accumulate values for this month
            if ($createdAt->greaterThanOrEqualTo($startOfMonth)) {
                $ticketDataLine['thisMonth']['avgCreationTime'][$weekOfMonth] += $creationTime;
                $ticketDataLine['thisMonth']['avgCloseTime'][$weekOfMonth] += $closeTime;
                $ticketDataLine['thisMonth']['avgIncidentDuration'][$weekOfMonth] += $incidentDuration;
                $ticketDataLine['thisMonth']['avgLifespan'][$weekOfMonth] += $lifespan;
                $counters['thisMonth'][$weekOfMonth]++;
            }

            // Accumulate values for this year
            if ($createdAt->greaterThanOrEqualTo($startOfYear)) {
                $ticketDataLine['thisYear']['avgCreationTime'][$monthOfYear] += $creationTime;
                $ticketDataLine['thisYear']['avgCloseTime'][$monthOfYear] += $closeTime;
                $ticketDataLine['thisYear']['avgIncidentDuration'][$monthOfYear] += $incidentDuration;
                $ticketDataLine['thisYear']['avgLifespan'][$monthOfYear] += $lifespan;
                $counters['thisYear'][$monthOfYear]++;
            }
        }

        // Calculate averages
        foreach (['thisWeek', 'thisMonth', 'thisYear'] as $period) {
            foreach ($ticketDataLine[$period]['avgCreationTime'] as $index => $value) {
                if ($counters[$period][$index] > 0) {
                    $ticketDataLine[$period]['avgCreationTime'][$index] /= $counters[$period][$index];
                    $ticketDataLine[$period]['avgCloseTime'][$index] /= $counters[$period][$index];
                    $ticketDataLine[$period]['avgIncidentDuration'][$index] /= $counters[$period][$index];
                    $ticketDataLine[$period]['avgLifespan'][$index] /= $counters[$period][$index];
                }
            }
        }

        return $ticketDataLine;
    }

    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // fucntion for the incident nature
    private function getProblemStatistics()
    {
        // Initialize the statistics array
        $problemStatistics = [];

        // Fetch all NatureIncident (Problem Nature)
        $natureIncidents = NatureIncident::all();

        // Fetch all tickets and their latest analyse log
        $tickets = Ticket::with(['latestAnalyseLog'])->get();

        foreach ($natureIncidents as $natureIncident) {
            // Initialize counters for each time period
            $todayCount = 0;
            $thisWeekCount = 0;
            $thisMonthCount = 0;
            $thisYearCount = 0;
            $totalCount = 0;

            foreach ($tickets as $ticket) {
                // Skip if the ticket does not have an analyse log or the log doesn't match the current nature incident
                if (!$ticket->latestAnalyseLog || $ticket->latestAnalyseLog->naruteIncidentID != $natureIncident->id) {
                    continue;
                }

                // Parse the creation date once
                $created_at = Carbon::parse($ticket->created_at);

                // Count tickets based on creation date
                if ($created_at->isToday()) {
                    $todayCount++;
                }
                if ($created_at->isCurrentWeek()) {
                    $thisWeekCount++;
                }
                if ($created_at->isCurrentMonth()) {
                    $thisMonthCount++;
                }
                if ($created_at->isCurrentYear()) {
                    $thisYearCount++;
                }

                // Count all tickets
                $totalCount++;
            }

            // Add the data to the statistics array
            $problemStatistics[] = [
                'problem_name' => $natureIncident->val,
                'tickets_today' => $todayCount,
                'tickets_this_week' => $thisWeekCount,
                'tickets_this_month' => $thisMonthCount,
                'tickets_this_year' => $thisYearCount,
                'total_tickets' => $totalCount,
            ];
        }

        return $problemStatistics;
    }

    private function getSolutionStatistics()
    {
        // Initialize the statistics array
        $solutionStatistics = [];

        // Fetch all NatureSolution (Solution Nature)
        $natureSolutions = NatureSolution::all();

        // Fetch all tickets and their latest recovery log
        $tickets = Ticket::with(['latestRecoveryLog'])->get();

        foreach ($natureSolutions as $natureSolution) {
            // Initialize counters for each time period
            $todayCount = 0;
            $thisWeekCount = 0;
            $thisMonthCount = 0;
            $thisYearCount = 0;
            $totalCount = 0;

            foreach ($tickets as $ticket) {
                // Skip if the ticket does not have a recovery log or the log doesn't match the current nature solution
                if (!$ticket->latestRecoveryLog || $ticket->latestRecoveryLog->naruteSolutionID != $natureSolution->id) {
                    continue;
                }

                // Parse the creation date once
                $created_at = Carbon::parse($ticket->created_at);

                // Count tickets based on creation date
                if ($created_at->isToday()) {
                    $todayCount++;
                }
                if ($created_at->isCurrentWeek()) {
                    $thisWeekCount++;
                }
                if ($created_at->isCurrentMonth()) {
                    $thisMonthCount++;
                }
                if ($created_at->isCurrentYear()) {
                    $thisYearCount++;
                }

                // Count all tickets
                $totalCount++;
            }

            // Add the data to the statistics array
            $solutionStatistics[] = [
                'solution_name' => $natureSolution->val,
                'tickets_today' => $todayCount,
                'tickets_this_week' => $thisWeekCount,
                'tickets_this_month' => $thisMonthCount,
                'tickets_this_year' => $thisYearCount,
                'total_tickets' => $totalCount,
            ];
        }

        return $solutionStatistics;
    }

    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // fucntion for the generla inftormation
    private function getTicketStatistics()
    {
        // Get the total number of tickets
        $totalTickets = Ticket::count();

        // Assuming that closed tickets have a specific status, e.g., status = 'closed'
        // Replace 'status' and 'closed' with your actual column name and closed status value
        $openTickets = Ticket::where('status', 1)->count();

        // Return an associative array with the statistics
        return [
            'total_tickets' => $totalTickets,
            'open_tickets' => $openTickets,
        ];
    }

    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // fucntion for the thicket and ear port relationgship
    private function getTicketDataBar()
    {
        // Define the labels
        $labels = Aerport::pluck('location')->toArray();

        // Initialize arrays for each time period
        $thisWeekTickets = [];
        $thisMonthTickets = [];
        $thisYearTickets = [];

        // Get current date
        $now = Carbon::now();

        // Iterate over each airport
        foreach (Aerport::all() as $aerport) {
            // Count tickets for this week
            $thisWeekTickets[] = $aerport->tickets()
                ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->count();

            // Count tickets for this month
            $thisMonthTickets[] = $aerport->tickets()
                ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                ->count();

            // Count tickets for this year
            $thisYearTickets[] = $aerport->tickets()
                ->whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])
                ->count();
        }

        // Prepare the data for the response
        $ticketDataBar = [
            'labels' => $labels,
            'thisWeek' =>  $thisWeekTickets,
            'thisMonth' => $thisMonthTickets,
            'thisYear' => $thisYearTickets
        ];

        // Return the data as a JSON response
        return $ticketDataBar;
    }

    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // fucntion for the top 5 users activity
    private function getUserActivityData()
    {
        // Retrieve all user ticket logs
        $userTicketsLogs = $this->getUserTicketLogs();

        // Initialize an array to store user activity counts
        $userActivityCounts = [];

        // Define the start of each time range
        $now = Carbon::now();
        $startOfWeek = $now->startOfWeek();
        $startOfMonth = $now->startOfMonth();
        $startOfYear = $now->startOfYear();

        foreach ($userTicketsLogs as $ticketLog) {
            foreach ($ticketLog['logs'] as $log) {
                $userId = $log['user']['id'];
                if($userId == 0 || (User::find($userId)->role() < 2)){
                    continue;
                }
                $logDate = Carbon::parse($log['date']);

                if (!isset($userActivityCounts[$userId])) {
                    $userActivityCounts[$userId] = [
                        'name' => $log['user']['name'],
                        'email' => $log['user']['email'] ?? 'Not Available',
                        'imgSrc' => $log['user']['imgURL'],
                        'thisWeek' => 0,
                        'thisMonth' => 0,
                        'thisYear' => 0
                    ];
                }

                // Update activity counts based on log date
                if ($logDate->greaterThanOrEqualTo($startOfWeek)) {
                    $userActivityCounts[$userId]['thisWeek']++;
                }
                if ($logDate->greaterThanOrEqualTo($startOfMonth)) {
                    $userActivityCounts[$userId]['thisMonth']++;
                }
                if ($logDate->greaterThanOrEqualTo($startOfYear)) {
                    $userActivityCounts[$userId]['thisYear']++;
                }
            }
        }

        // Function to get top 5 users by activity for a given time range
        function getTopUsers($activityCounts, $range)
        {
            // Sort users by activity count in descending order
            usort($activityCounts, function ($a, $b) use ($range) {
                return $b[$range] <=> $a[$range];
            });

            // Get top 5 users
            return array_slice($activityCounts, 0, 5);
        }

        // Prepare results for each time range
        $topUsers = [
            'thisWeek' => getTopUsers($userActivityCounts, 'thisWeek'),
            'thisMonth' => getTopUsers($userActivityCounts, 'thisMonth'),
            'thisYear' => getTopUsers($userActivityCounts, 'thisYear'),
        ];

        return $topUsers;
    }
}
