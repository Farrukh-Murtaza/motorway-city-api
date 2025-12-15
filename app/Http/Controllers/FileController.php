<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    

    public function show($path)
    {
        $storagePath = storage_path('app/' . $path);

        if (!file_exists($storagePath)) {
            abort(404);
        }

        return response()->file($storagePath);
    }


}
