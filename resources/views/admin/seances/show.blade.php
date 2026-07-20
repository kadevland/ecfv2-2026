@extends('layouts.admin')

@section('title', 'Détails Séance')

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.seances.index') }}" class="hover:text-gray-900">Séances</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Détails</span>
    </nav>
@endsection

@section('subtitle', 'Informations détaillées de la séance')

@section('actions')
    <x-button href="{{ route('admin.seances.edit', $seance->uuid) }}" theme="admin" icon="heroicon-o-pencil">
        Modifier
    </x-button>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Informations principales -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Informations de la séance</h2>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Film</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $seance->filmTitre }}</dd>
                        @if($seance->filmDureeMinutes > 0)
                            <dd class="text-sm text-gray-600">Durée: {{ $seance->filmDureeMinutes }}min</dd>
                        @endif
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Salle</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $seance->salleNom }}</dd>
                        @if($seance->salleCapacite > 0)
                            <dd class="text-sm text-gray-600">Capacité: {{ $seance->salleCapacite }} places</dd>
                        @endif
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Date et heure</dt>
                        <dd class="text-lg font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($seance->dateHeureDebut)->format('d/m/Y à H:i') }}
                        </dd>
                        <dd class="text-sm text-gray-600">
                            Fin: {{ \Carbon\Carbon::parse($seance->dateHeureFin)->format('H:i') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Version</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ strtoupper($seance->version) }}</dd>
                    </div>

                    @if($seance->dureeAdditionnelle)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Durée additionnelle</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $seance->dureeAdditionnelle }} min</dd>
                        <dd class="text-sm text-gray-600">Temps additionnel</dd>
                    </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Statut</dt>
                        <dd>
                            @php
                                $statusClasses = [
                                    'programmee' => 'bg-blue-100 text-blue-800',
                                    'en_cours' => 'bg-green-100 text-green-800',
                                    'terminee' => 'bg-gray-100 text-gray-800',
                                    'annulee' => 'bg-red-100 text-red-800',
                                ];
                                $statusClass = $statusClasses[$seance->statut] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ ucfirst($seance->statut) }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Places</dt>
                        <dd class="text-lg font-medium text-gray-900">
                            {{ $seance->placesDisponibles }}/{{ $seance->placesTotales }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Tarification -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Tarification</h2>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @if(isset($seance->tarification['tarifsBase']))
                        @foreach(\App\Domain\Enums\TypeTarif::cases() as $tarif)
                            @if(isset($seance->tarification['tarifsBase'][$tarif->value]))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">{{ $tarif->label() }}</dt>
                                    <dd class="text-lg font-medium text-gray-900">
                                        {{ number_format($seance->tarification['tarifsBase'][$tarif->value] / 100, 2) }}€
                                    </dd>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-1">Prix Minimum</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ number_format($seance->tarification['prixMin'], 2) }}€
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-1">Prix Maximum</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ number_format($seance->tarification['prixMax'], 2) }}€
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-1">Catégories</dt>
                            <dd class="text-sm text-gray-900">
                                {{ count($seance->tarification['categories']) }} catégorie(s)
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Qualités et Options -->
        @if($seance->qualiteProjection || $seance->qualiteSonore || $seance->placementLibre || !empty($seance->technologies))
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Qualités et Options</h2>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @if($seance->qualiteProjection)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Qualité de projection</dt>
                        <dd class="text-lg font-medium text-gray-900">
                            @php
                                $qualite = \App\Domain\Cinema\Enums\QualiteProjection::from($seance->qualiteProjection);
                            @endphp
                            {{ $qualite->getLabel() }}
                        </dd>
                    </div>
                    @endif

                    @if($seance->qualiteSonore)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Qualité sonore</dt>
                        <dd class="text-lg font-medium text-gray-900">
                            @php
                                $qualite = \App\Domain\Cinema\Enums\QualiteSonore::from($seance->qualiteSonore);
                            @endphp
                            {{ $qualite->getLabel() }}
                        </dd>
                    </div>
                    @endif

                    @if($seance->placementLibre)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Placement</dt>
                        <dd class="text-lg font-medium text-gray-900">
                            <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Placement libre
                            </span>
                        </dd>
                        <dd class="text-sm text-gray-600">Pas de numérotation des sièges</dd>
                    </div>
                    @endif

                    @if(!empty($seance->technologies))
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Technologies</dt>
                        <dd>
                            <div class="flex flex-wrap gap-2">
                                @foreach($seance->technologies as $key => $value)
                                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ is_bool($value) ? ucfirst(str_replace('_', ' ', $key)) : $value }}
                                    </span>
                                @endforeach
                            </div>
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
        @endif
    </div>
@endsection
