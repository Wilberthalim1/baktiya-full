<?php
namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use App\Models\SupplierPayment;
use App\Models\CustomerPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    public function supplierIndex()
    {
        $invoices = PurchaseInvoice::with('supplier', 'purchaseOrder')
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->latest()
            ->get();

        $payments = SupplierPayment::with('supplier', 'purchaseInvoice', 'creator')
            ->latest()
            ->paginate(10);

        return view('accounting.supplier.index', compact('invoices', 'payments'));
    }

    public function supplierCreate(Request $request)
    {
        $invoice = PurchaseInvoice::with('supplier', 'purchaseOrder.items.product')
            ->findOrFail($request->invoice_id);

        $docNo = SupplierPayment::generateDocNo();

        return view('accounting.supplier.create', compact('invoice', 'docNo'));
    }

    public function supplierStore(Request $request)
    {
        $request->validate([
            'purchase_invoice_id' => 'required|exists:purchase_invoices,id',
            'amount'              => 'required|numeric|min:1',
            'payment_date'        => 'required|date',
            'bank_name'           => 'required|string|max:100',
            'account_number'      => 'required|string|max:50',
            'remarks'             => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            SupplierPayment::create([
                'doc_no'              => SupplierPayment::generateDocNo(),
                'purchase_invoice_id' => $request->purchase_invoice_id,
                'supplier_id'         => PurchaseInvoice::find($request->purchase_invoice_id)->supplier_id,
                'created_by'          => Auth::id(),
                'amount'              => $request->amount,
                'payment_method'      => 'Transfer Bank',
                'bank_name'           => $request->bank_name,
                'account_number'      => $request->account_number,
                'payment_date'        => $request->payment_date,
                'status'              => 'pending_approval',
                'remarks'             => $request->remarks,
            ]);
        });

        return redirect()->route('accounting.supplier.index')
            ->with('success', 'Pembayaran berhasil disubmit! Menunggu approval management.');
    }

    public function supplierApprove(SupplierPayment $payment)
    {
        DB::transaction(function () use ($payment) {
            $payment->update([
                'status'      => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            $invoice = $payment->purchaseInvoice;
            $newPaid = $invoice->paid_amount + $payment->amount;
            $invoice->update([
                'paid_amount'    => $newPaid,
                'payment_status' => $newPaid >= $invoice->total ? 'paid' : 'partial',
            ]);
        });

        return back()->with('success', 'Pembayaran disetujui! Invoice telah diupdate.');
    }

    public function supplierReject(SupplierPayment $payment)
    {
        $payment->update(['status' => 'rejected']);
        return back()->with('success', 'Pembayaran ditolak.');
    }

    public function customerIndex()
    {
        $invoices = SalesInvoice::with('customer', 'salesOrder')
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->latest()
            ->get();

        $payments = CustomerPayment::with('customer', 'salesInvoice', 'creator')
            ->latest()
            ->paginate(10);

        return view('accounting.customer.index', compact('invoices', 'payments'));
    }

    public function customerCreate(Request $request)
    {
        $invoice = SalesInvoice::with('customer', 'salesOrder')
            ->findOrFail($request->invoice_id);

        $docNo = CustomerPayment::generateDocNo();

        return view('accounting.customer.create', compact('invoice', 'docNo'));
    }

    public function customerStore(Request $request)
    {
        $request->validate([
            'sales_invoice_id' => 'required|exists:sales_invoices,id',
            'amount'           => 'required|numeric|min:1',
            'payment_date'     => 'required|date',
            'remarks'          => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $invoice = SalesInvoice::find($request->sales_invoice_id);

            CustomerPayment::create([
                'doc_no'           => CustomerPayment::generateDocNo(),
                'sales_invoice_id' => $request->sales_invoice_id,
                'customer_id'      => $invoice->customer_id,
                'created_by'       => Auth::id(),
                'amount'           => $request->amount,
                'payment_method'   => 'Transfer Bank',
                'payment_date'     => $request->payment_date,
                'remarks'          => $request->remarks,
            ]);

            $newPaid = $invoice->paid_amount + $request->amount;
            $invoice->update([
                'paid_amount'    => $newPaid,
                'payment_status' => $newPaid >= $invoice->total ? 'paid' : 'partial',
            ]);
        });

        return redirect()->route('accounting.customer.index')
            ->with('success', 'Pelunasan customer berhasil dicatat!');
    }
}
