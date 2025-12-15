<?php

namespace App\Http\Controllers;

use App\Http\Resources\NomineeRelationResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\NomineeRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NomineeRelationController extends Controller
{

    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return  $this->success(NomineeRelationResource::collection(NomineeRelation::orderBy('id')->get()));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:nominee_relations,name',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $occupation = NomineeRelation::create($validator->validated());
        return $this->success(new NomineeRelationResource($occupation), 'Nominee Relation created', 201);
    
    }

    /**
     * Display the specified resource.
     */
    public function show(NomineeRelation $nomineeRelation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NomineeRelation $nomineeRelation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NomineeRelation $nomineeRelation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NomineeRelation $nomineeRelation)
    {
        //
    }
}
