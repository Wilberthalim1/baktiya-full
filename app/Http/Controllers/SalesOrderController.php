<?php
namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\InventoryTransfer;
use App\Models\InventoryTransferItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesOrder::with('customer', 'sales');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('so_number', 'like', '%'.$request->search.'%')
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', '%'.$request->search.'%'));
            });
        }

        if ($request->status) $query->where('status', $request->status);

        $user = Auth::user();
        if ($user->role === 'sales') $query->where('sales_id', $user->id);

        $salesOrders = $query->latest()->paginate(15);
        return view('sales.index', compact('salesOrders'));
    }

    public function create()
    {
        $customers = Customer::where('status', 'active')->orderBy('name')->get();
        $products = Product::with('category')->where('is_active', true)->orderBy('name')->get();
        return view('sales.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'          => 'required|exists:customers,id',
            'order_date'           => 'required|date',
            'required_date'        => 'required|date|after_or_equal:order_date',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.unit_price'   => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $so = SalesOrder::create([
                'so_number'     => SalesOrder::generateNumber(),
                'customer_id'   => $request->customer_id,
                'sales_id'      => Auth::id(),
                'order_date'    => $request->order_date,
                'required_date' => $request->required_date,
                'status'        => 'draft',
                'notes'         => $request->notes,
                'discount'      => $request->discount ?? 0,
                'subtotal'      => 0, 'tax' => 0, 'total' => 0,
            ]);

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $qty = $item['quantity'];
                $available = min($qty, $product->stock_quantity);
                $needPurchase = max(0, $qty - $available);

                SalesOrderItem::create([
                    'sales_order_id'    => $so->id,
                    'product_id'        => $item['product_id'],
                    'quantity'          => $qty,
                    'qty_available'     => $available,
                    'qty_need_purchase' => $needPurchase,
                    'unit_price'        => $item['unit_price'],
                    'discount'          => $item['discount'] ?? 0,
                    'total'             => $qty * $item['unit_price'] - ($item['discount'] ?? 0),
                ]);
            }

            $so->load('items');
            $so->recalculate();
        });

        return redirect()->route('sales.index')->with('success', 'Sales Order berhasil dibuat!');
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load('customer', 'sales', 'items.product', 'inventoryTransfer');
        return view('sales.show', compact('salesOrder'));
    }

    public function approve(SalesOrder $salesOrder)
    {
        DB::transaction(function () use ($salesOrder) {
            $salesOrder->load('items');
            $salesOrder->update(['status' => 'approved']);

            $docNo = 'IT' . substr($salesOrder->so_number, -4);

            $transfer = InventoryTransfer::create([
                'doc_no'         => $docNo,
                'sales_order_id' => $salesOrder->id,
                'created_by'     => Auth::id(),
                'status'         => 'pending',
            ]);

            foreach ($salesOrder->items as $item) {
                InventoryTransferItem::create([
                    'inventory_transfer_id' => $transfer->id,
                    'product_id'            => $item->product_id,
                    'qty_request'           => $item->quantity,
                    'qty_transfer'          => 0,
                    'unit_price'            => $item->unit_price,
                ]);
            }
        });

        return back()->with('success', 'SO disetujui! Inventory Transfer Request otomatis dibuat.');
    }

    public function cancel(SalesOrder $salesOrder)
    {
        $salesOrder->update(['status' => 'cancelled']);
        return back()->with('success', 'Sales Order dibatalkan.');
    }
}
