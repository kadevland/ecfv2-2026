<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use App\Application\Bus\CommandBus;
use App\Http\Mappers\Seance\CreateSeanceRequestMapper;

it('teste le pipeline complet de création de séance', function () {
    // Données complètes du formulaire
    $formData = [
        'film_id'             => '019a2ddb-4a45-7f6a-a123-456789abcdef',
        'salle_id'            => '019a2ddb-4a45-7f6a-b234-567890abcdef',
        'date_seance'         => '2025-12-01',
        'heure_debut'         => '20:30',
        'duree_additionnelle' => 15,
        'date_heure_debut'    => '2025-12-01 20:30:00',
        'date_heure_fin'      => '2025-12-01 22:45:00',
        'version'             => 'VF',
        'qualite_projection'  => '2K',
        'qualite_sonore'      => 'DOLBY_SURROUND',
        'placement_libre'     => true,
        'statut'              => 'programmee',
        'tarif_standard'      => 12.50,
        'tarif_reduit'        => 9.50,
        'tarif_enfant'        => 7.00,
    ];

    // 1. Test FormRequest validation
    $request   = new \App\Http\Requests\Admin\Seance\CreateSeanceRequest;
    $validator = \Illuminate\Support\Facades\Validator::make($formData, $request->rules(), $request->messages());
    expect($validator->passes())->toBeTrue('FormRequest validation should pass');

    // 2. Test RequestMapper
    $httpRequest = new Request($formData);
    $command     = CreateSeanceRequestMapper::toCommand($httpRequest);

    expect($command->filmUuid)->toBe($formData['film_id'])
        ->and($command->salleUuid)->toBe($formData['salle_id'])
        ->and($command->version)->toBe('vf'); // Le mapper convertit en lowercase

    // 3. Test Command validation
    expect($command->isValid())->toBeTrue('Command should be valid');

    // 4. Test Command Bus dispatch
    $commandBus = app(CommandBus::class);
    $result     = $commandBus->dispatch($command);

    if ($result->isSuccess()) {
        $seanceId = $result->getValue();
        expect($seanceId)->toBeString()->toHaveLength(36);
        echo "SUCCESS: Séance créée avec ID: $seanceId";
    } else {
        echo 'ERROR: ' . $result->getErrorMessage();
        //dump($result); // Debug pour voir l'erreur complète
    }

    expect($result->isSuccess())->toBeTrue('Command should execute successfully');
});

it('teste la validation avec des données invalides', function () {
    $formData = [
        'film_id'        => 'invalid-uuid',
        'version'        => 'invalid-version',
        'tarif_standard' => -5.00, // Prix négatif
    ];

    $request   = new \App\Http\Requests\Admin\Seance\CreateSeanceRequest;
    $validator = \Illuminate\Support\Facades\Validator::make($formData, $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue('Validation should fail for invalid data');
    expect($validator->errors()->count())->toBeGreaterThan(5);
});
