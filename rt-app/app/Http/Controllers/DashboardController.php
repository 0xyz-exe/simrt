<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Household, Resident, Due, Payment, Letter, Announcement};
use Illuminate\Support\Facades\{DB, Auth};
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Check if user is admin - show analytics, otherwise show simple dashboard
        if (Auth::user()->is_admin) {
            return $this->adminDashboard();
        } else {
            return $this->residentDashboard();
        }
    }

    private function residentDashboard()
    {
        return view('dashboard.index');
    }

    public function adminDashboard()
    {
        // Basic stats
        $stats = [
            'total_households' => Household::count(),
            'total_residents' => Resident::count(),
            'total_announcements' => Announcement::count(),
            'pending_letters' => Letter::where('status', 'pending')->count(),
        ];

        // Current month dues performance
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $duesStats = [
            'total_dues' => Due::where('month', $currentMonth)->where('year', $currentYear)->count(),
            'paid_dues' => Due::where('month', $currentMonth)->where('year', $currentYear)->where('status', 'paid')->count(),
            'unpaid_dues' => Due::where('month', $currentMonth)->where('year', $currentYear)->where('status', 'unpaid')->count(),
            'total_amount' => Due::where('month', $currentMonth)->where('year', $currentYear)->sum('amount'),
            'paid_amount' => Payment::whereYear('payment_date', $currentYear)
                                  ->whereMonth('payment_date', $currentMonth)
                                  ->sum('amount'),
        ];

        // Financial analytics (last 6 months)
        $financialData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;

            $totalDues = Due::where('month', $month)->where('year', $year)->sum('amount');
            $totalPayments = Payment::whereYear('payment_date', $year)
                                   ->whereMonth('payment_date', $month)
                                   ->sum('amount');

            $financialData[] = [
                'month' => $date->format('M Y'),
                'month_short' => $date->format('M'),
                'target' => $totalDues,
                'collected' => $totalPayments,
                'percentage' => $totalDues > 0 ? round(($totalPayments / $totalDues) * 100) : 0
            ];
        }

        // Letter statistics
        $letterStats = [
            'total' => Letter::count(),
            'pending' => Letter::where('status', 'pending')->count(),
            'approved' => Letter::where('status', 'approved')->count(),
            'rejected' => Letter::where('status', 'rejected')->count(),
        ];

        // Letter types breakdown
        $letterTypes = Letter::select('type', DB::raw('count(*) as count'))
                            ->groupBy('type')
                            ->get();

        // Recent activities (last 10)
        $recentActivities = collect();

        // Recent payments
        $recentPayments = Payment::with('due.household')
                                ->whereNotNull('payment_date')
                                ->orderBy('payment_date', 'desc')
                                ->limit(5)
                                ->get()
                                ->map(function($payment) {
                                    return [
                                        'type' => 'payment',
                                        'message' => "Pembayaran iuran dari {$payment->due->household->alamat}",
                                        'amount' => $payment->amount,
                                        'date' => $payment->payment_date,
                                        'icon' => '💰'
                                    ];
                                });

        // Recent letters
        $recentLetters = Letter::with('resident.household')
                              ->whereNotNull('created_at')
                              ->orderBy('created_at', 'desc')
                              ->limit(5)
                              ->get()
                              ->map(function($letter) {
                                  return [
                                      'type' => 'letter',
                                      'message' => "Pengajuan surat {$letter->type} dari {$letter->resident->nama}",
                                      'status' => $letter->status,
                                      'date' => $letter->created_at,
                                      'icon' => '📝'
                                  ];
                              });

        $recentActivities = $recentPayments->merge($recentLetters)
                                          ->filter(function($activity) {
                                              return !is_null($activity['date']);
                                          })
                                          ->sortByDesc('date')
                                          ->take(10);

        // Demographics
        $demographics = [
            'gender' => Resident::select('jenis_kelamin', DB::raw('count(*) as count'))
                              ->groupBy('jenis_kelamin')
                              ->get(),
            'age_groups' => $this->getAgeGroups()
        ];

        return view('dashboard.index', compact(
            'stats',
            'duesStats',
            'financialData',
            'letterStats',
            'letterTypes',
            'recentActivities',
            'demographics'
        ));
    }

    public function analytics()
    {
        // Basic stats
        $stats = [
            'total_households' => Household::count(),
            'total_residents' => Resident::count(),
            'total_announcements' => Announcement::count(),
            'pending_letters' => Letter::where('status', 'pending')->count(),
        ];

        // Current month dues performance
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $duesStats = [
            'total_dues' => Due::where('month', $currentMonth)->where('year', $currentYear)->count(),
            'paid_dues' => Due::where('month', $currentMonth)->where('year', $currentYear)->where('status', 'paid')->count(),
            'unpaid_dues' => Due::where('month', $currentMonth)->where('year', $currentYear)->where('status', 'unpaid')->count(),
            'total_amount' => Due::where('month', $currentMonth)->where('year', $currentYear)->sum('amount'),
            'paid_amount' => Payment::whereYear('payment_date', $currentYear)
                                  ->whereMonth('payment_date', $currentMonth)
                                  ->sum('amount'),
        ];

        // Financial analytics (last 6 months)
        $financialData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;

            $totalDues = Due::where('month', $month)->where('year', $year)->sum('amount');
            $totalPayments = Payment::whereYear('payment_date', $year)
                                   ->whereMonth('payment_date', $month)
                                   ->sum('amount');

            $financialData[] = [
                'month' => $date->format('M Y'),
                'month_short' => $date->format('M'),
                'target' => $totalDues,
                'collected' => $totalPayments,
                'percentage' => $totalDues > 0 ? round(($totalPayments / $totalDues) * 100) : 0
            ];
        }

        // Letter statistics
        $letterStats = [
            'total' => Letter::count(),
            'pending' => Letter::where('status', 'pending')->count(),
            'approved' => Letter::where('status', 'approved')->count(),
            'rejected' => Letter::where('status', 'rejected')->count(),
        ];

        // Letter types breakdown
        $letterTypes = Letter::select('type', DB::raw('count(*) as count'))
                            ->groupBy('type')
                            ->get();

        // Recent activities (last 10)
        $recentActivities = collect();

        // Recent payments
        $recentPayments = Payment::with('due.household')
                                ->whereNotNull('payment_date')
                                ->orderBy('payment_date', 'desc')
                                ->limit(5)
                                ->get()
                                ->map(function($payment) {
                                    return [
                                        'type' => 'payment',
                                        'message' => "Pembayaran iuran dari {$payment->due->household->alamat}",
                                        'amount' => $payment->amount,
                                        'date' => $payment->payment_date,
                                        'icon' => '💰'
                                    ];
                                });

        // Recent letters
        $recentLetters = Letter::with('resident.household')
                              ->whereNotNull('created_at')
                              ->orderBy('created_at', 'desc')
                              ->limit(5)
                              ->get()
                              ->map(function($letter) {
                                  return [
                                      'type' => 'letter',
                                      'message' => "Pengajuan surat {$letter->type} dari {$letter->resident->nama}",
                                      'status' => $letter->status,
                                      'date' => $letter->created_at,
                                      'icon' => '📝'
                                  ];
                              });

        $recentActivities = $recentPayments->merge($recentLetters)
                                          ->filter(function($activity) {
                                              return !is_null($activity['date']);
                                          })
                                          ->sortByDesc('date')
                                          ->take(10);

        // Demographics
        $demographics = [
            'gender' => Resident::select('jenis_kelamin', DB::raw('count(*) as count'))
                              ->groupBy('jenis_kelamin')
                              ->get(),
            'age_groups' => $this->getAgeGroups()
        ];

        return view('dashboard.analytics', compact(
            'stats',
            'duesStats',
            'financialData',
            'letterStats',
            'letterTypes',
            'recentActivities',
            'demographics'
        ));
    }

    public function getDashboardStats(Request $request)
    {
        try {
            $currentMonth = now()->month;
            $currentYear = now()->year;

            // Basic stats
            $stats = [
                'total_households' => Household::count(),
                'total_residents' => Resident::count(),
                'pending_letters' => Letter::where('status', 'pending')->count(),
                'total_letters' => Letter::count(),
            ];

            // Dues statistics
            $totalDues = Due::where('month', $currentMonth)->where('year', $currentYear)->count();
            $paidDues = Due::where('month', $currentMonth)->where('year', $currentYear)->where('status', 'paid')->count();
            $duesPercentage = $totalDues > 0 ? round(($paidDues / $totalDues) * 100) : 0;

            $duesStats = [
                'total_dues' => $totalDues,
                'paid_dues' => $paidDues,
                'dues_percentage' => $duesPercentage,
                'pending_percentage' => $stats['total_letters'] > 0 ? round(($stats['pending_letters'] / $stats['total_letters']) * 100) : 0
            ];

            // Recent activities
            $recentActivities = $this->getRecentActivities()->take(5);

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'dues' => $duesStats,
                    'activities' => $recentActivities,
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching dashboard stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getAgeGroups()
    {
        $ageGroups = [
            '0-17' => 0,
            '18-30' => 0,
            '31-50' => 0,
            '51-65' => 0,
            '65+' => 0
        ];

        $residents = Resident::all();

        foreach ($residents as $resident) {
            $age = Carbon::parse($resident->tanggal_lahir)->age;

            if ($age <= 17) $ageGroups['0-17']++;
            elseif ($age <= 30) $ageGroups['18-30']++;
            elseif ($age <= 50) $ageGroups['31-50']++;
            elseif ($age <= 65) $ageGroups['51-65']++;
            else $ageGroups['65+']++;
        }

        return $ageGroups;
    }

    public function mobileDashboard()
    {
        // General mobile dashboard for all users
        $stats = [
            'households' => Household::count(),
            'residents' => Resident::count(),
            'pendingLetters' => Letter::where('status', 'pending')->count(),
            'unpaidDues' => Due::where('status', 'unpaid')->count(),
        ];

        $recentActivities = $this->getRecentActivities();
        $collectionRate = $this->getCurrentCollectionRate();
        $paymentStats = $this->getCurrentPaymentStats();

        return view('admin.dashboard-mobile', compact(
            'stats',
            'recentActivities',
            'collectionRate',
            'paymentStats'
        ));
    }

    public function adminMobileDashboard()
    {
        // Admin-specific mobile dashboard
        $stats = [
            'households' => Household::count(),
            'residents' => Resident::count(),
            'pendingLetters' => Letter::where('status', 'pending')->count(),
            'unpaidDues' => Due::where('status', 'unpaid')->count(),
        ];

        $recentActivities = $this->getRecentActivities();
        $collectionRate = $this->getCurrentCollectionRate();
        $paymentStats = $this->getCurrentPaymentStats();

        return view('admin.dashboard-mobile', compact(
            'stats',
            'recentActivities',
            'collectionRate',
            'paymentStats'
        ));
    }

    public function mobileReports()
    {
        // Mobile-optimized reports view
        $monthlyData = $this->getMonthlyReportData();
        $yearlyData = $this->getYearlyReportData();

        return view('admin.reports-mobile', compact('monthlyData', 'yearlyData'));
    }

    public function mobileActivities()
    {
        // Mobile-optimized activities view
        $activities = $this->getAllActivities();

        return view('admin.activities-mobile', compact('activities'));
    }

    private function getRecentActivities()
    {
        $activities = collect();

        // Recent payments
        $recentPayments = Payment::with('due.household')
                                ->orderBy('payment_date', 'desc')
                                ->limit(3)
                                ->get()
                                ->map(function($payment) {
                                    return [
                                        'id' => 'payment_' . $payment->id,
                                        'description' => "Pembayaran iuran dari {$payment->due->household->alamat}",
                                        'time_ago' => $payment->payment_date->diffForHumans(),
                                        'type' => 'payment'
                                    ];
                                });

        // Recent letters
        $recentLetters = Letter::with('resident.household')
                              ->orderBy('created_at', 'desc')
                              ->limit(3)
                              ->get()
                              ->map(function($letter) {
                                  return [
                                      'id' => 'letter_' . $letter->id,
                                      'description' => "Pengajuan surat {$letter->type} dari {$letter->resident->nama}",
                                      'time_ago' => $letter->created_at->diffForHumans(),
                                      'type' => 'letter'
                                  ];
                              });

        return $activities->merge($recentPayments)
                         ->merge($recentLetters)
                         ->sortByDesc('time_ago')
                         ->take(5)
                         ->values();
    }

    private function getCurrentCollectionRate()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $totalDues = Due::where('month', $currentMonth)->where('year', $currentYear)->count();
        $paidDues = Due::where('month', $currentMonth)->where('year', $currentYear)->where('status', 'paid')->count();

        return $totalDues > 0 ? round(($paidDues / $totalDues) * 100) : 0;
    }

    private function getCurrentPaymentStats()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        return [
            'paid' => Due::where('month', $currentMonth)->where('year', $currentYear)->where('status', 'paid')->count(),
            'partial' => Due::where('month', $currentMonth)->where('year', $currentYear)->where('status', 'partial')->count(),
            'unpaid' => Due::where('month', $currentMonth)->where('year', $currentYear)->where('status', 'unpaid')->count(),
        ];
    }

    private function getMonthlyReportData()
    {
        // Implementation for monthly report data
        return [
            'collections' => [],
            'expenses' => [],
            'summary' => []
        ];
    }

    private function getYearlyReportData()
    {
        // Implementation for yearly report data
        return [
            'collections' => [],
            'growth' => [],
            'summary' => []
        ];
    }

    private function getAllActivities()
    {
        // Implementation for all activities
        return collect();
    }
}
