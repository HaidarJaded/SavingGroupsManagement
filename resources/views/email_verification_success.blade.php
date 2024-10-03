<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification Success</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f5f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .verification-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .verification-icon {
            /* Icon styling here (e.g., font-awesome or custom icon) */
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-icon">✅</div>
        <h1>{{$message}}</h1>
        <p>Thank you for verifying your email address. You can safely close this tab.</p>
    </div>
</body>
</html>
