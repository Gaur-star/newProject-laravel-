@extends('layout')

@section('content')

<h3>Dashboard</h3>

@if(session('success'))
<div class="alert alert-success">
{{ session('success') }}
</div>
@endif

<div class="row">

<div class="col-md-4">
<div class="card">
<div class="card-body">
<h5>Total Users</h5>
<p>Manage users from sidebar</p>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card">
<div class="card-body">
<h5>WordPress News</h5>
<p>View posts from all sites</p>
<a href="/news-dashboard" class="btn btn-primary btn-sm">Open</a>
</div>
</div>
</div>

</div>

@endsection