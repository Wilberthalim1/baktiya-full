<?php
namespace App\Http\Controllers;

use App\Models\InventoryTransfer;
use App\Models\InventoryTransferItem;
use App\Models\SalesOrder;
use App\Models\Product;
use App\Services\SalesInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    public function index()
    {
        $pending = InventoryTransfer::with('salesOrder.customer', 'items.product')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $transferred = InventoryTransfer::with('salesOrder.customer', 'processor')
            ->where('status', 'transferred')
            ->latest()
            ->paginate(10);

        return view('inventory.transfer.index', compact('pending', 'transferred'));
    }

    public function show(InventoryTransfer $transfer)
    {
        $transfer->load('salesOrder.customer', 'items.product', 'creator', 'processor');
        return view('inventory.transfer.show', compact('transfer'));
    }

    public function process(Request $request, InventoryTransfer $transfer)
    {
        $request->validate([
            'transfer_date' => 'required|date',
            'giver_name'    => 'required|string|max:100',
            'receiver_name' => 'required|string|max:100',
            'remarks'       => 'nullable|string',
        ]);

        // Cek stok mencukupi
        foreach ($transfer->items as $item) {
            if ($item->product->stock_quantity < $item->qty_request) {
                return back()->with('error', 'Stok ' . $item->product->name . ' tidak mencukupi! Stok tersedia: ' . $item->product->stock_quantity . ', dibutuhkan: ' . $item->qty_request);
            }
        }

        try {
            DB::transaction(function () use ($request, $transfer) {
                foreach ($transfer->items as $item) {
                    // Kurangi stok
                    $product = Product::find($item->product_id);
                    $stockBefore = $product->stock_quantity;
                    $product->decrement('stock_quantity', $item->qty_request);

                    // Catat stock movement
                    \App\Models\StockMovement::create([
                        'product_id'     => $item->product_id,
                        'created_by'     => Auth::id(),
                        'type'           => 'out',
                        'quantity'       => $item->qty_request,
                        'stock_before'   => $stockBefore,
                        'stock_after'    => $stockBefore - $item->qty_request,
                        'reference_type' => 'InventoryTransfer',
                        'reference_id'   => $transfer->id,
                        'unit_price'     => $item->unit_price,
                        'notes'          => 'Transfer ke SO: ' . $transfer->salesOrder->so_number,
                    ]);

                    $item->update(['qty_transfer' => $item->qty_request]);
                }

                $transfer->update([
                    'status'                => 'transferred',
                    'transfer_date'         => $request->transfer_date,
                    'processed_by'          => Auth::id(),
                    'giver_name'            => $request->giver_name,
                    'giver_confirmed_at'    => now(),
                    'receiver_name'         => $request->receiver_name,
                    'receiver_confirmed_at' => now(),
                    'remarks'               => $request->remarks,
                ]);

                // Update SO status
                $transfer->salesOrder->update(['status' => 'processing']);

                // Buat Sales Invoice otomatis
                app(SalesInvoiceService::class)->createFromTransfer($transfer);
            });
        } catch (\Throwable $e) {
            Log::error('Gagal memproses inventory transfer #' . $transfer->id . ': ' . $e->getMessage(), [
                'transfer_id' => $transfer->id,
                'exception'   => $e,
            ]);

            return back()->with('error', 'Terjadi kesalahan saat memproses transfer. Silakan coba lagi atau hubungi administrator.');
        }

        return redirect()->route('inventory.transfer.show', $transfer)
            ->with('success', 'Transfer berhasil! Stok telah dikurangi dan Sales Invoice otomatis dibuat.');
    }

    public function print(InventoryTransfer $transfer)
    {
        $transfer->load('salesOrder.customer', 'items.product', 'creator', 'processor');
        return view('inventory.transfer.print', compact('transfer'));
    }
}
