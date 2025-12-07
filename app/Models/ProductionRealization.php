<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionRealization extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_id',
        'quantity_produced',
        'quantity_rejected',
        'realization_date',
        'start_time',
        'end_time',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'realization_date' => 'date',
        'quantity_produced' => 'integer',
        'quantity_rejected' => 'integer',
    ];

    // Relationships
    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // Accessors
    public function getRejectRateAttribute()
    {
        $total = $this->quantity_produced + $this->quantity_rejected;
        if ($total == 0) {
            return 0;
        }
        return round(($this->quantity_rejected / $total) * 100, 2);
    }
}
