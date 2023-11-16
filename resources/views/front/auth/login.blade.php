@extends('front.layouts.main')

@section('content')
    <form method="POST" action="/auth/login">
        @csrf
        <input type="text" name="email" placeholder="Email" />
        <input type="password" name="password" placeholder="Пароль" />
        <input type="submit" value="Войти">
    </form>
@endsection
