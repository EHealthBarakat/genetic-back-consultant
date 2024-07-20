<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory,UUID,SoftDeletes;
    protected $fillable = [
        'name',
        'creator_id'

    ];
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

}
