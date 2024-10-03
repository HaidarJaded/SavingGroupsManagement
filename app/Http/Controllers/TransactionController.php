<?php

namespace App\Http\Controllers;

use App\Enums\TransactionTypes;
use App\Models\SavingGroup;
use App\Models\Subscriber;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::with(['savingGroup', 'subscriber'])->paginate(10);
        return view('transactions.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $savingGroups = SavingGroup::all();
        $subscribers = Subscriber::all();
        return view('transactions.create', compact('savingGroups', 'subscribers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'saving_group_id' => 'required|exists:saving_groups,id',
            'subscriber_id' => 'required|exists:subscribers,id',
            'amount' => 'required|numeric',
            'status' => 'required|in:' . implode(',', TransactionTypes::values()),
        ]);

        // Create a new transaction
        Transaction::create($request->all());

        return redirect()->route('transactions.index')->with('success', 'Transaction created successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
    }
}
