<!DOCTYPE html>
<html>

<head>
    <title>Subscriber Payments</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header">
                <h3>أهلا وسهلاً سيد {{ $subscriber->full_name }} هذه هي مدفوعاتك</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th></th>
                                @for ($j = 0; $j < $subscriber->saving_group->days_per_cycle; $j++)
                                    <th>اليوم {{ $j + 1 }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 0; $i < $subscriber->saving_group->subscribers_count; $i++)
                                            <tr>
                                                <td style="background-color: #f4f4f4;">الدور {{ $i + 1 }}</td>
                                                @for ($j = 0; $j < $subscriber->saving_group->days_per_cycle; $j++)
                                                                    @if (array_key_exists($i + 1, $payments) && in_array($j + 1, $payments[$i + 1]))
                                                                                        <td class="crossed-box">
                                                                                            {{ \Carbon\Carbon::parse($subscriber->saving_group->start_date)
                                                                        ->addDays(($i * $subscriber->saving_group->days_per_cycle) + $j)
                                                                        ->format('Y-m-d') }}
                                                                                        </td>
                                                                    @else
                                                                                        <td>
                                                                                            {{ \Carbon\Carbon::parse($subscriber->saving_group->start_date)
                                                                        ->addDays(($i * $subscriber->saving_group->days_per_cycle) + $j)
                                                                        ->format('Y-m-d') }}
                                                                                        </td>
                                                                    @endif
                                                @endfor
                                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>