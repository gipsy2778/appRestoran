@extends('layouts.app')

@section('title', 'Dashboard Manager')

@section('content')
<h4 class="fw-bold mb-4">Dashboard Manager</h4>
<p class="text-muted">Selamat datang, {{ auth()->user()->nama }}!</p>
@endsection