<?php

declare(strict_types=1);


use Selrahcd\LearnRector\ModifyComment\ModifyCommentRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (
    ContainerConfigurator $containerConfigurator
): void {
    $services = $containerConfigurator->services();
    $services->set(ModifyCommentRector::class);
};