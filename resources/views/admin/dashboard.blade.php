@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Admin Dashboard</h1>
    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit" class="btn btn-danger">Logout</button>
    </form>
    <!-- Add more admin-specific content here -->
</div>
@endsection
