<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AttendanceExportForPayroll implements FromView, WithColumnWidths, WithStyles
{
    protected $attendanceData;

    public function __construct($attendanceData)
    {
        $this->attendanceData = $attendanceData;
    }

    public function view(): View
    {
        return view('exports.attendance_report_payroll', [
            'attendanceData' => $this->attendanceData,
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 13,  // Emp ID
            'B' => 30,  // Employee FullName
            'C'  => 30,
            'D' => 27,  // Duty Hours to be rendered
            'E' => 25,  // Total Time Rendered
            'F' => 21,  // Final Time Deduction
            'G' => 21,  // Total Late
            'H' => 21,  // Total Undertime
            
        ];
    }

    public function styles(Worksheet $sheet)
{
    // Define border style
    $borderStyle = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'], // Black color
            ],
        ],
    ];

    // Define center alignment
    $centerAlignment = [
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
    ];

    // Define left alignment for column B
    $leftAlignment = [
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_LEFT,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
    ];

    // Merge cells A1:I1
    $sheet->mergeCells('A1:H1');

    // Bold and center-align the header row
    $sheet->getStyle('A1:H1')->getFont()->setBold(true);
    $sheet->getStyle('A1:H1')->applyFromArray($centerAlignment);

    // Merge cells A2:I2
    $sheet->mergeCells('A2:H2');

    // Center-align the content of the merged cells in row 2
    $sheet->getStyle('A2:H2')->applyFromArray($centerAlignment);

    // Bold the Emp ID column (row 2)
    $sheet->getStyle('A2:H2')->getFont()->setBold(true);
    $sheet->getStyle('A3:H3')->getFont()->setBold(true);

    // Apply center alignment to all columns except B
    $highestRow = $sheet->getHighestRow();
    $sheet->getStyle('A1:A' . $highestRow)->applyFromArray($centerAlignment);
    $sheet->getStyle('C1:H' . $highestRow)->applyFromArray($centerAlignment);

    // Apply left alignment to column B
    $sheet->getStyle('B1:B' . $highestRow)->applyFromArray($leftAlignment);

    // Add space below each row
    for ($row = 2; $row <= $highestRow; $row++) {
        $sheet->getRowDimension($row)->setRowHeight(20); // Set row height for spacing
    }

    // Apply borders to all cells in the used range
    $sheet->getStyle('A1:H' . $highestRow)->applyFromArray($borderStyle);
}

}