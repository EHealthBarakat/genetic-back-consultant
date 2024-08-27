<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Appointment extends Model
{
    use  HasFactory, Notifiable,UUID, SoftDeletes;
    protected $fillable = [
        'creator_id',
        'patient_id',
        'referred_at',

    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
