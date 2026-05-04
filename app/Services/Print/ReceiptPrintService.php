<?php
namespace App\Services\Print;

use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptPrintService
{
    public static function handle($sale)
    {
        $pdf = Pdf::loadView('receipt.print', [
            'sale' => $sale
        ])->setPaper([0, 0, 226.77, 600]); // 58mm

        return $pdf->stream('receipt.pdf');
    }
}