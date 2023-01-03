<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Team extends Model
{
    use HasFactory,SoftDeletes;

    public function TeamMembers()
    {
        return $this->hasMany(TeamHasMember::class, 'team_id', 'id');
    }

    public function getCreatedAtAttribute($value)
    {
        return (new Carbon($value))->format('d-m-Y h:i:s');
    }
    public function getUpdatedAtAttribute($value)
    {
        return (new Carbon($value))->format('d-m-Y h:i:s');
    }
}
