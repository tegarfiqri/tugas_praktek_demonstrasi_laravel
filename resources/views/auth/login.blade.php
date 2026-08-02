@extends('layouts.app')

@section('title', 'Login')

@section('content')
<h1>Login Admin</h1>
<form method="post" action="{{ route('login') }}" class="stacked-form">
  @csrf
  <label>Email
    <input type="email" name="email" value="{{ old('email') }}" required>
  </label>
  <label>Password
    <input type="password" name="password" required>
  </label>
  <button type="submit">Login</button>
</form>
@endsection
