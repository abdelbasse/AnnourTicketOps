<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UserInfoAdminUserManagamentController extends Controller
{
    public function fetch($id){
        $user = User::find($id);
        $UserTickets = $this->getUserTicketLogs($id);
        return response()->json([
            'user' => $user,
            'UserTickets' => $UserTickets,
            'lifeCyleOfTickets' => $this->getTicketStats($UserTickets),
            'MonthlyTicketStats' => $this->getMonthlyTicketStats($UserTickets),
            'AvergaeTimeOfTickets' => $this->getTicketDataLine($UserTickets),
            'ActivityHeatMap'=> $this->getUserActivityHeatmapData($UserTickets),
        ]);
    }

    //
    public function index($id){
        $user = User::find($id);
        $UserTickets = $this->getUserTicketLogs($id);
        return view('Admin.userPageInfo')->with([
            'user' => $user,
            'UserTickets' => $UserTickets,
            'lifeCyleOfTickets' => $this->getTicketStats($UserTickets),
            'MonthlyTicketStats' => $this->getMonthlyTicketStats($UserTickets),
            'AvergaeTimeOfTickets' => $this->getTicketDataLine($UserTickets),
            'ActivityHeatMap'=> $this->getUserActivityHeatmapData($UserTickets),
        ]);
    }

    public function change(Request $req,$id)
    {
        if ($req->type == 'pass') {
            $validation = $req->validate([
                'new_password' => 'required|confirmed|min:8',
                'new_password_confirmation' => 'required'
            ]);
            $User = User::find($id);
            $User->update([
                'password' => Hash::make($req->new_password)
            ]);
            return redirect()->route('personal-profile');
        } else if ($req->type == 'pic') {
            $req->validate([
                'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Check if the user has a custom profile image
            if (User::find($id)->imgUrl != 'img/users/user.png') {
                // Delete the current profile image
                $imagePath = public_path(User::find($id)->imgUrl);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }
            // Save the profile image
            if ($req->hasFile('profile_image')) {
                $image = $req->file('profile_image');
                $imageName = time() . '_' . uniqid() . '_' . $id . '.' . $image->getClientOriginalExtension();
                $imagePath = 'img/users/avatar';
                $image->move(public_path($imagePath), $imageName);

                $User = User::find($id);
                $User->update([
                    'imgUrl' => $imagePath . '/' . $imageName,
                ]);

                // Return the URL or other information about the uploaded image
                return response()->json(['success' => 'Profile image uploaded successfully.']);
            }
            return response()->json(['error' => 'Error uploading profile image.']);
        }
        return response()->json(['success' => false]);
    }

    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // functions for tickets logs
    private function GetInfoOfticket($userId , $ticketID){
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
        ])->where('UserID', $userId)->get();

        $RecoveryLogs = $ticket->recoveryLogs()->with([
            'getNatureSolution',
            'user',
        ])->where('UserID', $userId)->get();

        $transferLogs = $ticket->ticketOwnerShip()->with([
            'reserver',
            'owner',
        ])->where('reseverID', $userId)->where('ownerID', $userId)->get();

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
                        'imgURL' => $log->user->imgUrl,
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
                    'imgURL' => $log->user->imgUrl,
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
                            'name' => $ticket->getOwnerAtDateTime($log->dateRecovery)->Fname,
                            'imgURL' => $ticket->getOwnerAtDateTime($log->dateRecovery)->imgUrl,
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
                        'name' => $ticket->getOwnerAtDateTime($log->clotureDate)->Fname,
                        'imgURL' => $ticket->getOwnerAtDateTime($log->clotureDate)->imgUrl,
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
                    'name' => $log->owner->Fname,
                    'imgURL' => $log->owner->imgUrl,
                ],
                'logTypeIndex' => 1,
                'logType' => 'Transfer Owner',
                'date' => Carbon::parse($log->created_at)->format('Y-m-d H:i:s'),
                'LogData' => $log,
            ];
        }

        // Add Validation Logs
        foreach ($ValidationLogs as $log) {
            if($ticket->validation->user->id == $userId){
                $logs[] = [
                    'user' => [
                        'name' => $ticket->validation->user->Fname,
                        'imgURL' => $ticket->validation->user->profile_image,
                    ],
                    'logTypeIndex' => 5,
                    'logType' => 'Add Comments',
                    'date' => Carbon::parse($log->created_at)->format('Y-m-d H:i:s'),
                    'LogData' => $log,
                ];
            }
        }

        // Add Validation Log if it exists
        if ($ticket->hasValidation()) {
                if($ticket->validation->user->id == $userId){
                $logs[] = [
                    'user' => [
                        'name' => $ticket->validation->user->Fname,
                        'imgURL' => $ticket->validation->user->profile_image,
                    ],
                    'logTypeIndex' => 6,
                    'logType' => 'Validation',
                    'date' => Carbon::parse($ticket->validation->created_at)->format('Y-m-d H:i:s'),
                    'LogData' => $ticket->validation,
                ];
            }
        }

        if($ticket->DateCloture!=null){
            if($ticket->getOwnerAtDateTime($ticket->DateCloture)->id == $userId){
            $logs[] = [
                'user' => [
                    'name' => $ticket->getOwnerAtDateTime($ticket->DateCloture)->Fname,
                    'imgURL' => $ticket->getOwnerAtDateTime($ticket->DateCloture)->imgUrl,
                ],
                'logTypeIndex' => 8,
                'logType' => 'Ticket Cloture',
                'date' => Carbon::parse($ticket->DateCloture)->format('Y-m-d H:i:s'),
                'LogData' => '',
            ];
        }
        }

        // Sort logs by date
        usort($logs, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });


        // Add Ticket Creation Log
        if($ticket->user->id == $userId){
            $logs[] = [
                'user' => [
                    'name' => $ticket->user->Fname,
                    'imgURL' => $ticket->user->imgUrl,
                ],
                'logTypeIndex' => 0,
                'logType' => 'Ticket Creation',
                'date' => Carbon ::parse($ticket->created_at)->format('Y-m-d H:i:s'),
                'LogData' => '',
            ];
        }

        return $logs;

    }

    private function getUserTicketLogs($userId)
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
            $logs = $this->GetInfoOfticket($userId, $ticket->id);

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
    // fucntion for the hours and activity ....
    function getUserActivityHeatmapData($userTicketsLogs)
    {
        // Initialize the data array for the heatmap
        $data = [];

        // Initialize a 2D array for hours of the day (0 to 23) and days of the week (0 to 6)
        for ($hour = 0; $hour < 24; $hour++) {
            for ($day = 0; $day < 7; $day++) {
                $data[$day][$hour] = 0; // Default value for each hour of each day
            }
        }

        // Process each ticket and its logs
        foreach ($userTicketsLogs as $item) {
            $logs = $item['logs'];

            // Process each log entry
            foreach ($logs as $log) {
                $logDate = Carbon::parse($log['date']);
                $dayOfWeek = $logDate->dayOfWeekIso - 1; // 0 = Monday, 6 = Sunday
                $hourOfDay = $logDate->hour; // 0 to 23

                // Increment the count for the corresponding day and hour
                if (isset($data[$dayOfWeek][$hourOfDay])) {
                    $data[$dayOfWeek][$hourOfDay]++;
                }
            }
        }

        // Convert data into the format required by the heatmap
        $formattedData = [];
        foreach ($data as $day => $hours) {
            foreach ($hours as $hour => $count) {
                $formattedData[] = [$day, $hour, $count];
            }
        }

        return $formattedData;
    }


}
