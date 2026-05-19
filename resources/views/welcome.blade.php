<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h1 class="card-title">Welcome to Our Store</h1>
                        <p class="card-text">Please select your login type:</p>
                        <a href="{{ route('admin.login') }}" class="btn btn-primary btn-lg me-3">Admin Login</a>
                        <a href="{{ route('customer.login') }}" class="btn btn-secondary btn-lg">Customer Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
