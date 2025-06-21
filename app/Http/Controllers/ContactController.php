<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\CustomField;
use App\Models\ContactCustomFieldValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ContactController extends Controller
{
    public function index(Request $request)
    {
        $contacts = Contact::where('is_merged', '=', 0)->get();
        $allContacts = Contact::where('is_merged', '=', 0)->get();
        $customFields = CustomField::all();
        return view('contacts.index', compact('contacts', 'allContacts', 'customFields'));
    }

    public function fetch(Request $request)
    {
        $contacts = Contact::query()
            ->when($request->name, fn($q) => $q->where('name', 'like', '%' . $request->name . '%'))
            ->when($request->email, fn($q) => $q->where('email', 'like', '%' . $request->email . '%'))
            ->when($request->gender, fn($q) => $q->where('gender', $request->gender))
            ->when($request->phone, fn($q) => $q->where('phone', 'like', '%' . $request->phone . '%'))
            ->get();

        return view('partials.contacts-table', compact('contacts'));

    }

    public function show($id)
    {
        $contact = Contact::with('customFieldValues')->findOrFail($id);
        $contact->profile_image_url = asset('storage/' . $contact->profile_image);
        $contact->additional_file_url = asset('storage/' . $contact->additional_file);
        $customFields = [];
        foreach ($contact->customFieldValues as $cf) {
            $customFields[$cf->custom_field_id] = $cf->value;
        }
        return response()->json([
            'id' => $contact->id,
            'name' => $contact->name,
            'email' => $contact->email,
            'phone' => $contact->phone,
            'gender' => $contact->gender,
            'profile_image_url' => $contact->profile_image_url,
            'additional_file_url' => $contact->additional_file_url,
            'custom_fields' => $customFields

        ]);
    }

    public function store(Request $request)
    {
        // 🔍 Validate request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|digits_between:10,12|numeric',
            'gender' => 'required|in:Male,Female',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'additional_file' => 'nullable|file|max:5120', // 5MB
            'custom_fields.*' => 'nullable|string|max:255',
        ]);


        $contact = new Contact();
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->gender = $request->gender;

        if ($request->hasFile('profile_image')) {
            $contact->profile_image = $request->file('profile_image')->store('uploads', 'public');
        }

        if ($request->hasFile('additional_file')) {
            $contact->additional_file = $request->file('additional_file')->store('uploads', 'public');
        }

        $contact->save();

        // 🔁 Save custom field values
        if ($request->has('custom_fields')) {
            foreach ($request->custom_fields as $field_id => $value) {
                ContactCustomFieldValue::create([
                    'contact_id' => $contact->id,
                    'custom_field_id' => $field_id,
                    'value' => $value,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Contact created successfully.'
        ]);
    }


    // public function store(Request $request)
    // {
    //     $contact = new Contact();
    //     $contact->name = $request->name;
    //     $contact->email = $request->email;
    //     $contact->phone = $request->phone;
    //     $contact->gender = $request->gender;

    //     if ($request->hasFile('profile_image')) {
    //         $contact->profile_image = $request->file('profile_image')->store('uploads', 'public');
    //     }

    //     if ($request->hasFile('additional_file')) {
    //         $contact->additional_file = $request->file('additional_file')->store('uploads', 'public');
    //     }

    //     $contact->save();

    //     if ($request->has('custom_fields')) {
    //         foreach ($request->custom_fields as $field_id => $value) {
    //             ContactCustomFieldValue::create([
    //                 'contact_id' => $contact->id,
    //                 'custom_field_id' => $field_id,
    //                 'value' => $value,
    //             ]);
    //         }
    //     }

    //     return response()->json(['message' => 'Contact created successfully']);
    // }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required',
            'gender' => 'required|in:Male,Female',
        ];

        if ($request->has('custom_fields')) {
            foreach ($request->custom_fields as $field_id => $value) {
                //  $rules["custom_fields.$field_id"] = 'required'; // Adjust rule per type if needed
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $contact = Contact::findOrFail($id);
        $contact->update($request->only('name', 'email', 'phone', 'gender'));

        if ($request->hasFile('profile_image')) {
            $contact->profile_image = $request->file('profile_image')->store('uploads', 'public');
        }

        if ($request->hasFile('additional_file')) {
            $contact->additional_file = $request->file('additional_file')->store('uploads', 'public');
        }

        if ($request->has('custom_fields')) {
            foreach ($request->custom_fields as $field_id => $value) {
                ContactCustomFieldValue::updateOrCreate([
                    'contact_id' => $contact->id,
                    'custom_field_id' => $field_id,
                ], [
                    'value' => $value,
                ]);
            }
        }

        $contact->save();

        return response()->json(['message' => 'Contact updated successfully']);
    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return response()->json(['message' => 'Contact deleted successfully']);
    }

    public function diff($masterId, $secId)
    {
        $master = Contact::with('customFieldValues')->findOrFail($masterId);
        $secondary = Contact::with('customFieldValues')->findOrFail($secId);
        $customFields = CustomField::all();

        $getValues = function ($contact) use ($customFields) {
            $values = [];
            foreach ($customFields as $field) {
                $value = optional($contact->customFieldValues->where('custom_field_id', $field->id)->first())->value ?? '[—]';
                $values[] = [
                    'label' => $field->name,
                    'value' => $value
                ];
            }
            return $values;
        };

        return response()->json([
            'master' => [
                'id' => $master->id,
                'name' => $master->name,
                'email' => $master->email,
                'phone' => $master->phone,
                'custom_fields' => $getValues($master)
            ],
            'secondary' => [
                'id' => $secondary->id,
                'name' => $secondary->name,
                'email' => $secondary->email,
                'phone' => $secondary->phone,
                'custom_fields' => $getValues($secondary)
            ]
        ]);
    }

    public function merge(Request $r)
    {
        $master = Contact::findOrFail($r->master_id);
        $sec = Contact::findOrFail($r->secondary_id);

        // Merge email and phone if needed
        if ($sec->email && !str_contains($master->email, $sec->email)) {
            $master->email = trim(($master->email ?: '') . ', ' . $sec->email, ', ');
        }

        if ($sec->phone && !str_contains($master->phone, $sec->phone)) {
            $master->phone = trim(($master->phone ?: '') . ', ' . $sec->phone, ', ');
        }

        // Load custom fields
        $sec->load('customFieldValues');
        $master->load('customFieldValues');

        // Convert master's custom field values to associative array for easier access
        $masterFields = $master->customFieldValues->keyBy('custom_field_id');

        foreach ($sec->customFieldValues as $secField) {
            $masterField = $masterFields->get($secField->custom_field_id);

            // If master has no field or has it but it's empty, then update
            if (!$masterField || empty(trim($masterField->value))) {
                ContactCustomFieldValue::updateOrCreate(
                    [
                        'contact_id' => $master->id,
                        'custom_field_id' => $secField->custom_field_id,
                    ],
                    [
                        'value' => $secField->value,
                    ]
                );
            }
        }

        // Mark secondary as merged
        $sec->update([
            'is_merged' => true,
            'merged_into' => $master->id,
        ]);

        $master->save();

        return response()->json(['message' => 'Merged successfully']);
    }


}
