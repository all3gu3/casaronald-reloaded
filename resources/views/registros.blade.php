@extends('layouts.skeleton')
@section('title', 'Bitácora')
@section('description-page', 'Vista rápida de los últimos movimientos y concentrados por servicio')
@section('content')
    @include('dashboards.registros')
@endsection
