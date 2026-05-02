<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Print\BarcodePrintService;

class PrintController extends Controller
{
    //
    public function barcode(Request $request)
    {
        //  dd($request->all());
        return BarcodePrintService::handle($request->all());
    }
}
