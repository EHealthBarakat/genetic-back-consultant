<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class City extends Model
{
    use  HasFactory, Notifiable,UUID, SoftDeletes;
    protected $fillable = [
        'name',
        'state_id',
        'show_order',

    ];
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
    public function setShowOrderAttribute($value)
    {
        $this->attributes['show_order'] = $value ?? 1;
    }
}
