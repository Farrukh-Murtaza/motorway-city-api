<?php

namespace App\Http\Controllers;

use App\Http\Resources\AppDataResource;
use App\Models\Person;
use App\Models\Plot;
use App\Models\PlotSale;
use Illuminate\Http\Request;

class AppDataController extends Controller
{
    public function getAppData()
    {
        $data = [
            'people' => Person::all(),
            'plots' => Plot::with('activePlotSale.customer')->get(),
            'plotSales' => PlotSale::with(['customer', 'installments'])->get(),
        ];

        return new AppDataResource($data);
    }
}
