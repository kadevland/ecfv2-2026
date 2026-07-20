@extends('layouts.admin')

@section('title', 'Salle ' . $salle->nom)

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.salles.index') }}" class="hover:text-gray-900">Salles</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">{{ $salle->nom }}</span>
    </nav>
@endsection

@section('actions')
    <div class="flex space-x-3">
        <x-button :href="route('admin.salles.edit', $salle->uuid)" color="primary" theme="admin" icon="heroicon-o-pencil">
            Modifier
        </x-button>
        <x-button :href="route('admin.salles.index')" variant="outlined" theme="admin" icon="heroicon-o-arrow-left">
            Retour à la liste
        </x-button>
    </div>
@endsection

@section('content')
    <div class="flex flex-col gap-6">
        {{-- Salle Information --}}
        <x-card theme="admin" variant="shadow" title="Salle {{ $salle->nom }}" :subtitle="'Capacité: ' . $salle->capaciteTotale . ' places'" :hasHeader="true">
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Informations générales</h2>
                    <x-badge :color="$salle->statut === 'active' ? 'green' : ($salle->statut === 'maintenance' ? 'yellow' : 'red')" theme="admin">
                        {{ ucfirst($salle->statut) }}
                    </x-badge>
                </div>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nom de la salle</dt>
                        <dd class="mt-1 text-gray-900">{{ $salle->nom }}</dd>
                    </div>


                    <div>
                        <dt class="text-sm font-medium text-gray-500">Capacité totale</dt>
                        <dd class="mt-1 text-gray-900">{{ $salle->capaciteTotale }} places</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Statut</dt>
                        <dd class="mt-1">
                            @php
                                $statutEnum = \App\Domain\Cinema\Enums\StatutSalle::from($salle->statut);
                            @endphp
                            <x-badge :color="$statutEnum->getColor()" theme="admin">
                                {{ $statutEnum->getLabel() }}
                            </x-badge>
                        </dd>
                    </div>

                </div>

                <div class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Qualités de projection</dt>
                        <dd class="mt-1">
                            @if(!empty($salle->qualiteProjection))
                                <div class="flex flex-wrap gap-1">
                                    @foreach($salle->qualiteProjection as $qualite)
                                        <x-badge color="blue" theme="admin">{{ $qualite }}</x-badge>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-500">Aucune</span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Qualités sonores</dt>
                        <dd class="mt-1">
                            @if(!empty($salle->qualiteSonore))
                                <div class="flex flex-wrap gap-1">
                                    @foreach($salle->qualiteSonore as $qualite)
                                        <x-badge color="purple" theme="admin">{{ $qualite }}</x-badge>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-500">Aucune</span>
                            @endif
                        </dd>
                    </div>
                </div>
            </div>
        </x-card>

        {{-- Equipment & Accessibility --}}
        <x-card theme="admin" variant="shadow" title="Équipements et accessibilité" :hasHeader="true">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-2xl mb-2">
                        @if($salle->climatisation)
                            <span class="text-green-500">❄️</span>
                        @else
                            <span class="text-gray-400">❄️</span>
                        @endif
                    </div>
                    <div class="text-sm font-medium">Climatisation</div>
                    <div class="text-xs text-gray-500">{{ $salle->climatisation ? 'Disponible' : 'Non disponible' }}</div>
                </div>

                <div class="text-center">
                    <div class="text-2xl mb-2">
                        @if($salle->accessibilitePmr)
                            <span class="text-green-500">♿</span>
                        @else
                            <span class="text-gray-400">♿</span>
                        @endif
                    </div>
                    <div class="text-sm font-medium">Accessibilité PMR</div>
                    <div class="text-xs text-gray-500">{{ $salle->accessibilitePmr ? 'Accessible' : 'Non accessible' }}</div>
                </div>

            </div>
        </x-card>

        {{-- Configuration des sièges --}}
        <x-card theme="admin" variant="shadow" title="Configuration des places" :hasHeader="true">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $salle->placesStandard }}</div>
                    <div class="text-sm font-medium">Places Standard</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $salle->placesPmr }}</div>
                    <div class="text-sm font-medium">Places PMR</div>
                </div>
            </div>
            <div class="mt-4 text-center">
                <div class="text-sm text-gray-500">
                    {{ $salle->nombreRangees }} rangées × {{ $salle->placesParRangee }} places/rangée
                </div>
            </div>
        </x-card>

    </div>
@endsection
