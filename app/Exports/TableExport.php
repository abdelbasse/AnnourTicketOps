<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Ticket;
use Carbon\Carbon;

class TableExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $ids;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    // Fetch data based on the provided IDs
    public function array(): array
    {
        $tickets = Ticket::whereIn('id', $this->ids)->get();

        $data = [];
        $errorData = []; // Collect invalid rows here

        foreach ($tickets as $ticket) {
            $creationYearMonth = ''.(string)Carbon::parse($ticket->created_at,'Africa/Casablanca')->format('Ym'); // Use ticket creation date for YYYYMM
            $ticketNumber = (string) '0' . $creationYearMonth . (string)$ticket->id; // Format ticket number as YYYYMMID
            $ticketNumber = (string) $ticketNumber;
            // Convert dates to Carbon objects
            $dateIncident = $ticket->DateIncident ? Carbon::parse($ticket->DateIncident, 'Africa/Casablanca') : null;
            $dateRecovery = ($ticket->hasRecoveryLogs() && $ticket->latestRecoveryLog->dateRecovery) ? Carbon::parse($ticket->latestRecoveryLog->dateRecovery, 'Africa/Casablanca') : null;
            $dateCloture = $ticket->DateCloture ? Carbon::parse($ticket->DateCloture, 'Africa/Casablanca') : null;
            $dateCreatedAt = Carbon::parse($ticket->created_at, 'Africa/Casablanca'); // Add one hour

            $durreIncident = $this->getDuration($dateIncident, $dateRecovery);
            $durreTicket = $this->getDuration($dateCreatedAt, $dateCloture);

            $hasError = $ticket->status < 2 || !$ticket->hasRecoveryLogs() || !$ticket->hasAnalyseLogs() || !$dateIncident || !$dateRecovery || !$dateCloture || !$ticket->aerport;

            $row = [
                (string)$ticketNumber.'',
                $ticket->id,
                $ticket->aerport ? $ticket->aerport->address . '-' . $ticket->aerport->location : 'Missing',
                $dateIncident ? $dateIncident->format('Y/m/d') : 'Missing',
                $dateCreatedAt->format('Y/m/d'),
                $dateRecovery ? $dateRecovery->format('Y/m/d') : 'Missing',
                $dateCloture ? $dateCloture->format('Y/m/d') : 'Missing',

                $durreIncident['days'] != 0 ? $durreIncident['days'] : '0',
                $durreIncident['hours'] != 0 ? $durreIncident['hours'] : '0',
                $durreIncident['minutes'] != 0 ? $durreIncident['minutes'] : '0',

                $durreTicket['days'] != 0 ? $durreTicket['days'] : '0',
                $durreTicket['hours'] != 0 ? $durreTicket['hours'] : '0',
                $durreTicket['minutes'] != 0 ? $durreTicket['minutes'] : '0',

                $this->getTotalMinutes($dateIncident, $dateRecovery),
                $this->getTotalMinutes($dateCreatedAt, $dateCloture),

                $this->getTotalHours($dateIncident, $dateRecovery),
                $this->getTotalHours($dateCreatedAt, $dateCloture),

                ( $ticket->hasAnalyseLogs() && $ticket->latestAnalyseLog->naruteIncidentID != null)? $ticket->latestAnalyseLog->getNatureIncident->val : 'Missing',
                ( $ticket->hasAnalyseLogs() && $ticket->latestAnalyseLog->operatoreID ) ? $ticket->latestAnalyseLog->getOperatore->NTicket : '',
            ];

            // If data is missing, add to error list, otherwise add to main list
            if ($hasError) {
                $errorData[] = $row;
            } else {
                $data[] = $row;
            }
        }

        // Append error rows at the end
        return array_merge($data, $errorData);
    }

    // Define the headings for the columns
    public function headings(): array
    {
        return [
            'Ticket-Number',
            'ZAMMA-TKT',
            'SITE',
            'DATE_INCIDENT',
            'DATE_TICKET',
            'DATE_RECOVERY',
            'DATE_CLOTURE_TICKET',
            'Duree Incident (j)',
            'Duree Incident (h)',
            'Duree Incident (m)',
            'Duree ticket (j)',
            'Duree ticket (h)',
            'Duree ticket (m)',
            'DUREE_INCIDENT (min)',
            'DUREE_TICKET (min)',
            'DUREE_INCIDENT (h)',
            'DUREE_TICKET (h)',
            'Nature Incident',
            'N°TICKET OPERATEUR',
        ];
    }

    // Apply styles to the sheet
    public function styles(Worksheet $sheet)
    {
        // Apply styling to the header row
        $headerRow = 1;
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFF'], // White font color
            ],
            'fill' => [
                'fillType' => 'solid',
                'color' => ['argb' => '0000FF'], // Blue background color
            ],
            'alignment' => [
                'horizontal' => 'center',
            ],
        ];

        $sheet->getStyle('A' . $headerRow . ':S' . $headerRow)->applyFromArray($headerStyle);

        // Adjust row height
        $sheet->getRowDimension($headerRow)->setRowHeight(25);

        // Set column widths
        $columns = range('A', 'S');
        foreach ($columns as $column) {
            $sheet->getColumnDimension($column)->setWidth(20);
        }

        // Set column A format to text
        $sheet->getStyle('A')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        // Apply red background and white text to rows with missing data
        $rows = $sheet->getHighestRow();
        for ($row = 2; $row <= $rows; $row++) {
            $cellValue = $sheet->getCell('C' . $row)->getValue();
            if ($cellValue === 'Missing') {
                $sheet->getStyle('A' . $row . ':S' . $row)->applyFromArray([
                    'font' => [
                        'color' => ['argb' => 'FFFFFF'], // White text
                    ],
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['argb' => 'FF0000'], // Red background
                    ],
                ]);
            }
        }
    }

    private function getDuration($startDate, $endDate)
    {
        if($startDate == null || $endDate == null){
            return [
                'days' => 0,
                'hours' => 0,
                'minutes' => 0,
                'seconds' => 0
            ];
        }
        // Create copies of the start and end dates and format them to 'Y-m-d H:i:s'
        $startDateFormatted = $startDate->copy()->format('Y-m-d H:i:s');
        $endDateFormatted = $endDate->copy()->format('Y-m-d H:i:s');

        // Parse the formatted dates back into Carbon instances
        $start = Carbon::createFromFormat('Y-m-d H:i:s', $startDateFormatted);
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $endDateFormatted);

        // Calculate the interval between start and end dates
        $interval = $start->diff($end);

        // Extract days, hours, minutes, and seconds
        $days = $interval->days;
        $hours = $interval->h;
        $minutes = $interval->i;
        $seconds = $interval->s;

        // If there are more than 30 seconds, round the minutes up
        if ($seconds >= 30) {
            $minutes++;
        }

        // Format the result to return an associative array
        return [
            'days' => $days,
            'hours' => $hours,
            'minutes' => $minutes,
            'seconds' => $seconds
        ];
    }


    // Aggregated Duration Calculations
    private function getTotalHours($startDate, $endDate): float
    {
        $interval = $this->getDuration($startDate , $endDate);
        return ($interval['days'] * 24) + $interval['hours'] + ($interval['minutes'] / 60) + ($interval['seconds'] / 3600);
    }

    private function getTotalMinutes($startDate, $endDate): float
    {
        $interval = $this->getDuration($startDate , $endDate);
        return ($interval['days'] * 24 * 60) + ($interval['hours'] * 60) + $interval['minutes'] + ($interval['seconds'] / 60);
    }
}
