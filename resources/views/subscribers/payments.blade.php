@extends('layouts.sidebar')

@section('content')
<div class="container" >
   <center>
       <h1  style="direction:rtl"> المدفوعات ل {{ $subscriber->name }}</h1>
   </center>

    <table class="table table-bordered text-center">
        <thead>
            <tr>
                @for ($j = 0; $j < $subscriber->saving_group->days_per_cycle; $j++)
                    <th>اليوم {{ $j + 1 }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < $subscriber->saving_group->subscribers_count; $i++)
                <tr>
                    @for ($j = 0; $j < $subscriber->saving_group->days_per_cycle; $j++)
                        @if (array_key_exists($i + 1, $payments) && in_array($j + 1, $payments[$i + 1]))
                            <td class="crossed-box">
                                {{ \Carbon\Carbon::parse($subscriber->saving_group->start_date)
                            ->addDays(($i * $subscriber->saving_group->days_per_cycle) + $j)
                            ->format('Y-m-d') }}
                            </td>
                        @else
                            <td>
                                <form method="POST" action="{{ route('payments.store') }}" class="payment-form">
                                    @csrf
                                    <input type="hidden" name="saving_group_id" value="{{ $subscriber->saving_group->id }}">
                                    <input type="hidden" name="subscriber_id" value="{{ $subscriber->id }}">
                                    <input type="hidden" name="day_number" value="{{ $j + 1 }}">
                                    <input type="hidden" name="cycle_number" value="{{ $i + 1 }}">
                                    <button type="button" class="btn btn-link" onclick="confirmPayment(this)">
                                        {{ \Carbon\Carbon::parse($subscriber->saving_group->start_date)
                            ->addDays(($i * $subscriber->saving_group->days_per_cycle) + $j)
                            ->format('Y-m-d') }}
                                    </button>
                                </form>
                            </td>
                        @endif
                    @endfor
                </tr>
            @endfor
        </tbody>
    </table>

    <a href="{{ route('saving_groups.index') }}" class="btn btn-secondary">العودة الى صفحة الجمعيات</a>
</div>
<script>
    function confirmPayment(button) {
        // Display the confirmation dialog
        const confirmed = confirm("هل أنت متأكد من أنك تريد إضافة دفعة لهذا اليوم؟");
        if (confirmed) {
            // If confirmed, find the form and submit it
            button.closest('form').submit();
        }
    }
</script>
<style>
    /* Table styling */
    .table {
        width: 100%;
        margin: 20px 0;
        border-collapse: collapse;
    }

    .table th,
    .table td {
        padding: 15px;
        border: 1px solid #ddd;
        text-align: center;
        vertical-align: middle;
        position: relative;
    }

    .table th {
        background-color: #f4f4f4;
    }

    /* Improved Crossed out box for paid days */
    .crossed-box {
        background-color: #f9f9f9;
        position: relative;
        color: #333;
        width: 100px;
        /* Adjust width as needed */
        height: 50px;
        /* Adjust height as needed */
    }

    .crossed-box::before,
    .crossed-box::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 70%;
        /* Make sure the line stays inside the box */
        height: 2px;
        background-color: black;
        transform-origin: center;
        transform: translate(-50%, -50%) rotate(45deg);
    }

    .crossed-box::after {
        transform: translate(-50%, -50%) rotate(-45deg);
    }

    /* Style the unpaid day links */
    a {
        text-decoration: none;
        color: #007bff;
    }

    a:hover {
        color: #0056b3;
    }
</style>

@endsection