<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Print\BarcodePrintService;
use App\Services\Print\ReceiptPrintService;
use App\Models\Sale;
use App\Models\Company;

class PrintController extends Controller
{
    //
    public function barcode(Request $request)
    {
        //  dd($request->all());
        return BarcodePrintService::handle($request->all());
    }

    public function receipt(Request $request)
    {
        $sale = Sale::with('items.product')
            ->findOrFail($request->sale_id);
        $company = Company::first();

        return app(ReceiptPrintService::class)
            ->handle($sale,$company);
    }
}
