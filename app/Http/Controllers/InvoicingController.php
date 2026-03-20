<?php
namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\SalesOrder;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoicingController extends Controller
{
    public function salesIndex()
    {
        $invoices = SalesInvoice::with('customer', 'salesOrder')->latest()->paginate(15);
        return view('invoicing.sales.index', compact('invoices'));
    }

    public function salesCreate(Request $request)
    {
        $so = SalesOrder::with('customer', 'items.product')->findOrFail($request->so_id);
        return view('invoicing.sales.create', compact('so'));
    }

    public function salesStore(Request $request)
    {
        return redirect()->route('invoicing.sales.index');
    }

    public function salesShow(SalesInvoice $invoice)
    {
        $invoice->load('customer', 'salesOrder', 'items.product', 'inventoryTransfer.creator');
        return view('invoicing.sales.show', compact('invoice'));
    }

    public function salesPayment(Request $request, SalesInvoice $invoice)
    {
        return back();
    }

    public function salesPdf(SalesInvoice $invoice)
    {
        $invoice->load('salesOrder.customer', 'items.product', 'inventoryTransfer.creator');

        $pdf = Pdf::loadView('invoicing.sales.pdf', compact('invoice'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('invoice-' . $invoice->inv_number . '.pdf');
    }

    public function purchaseIndex()
    {
        $pending_pos = PurchaseOrder::with('supplier', 'purchaseRequest')
            ->where('status', 'sent')
            ->latest()
            ->get();

        $completed_invoices = PurchaseInvoice::with('purchaseOrder.supplier')
            ->latest()
            ->paginate(10);

        return view('invoicing.purchase.index', compact('pending_pos', 'completed_invoices'));
    }

    public function purchaseCreate(Request $request)
    {
        $po = PurchaseOrder::with('supplier', 'purchaseRequest', 'items.product', 'creator')
            ->where('status', 'sent')
            ->findOrFail($request->po_id);

        $docNo = 'INV-P' . str_pad(PurchaseInvoice::count() + 1, 3, '0', STR_PAD_LEFT);

        return view('invoicing.purchase.create', compact('po', 'docNo'));
    }

    public function purchaseStore(Request $request)
    {
        $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'receipt_date'      => 'required|date',
            'received_by'       => 'required|string|max:100',
            'remarks'           => 'nullable|string',
            'items'             => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $po = PurchaseOrder::with('items.product')->findOrFail($request->purchase_order_id);

            $gr = GoodsReceipt::create([
                'doc_no'            => GoodsReceipt::generateDocNo(),
                'purchase_order_id' => $po->id,
                'created_by'        => Auth::id(),
                'received_by'       => $request->received_by,
                'receipt_date'      => $request->receipt_date,
                'status'            => 'accepted',
                'remarks'           => $request->remarks,
            ]);

            $total = 0;

            foreach ($request->items as $poItemId => $data) {
                $poItem = PurchaseOrderItem::find($poItemId);
                $qtyReceived = (int) $data['qty_received'];

                GoodsReceiptItem::create([
                    'goods_receipt_id'       => $gr->id,
                    'product_id'             => $poItem->product_id,
                    'purchase_order_item_id' => $poItemId,
                    'qty_ordered'            => $poItem->quantity,
                    'qty_received'           => $qtyReceived,
                    'unit_price'             => $poItem->unit_price,
                    'condition'              => $data['condition'] ?? 'good',
                    'remarks'                => $data['remarks'] ?? null,
                ]);

                if ($qtyReceived > 0) {
                    Product::find($poItem->product_id)->increment('stock_quantity', $qtyReceived);
                    $poItem->increment('qty_received', $qtyReceived);
                }

                $total += $qtyReceived * $poItem->unit_price;
            }

            $invNumber = 'INV-P' . str_pad(PurchaseInvoice::count() + 1, 3, '0', STR_PAD_LEFT);

            PurchaseInvoice::create([
                'inv_number'        => $invNumber,
                'purchase_order_id' => $po->id,
                'supplier_id'       => $po->supplier_id,
                'created_by'        => Auth::id(),
                'invoice_date'      => $request->receipt_date,
                'due_date'          => now()->addDays(30)->toDateString(),
                'status'            => 'received',
                'payment_status'    => 'unpaid',
                'subtotal'          => $total,
                'tax'               => 0,
                'discount'          => 0,
                'total'             => $total,
                'notes'             => $request->remarks,
            ]);

            $po->update(['status' => 'received']);
        });

        return redirect()->route('invoicing.purchase.index')
            ->with('success', 'GRPO berhasil! Stok diupdate dan Invoice Pembelian tersimpan.');
    }
}
