<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'recruitment_id',
        'clock_in',
        'clock_out'
    ];

    protected $casts = [
        'clock_in' => 'datetime:H:i',
        'clock_out' => 'datetime:H:i'
    ];


    public function recruitment()
    {
        return $this->belongsTo(Recruitment::class);
    }
    
    public function getClockInAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('H:i');
    }

    public function getClockOutAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('H:i');
    }
}