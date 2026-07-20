<?php

declare(strict_types=1);

namespace App\Application\Contracts;

/**
 * Pattern Result pour gestion explicite des succès/erreurs
 *
 * Remplace les exceptions pour les cas d'erreur métier.
 * Rend le code plus expressif et améliore la type safety.
 */
abstract readonly class Result
{
    /**
     * Crée un résultat de succès
     */
    public static function success(mixed $value): Success
    {
        return new Success($value);
    }

    /**
     * Crée un résultat d'erreur
     */
    public static function error(mixed $error, ?string $message = null): Error
    {
        return new Error($error, $message);
    }

    /**
     * Vérifie si le résultat est un succès
     */
    abstract public function isSuccess(): bool;

    /**
     * Vérifie si le résultat est une erreur
     */
    public function isError(): bool
    {
        return !$this->isSuccess();
    }

    /**
     * Récupère la valeur (uniquement si succès)
     */
    abstract public function getValue(): mixed;

    /**
     * Récupère l'erreur (uniquement si erreur)
     */
    abstract public function getError(): mixed;

    /**
     * Récupère le message d'erreur (uniquement si erreur)
     */
    abstract public function getErrorMessage(): ?string;

    /**
     * Applique une transformation sur la valeur si succès
     */
    public function map(callable $fn): Result
    {
        return $this->isSuccess() ? self::success($fn($this->getValue())) : $this;
    }

    /**
     * Applique une transformation qui retourne un Result
     */
    public function flatMap(callable $fn): Result
    {
        return $this->isSuccess() ? $fn($this->getValue()) : $this;
    }

    /**
     * Retourne une valeur par défaut si erreur
     */
    public function getOrElse(mixed $default): mixed
    {
        return $this->isSuccess() ? $this->getValue() : $default;
    }

    /**
     * Exécute une action si succès
     */
    public function onSuccess(callable $fn): Result
    {
        if ($this->isSuccess()) {
            $fn($this->getValue());
        }

        return $this;
    }

    /**
     * Exécute une action si erreur
     */
    public function onError(callable $fn): Result
    {
        if ($this->isError()) {
            $fn($this->getError(), $this->getErrorMessage());
        }

        return $this;
    }
}
