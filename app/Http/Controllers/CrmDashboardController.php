<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;

use App\Models\Member;
use App\Models\Penjualan;
use App\Models\Piutang;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CrmDashboardController extends Controller
{
    use \App\Traits\HasOutletFilter;

    public function index()
    {
        $outlets = $this->getAccessibleOutlets();
        return view('admin.crm.index', compact('outlets'));
    }

    /**
     * Get comprehensive CRM analytics
     */
    public function getAnalytics(Request $request)
    {
        $outletId = $request->get('outlet_id', 'all');
        $period = $request->get('period', '30'); // days

        $startDate = Carbon::now()->subDays($period);
        $endDate = Carbon::now();

        // Customer Overview
        $customerStats = $this->getCustomerStats($outletId);
        
        // Sales Analytics
        $salesAnalytics = $this->getSalesAnalytics($outletId, $startDate, $endDate);
        
        // Top Customers
        $topCustomers = $this->getTopCustomers($outletId, $startDate, $endDate);
        
        // Customer Segmentation
        $segmentation = $this->getCustomerSegmentation($outletId);
        
        // Piutang Analysis
        $piutangAnalysis = $this->getPiutangAnalysis($outletId);
        
        // Growth Trends
        $growthTrends = $this->getGrowthTrends($outletId);
        
        // Customer Lifecycle
        $lifecycle = $this->getCustomerLifecycle($outletId, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => [
                'customer_stats' => $customerStats,
                'sales_analytics' => $salesAnalytics,
                'top_customers' => $topCustomers,
                'segmentation' => $segmentation,
                'piutang_analysis' => $piutangAnalysis,
                'growth_trends' => $growthTrends,
                'lifecycle' => $lifecycle,
            ]
        ]);
    }

    private function getCustomerStats($outletId)
    {
        $query = Member::query();
        
        if ($outletId !== 'all') {
            $query->where('id_outlet', $outletId);
        }

        $total = $query->count();
        $newThisMonth = (clone $query)->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->count();
        
        // Check if salesInvoices relationship exists, otherwise use penjualan
        $activeCustomers = (clone $query)->where(function($q) {
            $q->whereHas('salesInvoices', function($sq) {
                $sq->where('created_at', '>=', Carbon::now()->subDays(30));
            })->orWhereHas('penjualan', function($pq) {
                $pq->where('created_at', '>=', Carbon::now()->subDays(30));
            });
        })->count();

        return [
            'total' => $total,
            'new_this_month' => $newThisMonth,
            'active' => $activeCustomers,
            'inactive' => max(0, $total - $activeCustomers),
        ];
    }

    private function getSalesAnalytics($outletId, $startDate, $endDate)
    {
        $query = Penjualan::whereBetween('created_at', [$startDate, $endDate]);
        
        if ($outletId !== 'all') {
            $query->where('id_outlet', $outletId);
        }

        $totalRevenue = $query->sum('total_harga');
        $totalTransactions = $query->count();
        $avgTransactionValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'avg_transaction_value' => $avgTransactionValue,
        ];
    }

    private function getTopCustomers($outletId, $startDate, $endDate, $limit = 10)
    {
        $query = Member::select(
                'member.id_member',
                'member.nama',
                'member.telepon',
                'member.alamat',
                'member.id_tipe',
                'member.id_outlet',
                'member.kode_member',
                'member.created_at',
                'member.updated_at'
            )
            ->selectRaw('COUNT(penjualan.id_penjualan) as transaction_count')
            ->selectRaw('COALESCE(SUM(penjualan.total_harga), 0) as total_spent')
            ->selectRaw('COALESCE(AVG(penjualan.total_harga), 0) as avg_transaction')
            ->leftJoin('penjualan', 'member.id_member', '=', 'penjualan.id_member')
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('penjualan.created_at', [$startDate, $endDate])
                  ->orWhereNull('penjualan.created_at');
            });

        if ($outletId !== 'all') {
            $query->where('member.id_outlet', $outletId);
        }

        return $query->groupBy(
                'member.id_member',
                'member.nama',
                'member.telepon',
                'member.alamat',
                'member.id_tipe',
                'member.id_outlet',
                'member.kode_member',
                'member.created_at',
                'member.updated_at'
            )
            ->havingRaw('COUNT(penjualan.id_penjualan) > 0')
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get()
            ->map(function($customer) {
                return [
                    'id' => $customer->id_member,
                    'name' => $customer->nama ?? 'N/A',
                    'phone' => $customer->telepon ?? '-',
                    'transaction_count' => (int)$customer->transaction_count,
                    'total_spent' => (float)$customer->total_spent,
                    'avg_transaction' => (float)$customer->avg_transaction,
                    'segment' => $this->determineSegment($customer->total_spent),
                ];
            });
    }

    private function getCustomerSegmentation($outletId)
    {
        $query = Member::select(
                'member.id_member',
                'member.nama',
                'member.created_at'
            )
            ->selectRaw('COALESCE(SUM(penjualan.total_harga), 0) as lifetime_value')
            ->selectRaw('COUNT(penjualan.id_penjualan) as purchase_count')
            ->leftJoin('penjualan', 'member.id_member', '=', 'penjualan.id_member');

        if ($outletId !== 'all') {
            $query->where('member.id_outlet', $outletId);
        }

        $customers = $query->groupBy('member.id_member', 'member.nama', 'member.created_at')->get();

        $segments = [
            'vip' => 0,
            'loyal' => 0,
            'regular' => 0,
            'new' => 0,
            'at_risk' => 0,
        ];

        foreach ($customers as $customer) {
            $segment = $this->classifyCustomer($customer);
            $segments[$segment]++;
        }

        return $segments;
    }

    private function classifyCustomer($customer)
    {
        $lifetimeValue = $customer->lifetime_value ?? 0;
        $purchaseCount = $customer->purchase_count ?? 0;
        $daysSinceCreated = Carbon::parse($customer->created_at)->diffInDays(Carbon::now());
        
        // Get last purchase date
        $lastPurchase = Penjualan::where('id_member', $customer->id_member)
            ->orderBy('created_at', 'desc')
            ->first();
        
        $daysSinceLastPurchase = $lastPurchase 
            ? Carbon::parse($lastPurchase->created_at)->diffInDays(Carbon::now())
            : 999;

        // VIP: High value, frequent purchases
        if ($lifetimeValue >= 10000000 && $purchaseCount >= 10) {
            return 'vip';
        }
        
        // Loyal: Regular purchases, good value
        if ($purchaseCount >= 5 && $daysSinceLastPurchase <= 30) {
            return 'loyal';
        }
        
        // At Risk: Haven't purchased recently
        if ($purchaseCount > 0 && $daysSinceLastPurchase > 60) {
            return 'at_risk';
        }
        
        // New: Recently joined
        if ($daysSinceCreated <= 30) {
            return 'new';
        }
        
        // Regular: Default
        return 'regular';
    }

    private function determineSegment($totalSpent)
    {
        if ($totalSpent >= 10000000) return 'VIP';
        if ($totalSpent >= 5000000) return 'Premium';
        if ($totalSpent >= 1000000) return 'Regular';
        return 'New';
    }

    private function getPiutangAnalysis($outletId)
    {
        $query = Piutang::select('piutang.*', 'member.nama', 'member.telepon')
            ->join('member', 'piutang.id_member', '=', 'member.id_member')
            ->where('piutang.status', 'belum_lunas');

        if ($outletId !== 'all') {
            $query->where('member.id_outlet', $outletId);
        }

        $totalPiutang = (clone $query)->sum('piutang.piutang');
        $countPiutang = (clone $query)->count();
        
        $overdue = (clone $query)->where('piutang.tanggal_jatuh_tempo', '<', Carbon::now())->get();
        $totalOverdue = $overdue->sum('piutang');
        $countOverdue = $overdue->count();

        return [
            'total_piutang' => $totalPiutang,
            'count_piutang' => $countPiutang,
            'total_overdue' => $totalOverdue,
            'count_overdue' => $countOverdue,
            'overdue_customers' => $overdue->take(5)->map(function($p) {
                return [
                    'name' => $p->nama,
                    'phone' => $p->telepon,
                    'amount' => $p->piutang,
                    'due_date' => $p->tanggal_jatuh_tempo,
                    'days_overdue' => Carbon::parse($p->tanggal_jatuh_tempo)->diffInDays(Carbon::now()),
                ];
            }),
        ];
    }

    private function getGrowthTrends($outletId)
    {
        $months = [];
        $customerGrowth = [];
        $revenueGrowth = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');

            // Customer growth
            $query = Member::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            
            if ($outletId !== 'all') {
                $query->where('id_outlet', $outletId);
            }
            
            $customerGrowth[] = $query->count();

            // Revenue growth
            $revenueQuery = Penjualan::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            
            if ($outletId !== 'all') {
                $revenueQuery->where('id_outlet', $outletId);
            }
            
            $revenueGrowth[] = $revenueQuery->sum('total_harga');
        }

        return [
            'labels' => $months,
            'customer_growth' => $customerGrowth,
            'revenue_growth' => $revenueGrowth,
        ];
    }

    private function getCustomerLifecycle($outletId, $startDate, $endDate)
    {
        $query = Member::query();
        
        if ($outletId !== 'all') {
            $query->where('id_outlet', $outletId);
        }

        $newCustomers = (clone $query)->whereBetween('created_at', [$startDate, $endDate])->count();
        
        $returningCustomers = (clone $query)->whereHas('salesInvoices', function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        })->whereHas('salesInvoices', function($q) use ($startDate) {
            $q->where('created_at', '<', $startDate);
        })->count();

        $churnedCustomers = (clone $query)->whereDoesntHave('salesInvoices', function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        })->whereHas('salesInvoices', function($q) use ($startDate) {
            $q->where('created_at', '<', $startDate);
        })->count();

        return [
            'new' => $newCustomers,
            'returning' => $returningCustomers,
            'churned' => $churnedCustomers,
        ];
    }

    /**
     * Get customer predictions and recommendations
     */
    public function getPredictions(Request $request)
    {
        $outletId = $request->get('outlet_id', 'all');
        
        // Churn Risk Prediction
        $churnRisk = $this->predictChurnRisk($outletId);
        
        // Upsell Opportunities
        $upsellOpportunities = $this->identifyUpsellOpportunities($outletId);
        
        // Revenue Forecast
        $revenueForecast = $this->forecastRevenue($outletId);

        return response()->json([
            'success' => true,
            'data' => [
                'churn_risk' => $churnRisk,
                'upsell_opportunities' => $upsellOpportunities,
                'revenue_forecast' => $revenueForecast,
            ]
        ]);
    }

    private function predictChurnRisk($outletId)
    {
        $query = Member::select(
                'member.id_member',
                'member.nama',
                'member.telepon',
                'member.id_outlet'
            )
            ->selectRaw('MAX(penjualan.created_at) as last_purchase')
            ->selectRaw('COUNT(penjualan.id_penjualan) as purchase_count')
            ->leftJoin('penjualan', 'member.id_member', '=', 'penjualan.id_member');

        if ($outletId !== 'all') {
            $query->where('member.id_outlet', $outletId);
        }

        $customers = $query->groupBy('member.id_member', 'member.nama', 'member.telepon', 'member.id_outlet')->get();

        $highRisk = [];
        $mediumRisk = [];

        foreach ($customers as $customer) {
            if (!$customer->last_purchase) continue;

            $daysSinceLastPurchase = Carbon::parse($customer->last_purchase)->diffInDays(Carbon::now());
            
            $risk = [
                'id' => $customer->id_member,
                'name' => $customer->nama,
                'phone' => $customer->telepon,
                'days_since_purchase' => $daysSinceLastPurchase,
                'purchase_count' => $customer->purchase_count,
            ];

            if ($daysSinceLastPurchase > 90 && $customer->purchase_count >= 3) {
                $highRisk[] = $risk;
            } elseif ($daysSinceLastPurchase > 60 && $customer->purchase_count >= 2) {
                $mediumRisk[] = $risk;
            }
        }

        return [
            'high_risk' => collect($highRisk)->take(10),
            'medium_risk' => collect($mediumRisk)->take(10),
        ];
    }

    private function identifyUpsellOpportunities($outletId)
    {
        $query = Member::select(
                'member.id_member',
                'member.nama',
                'member.telepon',
                'member.id_outlet'
            )
            ->selectRaw('AVG(penjualan.total_harga) as avg_purchase')
            ->selectRaw('COUNT(penjualan.id_penjualan) as purchase_count')
            ->selectRaw('MAX(penjualan.created_at) as last_purchase')
            ->leftJoin('penjualan', 'member.id_member', '=', 'penjualan.id_member');

        if ($outletId !== 'all') {
            $query->where('member.id_outlet', $outletId);
        }

        return $query->groupBy('member.id_member', 'member.nama', 'member.telepon', 'member.id_outlet')
            ->having('purchase_count', '>=', 3)
            ->havingRaw('MAX(penjualan.created_at) >= ?', [Carbon::now()->subDays(30)])
            ->orderByDesc('avg_purchase')
            ->limit(10)
            ->get()
            ->map(function($customer) {
                return [
                    'id' => $customer->id_member,
                    'name' => $customer->nama,
                    'phone' => $customer->telepon,
                    'avg_purchase' => $customer->avg_purchase,
                    'purchase_count' => $customer->purchase_count,
                    'recommendation' => $this->generateRecommendation($customer),
                ];
            });
    }

    private function generateRecommendation($customer)
    {
        $avgPurchase = $customer->avg_purchase;
        
        if ($avgPurchase >= 1000000) {
            return 'Tawarkan paket premium atau membership VIP';
        } elseif ($avgPurchase >= 500000) {
            return 'Tawarkan bundling produk dengan diskon khusus';
        } else {
            return 'Tawarkan program loyalitas untuk meningkatkan frekuensi pembelian';
        }
    }

    private function forecastRevenue($outletId)
    {
        $historicalData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $query = Penjualan::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            
            if ($outletId !== 'all') {
                $query->where('id_outlet', $outletId);
            }
            
            $historicalData[] = $query->sum('total_harga');
        }

        // Simple linear regression for next 3 months
        $avgGrowth = 0;
        for ($i = 1; $i < count($historicalData); $i++) {
            if ($historicalData[$i-1] > 0) {
                $avgGrowth += ($historicalData[$i] - $historicalData[$i-1]) / $historicalData[$i-1];
            }
        }
        $avgGrowth = $avgGrowth / (count($historicalData) - 1);

        $forecast = [];
        $lastValue = end($historicalData);
        
        for ($i = 1; $i <= 3; $i++) {
            $lastValue = $lastValue * (1 + $avgGrowth);
            $forecast[] = round($lastValue);
        }

        return [
            'historical' => $historicalData,
            'forecast' => $forecast,
            'growth_rate' => round($avgGrowth * 100, 2),
        ];
    }
}
