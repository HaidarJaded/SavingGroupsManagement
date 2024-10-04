@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h2>المدفوعات</h2>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>الجمعية</th>
                <th>المشترك</th>
                <th>رقم الدور </th>
                <th>رقم اليوم  بالدور</th>
                <th>تاريخ الدفعة</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $payment)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $payment->savingGroup->name }}</td>
                    <td>{{ $payment->subscriber->full_name }}
                        @if ($payment->subscriber->phone)
                            ({{ $payment->subscriber->phone }})
                        @endif
                    </td>
                    <td>{{ $payment->cycle_number }}</td>
                    <td>{{ $payment->day_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $payments->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection