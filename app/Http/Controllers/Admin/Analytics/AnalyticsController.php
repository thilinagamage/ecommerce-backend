<?php

namespace App\Http\Controllers\Admin\Analytics;

use App\Http\Controllers\Controller;
use App\Models\Product\Order;
use App\Models\Product\Product;
use App\Models\User;
use App\Models\Marketing\LoyaltyPointTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Sales Analytics Dashboard
     */
    public function sales(Request $request)
    {
        $period = $request->get('period', '30'); // days
        $startDate = Carbon::now()->subDays($period);
        $endDate = Carbon::now();

        // Check what column name is used for total (grand_total or total)
        $totalColumn = \Schema::hasColumn('orders', 'grand_total') ? 'grand_total' : 'total';
        $paymentStatusColumn = \Schema::hasColumn('orders', 'payment_status') ? 'payment_status' : 'status';

        // Total Revenue
        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->sum($totalColumn);

        // Previous period revenue
        $previousStartDate = Carbon::now()->subDays($period * 2);
        $previousEndDate = $startDate;
        $previousRevenue = Order::whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->sum($totalColumn);

        $revenueGrowth = $previousRevenue > 0
            ? (($totalRevenue - $previousRevenue) / $previousRevenue) * 100
            : 100;

        // Total Orders
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $previousOrders = Order::whereBetween('created_at', [$previousStartDate, $previousEndDate])->count();
        $ordersGrowth = $previousOrders > 0
            ? (($totalOrders - $previousOrders) / $previousOrders) * 100
            : 100;

        // Average Order Value
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Sales by Status
        $salesByStatus = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as count'), DB::raw("sum($totalColumn) as total"))
            ->groupBy('status')
            ->get();

        // Daily Sales Chart
        $dailySales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw("SUM($totalColumn) as revenue")
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top Selling Products
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        // Payment Methods
        $paymentMethods = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw("sum($totalColumn) as total"))
            ->groupBy('payment_method')
            ->get();

        return view('analytics.sales', compact(
            'totalRevenue',
            'revenueGrowth',
            'totalOrders',
            'ordersGrowth',
            'avgOrderValue',
            'salesByStatus',
            'dailySales',
            'topProducts',
            'paymentMethods',
            'period'
        ));
    }

    /**
     * Customer Analytics Dashboard
     */
    public function customers(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = Carbon::now()->subDays($period);
        $endDate = Carbon::now();

        // Check column names
        $totalColumn = \Schema::hasColumn('orders', 'grand_total') ? 'grand_total' : 'total';

        // Total Customers
        $totalCustomers = User::count();
        $newCustomers = User::whereBetween('created_at', [$startDate, $endDate])->count();

        $previousStartDate = Carbon::now()->subDays($period * 2);
        $previousEndDate = $startDate;
        $previousNewCustomers = User::whereBetween('created_at', [$previousStartDate, $previousEndDate])->count();
        $customerGrowth = $previousNewCustomers > 0
            ? (($newCustomers - $previousNewCustomers) / $previousNewCustomers) * 100
            : 100;

        // Customer Lifetime Value - FIXED
        $avgLifetimeValue = DB::table('orders')
            ->whereNotNull('user_id')
            ->select('user_id', DB::raw("SUM($totalColumn) as customer_total"))
            ->groupBy('user_id')
            ->get()
            ->avg('customer_total') ?? 0;

        // Repeat Customer Rate
        $customersWithOrders = Order::whereNotNull('user_id')->distinct('user_id')->count('user_id');
        $repeatCustomers = Order::select('user_id')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();
        $repeatRate = $customersWithOrders > 0 ? ($repeatCustomers / $customersWithOrders) * 100 : 0;

        // Top Customers by Revenue
        $topCustomers = Order::whereNotNull('user_id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw("SUM(orders.$totalColumn) as total_spent")
            )
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        // Customer Acquisition Over Time
        $customerAcquisition = User::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Customer Segmentation by Orders
        $customerSegmentation = [
            'new' => User::whereDoesntHave('orders')->count(),
            'one_time' => DB::table('orders')
                ->select('user_id')
                ->whereNotNull('user_id')
                ->groupBy('user_id')
                ->havingRaw('COUNT(*) = 1')
                ->get()
                ->count(),
            'repeat' => DB::table('orders')
                ->select('user_id')
                ->whereNotNull('user_id')
                ->groupBy('user_id')
                ->havingRaw('COUNT(*) BETWEEN 2 AND 5')
                ->get()
                ->count(),
            'loyal' => DB::table('orders')
                ->select('user_id')
                ->whereNotNull('user_id')
                ->groupBy('user_id')
                ->havingRaw('COUNT(*) > 5')
                ->get()
                ->count(),
        ];

        // Geographic Distribution
        $geographicData = Order::join('users', 'orders.user_id', '=', 'users.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereNotNull('orders.billing_country')
            ->select(
                'orders.billing_country as country',
                DB::raw('COUNT(DISTINCT orders.user_id) as customer_count'),
                DB::raw("SUM(orders.$totalColumn) as revenue")
            )
            ->groupBy('country')
            ->orderBy('revenue', 'desc')
            ->limit(10)
            ->get();

        return view('analytics.customers', compact(
            'totalCustomers',
            'newCustomers',
            'customerGrowth',
            'avgLifetimeValue',
            'repeatRate',
            'topCustomers',
            'customerAcquisition',
            'customerSegmentation',
            'geographicData',
            'period'
        ));
    }

    /**
     * Product Analytics Dashboard
     */
    public function products(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = Carbon::now()->subDays($period);
        $endDate = Carbon::now();

        // Total Products
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'published')->count();
        $lowStockProducts = Product::where('stock_quantity', '<', 10)
            ->where('stock_quantity', '>', 0)
            ->count();
        $outOfStockProducts = Product::where('stock_quantity', '<=', 0)->count();

        // Best Sellers
        $bestSellers = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'products.regular_price',
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.quantity * order_items.price) as revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.regular_price')
            ->orderBy('units_sold', 'desc')
            ->limit(10)
            ->get();

        // Worst Performers
        $worstPerformers = Product::leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'products.stock_quantity',
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as units_sold')
            )
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.stock_quantity')
            ->orderBy('units_sold', 'asc')
            ->limit(10)
            ->get();

        // Category Performance
            $categoryPerformance = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('product_category', 'products.id', '=', 'product_category.product_id')
                ->join('categories', 'product_category.category_id', '=', 'categories.id')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->select(
                    'categories.name',
                    DB::raw('SUM(order_items.quantity) as units_sold'),
                    DB::raw('SUM(order_items.quantity * order_items.price) as revenue')
                )
                ->groupBy('categories.id', 'categories.name')
                ->orderBy('revenue', 'desc')
                ->get();

        return view('analytics.products', compact(
            'totalProducts',
            'activeProducts',
            'lowStockProducts',
            'outOfStockProducts',
            'bestSellers',
            'worstPerformers',
            'categoryPerformance',
            'period'
        ));
    }

    /**
     * Reports Dashboard
     */
    public function reports(Request $request)
    {
        $type = $request->get('type', 'sales');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $data = collect([]); // Initialize as empty collection

        switch ($type) {
            case 'sales':
                $data = $this->getSalesReport($startDate, $endDate);
                break;
            case 'products':
                $data = $this->getProductsReport($startDate, $endDate);
                break;
            case 'customers':
                $data = $this->getCustomersReport($startDate, $endDate);
                break;
            case 'inventory':
                $data = $this->getInventoryReport();
                break;
            case 'loyalty':
                $data = $this->getLoyaltyReport($startDate, $endDate);
                break;
        }

        return view('analytics.reports', compact('type', 'startDate', 'endDate', 'data'));
    }
    protected function getSalesReport($startDate, $endDate)
    {
        $totalColumn = \Schema::hasColumn('orders', 'grand_total') ? 'grand_total' : 'total';

        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

   protected function getProductsReport($startDate, $endDate)
{
    return collect(DB::table('order_items')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->whereBetween('orders.created_at', [$startDate, $endDate])
        ->select(
            'products.name',
            'products.sku',
            DB::raw('SUM(order_items.quantity) as total_sold'),
            DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
        )
        ->groupBy('products.id', 'products.name', 'products.sku')
        ->orderBy('total_revenue', 'desc')
        ->get());
}

protected function getCustomersReport($startDate, $endDate)
{
    $totalColumn = \Schema::hasColumn('orders', 'grand_total') ? 'grand_total' : 'total';

    $customers = User::withCount(['orders' => function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        }])
        ->with(['orders' => function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        }])
        ->get()
        ->map(function($user) use ($totalColumn) {
            $user->total_spent = $user->orders->sum($totalColumn);
            return $user;
        });

    // Return as collection and sort
    return $customers->sortByDesc('total_spent')->values();
}

protected function getInventoryReport()
{
    return Product::select('id', 'name', 'sku', 'stock_quantity', 'regular_price')
        ->orderBy('stock_quantity', 'asc')
        ->get();
}

protected function getLoyaltyReport($startDate, $endDate)
{
    return LoyaltyPointTransaction::with('user')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->orderBy('created_at', 'desc')
        ->get();
}
}
