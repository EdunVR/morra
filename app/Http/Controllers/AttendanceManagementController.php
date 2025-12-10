<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;

use App\Models\Attendance;
use App\Models\Recruitment;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use Carbon\Carbon;

class AttendanceManagementController extends Controller
{
    use \App\Traits\HasOutletFilter;


    public function index()
    {
        return view('admin.sdm.attendance.index');
    }

    public function getData(Request $request)
    {
        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $employeeId = $request->get('employee_id');
        $status = $request->get('status');

        $query = Attendance::with('employee');

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        if ($employeeId) {
            $query->where('recruitment_id', $employeeId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        $data = $attendances->map(function($att, $index) {
            return [
                'DT_RowIndex' => $index + 1,
                'id' => $att->id,
                'date' => Carbon::parse($att->date)->format('d/m/Y'),
                'employee_name' => $att->employee ? $att->employee->name : '-',
                'check_in' => $att->check_in ?? '-',
                'check_out' => $att->check_out ?? '-',
                'status' => '<span class="badge badge-' . $this->getStatusBadge($att->status) . '">' . $this->getStatusLabel($att->status) . '</span>',
                'work_hours' => number_format($att->work_hours, 2) . ' jam',
                'overtime_hours' => number_format($att->overtime_hours, 2) . ' jam',
                'notes' => $att->notes ?? '-',
                'action' => '
                    <button class="btn btn-sm btn-info" onclick="editAttendance(' . $att->id . ')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteAttendance(' . $att->id . ')">
                        <i class="fas fa-trash"></i>
                    </button>
                '
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get daily attendance table with full details
     */
    public function getDailyTable(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        
        // Get all active employees
        $employees = Recruitment::where('status', 'active')
            ->with(['workSchedule'])
            ->orderBy('name')
            ->get();

        $data = $employees->map(function($employee, $index) use ($date) {
            // Get attendance for this date
            $attendance = Attendance::where('recruitment_id', $employee->id)
                ->whereDate('date', $date)
                ->first();

            // Get work schedule
            $schedule = $employee->workSchedule ?? WorkSchedule::getOrCreateForRecruitment($employee->id);
            
            $scheduleIn = $schedule ? date('H:i', strtotime($schedule->clock_in)) : '08:00';
            $scheduleOut = $schedule ? date('H:i', strtotime($schedule->clock_out)) : '17:00';

            // Calculate metrics
            $clockIn = $attendance ? $attendance->clock_in : null;
            $clockOut = $attendance ? $attendance->clock_out : null;
            $status = $attendance ? $attendance->status : 'absent';
            
            // Calculate hours worked
            $hoursWorked = 0;
            if ($clockIn && $clockOut) {
                $start = Carbon::parse($clockIn);
                $end = Carbon::parse($clockOut);
                $totalMinutes = $end->diffInMinutes($start);
                
                // Subtract break time if exists
                if ($attendance && $attendance->break_out && $attendance->break_in) {
                    $breakStart = Carbon::parse($attendance->break_out);
                    $breakEnd = Carbon::parse($attendance->break_in);
                    $breakMinutes = $breakEnd->diffInMinutes($breakStart);
                    $totalMinutes -= $breakMinutes;
                }
                
                $hoursWorked = $totalMinutes / 60;
            }
            
            // Calculate late minutes
            $lateMinutes = 0;
            if ($clockIn && $scheduleIn) {
                $actual = Carbon::parse($clockIn);
                $scheduled = Carbon::parse($scheduleIn);
                if ($actual->gt($scheduled)) {
                    $lateMinutes = $actual->diffInMinutes($scheduled);
                }
            }
            
            // Calculate early minutes
            $earlyMinutes = 0;
            if ($clockOut && $scheduleOut) {
                $actual = Carbon::parse($clockOut);
                $scheduled = Carbon::parse($scheduleOut);
                if ($actual->lt($scheduled)) {
                    $earlyMinutes = $scheduled->diffInMinutes($actual);
                }
            }
            
            // Calculate overtime minutes
            $overtimeMinutes = 0;
            if ($attendance && $attendance->overtime_in && $attendance->overtime_out) {
                $overtimeStart = Carbon::parse($attendance->overtime_in);
                $overtimeEnd = Carbon::parse($attendance->overtime_out);
                $overtimeMinutes = $overtimeEnd->diffInMinutes($overtimeStart);
            }

            return [
                'DT_RowIndex' => $index + 1,
                'id' => $attendance ? $attendance->id : null,
                'fingerprint_id' => $employee->fingerprint_id ?? '-',
                'employee_name' => $employee->name,
                'position' => $employee->position ?? '-',
                'schedule_in' => $scheduleIn,
                'schedule_out' => $scheduleOut,
                'status' => $status, // Send actual status code for frontend
                'status_badge' => '<span class="badge badge-' . $this->getStatusBadge($status) . '">' . $this->getStatusLabel($status) . '</span>',
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
                'break_out' => $attendance ? $attendance->break_out : null,
                'break_in' => $attendance ? $attendance->break_in : null,
                'overtime_in' => $attendance ? $attendance->overtime_in : null,
                'overtime_out' => $attendance ? $attendance->overtime_out : null,
                'hours_worked' => $hoursWorked > 0 ? round($hoursWorked, 2) : null,
                'late_minutes' => $lateMinutes > 0 ? (int)$lateMinutes : 0,
                'early_minutes' => $earlyMinutes > 0 ? (int)$earlyMinutes : 0,
                'overtime_minutes' => $overtimeMinutes > 0 ? (int)$overtimeMinutes : 0,
                'action' => $attendance ? '
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-info" onclick="editAttendance(' . $attendance->id . ')" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger" onclick="deleteAttendance(' . $attendance->id . ')" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ' : '
                    <button class="btn btn-sm btn-success" onclick="addAttendance(' . $employee->id . ', \'' . $date . '\')" title="Tambah">
                        <i class="fas fa-plus"></i>
                    </button>
                '
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get monthly attendance calendar
     */
    public function getMonthlyTable(Request $request)
    {
        $month = $request->get('month', now()->format('m'));
        $year = $request->get('year', now()->format('Y'));
        
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        // Get all active employees
        $employees = Recruitment::where('status', 'active')
            ->orderBy('name')
            ->get();

        $data = $employees->map(function($employee, $index) use ($startDate, $endDate, $daysInMonth) {
            // Get all attendances for this month
            $attendances = Attendance::where('recruitment_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->keyBy(function($item) {
                    return Carbon::parse($item->date)->day;
                });

            $row = [
                'DT_RowIndex' => $index + 1,
                'employee_name' => $employee->name,
                'position' => $employee->position ?? '-',
            ];

            // Add status for each day
            $totalPresent = 0;
            $totalAbsent = 0;
            $totalLateMinutes = 0;
            $totalEarlyMinutes = 0;
            $totalOvertimeMinutes = 0;
            $totalHours = 0;
            
            // Get work schedule for this employee
            $schedule = $employee->workSchedule ?? WorkSchedule::getOrCreateForRecruitment($employee->id);
            $scheduleIn = $schedule ? date('H:i', strtotime($schedule->clock_in)) : '08:00';
            $scheduleOut = $schedule ? date('H:i', strtotime($schedule->clock_out)) : '17:00';

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $attendance = $attendances->get($day);
                
                if ($attendance) {
                    $status = $attendance->status;
                    $symbol = in_array($status, ['present', 'late']) ? '✓' : '-';
                    
                    // Count present days
                    if (in_array($status, ['present', 'late'])) {
                        $totalPresent++;
                    }
                    
                    // Count absent days (absent, leave, sick, permission)
                    if (in_array($status, ['absent', 'leave', 'sick', 'permission'])) {
                        $totalAbsent++;
                    }
                    
                    // Calculate hours worked for this day (same as daily table)
                    if ($attendance->clock_in && $attendance->clock_out) {
                        $start = Carbon::parse($attendance->clock_in);
                        $end = Carbon::parse($attendance->clock_out);
                        $minutes = abs($end->diffInMinutes($start)); // Use abs to prevent negative
                        
                        // Subtract break time
                        if ($attendance->break_out && $attendance->break_in) {
                            $breakStart = Carbon::parse($attendance->break_out);
                            $breakEnd = Carbon::parse($attendance->break_in);
                            $breakMinutes = abs($breakEnd->diffInMinutes($breakStart));
                            $minutes = max(0, $minutes - $breakMinutes); // Ensure not negative
                        }
                        
                        $totalHours += $minutes / 60;
                    }
                    
                    // Calculate late minutes (same as daily table)
                    if ($attendance->clock_in && $scheduleIn) {
                        $actual = Carbon::parse($attendance->clock_in);
                        $scheduled = Carbon::parse($scheduleIn);
                        if ($actual->gt($scheduled)) {
                            $totalLateMinutes += abs($actual->diffInMinutes($scheduled));
                        }
                    }
                    
                    // Calculate early minutes (same as daily table)
                    if ($attendance->clock_out && $scheduleOut) {
                        $actual = Carbon::parse($attendance->clock_out);
                        $scheduled = Carbon::parse($scheduleOut);
                        if ($actual->lt($scheduled)) {
                            $totalEarlyMinutes += abs($scheduled->diffInMinutes($actual));
                        }
                    }
                    
                    // Calculate overtime minutes (same as daily table)
                    if ($attendance->overtime_in && $attendance->overtime_out) {
                        $overtimeStart = Carbon::parse($attendance->overtime_in);
                        $overtimeEnd = Carbon::parse($attendance->overtime_out);
                        if ($overtimeEnd->gt($overtimeStart)) {
                            $totalOvertimeMinutes += abs($overtimeEnd->diffInMinutes($overtimeStart));
                        }
                    }

                    $tooltip = "Masuk: " . ($attendance->clock_in ?? '-') . "\n" .
                               "Keluar: " . ($attendance->clock_out ?? '-') . "\n" .
                               "Terlambat: " . ($attendance->late_minutes ?? 0) . " menit";

                    $row['day_' . $day] = '<span class="badge badge-' . $this->getStatusBadge($status) . '" title="' . $tooltip . '">' . $symbol . '</span>';
                } else {
                    $row['day_' . $day] = '<span class="text-muted">-</span>';
                    $totalAbsent++;
                }
            }

            // Add summary
            $row['total_present'] = $totalPresent;
            $row['total_absent'] = $totalAbsent;
            $row['total_hours'] = round($totalHours, 2);
            $row['total_late'] = $totalLateMinutes;
            $row['total_early'] = $totalEarlyMinutes;
            $row['total_overtime'] = round($totalOvertimeMinutes / 60, 2); // Convert to hours

            return $row;
        })->values();

        return response()->json([
            'success' => true,
            'data' => $data,
            'days_in_month' => $daysInMonth
        ]);
    }

    public function getEmployees(Request $request)
    {
        $employees = Recruitment::where('status', 'active')
            ->orderBy('name')
            ->get()
            ->map(function($emp) {
                return [
                    'id' => $emp->id,
                    'nama' => $emp->name,
                    'jabatan' => $emp->position ?? '-',
                    'departemen' => $emp->department ?? '-'
                ];
            });

        return response()->json($employees);
    }

    public function getStatistics(Request $request)
    {
        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        $query = Attendance::whereBetween('date', [$startDate, $endDate]);

        // Hadir hari ini (present + late)
        $hadir = (clone $query)->whereIn('status', ['present', 'late'])->count();
        
        // Terlambat (late_minutes > 0)
        $terlambat = (clone $query)->where('late_minutes', '>', 0)->count();
        
        // Tidak hadir (absent, leave, sick, permission)
        $tidakHadir = (clone $query)->whereIn('status', ['absent', 'leave', 'sick', 'permission'])->count();
        
        // Rata-rata jam kerja
        $avgHours = (clone $query)->whereIn('status', ['present', 'late'])
            ->avg('work_hours') ?? 0;

        return response()->json([
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'tidak_hadir' => $tidakHadir,
            'avg_hours' => round($avgHours, 2)
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:recruitments,id',
            'date' => 'required|date',
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,late,absent,leave,sick,permission',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check duplicate
        $exists = Attendance::where('recruitment_id', $request->employee_id)
            ->where('date', $request->date)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Absensi untuk karyawan ini pada tanggal tersebut sudah ada'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $employee = Recruitment::findOrFail($request->employee_id);
            
            // Get user's first outlet or default
            $outletId = auth()->user()->outlets()->first()->id_outlet ?? 1;

            $attendance = new Attendance([
                'outlet_id' => $outletId,
                'recruitment_id' => $request->employee_id,
                'employee_name' => $employee->name,
                'fingerprint_id' => $employee->fingerprint_id,
                'date' => $request->date,
                'clock_in' => $request->clock_in,
                'clock_out' => $request->clock_out,
                'break_out' => $request->break_out,
                'break_in' => $request->break_in,
                'overtime_in' => $request->overtime_in,
                'overtime_out' => $request->overtime_out,
                'status' => $request->status,
                'notes' => $request->notes,
                'created_by' => auth()->id()
            ]);

            // Auto-calculate all metrics
            $attendance->autoCalculate();
            $attendance->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil ditambahkan',
                'data' => $attendance
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating attendance: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $attendance = Attendance::with('employee')->findOrFail($id);

            return response()->json([
                'id' => $attendance->id,
                'employee_id' => $attendance->recruitment_id,
                'date' => $attendance->date,
                'clock_in' => $attendance->clock_in,
                'clock_out' => $attendance->clock_out,
                'break_out' => $attendance->break_out,
                'break_in' => $attendance->break_in,
                'overtime_in' => $attendance->overtime_in,
                'overtime_out' => $attendance->overtime_out,
                'status' => $attendance->status,
                'notes' => $attendance->notes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,late,absent,leave,sick,permission',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $attendance->clock_in = $request->clock_in;
            $attendance->clock_out = $request->clock_out;
            $attendance->break_out = $request->break_out;
            $attendance->break_in = $request->break_in;
            $attendance->overtime_in = $request->overtime_in;
            $attendance->overtime_out = $request->overtime_out;
            $attendance->status = $request->status;
            $attendance->notes = $request->notes;

            // Auto-calculate all metrics
            $attendance->autoCalculate();
            $attendance->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil diupdate',
                'data' => $attendance
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating attendance: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $attendance = Attendance::findOrFail($id);

            DB::beginTransaction();
            $attendance->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting attendance: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $employeeId = $request->get('employee_id');
        $status = $request->get('status');

        $query = Attendance::with('employee');

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        if ($employeeId) {
            $query->where('recruitment_id', $employeeId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        $pdf = Pdf::loadView('admin.sdm.attendance.pdf', [
            'attendances' => $attendances,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'title' => 'Laporan Absensi'
        ]);

        return $pdf->download('absensi-' . date('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $filters = [
            'start_date' => $request->get('start_date', now()->format('Y-m-d')),
            'end_date' => $request->get('end_date', now()->format('Y-m-d')),
            'recruitment_id' => $request->get('employee_id'),
            'status' => $request->get('status')
        ];

        return Excel::download(
            new AttendanceExport($filters), 
            'absensi-' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Get work schedules for all employees
     */
    public function getWorkSchedules(Request $request)
    {
        $employees = Recruitment::where('status', 'active')
            ->with('workSchedule')
            ->orderBy('name')
            ->get();

        $data = $employees->map(function($employee) {
            $schedule = $employee->workSchedule;
            
            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'position' => $employee->position ?? '-',
                'clock_in' => $schedule ? date('H:i', strtotime($schedule->clock_in)) : '08:00',
                'clock_out' => $schedule ? date('H:i', strtotime($schedule->clock_out)) : '17:00',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Set work hours for all employees or specific employee
     */
    public function setWorkHours(Request $request)
    {
        // Log request untuk debugging
        \Log::info('Set Work Hours Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'required|date_format:H:i',
            'employee_id' => 'nullable|exists:recruitments,id',
            'apply_to_all' => 'nullable|in:true,false,1,0' // Accept string or boolean
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $clockIn = $request->clock_in . ':00';
            $clockOut = $request->clock_out . ':00';

            // Convert apply_to_all to boolean
            $applyToAll = filter_var($request->apply_to_all, FILTER_VALIDATE_BOOLEAN);

            if ($applyToAll) {
                // Set for all active employees
                WorkSchedule::setForAllRecruitments($clockIn, $clockOut);
                $message = 'Jadwal kerja berhasil diset untuk semua karyawan';
            } else if ($request->employee_id) {
                // Set for specific employee
                WorkSchedule::setForRecruitment($request->employee_id, $clockIn, $clockOut);
                $message = 'Jadwal kerja berhasil diset untuk karyawan terpilih';
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Pilih karyawan atau centang "Terapkan ke semua karyawan"'
                ], 422);
            }

            DB::commit();

            \Log::info('Work hours set successfully');

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error setting work hours: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get work schedule for specific employee
     */
    public function getEmployeeSchedule($employeeId)
    {
        try {
            $employee = Recruitment::findOrFail($employeeId);
            $schedule = WorkSchedule::getOrCreateForRecruitment($employeeId);

            return response()->json([
                'success' => true,
                'data' => [
                    'employee_name' => $employee->name,
                    'clock_in' => date('H:i', strtotime($schedule->clock_in)),
                    'clock_out' => date('H:i', strtotime($schedule->clock_out)),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Export daily attendance as PDF
     */
    public function exportDailyPdf(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $dateFormatted = Carbon::parse($date)->format('d F Y');
        
        // Get all active employees
        $employees = Recruitment::where('status', 'active')
            ->with(['workSchedule'])
            ->orderBy('name')
            ->get();

        $data = $employees->map(function($employee) use ($date) {
            // Get attendance for this date
            $attendance = Attendance::where('recruitment_id', $employee->id)
                ->whereDate('date', $date)
                ->first();

            // Get work schedule
            $schedule = $employee->workSchedule ?? WorkSchedule::getOrCreateForRecruitment($employee->id);
            $scheduleIn = $schedule ? date('H:i', strtotime($schedule->clock_in)) : '08:00';
            $scheduleOut = $schedule ? date('H:i', strtotime($schedule->clock_out)) : '17:00';

            // Calculate metrics
            $clockIn = $attendance ? $attendance->clock_in : null;
            $clockOut = $attendance ? $attendance->clock_out : null;
            $status = $attendance ? $attendance->status : 'absent';
            
            // Calculate hours worked
            $hoursWorked = '-';
            if ($clockIn && $clockOut) {
                $start = Carbon::parse($clockIn);
                $end = Carbon::parse($clockOut);
                $minutes = abs($end->diffInMinutes($start));
                
                if ($attendance && $attendance->break_out && $attendance->break_in) {
                    $breakStart = Carbon::parse($attendance->break_out);
                    $breakEnd = Carbon::parse($attendance->break_in);
                    $breakMinutes = abs($breakEnd->diffInMinutes($breakStart));
                    $minutes = max(0, $minutes - $breakMinutes);
                }
                
                $hoursWorked = number_format($minutes / 60, 2);
            }
            
            // Calculate late minutes
            $lateMinutes = '-';
            $lateClass = '';
            if ($clockIn && $scheduleIn) {
                $actual = Carbon::parse($clockIn);
                $scheduled = Carbon::parse($scheduleIn);
                if ($actual->gt($scheduled)) {
                    $late = abs($actual->diffInMinutes($scheduled));
                    $lateMinutes = $late . ' mnt';
                    $lateClass = 'danger';
                }
            }
            
            // Calculate early minutes
            $earlyMinutes = '-';
            $earlyClass = '';
            if ($clockOut && $scheduleOut) {
                $actual = Carbon::parse($clockOut);
                $scheduled = Carbon::parse($scheduleOut);
                if ($actual->lt($scheduled)) {
                    $early = abs($scheduled->diffInMinutes($actual));
                    $earlyMinutes = $early . ' mnt';
                    $earlyClass = 'warning';
                }
            }
            
            // Calculate overtime
            $overtimeMinutes = '-';
            if ($attendance && $attendance->overtime_in && $attendance->overtime_out) {
                $overtimeStart = Carbon::parse($attendance->overtime_in);
                $overtimeEnd = Carbon::parse($attendance->overtime_out);
                if ($overtimeEnd->gt($overtimeStart)) {
                    $overtime = abs($overtimeEnd->diffInMinutes($overtimeStart));
                    $overtimeMinutes = $overtime . ' mnt';
                }
            }

            return [
                'fingerprint_id' => $employee->fingerprint_id ?? '-',
                'employee_name' => $employee->name,
                'position' => $employee->position ?? '-',
                'schedule_in' => $scheduleIn,
                'schedule_out' => $scheduleOut,
                'status_label' => $this->getStatusLabel($status),
                'status_class' => $this->getStatusBadge($status) === 'success' ? 'success' : 'danger',
                'clock_in' => $clockIn ?? '-',
                'clock_out' => $clockOut ?? '-',
                'break_out' => $attendance ? ($attendance->break_out ?? '-') : '-',
                'break_in' => $attendance ? ($attendance->break_in ?? '-') : '-',
                'overtime_in' => $attendance ? ($attendance->overtime_in ?? '-') : '-',
                'overtime_out' => $attendance ? ($attendance->overtime_out ?? '-') : '-',
                'hours_worked' => $hoursWorked,
                'late_minutes' => $lateMinutes,
                'late_class' => $lateClass,
                'early_minutes' => $earlyMinutes,
                'early_class' => $earlyClass,
                'overtime_minutes' => $overtimeMinutes,
            ];
        });

        $pdf = Pdf::loadView('admin.sdm.attendance.daily-pdf', [
            'data' => $data,
            'date' => $date,
            'dateFormatted' => $dateFormatted
        ])->setPaper('a4', 'landscape');

        return $pdf->download('absensi-harian-' . $date . '.pdf');
    }

    /**
     * Export monthly attendance as PDF calendar
     */
    public function exportMonthlyPdf(Request $request)
    {
        $month = $request->get('month', now()->format('m'));
        $year = $request->get('year', now()->format('Y'));

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        $employees = Recruitment::where('status', 'active')->orderBy('name')->get();

        $data = $employees->map(function($employee) use ($startDate, $endDate, $daysInMonth) {
            $attendances = Attendance::where('recruitment_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->keyBy(function($item) {
                    return Carbon::parse($item->date)->day;
                });

            $row = [
                'employee_name' => $employee->name,
                'position' => $employee->position ?? '-',
                'days' => []
            ];

            $totalPresent = 0;
            $totalAbsent = 0;
            $totalLateMinutes = 0;
            $totalEarlyMinutes = 0;
            $totalOvertimeMinutes = 0;
            $totalHours = 0;
            
            // Get work schedule
            $schedule = $employee->workSchedule ?? WorkSchedule::getOrCreateForRecruitment($employee->id);
            $scheduleIn = $schedule ? date('H:i', strtotime($schedule->clock_in)) : '08:00';
            $scheduleOut = $schedule ? date('H:i', strtotime($schedule->clock_out)) : '17:00';

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $attendance = $attendances->get($day);

                if ($attendance) {
                    $status = $attendance->status;
                    $symbol = in_array($status, ['present', 'late']) ? 'H' : '-';

                    if (in_array($status, ['present', 'late'])) {
                        $totalPresent++;
                    }
                    
                    if (in_array($status, ['absent', 'leave', 'sick', 'permission'])) {
                        $totalAbsent++;
                    }
                    
                    // Calculate hours worked
                    if ($attendance->clock_in && $attendance->clock_out) {
                        $start = Carbon::parse($attendance->clock_in);
                        $end = Carbon::parse($attendance->clock_out);
                        $minutes = abs($end->diffInMinutes($start));
                        
                        if ($attendance->break_out && $attendance->break_in) {
                            $breakStart = Carbon::parse($attendance->break_out);
                            $breakEnd = Carbon::parse($attendance->break_in);
                            $breakMinutes = abs($breakEnd->diffInMinutes($breakStart));
                            $minutes = max(0, $minutes - $breakMinutes);
                        }
                        
                        $totalHours += $minutes / 60;
                    }
                    
                    // Calculate late minutes
                    if ($attendance->clock_in && $scheduleIn) {
                        $actual = Carbon::parse($attendance->clock_in);
                        $scheduled = Carbon::parse($scheduleIn);
                        if ($actual->gt($scheduled)) {
                            $totalLateMinutes += abs($actual->diffInMinutes($scheduled));
                        }
                    }
                    
                    // Calculate early minutes
                    if ($attendance->clock_out && $scheduleOut) {
                        $actual = Carbon::parse($attendance->clock_out);
                        $scheduled = Carbon::parse($scheduleOut);
                        if ($actual->lt($scheduled)) {
                            $totalEarlyMinutes += abs($scheduled->diffInMinutes($actual));
                        }
                    }
                    
                    // Calculate overtime
                    if ($attendance->overtime_in && $attendance->overtime_out) {
                        $overtimeStart = Carbon::parse($attendance->overtime_in);
                        $overtimeEnd = Carbon::parse($attendance->overtime_out);
                        if ($overtimeEnd->gt($overtimeStart)) {
                            $totalOvertimeMinutes += abs($overtimeEnd->diffInMinutes($overtimeStart));
                        }
                    }

                    $row['days'][$day] = [
                        'symbol' => $symbol,
                        'status' => $status,
                        'clock_in' => $attendance->clock_in,
                        'clock_out' => $attendance->clock_out
                    ];
                } else {
                    $row['days'][$day] = ['symbol' => '-', 'status' => 'absent'];
                    $totalAbsent++;
                }
            }

            $row['summary'] = [
                'present' => $totalPresent,
                'absent' => $totalAbsent,
                'hours' => number_format($totalHours, 2),
                'late' => $totalLateMinutes > 0 ? $totalLateMinutes . ' mnt' : '-',
                'early' => $totalEarlyMinutes > 0 ? $totalEarlyMinutes . ' mnt' : '-',
                'overtime' => $totalOvertimeMinutes > 0 ? number_format($totalOvertimeMinutes / 60, 2) . ' jam' : '-'
            ];

            return $row;
        });

        $pdf = Pdf::loadView('admin.sdm.attendance.monthly-pdf', [
            'data' => $data,
            'month' => $month,
            'year' => $year,
            'monthName' => Carbon::create($year, $month)->format('F Y'),
            'daysInMonth' => $daysInMonth
        ])->setPaper('a3', 'landscape');

        return $pdf->download('absensi-bulanan-' . $year . '-' . $month . '.pdf');
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'hadir' => 'Hadir',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Alpha',
            'cuti' => 'Cuti',
            'present' => 'Hadir',
            'late' => 'Terlambat',
            'absent' => 'Alpha',
            'leave' => 'Izin',
            'sick' => 'Sakit',
            'permission' => 'Izin Khusus'
        ];

        return $labels[$status] ?? ucfirst($status);
    }

    private function getStatusBadge($status)
    {
        $badges = [
            'hadir' => 'success',
            'izin' => 'warning',
            'sakit' => 'info',
            'alpha' => 'danger',
            'cuti' => 'primary',
            'present' => 'success',
            'late' => 'warning',
            'absent' => 'danger',
            'leave' => 'warning',
            'sick' => 'info',
            'permission' => 'warning'
        ];

        return $badges[$status] ?? 'secondary';
    }
}
