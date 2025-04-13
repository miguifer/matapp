@include('includes.header')


@php

    $usuarioLogueado = json_decode($_SESSION['userLogin']['usuario']);

@endphp

<h1>PERFIL <strong>{{ $usuarioLogueado->login }}</strong></h1>

{{-- Aqui va a ir un calendario con la clases que se ha apuntado el usuario --}}

{{-- y le salen las que ha ido y las que va a ir --}}

@include('includes.footer')
