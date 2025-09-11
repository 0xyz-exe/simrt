<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Business;
use App\Models\Due;
use App\Models\Payment;
use App\Models\Resident;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LandingController extends Controller
{
    public function index()
    {
        // Data profil RT
        $profileData = [
            'rt_name' => 'RT 02/RW 04 Kedurus - Kepatihan',
            'address' => 'Jl. Melati No. 123, Kelurahan Sukamaju, Jakarta Selatan',
            'total_households' => Resident::count(),
            'total_residents' => Resident::sum('family_members'),
            'ketua_rt' => 'Bapak Ahmad Suryadi',
            'sekretaris' => 'Ibu Siti Rahayu',
            'bendahara' => 'Bapak Dedi Kurniawan',
            'description' => 'RT 02/RW 04 Kedurus - Kepatihan adalah komunitas yang solid dan peduli sesama. Kami berkomitmen untuk menciptakan lingkungan yang nyaman, aman, dan sejahtera bagi seluruh warga.'
        ];

        // Pengumuman terbaru (3 terakhir)
        $announcements = Announcement::orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // UMKM unggulan
        $featuredBusinesses = Business::active()
            ->featured()
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Kegiatan unggulan/terbaru
        $featuredActivities = Activity::active()
            ->upcoming()
            ->orderBy('start_date', 'asc')
            ->take(4)
            ->get();

        // Statistik transparansi iuran
        $currentMonth = now()->startOfMonth();
        $transparencyData = [
            'total_dues' => Due::where('month', $currentMonth->format('Y-m'))->sum('amount'),
            'collected_amount' => Payment::whereHas('due', function($query) use ($currentMonth) {
                $query->where('month', $currentMonth->format('Y-m'));
            })->where('status', 'paid')->sum('amount'),
            'pending_payments' => Payment::whereHas('due', function($query) use ($currentMonth) {
                $query->where('month', $currentMonth->format('Y-m'));
            })->where('status', 'pending')->count(),
            'paid_payments' => Payment::whereHas('due', function($query) use ($currentMonth) {
                $query->where('month', $currentMonth->format('Y-m'));
            })->where('status', 'paid')->count(),
        ];

        $transparencyData['collection_percentage'] = $transparencyData['total_dues'] > 0
            ? min(100, round(($transparencyData['collected_amount'] / $transparencyData['total_dues']) * 100))
            : 0;

        // Kategori UMKM dengan jumlah
        $businessCategories = Business::active()
            ->select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->get()
            ->mapWithKeys(function ($item) {
                $categories = Business::getCategories();
                return [$categories[$item->category] ?? $item->category => $item->total];
            });

        return view('landing.index', compact(
            'profileData',
            'announcements',
            'featuredBusinesses',
            'featuredActivities',
            'transparencyData',
            'businessCategories'
        ));
    }

    public function announcements()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')
            ->paginate(10);

        return view('landing.announcements', compact('announcements'));
    }

    public function businesses(Request $request)
    {
        $query = Business::active();

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search by name or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%");
            });
        }

        $businesses = $query->orderBy('featured', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->paginate(12);

        $categories = Business::getCategories();

        return view('landing.businesses', compact('businesses', 'categories'));
    }

    public function businessDetail(Business $business)
    {
        if (!$business->is_active) {
            abort(404);
        }

        return view('landing.business-detail', compact('business'));
    }

    public function transparency()
    {
        // Data transparansi 6 bulan terakhir
        $months = collect(range(0, 5))->map(function ($i) {
            return now()->subMonths($i)->startOfMonth();
        })->reverse();

        $transparencyData = $months->map(function ($month) {
            $monthStr = $month->format('Y-m');

            $totalDues = Due::where('month', $monthStr)->sum('amount');
            $collectedAmount = Payment::whereHas('due', function($query) use ($monthStr) {
                $query->where('month', $monthStr);
            })->where('status', 'paid')->sum('amount');

            $paidCount = Payment::whereHas('due', function($query) use ($monthStr) {
                $query->where('month', $monthStr);
            })->where('status', 'paid')->count();

            $totalCount = Payment::whereHas('due', function($query) use ($monthStr) {
                $query->where('month', $monthStr);
            })->count();

            return [
                'month' => $month->format('F Y'),
                'month_short' => $month->format('M Y'),
                'total_dues' => $totalDues,
                'collected_amount' => $collectedAmount,
                'paid_count' => $paidCount,
                'total_count' => $totalCount,
                'collection_percentage' => $totalDues > 0 ? round(($collectedAmount / $totalDues) * 100, 1) : 0,
                'payment_percentage' => $totalCount > 0 ? round(($paidCount / $totalCount) * 100, 1) : 0,
            ];
        });

        // Summary current month
        $currentMonth = now()->startOfMonth();
        $currentMonthData = [
            'month' => $currentMonth->format('F Y'),
            'total_dues' => Due::where('month', $currentMonth->format('Y-m'))->sum('amount'),
            'collected_amount' => Payment::whereHas('due', function($query) use ($currentMonth) {
                $query->where('month', $currentMonth->format('Y-m'));
            })->where('status', 'paid')->sum('amount'),
            'pending_count' => Payment::whereHas('due', function($query) use ($currentMonth) {
                $query->where('month', $currentMonth->format('Y-m'));
            })->where('status', 'pending')->count(),
            'paid_count' => Payment::whereHas('due', function($query) use ($currentMonth) {
                $query->where('month', $currentMonth->format('Y-m'));
            })->where('status', 'paid')->count(),
        ];

        $currentMonthData['collection_percentage'] = $currentMonthData['total_dues'] > 0
            ? round(($currentMonthData['collected_amount'] / $currentMonthData['total_dues']) * 100, 1)
            : 0;

        return view('landing.transparency', compact('transparencyData', 'currentMonthData'));
    }

    public function getLandingStats(Request $request)
    {
        try {
            // Data profil RT (counts)
            $profileStats = [
                'total_households' => Resident::count(),
                'total_residents' => Resident::sum('family_members'),
                'total_businesses' => Business::active()->count(),
                'total_announcements' => Announcement::where('is_active', true)->count(),
            ];

            // UMKM stats by category
            $businessCategories = Business::active()
                ->select('category', DB::raw('count(*) as total'))
                ->groupBy('category')
                ->get()
                ->mapWithKeys(function ($item) {
                    $categories = Business::getCategories();
                    return [$categories[$item->category] ?? $item->category => $item->total];
                });

            // Transparency data for current month
            $currentMonth = now()->startOfMonth();
            $transparencyData = [
                'total_dues' => Due::where('month', $currentMonth->format('Y-m'))->sum('amount'),
                'collected_amount' => Payment::whereHas('due', function($query) use ($currentMonth) {
                    $query->where('month', $currentMonth->format('Y-m'));
                })->where('status', 'paid')->sum('amount'),
                'pending_payments' => Payment::whereHas('due', function($query) use ($currentMonth) {
                    $query->where('month', $currentMonth->format('Y-m'));
                })->where('status', 'pending')->count(),
                'paid_payments' => Payment::whereHas('due', function($query) use ($currentMonth) {
                    $query->where('month', $currentMonth->format('Y-m'));
                })->where('status', 'paid')->count(),
            ];

            $transparencyData['collection_percentage'] = $transparencyData['total_dues'] > 0
                ? min(100, round(($transparencyData['collected_amount'] / $transparencyData['total_dues']) * 100))
                : 0;

            // Recent announcements count
            $recentAnnouncementsCount = Announcement::where('created_at', '>=', now()->subDays(7))
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'profile' => $profileStats,
                    'business_categories' => $businessCategories,
                    'transparency' => $transparencyData,
                    'recent_announcements' => $recentAnnouncementsCount,
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching landing stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
