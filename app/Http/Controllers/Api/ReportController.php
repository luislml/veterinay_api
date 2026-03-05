<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Shopping;
use App\Models\Product;
use App\Models\Consultation;
use App\Models\Movement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Gate;

class ReportController extends Controller
{
    /**
     * Get sales analytics for graphing and top products
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function salesAnalytics(Request $request)
    {
        Gate::authorize('viewSalesAnalytics', \App\Models\User::class);

        $validated = $request->validate([
            'veterinary_id' => 'required|exists:veterinaries,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $veterinaryId = $validated['veterinary_id'];
        $startDate = \Carbon\Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = \Carbon\Carbon::parse($validated['end_date'])->endOfDay();

        // 1. Total Amount per Date
        $salesAmounts = Sale::where('veterinary_id', $veterinaryId)
            ->where('state', 'Completado')
            ->whereBetween('date_sale', [$startDate, $endDate])
            ->selectRaw('DATE(date_sale) as date, SUM(amount) as total_amount')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');

        // 2. Total Products Quantity per Date
        $salesQuantities = Sale::join('product_sale', 'sales.id', '=', 'product_sale.sale_id')
            ->where('sales.veterinary_id', $veterinaryId)
            ->where('sales.state', 'Completado')
            ->whereBetween('sales.date_sale', [$startDate, $endDate])
            ->selectRaw('DATE(sales.date_sale) as date, SUM(product_sale.quantity) as total_products')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');

        // 3. Merge Data
        $mergedData = $salesAmounts->map(function ($sale, $date) use ($salesQuantities) {
            return [
                'date' => $date,
                'total_amount' => (float) $sale->total_amount,
                'total_products' => (int) ($salesQuantities[$date]->total_products ?? 0),
            ];
        })->values();

        return response()->json($mergedData);
    }

    /**
     * Get purchases analytics for graphing and top products
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function purchasesAnalytics(Request $request)
    {
        Gate::authorize('viewPurchasesAnalytics', \App\Models\User::class);

        $validated = $request->validate([
            'veterinary_id' => 'required|exists:veterinaries,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $veterinaryId = $validated['veterinary_id'];
        $startDate = \Carbon\Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = \Carbon\Carbon::parse($validated['end_date'])->endOfDay();

        // 1. Purchases grouped by date for graphing
        $purchasesByDate = Shopping::where('veterinary_id', $veterinaryId)
            ->where('state', 'Completado')
            ->whereBetween('date_shop', [$startDate, $endDate])
            ->selectRaw('DATE(date_shop) as date, COUNT(*) as total_purchases, SUM(amount) as total_amount')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // 2. Top 5 most-purchased products
        $topProducts = Product::select('products.id', 'products.name', 'products.code')
            ->selectRaw('SUM(product_shopping.quantity) as total_quantity_purchased')
            ->selectRaw('SUM(product_shopping.quantity * product_shopping.price_unit) as total_amount')
            ->selectRaw('COUNT(DISTINCT product_shopping.shopping_id) as times_purchased')
            ->join('product_shopping', 'products.id', '=', 'product_shopping.product_id')
            ->join('shoppings', 'product_shopping.shopping_id', '=', 'shoppings.id')
            ->where('shoppings.veterinary_id', $veterinaryId)
            ->where('shoppings.state', 'Completado')
            ->whereBetween('shoppings.date_shop', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name', 'products.code')
            ->orderByDesc('total_quantity_purchased')
            ->limit(5)
            ->get();

        // 3. Summary statistics
        $summary = Shopping::where('veterinary_id', $veterinaryId)
            ->where('state', 'Completado')
            ->whereBetween('date_shop', [$startDate, $endDate])
            ->selectRaw('COUNT(*) as total_purchases_count, SUM(amount) as total_purchases_amount, AVG(amount) as average_purchase_amount')
            ->first();

        return response()->json([
            'purchases_by_date' => $purchasesByDate,
            'top_products' => $topProducts,
            'summary' => [
                'total_purchases_count' => (int) $summary->total_purchases_count,
                'total_purchases_amount' => (float) $summary->total_purchases_amount,
                'average_purchase_amount' => round((float) $summary->average_purchase_amount, 2),
            ],
        ]);
    }

    /**
     * Get consultations analytics
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function consultationsAnalytics(Request $request)
    {
        Gate::authorize('viewConsultationsAnalytics', \App\Models\User::class);

        $validated = $request->validate([
            'veterinary_id' => 'required|exists:veterinaries,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $veterinaryId = $validated['veterinary_id'];
        $startDate = \Carbon\Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = \Carbon\Carbon::parse($validated['end_date'])->endOfDay();

        // Consultations grouped by date
        // Join: consultations -> pets -> clients -> vet_clients
        $consultationsByDate = Consultation::select(DB::raw('DATE(consultations.date) as date'), DB::raw('COUNT(*) as total_consultations'))
            ->join('pets', 'consultations.pet_id', '=', 'pets.id')
            ->join('clients', 'pets.client_id', '=', 'clients.id')
            ->join('vet_clients', 'clients.id', '=', 'vet_clients.client_id')
            ->where('vet_clients.veterinary_id', $veterinaryId)
            ->whereBetween('consultations.date', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(consultations.date)'))
            ->orderBy('date', 'asc')
            ->get();

        // Summary
        $totalConsultations = Consultation::join('pets', 'consultations.pet_id', '=', 'pets.id')
            ->join('clients', 'pets.client_id', '=', 'clients.id')
            ->join('vet_clients', 'clients.id', '=', 'vet_clients.client_id')
            ->where('vet_clients.veterinary_id', $veterinaryId)
            ->whereBetween('consultations.date', [$startDate, $endDate])
            ->count();

        return response()->json([
            'consultations_by_date' => $consultationsByDate,
            'summary' => [
                'total_consultations' => $totalConsultations,
            ],
        ]);
    }

    /**
     * Get dashboard consolidated metrics
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboard(Request $request)
    {
        Gate::authorize('viewDashboard', \App\Models\User::class);

        $validated = $request->validate([
            'veterinary_id' => 'required|exists:veterinaries,id',
        ]);

        $veterinaryId = $validated['veterinary_id'];
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        // Today's metrics
        $todaySales = Sale::where('veterinary_id', $veterinaryId)
            ->where('state', 'Completado')
            ->whereDate('date_sale', $today)
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(amount), 0) as total')
            ->first();

        $todayConsultations = Consultation::join('pets', 'consultations.pet_id', '=', 'pets.id')
            ->join('clients', 'pets.client_id', '=', 'clients.id')
            ->join('vet_clients', 'clients.id', '=', 'vet_clients.client_id')
            ->where('vet_clients.veterinary_id', $veterinaryId)
            ->whereDate('consultations.date', $today)
            ->count();

        // Today's cash balance from movements
        $todayBalance = Movement::where('veterinary_id', $veterinaryId)
            ->whereDate('created_at', $today)
            ->selectRaw('SUM(CASE WHEN type = "entrada" THEN amount ELSE -amount END) as balance')
            ->value('balance') ?? 0;

        // This month's totals
        $monthSales = Sale::where('veterinary_id', $veterinaryId)
            ->where('state', 'Completado')
            ->whereBetween('date_sale', [$monthStart, $monthEnd])
            ->sum('amount') ?? 0;

        $monthPurchases = Shopping::where('veterinary_id', $veterinaryId)
            ->where('state', 'Completado')
            ->whereBetween('date_shop', [$monthStart, $monthEnd])
            ->sum('amount') ?? 0;

        // Low stock products count
        $lowStockCount = Product::join('vet_product', 'products.id', '=', 'vet_product.product_id')
            ->where('vet_product.veterinary_id', $veterinaryId)
            ->where('products.stock', '<=', 10)
            ->count();

        return response()->json([
            'today' => [
                'sales_count' => (int) $todaySales->count,
                'sales_amount' => (float) $todaySales->total,
                'consultations_count' => $todayConsultations,
                'cash_balance' => (float) $todayBalance,
            ],
            'this_month' => [
                'sales_total' => (float) $monthSales,
                'purchases_total' => (float) $monthPurchases,
                'profit' => (float) ($monthSales - $monthPurchases),
            ],
            'alerts' => [
                'low_stock_products' => $lowStockCount,
            ],
        ]);
    }

    /**
     * Get products with low stock
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function lowStock(Request $request)
    {
        Gate::authorize('viewLowStock', \App\Models\User::class);

        $validated = $request->validate([
            'veterinary_id' => 'required|exists:veterinaries,id',
            'threshold' => 'nullable|integer|min:0|max:100',
        ]);

        $veterinaryId = $validated['veterinary_id'];
        $threshold = $validated['threshold'] ?? 10;

        $products = Product::select('products.id', 'products.name', 'products.code', 'products.stock', 'products.price')
            ->join('vet_product', 'products.id', '=', 'vet_product.product_id')
            ->where('vet_product.veterinary_id', $veterinaryId)
            ->where('products.stock', '<=', $threshold)
            ->orderBy('products.stock', 'asc')
            ->get();

        return response()->json([
            'threshold' => $threshold,
            'count' => $products->count(),
            'products' => $products,
        ]);
    }
    public function movementsAnalytics(Request $request)
    {
        Gate::authorize('viewAny', Movement::class);

        $start = $request->query('start'); // Ej: 2025-12-01
        $end = $request->query('end');     // Ej: 2025-12-03
        $veterinaryId = $request->query('veterinary_id'); // opcional

        // Query base
        $query = Movement::query();

        if ($start)
            $query->whereDate('created_at', '>=', $start);
        if ($end)
            $query->whereDate('created_at', '<=', $end);
        if ($veterinaryId)
            $query->where('veterinary_id', $veterinaryId);

        // Totales diarios
        $dailyTotals = $query->selectRaw("
                DATE(created_at) as movement_date,
                SUM(CASE WHEN type = 'entrada' THEN amount ELSE 0 END) as total_entry,
                SUM(CASE WHEN type = 'salida' THEN amount ELSE 0 END) as total_output,
                SUM(CASE WHEN type = 'entrada' THEN amount ELSE -amount END) as daily_balance
            ")
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at) ASC')
            ->get();

        // Calcular saldo acumulativo
        $saldoAcumulado = 0;
        $dailyTotals = $dailyTotals->map(function ($item) use (&$saldoAcumulado) {
            $saldoAcumulado += $item->daily_balance;
            $item->accumulated_balance = $saldoAcumulado;
            return $item;
        });

        // Total acumulado del rango completo
        $totalAcumulado = $saldoAcumulado;

        return response()->json([
            'daily_totals' => $dailyTotals,
            'total_accumulated' => $totalAcumulado,
        ]);
    }

    /**
     * Get KPI summary (Today vs Yesterday)
     */
    public function kpiSummary(Request $request)
    {
        Gate::authorize('viewDashboard', \App\Models\User::class); // Reusing dashboard permission

        $validated = $request->validate([
            'veterinary_id' => 'required|exists:veterinaries,id',
        ]);

        $veterinaryId = $validated['veterinary_id'];
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        // Helper function for Sales
        $getSalesStats = function ($date) use ($veterinaryId) {
            return Sale::where('veterinary_id', $veterinaryId)
                ->where('state', 'Completado')
                ->whereDate('date_sale', $date)
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(amount), 0) as total')
                ->first();
        };

        // Helper function for Purchases (for profit calc)
        $getPurchasesTotal = function ($date) use ($veterinaryId) {
            return Shopping::where('veterinary_id', $veterinaryId)
                ->where('state', 'Completado')
                ->whereDate('date_shop', $date)
                ->sum('amount');
        };

        // Helper function for Consultations
        $getConsultationsCount = function ($date) use ($veterinaryId) {
            return Consultation::join('pets', 'consultations.pet_id', '=', 'pets.id')
                ->join('clients', 'pets.client_id', '=', 'clients.id')
                ->join('vet_clients', 'clients.id', '=', 'vet_clients.client_id')
                ->where('vet_clients.veterinary_id', $veterinaryId)
                ->whereDate('consultations.date', $date)
                ->count();
        };

        // Get Data
        $salesToday = $getSalesStats($today);
        $salesYesterday = $getSalesStats($yesterday);

        $purchasesToday = $getPurchasesTotal($today);
        $purchasesYesterday = $getPurchasesTotal($yesterday);

        $consultationsToday = $getConsultationsCount($today);
        $consultationsYesterday = $getConsultationsCount($yesterday);

        return response()->json([
            'consultations' => [
                'today' => $consultationsToday,
                'yesterday' => $consultationsYesterday,
            ],
            'sales' => [
                'today' => [
                    'count' => (int) $salesToday->count,
                    'amount' => (float) $salesToday->total,
                ],
                'yesterday' => [
                    'count' => (int) $salesYesterday->count,
                    'amount' => (float) $salesYesterday->total,
                ],
            ],
            'earnings' => [
                'today' => (float) ($salesToday->total - $purchasesToday),
                'yesterday' => (float) ($salesYesterday->total - $purchasesYesterday),
            ],
        ]);
    }

    /**
     * Get last 5 attended pets
     */
    public function recentPatients(Request $request)
    {
        Gate::authorize('viewDashboard', \App\Models\User::class);

        $validated = $request->validate([
            'veterinary_id' => 'required|exists:veterinaries,id',
        ]);

        $recentConsultations = Consultation::select('pet_id', DB::raw('MAX(date) as date'))
            ->whereHas('pet.client.veterinaries', function ($query) use ($validated) {
                $query->where('veterinaries.id', $validated['veterinary_id']);
            })
            ->groupBy('pet_id')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->with([
                'pet' => function ($query) {
                    $query->select('id', 'name', 'client_id', 'race_id')
                        ->with(['files', 'client:id,name,last_name', 'race.typePet:id,name']);
                }
            ])
            ->get();

        $recentPets = $recentConsultations->map(function ($consultation) {
            $pet = $consultation->pet;
            return [
                'date' => $consultation->date,
                'pet_name' => $pet->name,
                'client_name' => $pet->client->name,
                'client_last_name' => $pet->client->last_name,
                'species' => $pet->race ? $pet->race->typePet->name : 'N/A',
            ];
        });
        return response()->json($recentPets);
    }

    /**
     * Get top 5 selling products
     */
    public function topSellingProducts(Request $request)
    {
        Gate::authorize('viewSalesAnalytics', \App\Models\User::class);

        $validated = $request->validate([
            'veterinary_id' => 'required|exists:veterinaries,id',
        ]);

        $topProducts = Product::select('products.id', 'products.name', 'products.code')
            ->selectRaw('SUM(product_sale.quantity) as total_quantity_sold')
            ->selectRaw('SUM(product_sale.quantity * product_sale.price_unit) as total_earnings')
            ->join('product_sale', 'products.id', '=', 'product_sale.product_id')
            ->join('sales', 'product_sale.sale_id', '=', 'sales.id')
            ->where('sales.veterinary_id', $validated['veterinary_id'])
            ->where('sales.state', 'Completado')
            ->groupBy('products.id', 'products.name', 'products.code')
            ->orderByDesc('total_quantity_sold')
            ->limit(5)
            ->get();

        return response()->json($topProducts);
    }

    /**
     * Get frequent consultations by reason
     */
    public function frequentConsultations(Request $request)
    {
        Gate::authorize('viewConsultationsAnalytics', \App\Models\User::class);

        $validated = $request->validate([
            'veterinary_id' => 'required|exists:veterinaries,id',
        ]);

        $frequent = Consultation::select('reason', DB::raw('COUNT(*) as count'))
            ->join('pets', 'consultations.pet_id', '=', 'pets.id')
            ->join('clients', 'pets.client_id', '=', 'clients.id')
            ->join('vet_clients', 'clients.id', '=', 'vet_clients.client_id')
            ->where('vet_clients.veterinary_id', $validated['veterinary_id'])
            ->whereNotNull('reason')
            ->groupBy('reason')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return response()->json($frequent);
    }

    /**
     * Get critical inventory
     */
    public function criticalInventory(Request $request)
    {
        Gate::authorize('viewLowStock', \App\Models\User::class);

        $validated = $request->validate([
            'veterinary_id' => 'required|exists:veterinaries,id',
        ]);

        $veterinaryId = $validated['veterinary_id'];

        // Products about to run out (stock between 1 and 10) - CAP 10
        $lowStock = Product::select('products.name', 'products.stock')
            ->join('vet_product', 'products.id', '=', 'vet_product.product_id')
            ->where('vet_product.veterinary_id', $veterinaryId)
            ->where('products.stock', '>', 0)
            ->where('products.stock', '<=', 10) // Umbral fijo de 10
            ->orderBy('products.stock', 'asc')
            ->limit(5)
            ->get();

        // Products out of stock (stock 0) - All of them
        $outOfStock = Product::select('products.name')
            ->join('vet_product', 'products.id', '=', 'vet_product.product_id')
            ->where('vet_product.veterinary_id', $veterinaryId)
            ->where('products.stock', '=', 0)
            ->get();

        return response()->json([
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
        ]);
    }
}
