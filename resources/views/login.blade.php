<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <title>Login</title>
</head>
<body>
<!-- <h2>Login</h2>     -->


@if(session('error'))
    <p style="color:red;">
        {{ session('error') }}
    </p>
@endif


    <div class="login-box">
            
        <form method="POST" action="/login">
            @csrf
            <!-- <label for="email" class="form-label">Email:</label><br><br> -->
            <input type="email" name="email" placeholder="Enter Email" required><br><br>
            <!-- <label>Password:</label><br><br> -->
            <input type="password" name="password" placeholder="Enter Password" required><br><br>
            <button type="submit">Login</button>
        </form>

        <!-- <form method="POST" action="/login">
            @csrf
            <div class="mb-3 mt-3">
            <label for="email">Email Id</label><br>
            <input type="email" class="form-control" id="email" placeholder="Enter email" name="email"><br>
            </div>
            <div class="mb-3">
            <label for="pwd">Password</label><br>
            <input type="password" class="form-control" id="pwd" placeholder="Enter password" name="pswd"><br>
            </div>
            <div class="form-check mb-3">
            <label class="form-check-label">
                <input class="form-check-input" type="checkbox" name="remember"> Remember me
            </label> -->
            <!-- </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form> --> 

    </div>


</body>
</html>