<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamHasMember extends Model
{
    use HasFactory,SoftDeletes;

    public function memberDetail()
    {
        return $this->hasOne(TeamMember::class, 'id', 'team_member_id');
    }
}
