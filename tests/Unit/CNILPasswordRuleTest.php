<?php

declare(strict_types=1);

use App\Rules\CNILPasswordRule;

it('rejects passwords shorter than 12 characters', function () {
    $rule = new CNILPasswordRule;

    $shortPasswords = [
        'Abc123!',      // 7 characters
        'Abc123!@',     // 9 characters
        'Abc123!@#$',   // 11 characters
    ];

    foreach ($shortPasswords as $password) {
        expect($rule->isValid($password))->toBeFalse();
        $errors = $rule->getErrors();
        expect($errors)->toContain('Le mot de passe doit contenir au moins 12 caractères.');
    }
});

it('accepts passwords with 12 or more characters', function () {
    $rule = new CNILPasswordRule;

    $validPassword = 'SuperFortePhrase9@';  // 18 caractères, sans mots interdits
    expect($rule->isValid($validPassword))->toBeTrue();
});

it('requires at least one uppercase letter', function () {
    $rule = new CNILPasswordRule;

    $noUppercasePassword = 'validpassword123!@';
    expect($rule->isValid($noUppercasePassword))->toBeFalse();

    $errors = $rule->getErrors();
    expect($errors)->toContain('Le mot de passe doit contenir au moins une lettre majuscule (A-Z).');
});

it('requires at least one lowercase letter', function () {
    $rule = new CNILPasswordRule;

    $noLowercasePassword = 'VALIDPASSWORD123!@';
    expect($rule->isValid($noLowercasePassword))->toBeFalse();

    $errors = $rule->getErrors();
    expect($errors)->toContain('Le mot de passe doit contenir au moins une lettre minuscule (a-z).');
});

it('requires at least one digit', function () {
    $rule = new CNILPasswordRule;

    $noDigitPassword = 'ValidPassword!@#$';
    expect($rule->isValid($noDigitPassword))->toBeFalse();

    $errors = $rule->getErrors();
    expect($errors)->toContain('Le mot de passe doit contenir au moins un chiffre (0-9).');
});

it('requires at least one special character', function () {
    $rule = new CNILPasswordRule;

    $noSpecialPassword = 'ValidPassword123';
    expect($rule->isValid($noSpecialPassword))->toBeFalse();

    $errors = $rule->getErrors();
    expect($errors)->toContain('Le mot de passe doit contenir au moins un caractère spécial (!@#$%^&*()_+-=[]{};\'"\\|,.<>/?~`).');
});

it('rejects passwords with common patterns', function () {
    $rule = new CNILPasswordRule;

    $commonPasswords = [
        'MyPassword123!@',  // contient "password"
        'AdminUser123!@#',  // contient "admin"
        'TestAccount123!',  // contient "test"
        'MyAzerty123!@#$',  // contient "azerty"
    ];

    foreach ($commonPasswords as $password) {
        expect($rule->isValid($password))->toBeFalse();
        $errors = $rule->getErrors();
        expect($errors)->toContain('Le mot de passe contient des éléments trop prévisibles ou communs.');
    }
});

it('rejects passwords with numeric sequences', function () {
    $rule = new CNILPasswordRule;

    $sequencePasswords = [
        'ValidPass123!@#',  // séquence 123
        'MyPassword987!@',  // séquence 987
        'Account456Test!',  // séquence 456
        'User890Complex!',  // séquence 890
    ];

    foreach ($sequencePasswords as $password) {
        expect($rule->isValid($password))->toBeFalse();
        $errors = $rule->getErrors();
        expect($errors)->toContain('Le mot de passe ne doit pas contenir de séquences numériques simples (ex: 123, 987).');
    }
});

it('rejects passwords with alphabetic sequences', function () {
    $rule = new CNILPasswordRule;

    $sequencePasswords = [
        'MyPasswordAbc123!',  // séquence abc
        'ValidPassXyz456!',   // séquence xyz
        'AccountDef789!@#',   // séquence def
    ];

    foreach ($sequencePasswords as $password) {
        expect($rule->isValid($password))->toBeFalse();
        $errors = $rule->getErrors();
        expect($errors)->toContain('Le mot de passe ne doit pas contenir de séquences alphabétiques simples (ex: abc, xyz).');
    }
});

