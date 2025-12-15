<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\OccupationResource;
use App\Models\NomineeRelation;
use App\Models\Occupation;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;

class CustomerController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of customers.
     */
    public function index()
    {
        $customers = Customer::latest()->paginate(10);
        return $this->success(CustomerResource::collection($customers));
    }


    /**
     * Display a listing of customers.
     */
    public function fieldData(){

        $relations = NomineeRelation::all();
        $occupations = Occupation::all();


         return $this->success([
              'relations' => $relations,
              'occupations' => $occupations,
         ]);
    }


    /**
     * Store a newly created customer.
     */
    public function store(Request $request)
    {

        // return json_encode($request->all());

        try {
            $validated = $this->validateData($request);

            if ($request->hasFile('customer_img')) {
                $validated['customer_img'] = $this->uploadImage($request->file('customer_img'), 'customers');
            }

            if ($request->hasFile('nominee_img')) {
                $validated['nominee_img'] = $this->uploadImage($request->file('nominee_img'), 'nominees');
            }

            $validated['user_id'] = auth()->id();

            $customer = Customer::create($validated);

            return $this->success(
                new CustomerResource($customer),
                'Customer created successfully.',
                201
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create customer',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ], 500);
            // return $this->error('Failed to create customer', 500, $e->getMessage());
        }
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        return $this->success(new CustomerResource($customer->load(['occupation', 'relation'])));
    }

    /**
     * Update the specified customer.
     */
    public function update(Request $request, Customer $customer)
    {
        try {
            $validated = $this->validateData($request, $customer->id);

            if ($request->hasFile('customer_img')) {
                $this->deleteImage($customer->customer_img);
                $validated['customer_img'] = $this->uploadImage($request->file('customer_img'), 'customers');
            }

            if ($request->hasFile('nominee_img')) {
                $this->deleteImage($customer->nominee_img);
                $validated['nominee_img'] = $this->uploadImage($request->file('nominee_img'), 'nominees');
            }

            $customer->update($validated);

            return $this->success(
                new CustomerResource($customer->fresh()),
                'Customer updated successfully.'
            );
        } catch (\Exception $e) {
            return $this->error('Failed to update customer', 500, $e->getMessage());
        }
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(Customer $customer)
    {
        try {
            $this->deleteImage($customer->customer_img);
            $this->deleteImage($customer->nominee_img);
            $customer->delete();

            return $this->success(null, 'Customer deleted successfully.');
        } catch (\Exception $e) {
            return $this->error('Failed to delete customer', 500, $e->getMessage());
        }
    }

    /**
     * Validate customer data (DRY principle).
     */
    protected function validateData(Request $request, $id = null): array
    {
        return $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'father_or_husband_name' => 'required|string|max:150',
            'gender' => ['required', Rule::in(['male', 'female'])],
            'phone' => 'nullable|string|max:20',
            'mobile' => 'required|string|max:20',
            'occupation_id' => 'required|integer|exists:occupations,id',
            'cnic' => [
                'required',
                'string',
                'max:17',
                Rule::unique('customers', 'cnic')->ignore($id)
            ],
            'email' => 'nullable|email|max:100',
            'dob' => 'required|date',
            'postal_address' => 'required|string|max:255',
            'residential_address' => 'nullable|string|max:255',
            'customer_img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nominee_name' => 'required|string|max:100',
            'nominee_relation_id' => 'required|integer|exists:nominee_relations,id',
            'nominee_cnic' => 'required|string|max:15',
            'nominee_address' => 'required|string|max:255',
            'nominee_img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);
    }

    protected function uploadImage($file, $folder): string
    {
        return $file->store("private/uploads/{$folder}");
    }

    protected function deleteImage(?string $path): void
    {
        if ($path && Storage::exists($path)) {
            Storage::delete($path);
        }
    }
}
