<?php


namespace PhantomJs\Tests;

use PhantomJs\Cache\FileCache;
use PhantomJs\DependencyInjection\ServiceContainer;


class TestCase extends \PHPUnit\Framework\TestCase
{
    public function getContainer(): ServiceContainer
    {
        return ServiceContainer::getInstance();
    }

}