it('rejects passwords with keyboard sequences', function () {
    $rule = new CNILPasswordRule;

    $keyboardPasswords = [
        'MyPasswordQwe123!',  // séquence qwe
        'ValidPassAsd456!',   // séquence asd
        'AccountZxc789!@#',   // séquence zxc
    ];

    foreach ($keyboardPasswords as $password) {
        expect($rule->isValid($password))->toBeFalse();
        $errors = $rule->getErrors();
        expect($errors)->toContain('Le mot de passe ne doit pas contenir de séquences de clavier simples (ex: qwe, asd).');
    }
});

it('rejects passwords with excessive character repetition', function () {
    $rule = new CNILPasswordRule;

    $repetitionPasswords = [
        'ValidPasssss123!',   // 4 s consécutifs
        'MyPasswordddd45!',   // 3 d consécutifs
        'AccountTTTT789!@',   // 4 T consécutifs
    ];

    foreach ($repetitionPasswords as $password) {
        expect($rule->isValid($password))->toBeFalse();
        $errors = $rule->getErrors();
        expect($errors)->toContain('Le mot de passe ne doit pas contenir plus de 2 caractères identiques consécutifs.');
    }
});

it('accepts valid CNIL-compliant passwords', function () {
    $rule = new CNILPasswordRule;

    $validPasswords = [
        'SuperFortePhrase9@',      // 18 chars, complexe
        'ComplicatedPhrase9$',     // 19 chars, strong
        'UltraS3cur3Maison!@',     // 18 chars, very strong
        'MaSuperPhrase2024#',      // 18 chars, fort
        'ComplexeMotif8!@#',       // 17 chars, valide
    ];

    foreach ($validPasswords as $password) {
        expect($rule->isValid($password))->toBeTrue("Le mot de passe '$password' devrait être valide");
        expect($rule->getErrors())->toBeEmpty("Le mot de passe '$password' ne devrait pas avoir d'erreurs");
    }
});

it('validates with closure correctly when password is valid', function () {
    $rule   = new CNILPasswordRule;
    $errors = [];

    $fail = function (string $message) use (&$errors) {
        $errors[] = $message;
    };

    $rule->validate('password', 'ValidP@ssw0rd2024!', $fail);

    expect($errors)->toBeEmpty();
});

it('validates with closure correctly when password is invalid', function () {
    $rule   = new CNILPasswordRule;
    $errors = [];

    $fail = function (string $message) use (&$errors) {
        $errors[] = $message;
    };

    $rule->validate('password', 'weak', $fail);

    expect($errors)->not->toBeEmpty();
    expect($errors)->toContain('Le mot de passe doit contenir au moins 12 caractères.');
});

it('handles non-string input gracefully', function () {
    $rule   = new CNILPasswordRule;
    $errors = [];

    $fail = function (string $message) use (&$errors) {
        $errors[] = $message;
    };

    $rule->validate('password', 123, $fail);

    expect($errors)->toContain('Le mot de passe doit être une chaîne de caractères.');
});

it('detects repetitive patterns', function () {
    $rule = new CNILPasswordRule;

    $repetitivePasswords = [
        'abcabcabcABC123!',    // motif "abc" répété
        '123123123Abc!@#',     // motif "123" répété
        'aaabbbcccDDD123!',    // pas de répétition de motifs mais des répétitions de chars
    ];

    foreach ($repetitivePasswords as $password) {
        $isValid = $rule->isValid($password);
        if (!$isValid) {
            $errors           = $rule->getErrors();
            $hasExpectedError = false;
            foreach ($errors as $error) {
                if (str_contains($error, 'caractères identiques consécutifs') ||
                    str_contains($error, 'motifs répétitifs simples')) {
                    $hasExpectedError = true;
                    break;
                }
            }
            expect($hasExpectedError)->toBeTrue("Le mot de passe '$password' devrait avoir une erreur de répétition");
        }
    }
});

it('validates multibyte characters correctly', function () {
    $rule = new CNILPasswordRule;

    // Mot de passe avec des caractères accentués
    $passwordWithAccents = 'MônMötDéPàssé2024!';
    expect($rule->isValid($passwordWithAccents))->toBeTrue();

    // Vérifier que mb_strlen est utilisé correctement
    $shortPasswordWithAccents = 'Mön123!';  // 7 chars multibyte
    expect($rule->isValid($shortPasswordWithAccents))->toBeFalse();
    $errors = $rule->getErrors();
    expect($errors)->toContain('Le mot de passe doit contenir au moins 12 caractères.');
});
