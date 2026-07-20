<?php

declare(strict_types=1);

namespace App\Application\Bus;

use InvalidArgumentException;

/**
 * Registry pour mapper les Commands vers leurs Handlers
 *
 * Système centralisé et explicite pour enregistrer les associations Command -> Handler
 */
final class CommandRegistry
{
    /** @var array<string, array<string>|string> */
    private array $commandToHandler = [];

    /**
     * Enregistre une association Command -> Handler(s)
     *
     * @param string|array<string> $handlerClass
     */
    public function register(string $commandClass, string|array $handlerClass): self
    {
        if (!class_exists($commandClass)) {
            throw new InvalidArgumentException("Command class [{$commandClass}] does not exist");
        }

        $handlers = is_array($handlerClass) ? $handlerClass : [$handlerClass];

        foreach ($handlers as $handler) {
            if (!class_exists($handler)) {
                throw new InvalidArgumentException("Handler class [{$handler}] does not exist");
            }
        }

        $this->commandToHandler[$commandClass] = $handlerClass;

        return $this;
    }

    /**
     * Récupère le(s) handler(s) pour une command donnée
     *
     * @return array<string>
     */
    public function getHandlers(string $commandClass): array
    {
        if (!isset($this->commandToHandler[$commandClass])) {
            throw new InvalidArgumentException("No handler registered for command [{$commandClass}]");
        }

        $handlers = $this->commandToHandler[$commandClass];

        return is_array($handlers) ? $handlers : [$handlers];
    }

    /**
     * Vérifie si une command a un handler enregistré
     */
    public function hasHandler(string $commandClass): bool
    {
        return isset($this->commandToHandler[$commandClass]);
    }

    /**
     * Récupère tous les mappings enregistrés
     *
     * @return array<string, array<string>|string>
     */
    public function getAllMappings(): array
    {
        return $this->commandToHandler;
    }

    /**
     * Efface tous les mappings
     */
    public function clear(): self
    {
        $this->commandToHandler = [];

        return $this;
    }

    /**
     * Enregistre plusieurs mappings en une fois
     *
     * @param array<string, array<string>|string> $mappings
     */
    public function registerMany(array $mappings): self
    {
        foreach ($mappings as $command => $handler) {
            $this->register($command, $handler);
        }

        return $this;
    }
}
