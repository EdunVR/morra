<?php

namespace App\Http\Controllers;

use App\Models\PayrollCoaSetting;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\HasOutletFilter;

class PayrollCoaSettingController extends Controller
{
    use HasOutletFilter;

    public function index(Request $request)
    {
        $outlets = $this->getUserOutlets();
        
        // Get expense accounts (Beban)
        $expenseAccounts = ChartOfAccount::where('account_type', 'expense')
            ->orderBy('account_code')
            ->get();
        
        // Get liability accounts (Hutang)
        $liabilityAccounts = ChartOfAccount::where('account_type', 'liability')
            ->orderBy('account_code')
            ->get();
        
        // Get asset accounts (Kas/Bank/Piutang)
        $assetAccounts = ChartOfAccount::where('account_type', 'asset')
            ->orderBy('account_code')
            ->get();

        return view('admin.sdm.payroll.coa-settings', compact('outlets', 'expenseAccounts', 'liabilityAccounts', 'assetAccounts'));
    }

    public function getSettings(Request $request)
    {
        $outletId = $request->get('outlet_id');
        
        if (!$outletId) {
            return response()->json([
                'success' => false,
                'message' => 'Outlet ID required'
            ], 422);
        }

        // Validate outlet access
        $this->authorizeOutletAccess($outletId);

        $setting = PayrollCoaSetting::where('outlet_id', $outletId)->first();

        return response()->json([
            'success' => true,
            'data' => $setting
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'outlet_id' => 'required|exists:outlets,id_outlet',
            'salary_expense_account_id' => 'required|exists:chart_of_accounts,id',
            'overtime_expense_account_id' => 'nullable|exists:chart_of_accounts,id',
            'bonus_expense_account_id' => 'nullable|exists:chart_of_accounts,id',
            'allowance_expense_account_id' => 'nullable|exists:chart_of_accounts,id',
            'tax_payable_account_id' => 'required|exists:chart_of_accounts,id',
            'loan_receivable_account_id' => 'nullable|exists:chart_of_accounts,id',
            'salary_payable_account_id' => 'required|exists:chart_of_accounts,id',
            'cash_account_id' => 'required|exists:chart_of_accounts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate outlet access
        $this->authorizeOutletAccess($request->outlet_id);

        try {
            DB::beginTransaction();

            $setting = PayrollCoaSetting::updateOrCreate(
                ['outlet_id' => $request->outlet_id],
                $request->only([
                    'salary_expense_account_id',
                    'overtime_expense_account_id',
                    'bonus_expense_account_id',
                    'allowance_expense_account_id',
                    'tax_payable_account_id',
                    'loan_receivable_account_id',
                    'salary_payable_account_id',
                    'cash_account_id',
                ])
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Setting COA berhasil disimpan',
                'data' => $setting
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving payroll COA settings: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data'
            ], 500);
        }
    }
}
