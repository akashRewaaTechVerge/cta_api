<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Error extends Model
{
    use HasFactory,SoftDeletes;

    public function getCreatedAtAttribute($value)
    {
        return (new Carbon($value))->format('d-m-Y h:i:s');
    }
}
