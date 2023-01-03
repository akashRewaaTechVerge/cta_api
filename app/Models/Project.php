<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory,SoftDeletes;

    // public function project_owner()
    // {
    //     return $this->hasOne(ProjectHasOwner::class,'id','project_id');
    // }

    public function projectOwner()
    {
        return $this->hasOne(ProjectHasOwner::class, 'project_id', 'id');
    }

    public function createdBy()
    {
        return $this->hasOne(User::class,'id','created_by');
    }

    public function document()
    {
        return $this->hasMany(ProjectHasDocument::class,'project_id','id');
    }

    // public function getDocumentAttribute($value)
    // {
    //     if ($value) {
    //         return asset('/documents/' . $value);
    //     } else {
    //         return null;
    //     }
    // }

}
