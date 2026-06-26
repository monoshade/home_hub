<?php

declare(strict_types=1);

namespace App;

/**
 * Which environment the app runs as. Gates environment-specific behaviour
 * (CORS, error verbosity, ...).
 */
enum Environment: string
{
    case Prod = 'prod';
    case Demo = 'demo';
}
