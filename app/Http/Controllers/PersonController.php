<?php

namespace App\Http\Controllers;

use App\Http\Resources\PeopleResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PersonController extends Controller
{
     use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $people = Person::latest()->paginate(10);
        return $this->success(PeopleResource::collection($people));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //

        
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request)
    {

   
        try {
            $validated = $this->validateData($request);
                
            if ($request->hasFile('person_img')) {
                $validated['person_img'] = $this->uploadImage($request->file('person_img'), "$request->cnic");
            }

             if ($request->hasFile('cnic_img')) {
                $validated['cnic_img'] = $this->uploadImage($request->file('cnic_img'), "$request->cnic");
            }

            $validated['user_id'] = auth()->id();

            $customer = Person::create($validated);

            return $this->success(
                new PeopleResource($customer),
                'Person created successfully.',
                201
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create Person',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ], 500);
           
        }
    }

    /**
     * Display the specified customer.
     */
    public function show(Person $customer)
    {
        return $this->success(new PeopleResource($customer->load(['occupation', 'relation'])));
    }

    /**
     * Update the specified customer.
     */
    public function update(Request $request, Person $person)
    {

        try {
            $validated = $this->validateData($request, $person->id);


            if ($request->hasFile('person_img')) {
                $this->deleteImage($person->person_img);
                $validated['person_img'] = $this->uploadImage($request->file('person_img'), "{$person->cnic}");
            }

            if ($request->hasFile('cnic_img')) {
                $this->deleteImage($person->cnic_img);
                $validated['cnic_img'] = $this->uploadImage($request->file('cnic_img'), "{$person->cnic}");
            }

            $person->update($validated);

            return $this->success(
                new PeopleResource($person->fresh()),
                'Person updated successfully.'
            );
        } catch (\Exception $e) {
            return $this->error('Failed to update customer', 500, $e->getMessage());
        }
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(Person $person)
    {
        try {
            $this->deleteImage($person->person_img);
            $this->deleteImage($person->nominee_img);
            $person->delete();

            return $this->success(null, 'person deleted successfully.');
        } catch (\Exception $e) {
            return $this->error('Failed to delete person', 500, $e->getMessage());
        }
    }

    /**
     * Validate customer data (DRY principle).
     */
    protected function validateData(Request $request, $id = null): array
    {
        return $request->validate([
            'first_name' => 'required|string|min:3|max:50',
            'last_name' => 'required|string|min:3|max:50',
            'father_or_husband_name' => 'required|string|min:3|max:50',

            'gender' => ['required', Rule::in(['male', 'female'])],

            'mobile' => [
                'required',
                'string',
                'regex:/^\d{4}-\d{7}$/',
                Rule::unique('people', 'mobile')->ignore($id),
            ],

            'occupation_id' => 'required|integer|exists:occupations,id',

            'cnic' => [
                'required',
                'string',
                'regex:/^\d{5}-\d{7}-\d{1}$/',
                Rule::unique('people', 'cnic')->ignore($id),
            ],

            'email' => 'nullable|email|max:100',
            'dob' => 'required|date',
            'postal_address' => 'required|string|max:255',

            'person_img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cnic_img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    }

    public function uploadPrivate($file, $folder)
    {
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

        // MUST specify disk: private
        return $file->storeAs($folder, $filename, 'private');
    }

    protected function uploadImage($file, $folder): string
    {
        return $file->store("private/persons/{$folder}");
    }

    protected function deleteImage(?string $path): void
    {
        if ($path && Storage::exists($path)) {
            Storage::delete($path);
        }
    }
}
