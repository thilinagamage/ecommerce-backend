<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product\Order;
use App\Models\Product\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 30);

        // Check column name
        $totalColumn = \Schema::hasColumn('orders', 'grand_total') ? 'grand_total' : 'total';

        // Today's stats
        $todaySales = Order::whereDate('created_at', Carbon::today())->sum($totalColumn) ?? 0;
        $yesterdaySales = Order::whereDate('created_at', Carbon::yesterday())->sum($totalColumn) ?? 0;
        $salesGrowth = $yesterdaySales > 0 ? (($todaySales - $yesterdaySales) / $yesterdaySales) * 100 : 0;

        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();
        $yesterdayOrders = Order::whereDate('created_at', Carbon::yesterday())->count();
        $ordersGrowth = $yesterdayOrders > 0 ? (($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100 : 0;

        // Customer stats
        $totalCustomers = User::count();
        $newCustomersToday = User::whereDate('created_at', Carbon::today())->count();

        // Product stats
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('stock_quantity', '<', 10)->count();

        // Revenue data for chart - Generate all dates in range even if no sales
        $startDate = Carbon::now()->subDays($period);
        $endDate = Carbon::now();

        $revenueQuery = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw("SUM($totalColumn) as revenue")
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill in missing dates with zero revenue
        $revenueData = collect();
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $revenueData->push([
                'date' => $currentDate->format('M d'),
                'revenue' => $revenueQuery->get($dateStr)->revenue ?? 0
            ]);
            $currentDate->addDay();
        }

        // Orders by status
        $ordersByStatus = Order::whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // If no orders by status, create default data
        if ($ordersByStatus->isEmpty()) {
            $ordersByStatus = collect([
                (object)['status' => 'pending', 'count' => 0],
                (object)['status' => 'processing', 'count' => 0],
                (object)['status' => 'completed', 'count' => 0],
            ]);
        }

        // Recent orders
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Top products
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [Carbon::now()->subDays($period), Carbon::now()])
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();

        // Low stock items
        $lowStockItems = Product::where('stock_quantity', '<=', 10)
            ->orderBy('stock_quantity', 'asc')
            ->limit(10)
            ->get();

        // Recent reviews
        $recentReviews = collect([]);

        return view('dashboard.index', compact(
            'todaySales',
            'salesGrowth',
            'todayOrders',
            'ordersGrowth',
            'totalCustomers',
            'newCustomersToday',
            'totalProducts',
            'lowStockProducts',
            'revenueData',
            'ordersByStatus',
            'recentOrders',
            'topProducts',
            'lowStockItems',
            'recentReviews'
        ));
    }
}
