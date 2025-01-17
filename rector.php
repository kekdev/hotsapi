<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Laravel\Set\LaravelSetList;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PHP_VERSION_FEATURES,  \Rector\Core\ValueObject\PhpVersion::PHP_80);
    $parameters->set(Option::PATHS, [
        __DIR__ . '/app',
        __DIR__ . '/config',
        __DIR__ . '/database',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ]);

    $containerConfigurator->services()
        ->set(SetList::class)
        ->args([
            [
                SetList::PHP_80,
                LaravelSetList::LARAVEL_80,
                \Rector\Set\ValueObject\LevelSetList::UP_TO_PHP_80,
                \Rector\Laravel\Set\LaravelLevelSetList::UP_TO_LARAVEL_80,
            ],
        ]);
};
