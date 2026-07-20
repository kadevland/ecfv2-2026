<?php

declare(strict_types=1);

namespace App\Application\Bus;

use InvalidArgumentException;

/**
 * Registry pour mapper les Queries vers leurs Handlers
 *
 * Système centralisé et explicite pour enregistrer les associations Query -> Handler
 */
final class QueryRegistry
{
    /** @var array<string, array<string>|string> */
    private array $queryToHandler = [];

    /**
     * Enregistre une association Query -> Handler(s)
     *
     * @param string|array<string> $handlerClass
     */
    public function register(string $queryClass, string|array $handlerClass): self
    {
        if (!class_exists($queryClass)) {
            throw new InvalidArgumentException("Query class [{$queryClass}] does not exist");
        }

        $handlers = is_array($handlerClass) ? $handlerClass : [$handlerClass];

        foreach ($handlers as $handler) {
            if (!class_exists($handler)) {
                throw new InvalidArgumentException("Handler class [{$handler}] does not exist");
            }
        }

        $this->queryToHandler[$queryClass] = $handlerClass;

        return $this;
    }

    /**
     * Récupère le(s) handler(s) pour une query donnée
     *
     * @return array<string>
     */
    public function getHandlers(string $queryClass): array
    {
        if (!isset($this->queryToHandler[$queryClass])) {
            throw new InvalidArgumentException("No handler registered for query [{$queryClass}]");
        }

        $handlers = $this->queryToHandler[$queryClass];

        return is_array($handlers) ? $handlers : [$handlers];
    }

    /**
     * Vérifie si une query a un handler enregistré
     */
    public function hasHandler(string $queryClass): bool
    {
        return isset($this->queryToHandler[$queryClass]);
    }

    /**
     * Récupère tous les mappings enregistrés
     *
     * @return array<string, array<string>|string>
     */
    public function getAllMappings(): array
    {
        return $this->queryToHandler;
    }

    /**
     * Efface tous les mappings
     */
    public function clear(): self
    {
        $this->queryToHandler = [];

        return $this;
    }

    /**
     * Enregistre plusieurs mappings en une fois
     *
     * @param array<string, array<string>|string> $mappings
     */
    public function registerMany(array $mappings): self
    {
        foreach ($mappings as $query => $handler) {
            $this->register($query, $handler);
        }

        return $this;
    }
}
