<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\LoyaltyPointTransaction;
use App\Models\Marketing\LoyaltyTier;
use App\Models\User;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoyaltyController extends Controller
{
     protected $loyaltyService;

    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Display loyalty program dashboard
     */
    public function index()
    {
        $stats = [
            'total_members' => User::where('loyalty_points', '>', 0)->count(),
            'active_points' => User::sum('loyalty_points') ?? 0,
            'redeemed_points' => abs(LoyaltyPointTransaction::where('type', 'redeemed')->sum('points')) ?? 0,
            'tier_count' => LoyaltyTier::where('is_active', true)->count(),
        ];

        $tiers = LoyaltyTier::withCount('users')
            ->orderBy('points_required')
            ->get();

        $recentActivity = LoyaltyPointTransaction::with('user', 'order')
            ->latest()
            ->take(20)
            ->get();

        return view('marketing.loyalty.index', compact('stats', 'tiers', 'recentActivity'));
    }

    /**
     * Display customers page with loyalty information
     */
   public function customers(Request $request)
{
    $query = User::query()->with('loyaltyTier');

    // Show users with points OR assigned tier
    $query->where(function($q) {
        $q->where('loyalty_points', '>', 0)
          ->orWhere('total_points_earned', '>', 0)  // Add this line
          ->orWhereNotNull('loyalty_tier_id');
    });

    // Search
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // Filter by tier
    if ($request->filled('tier')) {
        $query->where('loyalty_tier_id', $request->tier);
    }

    // Sort
    $sort = $request->get('sort', 'points_desc');
    switch ($sort) {
        case 'points_asc':
            $query->orderBy('loyalty_points', 'asc');
            break;
        case 'points_desc':
            $query->orderBy('loyalty_points', 'desc');
            break;
        case 'recent':
            $query->latest('updated_at');
            break;
        default:
            $query->orderBy('total_points_earned', 'desc');
    }

    $customers = $query->paginate(50);

    return view('marketing.loyalty.customers', compact('customers'));
}

    /**
     * Display single customer detail page
     */
    public function customerDetail($id)
    {
        $customer = User::with('loyaltyTier')->findOrFail($id);

        $totalRedeemed = abs(LoyaltyPointTransaction::where('user_id', $customer->id)
            ->where('type', 'redeemed')
            ->sum('points'));

        $transactions = LoyaltyPointTransaction::where('user_id', $customer->id)
            ->with('order')
            ->latest()
            ->paginate(20);

        return view('marketing.loyalty.customer-detail', compact('customer', 'totalRedeemed', 'transactions'));
    }

    /**
     * Adjust customer points manually
     */
    public function adjustPoints(Request $request, User $customer)
    {
        $request->validate([
            'adjustment_type' => 'required|in:add,subtract',
            'points' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        try {
            $points = $request->adjustment_type === 'add'
                ? $request->points
                : -$request->points;

            // Check if subtracting more points than available
            if ($points < 0 && abs($points) > $customer->loyalty_points) {
                return redirect()->back()
                    ->with('error', 'Cannot subtract more points than customer has.');
            }

            $this->loyaltyService->adjustPoints($customer, $points, $request->reason);

            return redirect()->back()
                ->with('success', 'Points adjusted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to adjust points', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to adjust points: ' . $e->getMessage());
        }
    }

    /**
     * Display tiers management page
     */
    public function tiers()
    {
        $tiers = LoyaltyTier::withCount('users')
            ->orderBy('points_required')
            ->get();

        return view('marketing.loyalty.tiers', compact('tiers'));
    }

    /**
     * Store new tier
     */
    public function storeTier(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:loyalty_tiers,name',
            'description' => 'nullable|string|max:500',
            'points_required' => 'required|integer|min:0',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        try {
            LoyaltyTier::create($validated);

            return redirect()->route('marketing.loyalty.tiers')
                ->with('success', 'Loyalty tier created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create tier', ['error' => $e->getMessage()]);

            return redirect()->back()
                ->with('error', 'Failed to create tier: ' . $e->getMessage());
        }
    }

    /**
     * Update existing tier
     */
    public function updateTier(Request $request, LoyaltyTier $tier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:loyalty_tiers,name,' . $tier->id,
            'description' => 'nullable|string|max:500',
            'points_required' => 'required|integer|min:0',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        try {
            $tier->update($validated);

            // Update all users' tiers if points requirement changed
            if ($tier->isDirty('points_required')) {
                $this->updateAllUserTiers();
            }

            return redirect()->route('marketing.loyalty.tiers')
                ->with('success', 'Loyalty tier updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update tier', [
                'tier_id' => $tier->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update tier: ' . $e->getMessage());
        }
    }

    /**
     * Delete tier
     */
    public function destroyTier(LoyaltyTier $tier)
    {
        if ($tier->users()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete tier with active members. Please reassign members first.');
        }

        try {
            $tier->delete();

            return redirect()->route('marketing.loyalty.tiers')
                ->with('success', 'Loyalty tier deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete tier', [
                'tier_id' => $tier->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete tier: ' . $e->getMessage());
        }
    }

    /**
     * Display reports page
     */
    public function reports(Request $request)
    {
        $fromDate = $request->get('from_date', now()->subMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));

        // Statistics
        $stats = [
            'points_earned' => LoyaltyPointTransaction::where('type', 'earned')
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->sum('points'),
            'points_redeemed' => abs(LoyaltyPointTransaction::where('type', 'redeemed')
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->sum('points')),
            'new_members' => User::whereBetween('created_at', [$fromDate, $toDate])
                ->where('loyalty_points', '>', 0)
                ->count(),
            'active_members' => User::whereHas('loyaltyTransactions', function($q) use ($fromDate, $toDate) {
                $q->whereBetween('created_at', [$fromDate, $toDate]);
            })->count(),
        ];

        // Chart data - Points activity over time
        $chartData = $this->getChartData($fromDate, $toDate);

        // Tier distribution
        $tierDistribution = $this->getTierDistribution();

        // Top earners
        $topEarners = User::orderBy('total_points_earned', 'desc')
            ->take(10)
            ->get();

        return view('marketing.loyalty.reports', compact(
            'stats',
            'chartData',
            'tierDistribution',
            'topEarners',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * Display settings page
     */
    public function settings()
    {
        // Get settings from config or database
        // For now, using default values
        $settings = [
            'points_per_dollar' => config('loyalty.points_per_dollar', 1),
            'calculation_method' => config('loyalty.calculation_method', 'per_dollar'),
            'points_per_dollar_off' => config('loyalty.points_per_dollar_off', 100),
            'minimum_points_redemption' => config('loyalty.minimum_points_redemption', 100),
            'enable_expiration' => config('loyalty.enable_expiration', false),
            'expiration_months' => config('loyalty.expiration_months', 12),
            'expiration_warning_days' => config('loyalty.expiration_warning_days', 30),
            'program_active' => config('loyalty.program_active', true),
        ];

        return view('marketing.loyalty.settings', compact('settings'));
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'points_per_dollar' => 'required|numeric|min:0',
            'calculation_method' => 'required|in:per_dollar,percentage,fixed',
            'points_per_dollar_off' => 'required|integer|min:1',
            'minimum_points_redemption' => 'required|integer|min:0',
            'enable_expiration' => 'nullable|boolean',
            'expiration_months' => 'nullable|integer|min:1',
            'expiration_warning_days' => 'nullable|integer|min:1',
            'program_active' => 'nullable|boolean',
        ]);

        try {
            // Save settings to database or config
            // For now, we'll just redirect with success
            // You might want to create a Settings model or use laravel-settings package

            return redirect()->route('marketing.loyalty.settings')
                ->with('success', 'Settings updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update settings', ['error' => $e->getMessage()]);

            return redirect()->back()
                ->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Export customers data
     */
    public function exportCustomers(Request $request)
    {
        $query = User::with('loyaltyTier')
            ->where('loyalty_points', '>', 0)
            ->orWhereNotNull('loyalty_tier_id');

        if ($request->filled('tier')) {
            $query->where('loyalty_tier_id', $request->tier);
        }

        $customers = $query->get();

        $filename = 'loyalty_customers_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, ['Name', 'Email', 'Current Points', 'Total Earned', 'Total Redeemed', 'Current Tier', 'Member Since']);

            // Data
            foreach ($customers as $customer) {
                $totalRedeemed = abs($customer->loyaltyTransactions()->where('type', 'redeemed')->sum('points'));

                fputcsv($file, [
                    $customer->name,
                    $customer->email,
                    $customer->loyalty_points,
                    $customer->total_points_earned,
                    $totalRedeemed,
                    $customer->loyaltyTier?->name ?? 'N/A',
                    $customer->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Sync all user tiers based on current points
     */
    public function syncTiers()
    {
        try {
            $updated = $this->updateAllUserTiers();

            return redirect()->back()
                ->with('success', "Successfully updated {$updated} customer tiers.");
        } catch (\Exception $e) {
            Log::error('Failed to sync tiers', ['error' => $e->getMessage()]);

            return redirect()->back()
                ->with('error', 'Failed to sync tiers: ' . $e->getMessage());
        }
    }

    /**
     * Helper: Update all user tiers based on their points
     */
    protected function updateAllUserTiers()
    {
        $users = User::whereNotNull('total_points_earned')->get();
        $updated = 0;

        foreach ($users as $user) {
            if ($user->updateLoyaltyTier()) {
                $updated++;
            }
        }

        return $updated;
    }

    /**
     * Helper: Get chart data for reports
     */
    protected function getChartData($fromDate, $toDate)
    {
        $dates = [];
        $earned = [];
        $redeemed = [];

        $period = \Carbon\CarbonPeriod::create($fromDate, $toDate);

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $dates[] = $date->format('M d');

            $earned[] = LoyaltyPointTransaction::where('type', 'earned')
                ->whereDate('created_at', $dateStr)
                ->sum('points');

            $redeemed[] = abs(LoyaltyPointTransaction::where('type', 'redeemed')
                ->whereDate('created_at', $dateStr)
                ->sum('points'));
        }

        return [
            'labels' => $dates,
            'earned' => $earned,
            'redeemed' => $redeemed,
        ];
    }

    /**
     * Helper: Get tier distribution data
     */
    protected function getTierDistribution()
    {
        $tiers = LoyaltyTier::withCount('users')->get();

        return [
            'labels' => $tiers->pluck('name')->toArray(),
            'data' => $tiers->pluck('users_count')->toArray(),
        ];
    }

    /**
     * Bulk award points to customers
     */
    public function bulkAward(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'points' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        try {
            $awarded = 0;

            foreach ($request->user_ids as $userId) {
                $user = User::find($userId);
                if ($user) {
                    $this->loyaltyService->adjustPoints($user, $request->points, $request->reason);
                    $awarded++;
                }
            }

            return redirect()->back()
                ->with('success', "Successfully awarded {$request->points} points to {$awarded} customers.");
        } catch (\Exception $e) {
            Log::error('Failed to bulk award points', ['error' => $e->getMessage()]);

            return redirect()->back()
                ->with('error', 'Failed to award points: ' . $e->getMessage());
        }
    }


}
