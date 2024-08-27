<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Country extends Model
{
    use  HasFactory, Notifiable,UUID, SoftDeletes;
    protected $fillable = [
        'name',
        'nationality_name',
        'iso_code',
        'currency',
        'phone_code',
        'show_order',

    ];
    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }
    public function setShowOrderAttribute($value)
    {
        $this->attributes['show_order'] = $value ?? 1;
    }
}
