<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use Illuminate\Http\Request;

namespace App\Http\Controllers;

use App\Models\CustomField;
use Illuminate\Http\Request;

class CustomFieldController extends Controller
{
    public function index()
    {
        $fields = CustomField::all();
        return view('custom_fields.index', compact('fields'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:text,number,date,textarea'
        ]);
        CustomField::create($request->only('name', 'type'));
        return redirect()->back()->with('success', 'Custom field added.');
    }

    public function destroy($id)
    {
        CustomField::destroy($id);
        return redirect()->back()->with('success', 'Custom field deleted.');
    }

    public function merge(Request $r, $id)
{
    $master = Contact::findOrFail($id);
    $sec = Contact::findOrFail($r->secondary_id);

    // 1. Merge emails
    if ($sec->email && !str_contains($master->email, $sec->email)) {
        $master->email = trim(($master->email ?: '') . ', ' . $sec->email, ', ');
    }

    // 2. Merge phones
    if ($sec->phone && !str_contains($master->phone, $sec->phone)) {
        $master->phone = trim(($master->phone ?: '') . ', ' . $sec->phone, ', ');
    }

    // 3. Merge custom fields
    foreach ($sec->customFields as $cv) {
        $exists = $master->customFields()
            ->where('custom_field_id', $cv->custom_field_id)
            ->first();

        if ($exists) {
            if ($exists->value !== $cv->value) {
                // Policy: keep master (skip), or append? Here, _skip_ to preserve master
            }
        } else {
            ContactCustomFieldValue::create([
                'contact_id' => $master->id,
                'custom_field_id' => $cv->custom_field_id,
                'value' => $cv->value,
            ]);
        }
    }

    // 4. Update merge flags
    $sec->is_merged = true;
    $sec->merged_into = $master->id;
    $sec->save();

    $master->save();

    return response()->json(['message' => 'Contacts merged successfully']);
}

}

