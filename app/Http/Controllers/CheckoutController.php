<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string',
            'total' => 'required|numeric',
            'payment_method' => 'required|string',
        ]);

        $userId = session('user_id') ?? session('id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Kailangan munang mag-log in bago makapag-checkout.');
        }

        $documentFileName = null;
        if ($request->hasFile('document_file') && $request->file('document_file')->isValid()) {
            $file = $request->file('document_file');
            $documentFileName = 'doc_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $documentFileName);
        }

        $proofFileName = null;
        if ($request->hasFile('proof_payment') && $request->file('proof_payment')->isValid()) {
            $proof = $request->file('proof_payment');
            $proofFileName = 'proof_' . time() . '_' . uniqid() . '.' . $proof->getClientOriginalExtension();
            $proof->move(public_path('uploads'), $proofFileName);
        }

        DB::table('orders')->insert([
            'user_id' => $userId,
            'order_code' => 'ORD-' . strtoupper(uniqid()),
            'product_name' => $request->input('product_name', 'Standard Print'),
            'document_file' => $documentFileName,
            'proof_image' => $proofFileName,
            'total' => $request->input('total'),
            'payment_method' => $request->input('payment_method'),
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('my.orders')->with('success', 'Matagumpay na naipadala ang iyong order!');
    }
}