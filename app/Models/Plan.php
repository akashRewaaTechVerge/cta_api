<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'id',
        'duration_id',
        'title',
        'description',
        'amount',
    ];

    // protected $appends = ['total_users'];

    public function getTotalUsersAttribute()
    {
        return $this->hasMany(PlanHasUser::class,'user_id')->count();
    }
    
    public function activeUser()
    {
        return $this->hasMany(PlanHasUser::class, 'plan_id', 'id');
    }
}
