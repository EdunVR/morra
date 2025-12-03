<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'recruitment_id',
        'date',
        'clock_in',
        'clock_out',
        'hours_worked',
    ];

    // Relasi ke tabel employees
    public function recruitment()
    {
        return $this->belongsTo(recruitment::class);
    }
}