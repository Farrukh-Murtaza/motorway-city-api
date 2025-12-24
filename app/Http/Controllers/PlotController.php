<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlotResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Plot;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PlotController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Plot::with('activePlotSale.customer');
            
            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            // Filter by block
            if ($request->has('block')) {
                $query->where('block', $request->block);
            }
            
            // Filter by category
            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            // Filter by sector
            if ($request->has('sector')) {
                $query->where('sector', $request->sector);
            }

            // Search by plot name
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            $plots = $query->get();

            return $this->success(PlotResource::collection($plots), 'Plots retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve plots: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'block' => 'required|string|max:50',
                'sector' => 'nullable|string|max:50',
                'category' => 'required|string|max:100',
                'width' => 'required|numeric|min:0',
                'length' => 'required|numeric|min:0',
                'marla' => 'required|string|max:50',
                'total_price' => 'nullable|numeric|min:0',
                'booking_amount' => 'nullable|numeric|min:0',
                'is_corner' => 'boolean',
                'is_park_face' => 'boolean',
                'is_forty_feet' => 'boolean',
                'status' => 'in:available,booked,sold,cancelled',
            ]);

            $plot = Plot::create($validated);

            return $this->success(new PlotResource($plot), 'Plot created successfully', 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->error('Failed to create plot: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Plot $plot): JsonResponse
    {
        try {
            $plot->load('currentOwner');
            return $this->success(new PlotResource($plot), 'Plot retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve plot: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Plot $plot): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'block' => 'sometimes|string|max:50',
                'sector' => 'nullable|string|max:50',
                'category' => 'sometimes|string|max:100',
                'width' => 'sometimes|numeric|min:0',
                'length' => 'sometimes|numeric|min:0',
                'marla' => 'sometimes|string|max:50',
                'total_price' => 'nullable|numeric|min:0',
                'booking_amount' => 'nullable|numeric|min:0',
                'is_corner' => 'boolean',
                'is_park_face' => 'boolean',
                'is_forty_feet' => 'boolean',
                'status' => 'sometimes|in:available,booked,sold,cancelled',
                'current_owner_id' => 'nullable|exists:customers,id',
                'booking_date' => 'nullable|date',
                'possession_date' => 'nullable|date',
            ]);

            $plot->update($validated);

            return $this->success(new PlotResource($plot->fresh()), 'Plot updated successfully');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->error('Failed to update plot: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update plot status
     */
    public function updateStatus(Request $request, Plot $plot): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:available,booked,sold,cancelled'
            ]);

            $plot->update($validated);

            return $this->success(new PlotResource($plot->fresh()), 'Plot status updated successfully');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->error('Failed to update plot status: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plot $plot): JsonResponse
    {
        try {
            // Check if plot can be deleted (not booked or sold)
            if ($plot->isBooked() || $plot->isSold()) {
                return $this->error('Cannot delete a plot that is booked or sold', 422);
            }

            $plot->delete();

            return $this->success(null, 'Plot deleted successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to delete plot: ' . $e->getMessage(), 500);
        }
    }

   
}