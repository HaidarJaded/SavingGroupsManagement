<!DOCTYPE html>
<html>
<head>
    <title>الاستعلام عن المدفوعات</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card w-50 p-4 shadow">
            <h2 class="text-center mb-4">الاستعلام عن المدفوعات</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('subscriber.login.submit') }}">
                @csrf
                <div class="form-group">
                    <label for="code">كود الاشتراك:</label>
                    <input type="password" name="code" id="code" class="form-control" placeholder="أدخل الكود الخاص بك" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">أستعلام</button>
            </form>
        </div>
    </div>
</body>
</html>
