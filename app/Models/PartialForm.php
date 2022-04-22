<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartialForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_value',
        'form_type'
    ];
}
