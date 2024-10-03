<?php

namespace App\Http\Controllers;

use App\Models\SavingGroup;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subscribers = Subscriber::all();
        return view('subscribers.index', compact('subscribers'));
    }

    public function show_payments(int $subscriber_id)
    {
        $subscriber = Subscriber::with(['saving_group'])->findOrFail($subscriber_id);
        $payments = static::get_specific_payments_format($subscriber->payments()->select('day_number', 'cycle_number')->get());
        return view('subscribers.payments', compact(['subscriber', 'payments']));

    }
    private function get_specific_payments_format( $payments)
    {
        $cycleDays = []; 
        foreach ($payments as $payment) {
            $cycleNumber =  $payment->cycle_number; 
            $dayNumber =  $payment->day_number; 

            if (!isset($cycleDays[$cycleNumber])) {
                $cycleDays[$cycleNumber] = [];
            }

            $cycleDays[$cycleNumber][] = $dayNumber; 
        }
        return $cycleDays;
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscriber $subscriber)
    {
        return $subscriber;

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subscriber $subscriber)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subscriber $subscriber)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscriber $subscriber)
    {
        //
    }
}
