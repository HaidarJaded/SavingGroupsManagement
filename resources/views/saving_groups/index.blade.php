@extends('layouts.sidebar')

@section('content')
    <div class="container">
        <h1>الجمعيات</h1>
        <a href="{{ route('saving_groups.create') }}" class="btn btn-primary">إنشاء جمعية جديدة</a>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>عدد المشتركين</th>
                    <th>المبلغ باليوم</th>
                    <th>طول الدور</th>
                    <th>المبلغ الاجمالي</th>
                    <th>تاريخ بدء الجمعية</th>
                    <th>تاريخ انتهاء الجمعية</th>
                    <th>رقم الدور الحالي</th>
                    <th>رقم اليوم الحالي بالدور</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($savingGroups as $savingGroup)
                    <tr>
                        <td>{{ $savingGroup->name }}</td>
                        <td>{{ $savingGroup->subscribers_count }}</td>
                        <td>{{ $savingGroup->amount_per_day }}</td>
                        <td>{{ $savingGroup->days_per_cycle }}</td>
                        <td>{{ $savingGroup->total_amount }}</td>
                        <td>{{ $savingGroup->start_date }}</td>
                        <td>{{ $savingGroup->end_date }}</td>
                        <td>{{ $savingGroup->current_cycle }}</td>
                        <td>{{ $savingGroup->current_day }}</td>
                        <td>


                            <!-- Button for other CRUD actions like edit/delete -->
                            <!-- Example: -->
                            <a href="{{ route('saving_group_subscribers', $savingGroup->id) }}" class="btn btn-primary">عرض المشتركين</a>
                            <form action="{{ route('saving_groups.destroy', $savingGroup->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">حذف</button>
                            </form>
                        </td>
                    </tr>

                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    <!-- Make sure Bootstrap JS and jQuery are included -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
