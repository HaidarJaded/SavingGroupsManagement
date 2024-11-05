@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h1>{{ $savingGroup->name }} - الدفعات للدور {{ $savingGroup->current_cycle }}</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th></th>
                @for ($i = 0; $i < $savingGroup->days_per_cycle; $i++)
                    <th> {{ $i + 1 }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach ($subscribersData as $data)
                <tr>
                    <td>{{ $data['subscriber']->full_name }}</td>
                    @for ($j = 0; $j < $savingGroup->days_per_cycle; $j++)
                        @if (in_array($j + 1, $data['payments']))
                            <td class="crossed-box">
                                {{ \Carbon\Carbon::parse($startDate)
                            ->addDays($j)
                            ->format('Y-m-d') }}
                            </td>
                        @else
                            <td>
                                <form method="POST" action="{{ route('payments.store') }}" class="payment-form">
                                    @csrf
                                    <input type="hidden" name="saving_group_id" value="{{ $savingGroup->id }}">
                                    <input type="hidden" name="subscriber_id" value="{{ $data['subscriber']->id }}">
                                    <input type="hidden" name="day_number" value="{{ $j + 1 }}">
                                    <input type="hidden" name="cycle_number" value="{{ $savingGroup->current_cycle }}">
                                    <button type="button" class="btn btn-link"
                                        onclick="confirmPayment(this, {{ json_encode($data['payments']) }})">
                                        {{ \Carbon\Carbon::parse($startDate)
                            ->addDays($j)
                            ->format('Y-m-d') }}
                                    </button>
                                </form>
                            </td>
                        @endif
                    @endfor
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    function confirmPayment(button, previousPayments) {
        const currentDay = parseInt(button.closest('form').querySelector('input[name="day_number"]').value);
        let allPreviousPaid = true;

        for (let i = 1; i < currentDay; i++) {
            if (!previousPayments.includes(i)) {
                allPreviousPaid = false;
                break;
            }
        }

        if (allPreviousPaid) {
            if (confirm(`هل تريد اضافة دفعة لليوم رقم ${currentDay}?`)) {
                button.closest('form').submit();
            }
            return;
        }
        if (!confirm(`يوجد أيام قبل اليوم رقم  ${currentDay} غير مدفوعة هل تريد إضافة دفعات لها?`)) {
            return;
        }
        const form = button.closest('form');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'pay_all_unpaid';
        input.value = 'true';
        form.appendChild(input);
        form.submit();
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