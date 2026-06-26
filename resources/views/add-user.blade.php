<!-- <!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
</head>
<body> -->


@extends('layout')

@section('content')

<h2>Add New User</h2>

@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form method="POST" action="/add-user">
    @csrf

    <div class="mb-3">
        <label for="">Name</label>
        <input type="text" name="name" placeholder="Name" required class="form-control"><br><br>
    </div>
    <div class="mb-3">
        <label for="">Email</label>
        <input type="email" name="email" placeholder="Email" required class="form-control"><br><br>
    </div>
    <div class="mb-3">
        <label for="">Password</label>
        <input type="password" name="password" placeholder="Password" required class="form-control"><br><br>
    </div>

    <button type="submit" class="btn btn-success">Add User</button>
</form>

<br>
<!-- <a href="/dashboard">Back to Dashboard</a> -->


@endsection


<!-- </body>
</html> -->