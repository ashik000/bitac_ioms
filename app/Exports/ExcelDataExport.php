<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelDataExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{

    protected $data;
    protected $headings;

    /**
     * Write code on Method
     *
     * @param $data
     * @param $headings
     */
    public function __construct($data,$headings)
    {
        $this->data = $data;
        $this->headings = $headings;
    }


    /**
     * @return array
     */
    public function array(): array
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return $this->headings;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => array('rgb' => 'FFFFFF')
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => '4990a6',
                    ],
                ],
            ],
        ];

    }
}
