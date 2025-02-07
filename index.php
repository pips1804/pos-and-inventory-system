<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/style/style.css">
</head>

<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-sm p-4" style="width: 300px;">
        <h2 class="text-center mb-3">Login</h2>
        <form method="post" action="controllers/login.php">
            <div class="mb-3">
                <input type="email" class="form-control" placeholder="Email" required name="email" style="background-color: #303841 !important; color: #EEEEEE" />
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" placeholder="Password" required name="password" style="background-color: #303841 !important; color: #EEEEEE" />
            </div>
            <button type="submit" class="btn btn-primary w-100" name="login">Login</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
