@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-fw fa-user mr-2"></i>
        @if($role == 1)
            Data Super Admin
        @elseif($role == 2)
           Data Admin
        @elseif($role == 3)
            Data Klien
        @endif
    </h1>
@endsection