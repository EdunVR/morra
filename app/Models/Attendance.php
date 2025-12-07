<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'recruitment_id',
        'employee_name',
        'fingerprint_id',
        'date',
        'clock_in',
        'break_out',
        'break_in',
        'clock_out',
        'overtime_in',
        'overtime_out',
        'status',
        'work_hours',
        'overtime_hours',
        'hours_worked',
        'late_minutes',
        'early_minutes',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime',
        'work_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'hours_worked' => 'decimal:2',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    public function employee()
    {
        return $this->belongsTo(Recruitment::class, 'recruitment_id');
    }

    public function workSchedule()
    {
        return $this->hasOneThrough(
            WorkSchedule::class,
            Recruitment::class,
            'id', // Foreign key on recruitments table
            'recruitment_id', // Foreign key on work_schedules table
            'recruitment_id', // Local key on attendances table
            'id' // Local key on recruitments table
        );
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Auto-calculate work hours (Total Jam Kerja)
    public function calculateWorkHours()
    {
        if (!$this->clock_in || !$this->clock_out) {
            return 0;
        }

        try {
            // Get date in Y-m-d format
            $dateStr = $this->date instanceof \Carbon\Carbon 
                ? $this->date->format('Y-m-d') 
                : date('Y-m-d', strtotime($this->date));

            // Parse clock_in - if it's already a datetime, use it directly
            $start = Carbon::parse($this->clock_in);
            // If clock_in doesn't have date, add it
            if ($start->format('Y-m-d') === '1970-01-01' || $start->format('Y-m-d') === '-0001-11-30') {
                $start = Carbon::parse($dateStr . ' ' . $start->format('H:i:s'));
            }

            // Parse clock_out - if it's already a datetime, use it directly
            $end = Carbon::parse($this->clock_out);
            // If clock_out doesn't have date, add it
            if ($end->format('Y-m-d') === '1970-01-01' || $end->format('Y-m-d') === '-0001-11-30') {
                $end = Carbon::parse($dateStr . ' ' . $end->format('H:i:s'));
            }
            
            $totalMinutes = $end->diffInMinutes($start);
            
            // Subtract break time if exists
            if ($this->break_out && $this->break_in) {
                $breakStart = Carbon::parse($this->break_out);
                if ($breakStart->format('Y-m-d') === '1970-01-01' || $breakStart->format('Y-m-d') === '-0001-11-30') {
                    $breakStart = Carbon::parse($dateStr . ' ' . $breakStart->format('H:i:s'));
                }
                
                $breakEnd = Carbon::parse($this->break_in);
                if ($breakEnd->format('Y-m-d') === '1970-01-01' || $breakEnd->format('Y-m-d') === '-0001-11-30') {
                    $breakEnd = Carbon::parse($dateStr . ' ' . $breakEnd->format('H:i:s'));
                }
                
                $breakMinutes = $breakEnd->diffInMinutes($breakStart);
                $totalMinutes -= $breakMinutes;
            }
            
            return round($totalMinutes / 60, 2);
        } catch (\Exception $e) {
            \Log::error('Error calculating work hours: ' . $e->getMessage());
            return 0;
        }
    }

    // Calculate late minutes (Terlambat)
    public function calculateLateMinutes($scheduleClockIn = null)
    {
        if (!$this->clock_in) {
            return 0;
        }

        try {
            // Get schedule from work_schedule if not provided
            if (!$scheduleClockIn) {
                $workSchedule = WorkSchedule::where('recruitment_id', $this->recruitment_id)->first();
                $scheduleClockIn = $workSchedule ? $workSchedule->clock_in : '08:00:00';
            }

            // Get date in Y-m-d format
            $dateStr = $this->date instanceof \Carbon\Carbon 
                ? $this->date->format('Y-m-d') 
                : date('Y-m-d', strtotime($this->date));

            // Parse scheduled time
            $scheduled = Carbon::parse($dateStr . ' ' . $scheduleClockIn);

            // Parse actual clock_in
            $actual = Carbon::parse($this->clock_in);
            if ($actual->format('Y-m-d') === '1970-01-01' || $actual->format('Y-m-d') === '-0001-11-30') {
                $actual = Carbon::parse($dateStr . ' ' . $actual->format('H:i:s'));
            }
            
            if ($actual->greaterThan($scheduled)) {
                return $actual->diffInMinutes($scheduled);
            }
            
            return 0;
        } catch (\Exception $e) {
            \Log::error('Error calculating late minutes: ' . $e->getMessage());
            return 0;
        }
    }

    // Calculate early leave minutes (Pulang Cepat)
    public function calculateEarlyMinutes($scheduleClockOut = null)
    {
        if (!$this->clock_out) {
            return 0;
        }

        try {
            // Get schedule from work_schedule if not provided
            if (!$scheduleClockOut) {
                $workSchedule = WorkSchedule::where('recruitment_id', $this->recruitment_id)->first();
                $scheduleClockOut = $workSchedule ? $workSchedule->clock_out : '17:00:00';
            }

            // Get date in Y-m-d format
            $dateStr = $this->date instanceof \Carbon\Carbon 
                ? $this->date->format('Y-m-d') 
                : date('Y-m-d', strtotime($this->date));

            // Parse scheduled time
            $scheduled = Carbon::parse($dateStr . ' ' . $scheduleClockOut);

            // Parse actual clock_out
            $actual = Carbon::parse($this->clock_out);
            if ($actual->format('Y-m-d') === '1970-01-01' || $actual->format('Y-m-d') === '-0001-11-30') {
                $actual = Carbon::parse($dateStr . ' ' . $actual->format('H:i:s'));
            }
            
            if ($actual->lessThan($scheduled)) {
                return $scheduled->diffInMinutes($actual);
            }
            
            return 0;
        } catch (\Exception $e) {
            \Log::error('Error calculating early minutes: ' . $e->getMessage());
            return 0;
        }
    }

    // Calculate overtime minutes (Lembur)
    public function calculateOvertimeMinutes($scheduleClockOut = null)
    {
        if (!$this->clock_out) {
            return 0;
        }

        try {
            // Get schedule from work_schedule if not provided
            if (!$scheduleClockOut) {
                $workSchedule = WorkSchedule::where('recruitment_id', $this->recruitment_id)->first();
                $scheduleClockOut = $workSchedule ? $workSchedule->clock_out : '17:00:00';
            }

            // Get date in Y-m-d format
            $dateStr = $this->date instanceof \Carbon\Carbon 
                ? $this->date->format('Y-m-d') 
                : date('Y-m-d', strtotime($this->date));

            // Parse scheduled time
            $scheduled = Carbon::parse($dateStr . ' ' . $scheduleClockOut);

            // Parse actual clock_out
            $actual = Carbon::parse($this->clock_out);
            if ($actual->format('Y-m-d') === '1970-01-01' || $actual->format('Y-m-d') === '-0001-11-30') {
                $actual = Carbon::parse($dateStr . ' ' . $actual->format('H:i:s'));
            }
            
            if ($actual->greaterThan($scheduled)) {
                return $actual->diffInMinutes($scheduled);
            }
            
            return 0;
        } catch (\Exception $e) {
            \Log::error('Error calculating overtime minutes: ' . $e->getMessage());
            return 0;
        }
    }

    // Auto-calculate overtime hours (from overtime_in and overtime_out)
    public function calculateOvertimeHours()
    {
        if (!$this->overtime_in || !$this->overtime_out) {
            return 0;
        }

        try {
            // Get date in Y-m-d format
            $dateStr = $this->date instanceof \Carbon\Carbon 
                ? $this->date->format('Y-m-d') 
                : date('Y-m-d', strtotime($this->date));

            // Parse overtime_in
            $start = Carbon::parse($this->overtime_in);
            if ($start->format('Y-m-d') === '1970-01-01' || $start->format('Y-m-d') === '-0001-11-30') {
                $start = Carbon::parse($dateStr . ' ' . $start->format('H:i:s'));
            }

            // Parse overtime_out
            $end = Carbon::parse($this->overtime_out);
            if ($end->format('Y-m-d') === '1970-01-01' || $end->format('Y-m-d') === '-0001-11-30') {
                $end = Carbon::parse($dateStr . ' ' . $end->format('H:i:s'));
            }
            
            return round($end->diffInMinutes($start) / 60, 2);
        } catch (\Exception $e) {
            \Log::error('Error calculating overtime hours: ' . $e->getMessage());
            return 0;
        }
    }

    // Auto-determine status
    public function determineStatus()
    {
        if (!$this->clock_in) {
            return 'absent';
        }

        // Check if late more than 15 minutes
        $lateMinutes = $this->calculateLateMinutes();
        
        if ($lateMinutes > 15) {
            return 'late';
        }
        
        return 'present';
    }

    // Auto-calculate all fields
    public function autoCalculate()
    {
        // Get work schedule
        $workSchedule = WorkSchedule::where('recruitment_id', $this->recruitment_id)->first();
        $scheduleClockIn = $workSchedule ? $workSchedule->clock_in : '08:00:00';
        $scheduleClockOut = $workSchedule ? $workSchedule->clock_out : '17:00:00';

        // Calculate all metrics
        $this->hours_worked = $this->calculateWorkHours();
        $this->work_hours = $this->hours_worked; // Keep both for compatibility
        $this->late_minutes = $this->calculateLateMinutes($scheduleClockIn);
        $this->early_minutes = $this->calculateEarlyMinutes($scheduleClockOut);
        
        // Calculate overtime from schedule (not from overtime_in/out)
        $overtimeMinutes = $this->calculateOvertimeMinutes($scheduleClockOut);
        
        // If there's also overtime_in/out, add those hours
        $overtimeFromFields = $this->calculateOvertimeHours();
        $this->overtime_hours = round(($overtimeMinutes / 60) + $overtimeFromFields, 2);
        
        // Only auto-determine status if not manually set to leave/sick/permission
        if (!in_array($this->status, ['leave', 'sick', 'permission', 'absent'])) {
            $this->status = $this->determineStatus();
        }
        
        return $this;
    }

    // Get schedule times for display
    public function getScheduleTimes()
    {
        $workSchedule = WorkSchedule::where('recruitment_id', $this->recruitment_id)->first();
        
        return [
            'clock_in' => $workSchedule ? date('H:i', strtotime($workSchedule->clock_in)) : '08:00',
            'clock_out' => $workSchedule ? date('H:i', strtotime($workSchedule->clock_out)) : '17:00',
        ];
    }

    // Scopes
    public function scopeByOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByEmployee($query, $recruitmentId)
    {
        return $query->where('recruitment_id', $recruitmentId);
    }
}
