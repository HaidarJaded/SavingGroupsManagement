@extends('layouts.sidebar')

@section('content')
    <div class="container">
        <h1>{{ $savingGroup->name }}</h1>
        <p>Subscribers Count: {{ $savingGroup->subscribers_count }}</p>
        <p>Amount Per Day: {{ $savingGroup->amount_per_day }}</p>
        <p>Total Amount: {{ $savingGroup->total_amount }}</p>
        <p>End Date: {{ $savingGroup->end_date }}</p>
    </div>
@endsection
