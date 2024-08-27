<?php

namespace App\Models;

use App\Enums\DegreeEnum;
use App\Enums\MaritalEnum;
use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Patient extends Model
{
    use  HasFactory, Notifiable,UUID, SoftDeletes;

    protected $fillable = [
        'user_id',
        'city_id',
        'national_code',
        'spouse_national_code',
        'address',
        'father_name',
        'marital_enum',
        'degree_enum'

    ];
    /**
     * @var string[]
     */
    protected $casts = [
        'degree' => DegreeEnum::class,
        'marital' => MaritalEnum::class

    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
