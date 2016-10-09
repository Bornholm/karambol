<?php

namespace Karambol\Provider;

use Doctrine\Common\Cache\ArrayCache;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class DoctrineORMServiceProvider implements ServiceProviderInterface
{
    protected $databaseConfig;
    protected $entitiesFiles;
    protected $debug;

    public function __construct($entitiesFiles, $databaseConfig, $debug) {
        $this->entitiesFiles = $entitiesFiles;
        $this->databaseConfig = $databaseConfig;
        $this->debug = $debug;
    }

    public function register(Application $app) {

      $doctrineConfig = Setup::createAnnotationMetadataConfiguration($this->entitiesFiles, $this->debug, null, new ArrayCache(), false);
      $databaseConfig = $this->databaseConfig;

      $app['orm'] = $app->share(function() use ($doctrineConfig, $databaseConfig) {
        return EntityManager::create($databaseConfig, $doctrineConfig);
      });

    }

    public function boot(Application $app) {}

}
