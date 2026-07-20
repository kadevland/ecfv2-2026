<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Règle de validation des mots de passe selon les recommandations CNIL
 *
 * Exigences CNIL pour les mots de passe :
 * - Au moins 12 caractères
 * - Au moins 1 majuscule (A-Z)
 * - Au moins 1 minuscule (a-z)
 * - Au moins 1 chiffre (0-9)
 * - Au moins 1 caractère spécial
 * - Pas de séquences simples (123, abc, etc.)
 * - Pas de répétitions excessives (aaa, 111, etc.)
 */
class CNILPasswordRule implements ValidationRule
{
    private array $errors = [];

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('Le mot de passe doit être une chaîne de caractères.', null);

            return;
        }

        $this->errors = [];

        $this->validateLength($value);
        $this->validateCharacterTypes($value);
        $this->validatePatterns($value);
        $this->validateSequences($value);
        $this->validateRepetitions($value);

        if (!empty($this->errors)) {
            foreach ($this->errors as $error) {
                $fail($error, null);
            }
        }
    }

    /**
     * Obtenir la liste des erreurs pour les tests
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Vérifier si un mot de passe est valide sans déclencher d'erreurs
     */
    public function isValid(string $password): bool
    {
        $this->errors = [];

        $this->validateLength($password);
        $this->validateCharacterTypes($password);
        $this->validatePatterns($password);
        $this->validateSequences($password);
        $this->validateRepetitions($password);

        return empty($this->errors);
    }

    /**
     * Valider la longueur minimale (12 caractères CNIL)
     */
    private function validateLength(string $password): void
    {
        if (mb_strlen($password) < 12) {
            $this->errors[] = 'Le mot de passe doit contenir au moins 12 caractères.';
        }
    }

    /**
     * Valider les types de caractères requis
     */
    private function validateCharacterTypes(string $password): void
    {
        // Au moins une majuscule
        if (!preg_match('/[A-Z]/', $password)) {
            $this->errors[] = 'Le mot de passe doit contenir au moins une lettre majuscule (A-Z).';
        }

        // Au moins une minuscule
        if (!preg_match('/[a-z]/', $password)) {
            $this->errors[] = 'Le mot de passe doit contenir au moins une lettre minuscule (a-z).';
        }

        // Au moins un chiffre
        if (!preg_match('/[0-9]/', $password)) {
            $this->errors[] = 'Le mot de passe doit contenir au moins un chiffre (0-9).';
        }

        // Au moins un caractère spécial
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?~`]/', $password)) {
            $this->errors[] = 'Le mot de passe doit contenir au moins un caractère spécial (!@#$%^&*()_+-=[]{};\'"\\|,.<>/?~`).';
        }
    }

    /**
     * Valider les motifs interdits
     */
    private function validatePatterns(string $password): void
    {
        // Mots de passe trop communs ou simples
        $commonPatterns = [
            '/password/i',
            '/motdepasse/i',
            '/mdp/i',
            '/admin/i',
            '/user/i',
            '/login/i',
            '/test/i',
            '/azerty/i',
            '/qwerty/i',
            '/123456/i',
            '/000000/i',
            '/111111/i',
        ];

        foreach ($commonPatterns as $pattern) {
            if (preg_match($pattern, $password)) {
                $this->errors[] = 'Le mot de passe contient des éléments trop prévisibles ou communs.';
                break;
            }
        }
    }

    /**
     * Valider les séquences interdites
     */
    private function validateSequences(string $password): void
    {
        // Séquences numériques croissantes/décroissantes
        if (preg_match('/(?:012|123|234|345|456|567|678|789|890|901|210|321|432|543|654|765|876|987|098|109)/', $password)) {
            $this->errors[] = 'Le mot de passe ne doit pas contenir de séquences numériques simples (ex: 123, 987).';
        }

        // Séquences alphabétiques
        if (preg_match('/(?:abc|bcd|cde|def|efg|fgh|ghi|hij|ijk|jkl|klm|lmn|mno|nop|opq|pqr|qrs|rst|stu|tuv|uvw|vwx|wxy|xyz|zyx|yxw|xwv|wvu|vut|uts|tsr|srq|rqp|qpo|pon|onm|nml|mlk|lkj|kji|jih|ihg|hgf|gfe|fed|edc|dcb|cba)/i', $password)) {
            $this->errors[] = 'Le mot de passe ne doit pas contenir de séquences alphabétiques simples (ex: abc, xyz).';
        }

        // Séquences de clavier
        if (preg_match('/(?:qwe|wer|ert|rty|tyu|yui|uio|iop|asd|sdf|dfg|fgh|ghj|hjk|jkl|zxc|xcv|cvb|vbn|bnm|poi|oiu|iuy|uyt|ytr|tre|rew|ewq|lkj|kjh|jhg|hgf|gfd|fds|dsa|mnb|nbv|vbc|bcx|cxz|zaq|qaz|wsx|sxw|edc|cde|rfv|vfr|tgb|bgt|yhn|nhy|ujm|mju|ik|ki)/i', $password)) {
            $this->errors[] = 'Le mot de passe ne doit pas contenir de séquences de clavier simples (ex: qwe, asd).';
        }
    }

    /**
     * Valider les répétitions excessives
     */
    private function validateRepetitions(string $password): void
    {
        // Plus de 2 caractères identiques consécutifs
        if (preg_match('/(.)\1{2,}/', $password)) {
            $this->errors[] = 'Le mot de passe ne doit pas contenir plus de 2 caractères identiques consécutifs.';
        }

        // Motifs répétitifs simples
        if (preg_match('/(.{1,3})\1{2,}/', $password)) {
            $this->errors[] = 'Le mot de passe ne doit pas contenir de motifs répétitifs simples.';
        }
    }
}
