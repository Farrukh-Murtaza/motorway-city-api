<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponseTrait;
use App\Http\Resources\OccupationResource;
use App\Models\Occupation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OccupationController extends Controller
{
     use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         return  $this->success(OccupationResource::collection(Occupation::orderBy('id')->get()));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:occupations,name',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $occupation = Occupation::create($validator->validated());
        return $this->success(new OccupationResource($occupation), 'Occupation created', 201);
    
    }

    /**
     * Display the specified resource.
     */
    public function show(Occupation $occupation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Occupation $occupation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Occupation $occupation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Occupation $occupation)
    {
       $occupation->delete();
        return $this->success(null, 'Occupation deleted');
    }
}
