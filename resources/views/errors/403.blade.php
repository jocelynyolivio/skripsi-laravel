<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>403 - Access Denied</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-container {
            background: white;
            border-radius: 1rem;
            padding: 3rem;
            box-shadow: 0 0.75rem 2rem rgba(0, 0, 0, 0.1);
            max-width: 500px;
            text-align: center;
        }

        .error-code {
            font-size: 5rem;
            font-weight: bold;
            color: #dc3545;
        }

        .error-message {
            font-size: 1.25rem;
            color: #6c757d;
        }

        .btn-back {
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">403</div>
        <h4 class="mb-3">Access Denied</h4>
        <p class="error-message">
            Sorry, you do not have permission to access this page.<br>
            Please contact your manager if you believe this is a mistake.
        </p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-back">
            ← Back to Dashboard
        </a>
    </div>
</body>
</html>
