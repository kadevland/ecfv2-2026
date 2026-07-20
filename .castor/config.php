<?php

declare(strict_types=1);

use Castor\Context;
use Castor\Attribute\AsContext;

use function Castor\load_dot_env;

#[AsContext(default: true)]
function createContext(): Context
{
    $envVars = [];

    // Charger les fichiers env selon l'environnement
    foreach (EvnFiles::prod() as $envFile) {
        $envPath = ROOT_PATH . '/' . $envFile->value;
        if (file_exists($envPath)) {
            $env     = load_dot_env($envPath);
            $envVars = array_merge($envVars, $env);
        }
    }

    return new Context($envVars);
}
