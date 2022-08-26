<?php


namespace PhantomJs\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;


class ServiceContainer extends ContainerBuilder
{
    private static ?ServiceContainer $instance = null;

    public static function getInstance(): ServiceContainer
    {
        if (null === self::$instance) {
            self::$instance = new static();

            $loader = new YamlFileLoader(self::$instance, new FileLocator(__DIR__.'/../Resources/config'));
            $loader->load('config.yml');
            $loader->load('services.yml');

            self::$instance->setParameter('phantomjs.cache_dir', sys_get_temp_dir());
            self::$instance->setParameter('phantomjs.resource_dir', __DIR__.'/../Resources');
        }

        return self::$instance;
    }
}
