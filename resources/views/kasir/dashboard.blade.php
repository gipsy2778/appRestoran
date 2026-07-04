@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
<h4 class="fw-bold mb-4">Dashboard Kasir</h4>
<p class="text-muted">Selamat datang, {{ auth()->user()->nama }}!</p>
@endsection