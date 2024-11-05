<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaymentRequest;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments=Payment::with(['savingGroup', 'subscriber'])->orderBy('created_at','desc')->paginate(10);
        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $savingGroupId = $request->input('saving_group_id');
        $subscriberId = $request->input('subscriber_id');
        $dayNumber = $request->input('day_number');
        $cycleNumber = $request->input('cycle_number');
        $payAllUnpaid = $request->input('pay_all_unpaid', false);

        if ($payAllUnpaid) {    
            // Logic to add payments for all previous unpaid days up to the current day
            for ($i = 1; $i <= $dayNumber; $i++) {
                if (
                    !Payment::where('saving_group_id', $savingGroupId)
                        ->where('subscriber_id', $subscriberId)
                        ->where('day_number', $i)
                        ->where('cycle_number', $cycleNumber)
                        ->exists()
                ) {
                    Payment::create([
                        'saving_group_id' => $savingGroupId,
                        'subscriber_id' => $subscriberId,
                        'day_number' => $i,
                        'cycle_number' => $cycleNumber,
                    ]);
                }
            }
        } else {
            // Logic to add payment only for the selected day
            Payment::create([
                'saving_group_id' => $savingGroupId,
                'subscriber_id' => $subscriberId,
                'day_number' => $dayNumber,
                'cycle_number' => $cycleNumber,
            ]);
        }

        return redirect()->back()->with('success', 'Payment(s) recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
