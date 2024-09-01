<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incident Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
            /* Reduced text size */
        }

        header {
            display: flex;
            justify-content: center;
            margin-bottom: 0;
            font-size: 14px;
            /* Slightly larger for the header */
        }

        header h1 {
            margin: 0;
            font-size: 18px;
            /* Adjusted header title size */
        }

        .section-title {
            background-color: #003366;
            color: white;
            padding: 5px;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table th,
        .info-table td {
            padding: 8px;
            border: 1px solid #ccc;
            text-align: left;
            font-size: 12px;
            /* Match the reduced body text size */
        }

        .info-table th {
            background-color: #f2f2f2;
        }

        .footer {
            text-align: left;
            margin-top: 30px;
            font-size: 10px;
            /* Smaller text for the footer */
            color: gray;
        }

        .container {
            border: 1px solid black;
            padding-top: 15px;
        }

        th,
        td,
        .td {
            padding: 8px;
        }

        th {
            background-color: rgb(171, 237, 255);
            text-align: left;
            max-width: 145px;
            min-width: 145px;
        }
    </style>
</head>

@php
    function getYearFromDateTime($dateTimeString)
    {
        $datetime = new DateTime($dateTimeString);
        return $datetime->format('Y'); // Format as YYYY-MM-DD
    }

    // Function to return the month from a DateTime object
    function getMonthFromDateTime($dateTimeString)
    {
        $datetime = new DateTime($dateTimeString);
        return $datetime->format('m'); // Format as MM
    }

    // Function to return the day from a DateTime object
    function getDayFromDateTime($dateTimeString)
    {
        $datetime = new DateTime($dateTimeString);
        return $datetime->format('d'); // Format as DD
    }

    // Function to return the hour from a DateTime object
    function getHourFromDateTime($dateTimeString)
    {
        $datetime = new DateTime($dateTimeString);
        return $datetime->format('H'); // Format as HH (24-hour)
    }

    // Function to return the minute from a DateTime object
    function getMinuteFromDateTime($dateTimeString)
    {
        $datetime = new DateTime($dateTimeString);
        return $datetime->format('i'); // Format as MM (minutes)
    }

    // Function to return the second from a DateTime object
    function getSecondFromDateTime($dateTimeString)
    {
        $datetime = new DateTime($dateTimeString);
        return $datetime->format('s'); // Format as SS (seconds)
    }

    // Function to return only the date part (YYYY-MM-DD) from a DateTime object
    function getOnlyDateFromDateTime($dateTimeString)
    {
        $datetime = new DateTime($dateTimeString);
        return $datetime->format('Y-m-d'); // Format as YYYY-MM-DD
    }

@endphp

@php
    function diffInDays($startDateTime, $endDateTime)
    {
        $start = new DateTime($startDateTime);
        $end = new DateTime($endDateTime);
        $diff = $start->diff($end);
        return $diff->format('%dj %Hh %imin');
    }

    function diffInHours($startDateTime, $endDateTime)
    {
        $start = new DateTime($startDateTime);
        $end = new DateTime($endDateTime);
        $diff = $start->diff($end);
        $hours = $diff->days * 24 + $diff->h;
        return sprintf('%dh %imin', $hours, $diff->i);
    }

    function diffInMinutes($startDateTime, $endDateTime)
    {
        $start = new DateTime($startDateTime);
        $end = new DateTime($endDateTime);
        $interval = $end->diff($start);
        $minutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;
        return sprintf('%dmin', $minutes);
    }
@endphp

<style>
    img {
        max-width: 400px; /* Adjust this value based on your needs */
        height: auto; /* Maintains aspect ratio */
    }
</style>
<body style="margin: 0; padding: 0px;">
    <header class="footer" style="margin: 0px; padding: 0px; margin-bottom: 8px;">
        <div style="text-align: left;">
            <p style="margin: 0; padding: 2px;">ANNOUR Technologies</p>
            <p style="margin: 0; padding: 2px;">NSM-BPF-ONDA-WAN</p>
        </div>
    </header>

    <div class="container" style="margin: 0; padding-top: 10px;">
        <div class="header">
            <div style="text-align: center;">
                <h2 style="margin: 0px; padding: 0px;">FICHE INCIDENT</h2>
                <p style="margin: 0px; padding: 0px; margin-bottom: 8px;"><strong>N° Ticket :</strong>
                    {{ getYearFromDateTime($ticket->created_at) }}{{ getMonthFromDateTime($ticket->created_at) }}{{ $ticket->id }}
                </p>
            </div>
        </div>
        <table border="1"
            style="width: 100%; margin: 0px; padding: 0px; border-collapse: collapse; table-layout: auto;">
            <tr>
                <td colspan="2" style="margin: 0px; padding: 0px;">
                    <b>
                        <div class="section-title td" style="margin: 0px !important;">Informations de l'Incident
                            (INCIDENT-INFO)</div>
                    </b>
                </td>
            </tr>
            <tr>
                <th>Date / Heure Incident</th>
                <td>{{ getOnlyDateFromDateTime($ticket->DateIncident) }} :
                    {{ getHourFromDateTime($ticket->DateIncident) }}h{{ getMinuteFromDateTime($ticket->DateIncident) }}
                </td>
            </tr>
            <tr>
                <th>Date/Heure Ticket</th>
                <td>{{ getOnlyDateFromDateTime($ticket->created_at) }} :
                    {{ getHourFromDateTime($ticket->created_at) }}h{{ getMinuteFromDateTime($ticket->created_at) }}</td>
            </tr>
            <tr>
                <th>Site</th>
                <td>AEROPORT {{ $ticket->aerport->location }}</td>
            </tr>
            <tr>
                <th>Support De Notification</th>
                <td>
                    {{ $ticket->NaturNotification }}
                </td>
            </tr>
            <tr>
                <th>Contact Réclamation</th>
                <td> {{ $ticket->contactReclamation }} / (NS# {{ $ticket->id }})</td>
            </tr>
            <tr>
                <th>Nature Incident</th>
                <td>{{ $ticket->latestAnalyseLog->getNatureIncident->val }} l'aéroport
                    {{ $ticket->aerport->location }}</td>
            </tr>
            <tr>
                <td colspan="2" style="margin: 0px; padding: 0px;">
                    <b>
                        <div class="section-title td" style="margin: 0px !important;">Information de la Résolution de
                            l'Incident (RECOVERY-INFO)</div>
                    </b>
                </td>
            </tr>
            <tr>
                <th>Date/Heure Recovery</th>
                <td>{{ getOnlyDateFromDateTime($ticket->latestRecoveryLog->dateRecovery) }} :
                    {{ getHourFromDateTime($ticket->latestRecoveryLog->dateRecovery) }}h{{ getMinuteFromDateTime($ticket->latestRecoveryLog->dateRecovery) }}
                </td>
            </tr>
            <tr>
                <th>Date/Heure Clôture Ticket</th>
                <td>{{ getOnlyDateFromDateTime($ticket->DateCloture) }} :
                    {{ getHourFromDateTime($ticket->DateCloture) }}h{{ getMinuteFromDateTime($ticket->DateCloture) }}
                </td>
            </tr>
            <tr>
                {{-- calcul --}}
                <th>Durée de l'Incident</th>
                <td>{{ diffInDays($ticket->DateIncident, $ticket->latestRecoveryLog->dateRecovery) }}</td>
            </tr>
            <tr>
                {{-- calcul --}}
                <th>Durée Traitement Ticket</th>
                <td>{{ diffInDays($ticket->DateIncident, $ticket->DateCloture) }}</td>
            </tr>
            <tr>
                <th>Nature de la solution</th>
                <td>{{ $ticket->latestRecoveryLog->getNatureSolution->val }} @if ($ticket->hasAnalyseLogs()) @if ($ticket->latestAnalyseLog->operatoreID != null) sous Ticket N° : {{ $ticket->latestAnalyseLog->getOperatore->NTicket }} @endif @endif</td>
            </tr>
            <tr>
                <th>Périmètre Incident</th>
                <td>Equipement {{ $ticket->latestAnalyseLog->getEquipement->equipement }}</td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: top;">Description de la solution</th>
                <td>
                    {!! $reportBody !!}
                </td>
            </tr>
        </table>
    </div>

    <footer class="footer">
        <p>ANNOUR Technologies / ONDA</p>
    </footer>
</body>

</html>
