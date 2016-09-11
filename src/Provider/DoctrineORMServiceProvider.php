<?php

namespace Karambol\Provider;

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
      $config = Setup::createAnnotationMetadataConfiguration($this->entitiesFiles, $this->debug, null, null, false);
      $app['orm'] = EntityManager::create($this->databaseConfig, $config);
    }

    public function boot(Application $app) {}

}
