<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'gender', 'profile_image', 'additional_file', 'is_merged', 'merged_into'];

    public function customFieldValues()
    {
        return $this->hasMany(ContactCustomFieldValue::class);
    }
}
