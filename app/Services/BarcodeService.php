<?php
namespace App\Services;

use Milon\Barcode\Facades\DNS1DFacade as DNS1D;

class BarcodeService
{
    public static function generate(string $code): string
    {
        return DNS1D::getBarcodeHTML($code, 'C128');
    }
}