@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h2>الحوالات</h2>
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
                <th>المبلغ</th>
                <th>نوع الحوالة</th>
                <th>وصف</th>
                <th>تاريخ الحوالة</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $transaction->savingGroup->name }}</td>
                    <td>{{ $transaction->subscriber->full_name }}
                        @if ($transaction->subscriber->phone)
                            ({{ $transaction->subscriber->phone }})
                        @endif
                    </td>
                    <td>{{ number_format($transaction->amount, 2) }}</td>
                    <td>{{ $transaction->status }}</td>
                    <td>{{ $transaction->description }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->payment_date)->format('Y-m-d H:i') }}</td>
                    <td>
                        <form action="{{ route('transactions.destroy', $transaction->id ) }}" method="POST"
                            style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">حذف</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $transactions->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection