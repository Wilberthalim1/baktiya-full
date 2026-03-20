<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\PurchaseOrder;
use App\Models\SalesInvoice;
use App\Models\StockMovement;

class DashboardInventoryController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::where('is_active', true)->count(),
            'low_stock' => Product::where('is_active', true)->whereRaw('stock_quantity <= min_stock')->count(),
            'out_of_stock' => Product::where('is_active', true)->where('stock_quantity', '<=', 0)->count(),
            'total_so' => SalesOrder::whereMonth('created_at', now()->month)->count(),
            'total_po' => PurchaseOrder::whereMonth('created_at', now()->month)->count(),
            'pending_invoices' => SalesInvoice::where('payment_status', 'unpaid')->count(),
        ];

        $low_stock_products = Product::with('category')
            ->where('is_active', true)
            ->whereRaw('stock_quantity <= min_stock')
            ->orderBy('stock_quantity')
            ->take(10)
            ->get();

        $recent_so = SalesOrder::with('customer')
            ->latest()
            ->take(5)
            ->get();

        $recent_movements = StockMovement::with('product')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact('stats', 'low_stock_products', 'recent_so', 'recent_movements'));
    }

    public function inventory(\Illuminate\Http\Request $request)
    {
        $query = Product::with('category')->where('is_active', true);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('code', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->stock_status === 'low') {
            $query->whereRaw('stock_quantity <= min_stock')->where('stock_quantity', '>', 0);
        } elseif ($request->stock_status === 'out') {
            $query->where('stock_quantity', '<=', 0);
        }

        $products = $query->orderBy('name')->paginate(20);
        $categories = \App\Models\Category::all();

        return view('inventory.index', compact('products', 'categories'));
    }
}
