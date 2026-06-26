<?php

declare(strict_types=1);

namespace App;

/**
 * Which database the app talks to. The concrete connection name for each case
 * is resolved in {@see Database::connection()}.
 */
enum Db: string
{
    case Prod = 'prod';
    case Demo = 'demo';
    case Test = 'test';
}
