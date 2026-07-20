<?php

declare(strict_types=1);

use App\Infrastructure\Casts\AsUrl;
use App\Domain\Shared\ValueObjects\Url;
use Illuminate\Database\Eloquent\Model;

describe('AsUrl Cast', function () {
    beforeEach(function () {
        $this->cast  = new AsUrl;
        $this->model = new class extends Model {};
    });

    describe('get() - Hydration depuis DB', function () {
        it('retourne null pour valeur null', function () {
            $result = $this->cast->get($this->model, 'site_web', null, []);
            expect($result)->toBeNull();
        });

        it('retourne null pour URL invalide', function () {
            $result = $this->cast->get($this->model, 'site_web', 'invalid-url', []);
            expect($result)->toBeNull();
        });

        it('retourne Url pour URL valide', function () {
            $result = $this->cast->get($this->model, 'site_web', 'https://example.com', []);

            expect($result)->toBeInstanceOf(Url::class);
            expect($result->toString())->toBe('https://example.com');
            expect($result->getScheme())->toBe('https');
            expect($result->getDomain())->toBe('example.com');
        });

        it('garde l\'URL telle quelle (pas de normalisation)', function () {
            $result = $this->cast->get($this->model, 'site_web', 'HTTPS://EXAMPLE.COM/PATH', []);

            expect($result)->toBeInstanceOf(Url::class);
            expect($result->toString())->toBe('HTTPS://EXAMPLE.COM/PATH');
            expect($result->getScheme())->toBe('HTTPS');
            expect($result->getDomain())->toBe('EXAMPLE.COM');
        });

        it('gère différents protocoles', function () {
            $httpsUrl = $this->cast->get($this->model, 'site_web', 'https://secure.example.com', []);
            $httpUrl  = $this->cast->get($this->model, 'site_web', 'http://example.com', []);

            expect($httpsUrl)->toBeInstanceOf(Url::class);
            expect($httpsUrl->isSecure())->toBeTrue();

            expect($httpUrl)->toBeInstanceOf(Url::class);
            expect($httpUrl->isSecure())->toBeFalse();
        });

        it('gère les URLs complexes avec chemins et paramètres', function () {
            $complexUrl = $this->cast->get($this->model, 'site_web', 'https://www.example.com/path/to/page?param=value&other=123#section', []);

            expect($complexUrl)->toBeInstanceOf(Url::class);
            expect($complexUrl->toString())->toContain('path/to/page');
            expect($complexUrl->toString())->toContain('param=value');
        });

        it('rejette les protocoles non supportés', function () {
            $result = $this->cast->get($this->model, 'site_web', 'ftp://example.com', []);
            expect($result)->toBeNull();
        });
    });

    describe('set() - Persistence vers DB', function () {
        it('convertit Url VO vers string', function () {
            $url    = Url::fromString('https://example.com');
            $result = $this->cast->set($this->model, 'site_web', $url, []);

            expect($result)->toBe('https://example.com');
        });

        it('convertit string URL vers string (sans normalisation)', function () {
            $result = $this->cast->set($this->model, 'site_web', 'HTTPS://EXAMPLE.COM/PATH', []);
            expect($result)->toBe('HTTPS://EXAMPLE.COM/PATH');
        });

        it('lève exception pour string invalide', function () {
            expect(fn () => $this->cast->set($this->model, 'site_web', 'invalid-url', []))
                ->toThrow(InvalidArgumentException::class);
        });

        it('lève exception pour protocole non supporté', function () {
            expect(fn () => $this->cast->set($this->model, 'site_web', 'ftp://example.com', []))
                ->toThrow(InvalidArgumentException::class);
        });

        it('gère les URLs complexes', function () {
            $complexUrl = 'https://www.example.com/path/to/page?param=value&other=123#section';
            $result     = $this->cast->set($this->model, 'site_web', $complexUrl, []);
            expect($result)->toBe($complexUrl);
        });
    });

    describe('Cycle complet DB', function () {
        it('round-trip: set puis get conserve la valeur exacte', function () {
            $originalUrl = Url::fromString('https://www.example.com/path');

            // Simule sauvegarde en DB
            $dbValue = $this->cast->set($this->model, 'site_web', $originalUrl, []);

            // Simule lecture depuis DB
            $retrievedUrl = $this->cast->get($this->model, 'site_web', $dbValue, []);

            expect($retrievedUrl)->toBeInstanceOf(Url::class);
            expect($retrievedUrl->equals($originalUrl))->toBeTrue();
        });

        it('round-trip préserve la casse (pas de normalisation)', function () {
            $mixedCaseUrl = 'HTTPS://EXAMPLE.COM/PATH';
            $dbValue      = $this->cast->set($this->model, 'site_web', $mixedCaseUrl, []);
            $retrievedUrl = $this->cast->get($this->model, 'site_web', $dbValue, []);

            expect($retrievedUrl)->toBeInstanceOf(Url::class);
            expect($retrievedUrl->toString())->toBe($mixedCaseUrl);
        });
    });
});
