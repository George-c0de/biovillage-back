@extends('mail.layouts.main')

@section('content')
    Привет {{ $name ?? 'XX' }}
@endsection