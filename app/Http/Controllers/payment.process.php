use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

public function process(Request $request)
{
    $userId = session('user_id') ?? session('id');

    if (!$userId) {
        return redirect()->route('login')->with('message', 'Mag-login muna.');
    }

    $proof = null;
    if ($request->hasFile('proof_payment')) {
        $file  = $request->file('proof_payment');
        $proof = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads'), $proof);
    }

    DB::table('orders')->insert([
        'user_id'        => $userId,
        'order_code'     => 'ORD-' . strtoupper(substr(uniqid(), -6)),
        'product_name'   => $request->product_name,
        'specifications' => $request->specifications,
        'document_file'  => $request->uploaded_doc,
        'total'          => $request->total_amount,
        'payment_method' => $request->payment_method,
        'payment_status' => $request->payment_method === 'cash_on_pickup' ? 'Unpaid' : 'Paid',
        'status'         => 'Pending',
        'created_at'     => now(),
    ]);

    return redirect()->route('home', ['ordered' => 'success']);
}