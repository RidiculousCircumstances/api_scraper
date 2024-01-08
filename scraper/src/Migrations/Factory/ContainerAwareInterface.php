<?php

namespace App\Migrations\Factory;

use Symfony\Component\DependencyInjection\ContainerInterface;

interface ContainerAwareInterface
{
    public function setContainer(ContainerInterface $container): void;
}