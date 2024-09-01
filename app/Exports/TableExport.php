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
        foreach ($tickets as $ticket) {
            $creationYearMonth = Carbon::parse($ticket->created_at)->format('Ym'); // Use ticket creation date for YYYYMM
            $ticketNumber = $creationYearMonth . $ticket->id; // Format ticket number as YYYYMMID

            // Convert dates to Carbon objects
            $dateIncident = Carbon::parse($ticket->DateIncident);
            $dateRecovery = Carbon::parse($ticket->latestRecoveryLog->dateRecovery);
            $dateCloture = Carbon::parse($ticket->DateCloture);
            $dateCreatedAt = Carbon::parse($ticket->created_at); // Add one hour

            $durreIncident = $this->getDuration($dateIncident, $dateRecovery);
            $durreTicket = $this->getDuration($dateCreatedAt, $dateCloture);

            $data[] = [
                $ticketNumber,
                $ticket->id,
                $ticket->aerport->address . '-' . $ticket->aerport->location,
                $dateIncident->format('Y-m-d H:i:s'),
                $dateCreatedAt->format('Y-m-d H:i:s'),
                $dateRecovery->format('Y-m-d H:i:s'),
                $dateCloture->format('Y-m-d H:i:s'),

                $durreIncident['days'] != 0 ? $durreIncident['days'] : '00',
                $durreIncident['hours'] != 0 ? $durreIncident['hours'] : '00',
                $durreIncident['minutes'] != 0 ? $durreIncident['minutes'] : '00',

                $durreTicket['days'] != 0 ? $durreTicket['days'] : '00',
                $durreTicket['hours'] != 0 ? $durreTicket['hours'] : '00',
                $durreTicket['minutes'] != 0 ? $durreTicket['minutes'] : '00',

                $this->getTotalMinutes($dateIncident, $dateRecovery) != 0 ? $this->getTotalMinutes($dateIncident, $dateRecovery) : '00',
                $this->getTotalMinutes($dateCreatedAt, $dateCloture) != 0 ? $this->getTotalMinutes($dateCreatedAt, $dateCloture) : '00',

                $this->getTotalHours($dateIncident, $dateRecovery) != 0 ? $this->getTotalHours($dateIncident, $dateRecovery) : '00',
                $this->getTotalHours($dateCreatedAt, $dateCloture) != 0 ? $this->getTotalHours($dateCreatedAt, $dateCloture) : '00',

                $ticket->latestAnalyseLog->getNatureIncident->val,
                $ticket->latestAnalyseLog->operatoreID ? $ticket->latestAnalyseLog->getOperatore->NTicket : '',
            ];
        }

        return $data;
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
        $headerRow = 1; // Header row number
        $style = [
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

        $sheet->getStyle('A' . $headerRow . ':S' . $headerRow)->applyFromArray($style);

        // Adjust row height
        $sheet->getRowDimension($headerRow)->setRowHeight(25); // Increase row height

        // Adjust column widths
        $columns = range('A', 'S'); // Adjust column range as needed
        foreach ($columns as $column) {
            $sheet->getColumnDimension($column)->setWidth(20); // Set column width
        }
    }

    private function getDuration($startDate, $endDate)
    {
        $interval = $startDate->diff($endDate);

        // Extract days, hours, and minutes
        $days = $interval->days;
        $hours = $interval->h;
        $minutes = $interval->i;

        // Format the result to return an associative array
        return [
            'days' => $days,
            'hours' => $hours,
            'minutes' => $minutes
        ];
    }

    // Aggregated Duration Calculations
    private function getTotalHours($startDate, $endDate): float
    {
        $interval = $startDate->diff($endDate);
        return ($interval->days * 24) + $interval->h + ($interval->i / 60) + ($interval->s / 3600);
    }

    private function getTotalMinutes($startDate, $endDate): float
    {
        $interval = $startDate->diff($endDate);
        return ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i + ($interval->s / 60);
    }
}
