@extends('layouts.skeleton')

@section('title', 'Iniciar sesión')

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: 100vh; background: #4872AE;">
    <div class="card shadow" style="width: 100%; max-width: 400px; border-radius: 14px;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <img src="{{ asset('img/RMHC_Mexico_logo.png') }}" alt="Casa Ronald McDonald" style="max-width: 160px;">
                <h4 class="mt-3 mb-0" style="color: #38598A; font-weight: 800;">Casa Ronald</h4>
                <small class="text-muted">Expediente electrónico</small>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger py-2">
                    @foreach ($errors->all() as $error)
                        <small class="d-block">{{ $error }}</small>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           class="form-control" required autofocus autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input id="password" type="password" name="password"
                           class="form-control" required autocomplete="current-password">
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label class="form-check-label" for="remember">Mantener sesión abierta</label>
                </div>
                <button type="submit" class="btn btn-block" style="background: #FFC72C; color: #5c4500; font-weight: 700;">
                    Entrar
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
