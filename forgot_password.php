<!--This HTML code creates a "Forgot Password" page for a car service tracking system, 
featuring a form where users can enter their registered email address to receive a password recovery email, 
along with navigation links to return to the login page, all styled using Bootstrap.-->

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Forgot Password</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <div class="background"></div>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="">Car Service Tracker</a>
            </div>
        </nav>
        <div class="container my-5">
            <h2 class="text-center mb-4">Forgot Password</h2>
            <form class="mx-auto mt-4" style="max-width: 400px;" method="post" action="send_password_rest.php">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter your registered email" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Send Recovery Email</button>
            </form>
            <div class="text-center mt-3">
                <a href="login.php" class="text-primary">Back to Login</a>
            </div>
        </div>
    </body>
</html>
