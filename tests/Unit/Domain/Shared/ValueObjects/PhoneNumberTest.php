<?php

declare(strict_types=1);

use App\Domain\Shared\ValueObjects\PhoneNumber;

describe('PhoneNumber Value Object', function () {
    it('peut créer un téléphone depuis format E164 valide', function () {
        $phone = PhoneNumber::fromE164('+33612345678');

        expect($phone->telephoneE164)->toBe('+33612345678');
        expect($phone->telephoneInternational)->toBe('+33 6 12 34 56 78');
    });

    it('lève exception pour E164 invalide', function () {
        expect(fn () => PhoneNumber::fromE164('invalid'))
            ->toThrow(InvalidArgumentException::class);
    });

    it('tryFromE164 retourne null pour données invalides', function () {
        expect(PhoneNumber::tryFromE164('invalid'))->toBeNull();
        expect(PhoneNumber::tryFromE164(null))->toBeNull();
    });

    it('compare deux téléphones par égalité', function () {
        $phone1 = PhoneNumber::fromE164('+33612345678');
        $phone2 = PhoneNumber::fromE164('+33612345678');
        $phone3 = PhoneNumber::fromE164('+33687654321');

        expect($phone1->equals($phone2))->toBeTrue();
        expect($phone1->equals($phone3))->toBeFalse();
    });

    it('méthodes compatibilité fonctionnent', function () {
        $phone = PhoneNumber::fromE164('+33612345678');

        expect($phone->toE164())->toBe('+33612345678');
        expect($phone->toInternational())->toBe('+33 6 12 34 56 78');
        expect($phone->toNational())->toBe('06 12 34 56 78');
    });

    it('peut créer depuis numéro français', function () {
        $phone = PhoneNumber::fromTelephoneEtPays('06 12 34 56 78', 'FR');

        expect($phone->telephoneE164)->toBe('+33612345678');
        expect($phone->countryCode)->toBe('FR');
        expect($phone->indicatifTelephonique)->toBe(33);
    });

    it('détecte les mobiles français', function () {
        $mobile = PhoneNumber::fromTelephoneEtPays('06 12 34 56 78', 'FR');
        $fixe   = PhoneNumber::fromTelephoneEtPays('01 42 34 56 78', 'FR');

        expect($mobile->isMobile)->toBeTrue();
        expect($fixe->isMobile)->toBeFalse();
    });
});
