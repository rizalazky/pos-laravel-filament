<?php
namespace App\Services\Print;

use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptPrintService
{
    public static function handle($sale,$company)
    {
        $pdf = Pdf::loadView('receipt.print', [
            'sale' => $sale,
            'company' => $company
        ])->setPaper([0, 0, 226.77, 600]); // 58mm

        return $pdf->stream('receipt.pdf');
    }
}