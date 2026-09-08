@extends('layouts.skeleton')
@section('title', 'Expedientes')
@section('description-page', 'Expediente de niños y acompañantes')
@section('content')
    <div class="pagina">
        @include('parts.preview')
        @include('forms.acompanante')
        @include('forms.nino')
        @include('forms.expediente')
        @include('datatables.acompanantes')

        <section class="pagina-tarjeta">
            @include('datatables.ninos')
        </section>
    </div>
@endsection
