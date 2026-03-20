<?php
namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchasingController extends Controller
{
    // PR
    public function prIndex(Request $request)
    {
        $query = PurchaseRequest::with('creator', 'items');
        if ($request->search) {
            $query->where('doc_no', 'like', '%'.$request->search.'%')
                  ->orWhere('remarks', 'like', '%'.$request->search.'%');
        }
        if ($request->status) $query->where('status', $request->status);
        $prs = $query->latest()->paginate(15);
        return view('purchasing.pr.index', compact('prs'));
    }

    public function prCreate()
    {
        $products = Product::with('category')->where('is_active', true)->orderBy('name')->get();
        $docNo = PurchaseRequest::generateDocNo();
        return view('purchasing.pr.create', compact('products', 'docNo'));
    }

    public function prStore(Request $request)
    {
        $request->validate([
            'request_date' => 'required|date',
            'items' => 'required|array|min:1|max:7',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $pr = PurchaseRequest::create([
                'doc_no' => PurchaseRequest::generateDocNo(),
                'created_by' => Auth::id(),
                'request_date' => $request->request_date,
                'status' => $request->action === 'pending' ? 'pending' : 'draft',
                'remarks' => $request->remarks,
            ]);

            foreach ($request->items as $item) {
                PurchaseRequestItem::create([
                    'purchase_request_id' => $pr->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'remarks' => $item['remarks'] ?? 'low_stock',
                ]);
            }
        });

        return redirect()->route('purchasing.pr.index')->with('success', 'Purchase Request berhasil dibuat!');
    }

    public function prShow(PurchaseRequest $pr)
    {
        $pr->load('creator', 'approver', 'items.product', 'purchaseOrders.supplier');
        return view('purchasing.pr.show', compact('pr'));
    }

    public function prApprove(PurchaseRequest $pr)
    {
        $pr->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'PR ' . $pr->doc_no . ' telah disetujui!');
    }

    public function prReject(PurchaseRequest $pr)
    {
        $pr->update(['status' => 'rejected']);
        return back()->with('success', 'PR ditolak.');
    }

    // PO
    public function poIndex()
    {
        $pos = PurchaseOrder::with('supplier', 'purchaseRequest')->latest()->paginate(15);
        return view('purchasing.po.index', compact('pos'));
    }

    public function poCreate(Request $request)
    {
        $pr = null;
        $error = null;

        if ($request->doc_no) {
            $pr = PurchaseRequest::with('items.product', 'creator')
                ->where('doc_no', strtoupper($request->doc_no))->first();
            if (!$pr) {
                $error = 'Doc No "' . $request->doc_no . '" tidak ditemukan!';
            } elseif ($pr->status !== 'approved') {
                $error = 'PR ' . $pr->doc_no . ' belum disetujui (Status: ' . $pr->status_label . ')';
                $pr = null;
            }
        }

        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $docNo = PurchaseOrder::generateDocNo();
        return view('purchasing.po.create', compact('pr', 'suppliers', 'docNo', 'error'));
    }

    public function poStore(Request $request)
    {
        $request->validate([
            'purchase_request_id' => 'required|exists:purchase_requests,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'req_deliver_date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $po = PurchaseOrder::create([
                'doc_no' => PurchaseOrder::generateDocNo(),
                'purchase_request_id' => $request->purchase_request_id,
                'supplier_id' => $request->supplier_id,
                'created_by' => Auth::id(),
                'order_date' => $request->order_date,
                'req_deliver_date' => $request->req_deliver_date,
                'status' => 'draft',
                'remarks' => $request->remarks,
                'total_price' => 0,
            ]);

            foreach ($request->items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'qty_received' => 0,
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            $po->load('items');
            $po->recalculate();
            PurchaseRequest::find($request->purchase_request_id)->update(['status' => 'ordered']);
        });

        return redirect()->route('purchasing.po.index')->with('success', 'Purchase Order berhasil dibuat!');
    }

    public function poShow(PurchaseOrder $po)
    {
        $po->load('supplier', 'purchaseRequest.items.product', 'items.product', 'creator', 'goodsReceipts');
        return view('purchasing.po.show', compact('po'));
    }

    public function poSend(PurchaseOrder $po)
    {
        $po->update(['status' => 'sent']);
        return back()->with('success', 'PO ' . $po->doc_no . ' berhasil dikirim ke Supplier!');
    }

    public function poCancel(PurchaseOrder $po)
    {
        $po->update(['status' => 'cancelled']);
        PurchaseRequest::find($po->purchase_request_id)->update(['status' => 'approved']);
        return back()->with('success', 'PO dibatalkan.');
    }

    // GRPO
    public function grpoStore(Request $request, PurchaseOrder $po)
    {
        $request->validate([
            'receipt_date' => 'required|date',
            'received_by'  => 'required|string|max:100',
            'remarks'      => 'nullable|string',
            'items'        => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($request, $po) {
            $gr = GoodsReceipt::create([
                'doc_no'              => GoodsReceipt::generateDocNo(),
                'purchase_order_id'   => $po->id,
                'created_by'          => Auth::id(),
                'received_by'         => $request->received_by,
                'receipt_date'        => $request->receipt_date,
                'status'              => 'accepted',
                'remarks'             => $request->remarks,
            ]);

            foreach ($request->items as $poItemId => $data) {
                $poItem = PurchaseOrderItem::find($poItemId);
                $qtyReceived = (int) $data['qty_received'];

                GoodsReceiptItem::create([
                    'goods_receipt_id'        => $gr->id,
                    'product_id'              => $poItem->product_id,
                    'purchase_order_item_id'  => $poItemId,
                    'qty_ordered'             => $poItem->quantity,
                    'qty_received'            => $qtyReceived,
                    'unit_price'              => $poItem->unit_price,
                    'condition'               => $data['condition'] ?? 'good',
                    'remarks'                 => $data['remarks'] ?? null,
                ]);

                $poItem->increment('qty_received', $qtyReceived);

                // Update stok produk
                $product = Product::find($poItem->product_id);
                $product->increment('stock_quantity', $qtyReceived);
            }

            $po->update(['status' => 'received']);
        });

        return redirect()->route('purchasing.po.show', $po)->with('success', 'GRPO berhasil! Stok produk telah diupdate.');
    }

    public function grpoCancel(PurchaseOrder $po)
    {
        $po->update(['status' => 'cancelled']);
        PurchaseRequest::find($po->purchase_request_id)->update(['status' => 'approved']);
        return redirect()->route('purchasing.po.index')->with('success', 'PO dibatalkan.');
    }
}
