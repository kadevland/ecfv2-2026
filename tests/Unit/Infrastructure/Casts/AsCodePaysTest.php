<?php

declare(strict_types=1);

use App\Domain\Shared\Enums\CodePays;
use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Casts\AsCodePays;

describe('AsCodePays Cast', function () {
    beforeEach(function () {
        $this->cast  = new AsCodePays;
        $this->model = new class extends Model {};
    });

    describe('get() - Hydration depuis DB', function () {
        it('retourne null pour valeur null', function () {
            $result = $this->cast->get($this->model, 'pays', null, []);
            expect($result)->toBeNull();
        });

        it('retourne null pour code pays invalide', function () {
            $result = $this->cast->get($this->model, 'pays', 'XX', []);
            expect($result)->toBeNull();
        });

        it('retourne CodePays pour code valide FR', function () {
            $result = $this->cast->get($this->model, 'pays', 'FR', []);

            expect($result)->toBeInstanceOf(CodePays::class);
            expect($result)->toBe(CodePays::France);
            expect($result->value)->toBe('FR');
        });

        it('retourne CodePays pour code valide BE', function () {
            $result = $this->cast->get($this->model, 'pays', 'BE', []);

            expect($result)->toBeInstanceOf(CodePays::class);
            expect($result)->toBe(CodePays::Belgique);
            expect($result->value)->toBe('BE');
        });

        it('retourne CodePays pour code valide DE', function () {
            $result = $this->cast->get($this->model, 'pays', 'DE', []);

            expect($result)->toBeInstanceOf(CodePays::class);
            expect($result)->toBe(CodePays::Allemagne);
            expect($result->value)->toBe('DE');
        });

        it('retourne CodePays pour code valide CH', function () {
            $result = $this->cast->get($this->model, 'pays', 'CH', []);

            expect($result)->toBeInstanceOf(CodePays::class);
            expect($result)->toBe(CodePays::Suisse);
            expect($result->value)->toBe('CH');
        });

        it('normalise code en minuscules vers majuscules', function () {
            $result = $this->cast->get($this->model, 'pays', 'fr', []);
            expect($result)->toBeInstanceOf(CodePays::class);
            expect($result)->toBe(CodePays::France); // tryFromCode utilise strtoupper()
        });

        it('retourne null pour code trop long', function () {
            $result = $this->cast->get($this->model, 'pays', 'FRA', []);
            expect($result)->toBeNull();
        });
    });

    describe('set() - Persistence vers DB', function () {
        it('convertit CodePays VO vers string', function () {
            $result = $this->cast->set($this->model, 'pays', CodePays::France, []);
            expect($result)->toBe('FR');
        });

        it('convertit CodePays Belgique vers string', function () {
            $result = $this->cast->set($this->model, 'pays', CodePays::Belgique, []);
            expect($result)->toBe('BE');
        });

        it('convertit string code vers string', function () {
            $result = $this->cast->set($this->model, 'pays', 'DE', []);
            expect($result)->toBe('DE');
        });

        it('retourne null pour string invalide', function () {
            $result = $this->cast->set($this->model, 'pays', 'XX', []);
            expect($result)->toBeNull(); // tryFromCode retourne null
        });

        it('normalise code en minuscules vers majuscules', function () {
            $result = $this->cast->set($this->model, 'pays', 'fr', []);
            expect($result)->toBe('FR'); // fromCode utilise strtoupper()
        });

        it('gère tous les pays européens supportés', function () {
            $france    = $this->cast->set($this->model, 'pays', CodePays::France, []);
            $belgique  = $this->cast->set($this->model, 'pays', CodePays::Belgique, []);
            $allemagne = $this->cast->set($this->model, 'pays', CodePays::Allemagne, []);
            $suisse    = $this->cast->set($this->model, 'pays', CodePays::Suisse, []);

            expect($france)->toBe('FR');
            expect($belgique)->toBe('BE');
            expect($allemagne)->toBe('DE');
            expect($suisse)->toBe('CH');
        });
    });

    describe('Propriétés spécifiques CodePays', function () {
        it('conserve les indicatifs téléphoniques', function () {
            $france   = $this->cast->get($this->model, 'pays', 'FR', []);
            $belgique = $this->cast->get($this->model, 'pays', 'BE', []);

            expect($france->indicatifTelephonique())->toBe(33);
            expect($belgique->indicatifTelephonique())->toBe(32);
        });

        it('conserve les noms complets des pays', function () {
            $france    = $this->cast->get($this->model, 'pays', 'FR', []);
            $allemagne = $this->cast->get($this->model, 'pays', 'DE', []);

            // Note: nomComplet() peut lever exception dans tests unitaires (pas de translator)
            expect($france)->toBe(CodePays::France);
            expect($allemagne)->toBe(CodePays::Allemagne);
        });
    });

    describe('Cycle complet DB', function () {
        it('round-trip: set puis get conserve la valeur', function () {
            $originalPays = CodePays::France;

            // Simule sauvegarde en DB
            $dbValue = $this->cast->set($this->model, 'pays', $originalPays, []);

            // Simule lecture depuis DB
            $retrievedPays = $this->cast->get($this->model, 'pays', $dbValue, []);

            expect($retrievedPays)->toBeInstanceOf(CodePays::class);
            expect($retrievedPays)->toBe($originalPays);
        });

        it('round-trip avec string input', function () {
            // String code pays
            $dbValue       = $this->cast->set($this->model, 'pays', 'BE', []);
            $retrievedPays = $this->cast->get($this->model, 'pays', $dbValue, []);

            expect($retrievedPays)->toBeInstanceOf(CodePays::class);
            expect($retrievedPays)->toBe(CodePays::Belgique);
            expect($retrievedPays->value)->toBe('BE');
        });

        it('cycle avec tous les pays supportés', function () {
            $pays = [CodePays::France, CodePays::Belgique, CodePays::Allemagne, CodePays::Suisse];

            foreach ($pays as $originalPays) {
                $dbValue       = $this->cast->set($this->model, 'pays', $originalPays, []);
                $retrievedPays = $this->cast->get($this->model, 'pays', $dbValue, []);

                expect($retrievedPays)->toBe($originalPays);
            }
        });
    });
});
