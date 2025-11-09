<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password - Renato's Place</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/style.css" />
    <link rel="icon" href="../assets/favicon.ico">
</head>
<body>
    <nav style="background-color:rgba(0, 0, 0, 0.1);" class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand">Renato's Place Private Resort and Events</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <br><br>
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h4>Forgot Password</h4>
                </div>
                <div class="panel-body">
                    <form action="process_forgot_password.php" method="POST">
                        <div class="form-group">
                            <label>Enter your Username</label>
                            <input type="text" name="username" class="form-control" required />
                        </div>
                        <div class="form-group">
                            <button type="submit" name="submit" class="btn btn-primary btn-block">Submit</button>
                        </div>
                    </form>
                    <a href="index.php" class="btn btn-link">Back to Login</a>
                </div>
            </div>
        </div>
        <div class="col-md-4"></div>
    </div>

    <div style="text-align:right; margin-right:10px;" class="navbar navbar-default navbar-fixed-bottom">
        <label>&copy; Renato's Place Private Resort and Events</label>
    </div>
</body>
</html>
