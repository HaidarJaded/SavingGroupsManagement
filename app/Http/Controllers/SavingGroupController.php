<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSavingGroupRequest;
use App\Http\Requests\UpdateSavingGroupRequest;
use App\Models\SavingGroup;
use App\Models\Subscriber;
use App\Models\SubscribersSavingGroups;
use Illuminate\Http\Request;

class SavingGroupController extends Controller
{

    public function saving_group_subscribers(int $saving_group_id)
    {
        $savingGroup = SavingGroup::with('subscribers')->findOrFail($saving_group_id);
        return view('saving_groups.index_subscribers', compact('savingGroup'));
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $savingGroups = SavingGroup::with('subscribers')->orderBy('created_at','desc')->get(); // Eager load subscribers
        return view('saving_groups.index', compact('savingGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('saving_groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateSavingGroupRequest $request)
    {
        $subscribers = $request->input('subscribers', []);
        if (count($subscribers) < 1) {
            return redirect()->back()->withErrors(['subscribers' => 'You must add at least one subscriber.'])->withInput();
        }
        $savingGroup = SavingGroup::create($request->all());
        foreach ($subscribers as $subscriber) {
            $subscriber['saving_group_id'] = $savingGroup->id;
            $subscriber = Subscriber::create($subscriber);
        }
        return redirect()->route('saving_groups.index')->with('success', 'Saving Group created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(SavingGroup $savingGroup)
    {
        return view('saving_groups.show', compact('savingGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SavingGroup $savingGroup)
    {
        return view('saving_groups.edit', compact('savingGroup'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSavingGroupRequest $request, SavingGroup $savingGroup)
    {
        $savingGroup->update($request->all());

        return redirect()->route('saving_groups.index')->with('success', 'Saving Group updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SavingGroup $savingGroup)
    {
        $savingGroup->delete();

        return redirect()->route('saving_groups.index')->with('success', 'Saving Group deleted successfully');
    }

    public function getSavingGroupPayments(int $saving_group_id)
    {
        $savingGroup = SavingGroup::with('subscribers.payments')->findOrFail($saving_group_id);
        $startDate = \Carbon\Carbon::parse($savingGroup->start_date)->addDays($savingGroup->days_per_cycle * ($savingGroup->current_cycle - 1));
        $subscribersData = $savingGroup->subscribers->map(function ($subscriber) use ($savingGroup) {
            $payments = $subscriber->payments()
                ->where('cycle_number', $savingGroup->current_cycle)
                ->where('saving_group_id', $savingGroup->id)
                ->pluck('day_number');
    
            return [
                'subscriber' => $subscriber,
                'payments' => $payments->toArray(), // Convert collection to array if needed
            ];
        });
        return view('saving_groups.payments_in_current_cycle', compact('savingGroup', 'subscribersData','startDate'));
    }
    
}
