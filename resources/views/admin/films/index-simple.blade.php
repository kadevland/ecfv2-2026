@extends('layouts.admin')

@section('title', 'Gestion des Films')

@section('content')
    <h1>Liste des Films</h1>
    <p>Total: {{ $total }}</p>
    <p>Page {{ $currentPage }} / {{ $totalPages }}</p>

    @if(count($films) > 0)
        <ul>
            @foreach($films as $film)
                <li>{{ $film->titre ?? 'Film sans titre' }}</li>
            @endforeach
        </ul>
    @else
        <p>Aucun film trouvé</p>
    @endif
@endsection