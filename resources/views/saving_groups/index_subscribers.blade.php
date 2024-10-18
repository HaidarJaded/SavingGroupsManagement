@extends('layouts.sidebar')

@section('content')
    <div class="container" style="direction: rtl">
        <h1>المشتركين ل جمعية {{ $savingGroup->name }}</h1>
        
        <p><strong>عدد المشتركين:</strong> {{ $savingGroup->subscribers->count() }}</p>
        <a href="{{ route('saving_group_payments',$savingGroup->id) }}" class="btn btn-primary" style="margin-bottom: 10px;">التحكم بدفعات هذا الدور</a>
        @if ($savingGroup->subscribers->isEmpty())
            <p>No subscribers found for this saving group.</p>
        @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>الكنية</th>
                        <th>رقم الهاتف</th>
                        <th>الكود</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($savingGroup->subscribers as $subscriber)
                        <tr>
                            <td>{{ $subscriber->name }}</td>
                            <td>{{ $subscriber->last_name }}</td>
                            <td>{{ $subscriber->phone }}</td>
                            <td>{{ $subscriber->code }}</td>
                            <td><a href="{{ route('payments.subscriber', $subscriber->id) }}" class="btn btn-primary">عرض الدفعات</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <a href="{{ route('saving_groups.index') }}" class="btn btn-secondary">العودة إلى صفحة الجمعيات</a>
    </div>
@endsection
