<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class State extends Model
{
    use  HasFactory, Notifiable,UUID, SoftDeletes;
    protected $fillable = [
        'name',
        'country_id',
        'show_order',

    ];
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
    public function setShowOrderAttribute($value)
    {
        $this->attributes['show_order'] = $value ?? 1;
    }
}
