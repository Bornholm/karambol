<?php

namespace Karambol\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\EventManager;
use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use Doctrine\ORM\Events;
use Karambol\Account\UserInterface;

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

      $eventManager  = new EventManager();
      $targetEntityListener = new ResolveTargetEntityListener();
      $targetEntityListener->addResolveTargetEntity(UserInterface::class, $app['user_entity'], []);
      $eventManager->addEventListener(Events::loadClassMetadata, $targetEntityListener);

      $app['orm'] = EntityManager::create($this->databaseConfig, $config, $eventManager);

    }

    public function boot(Application $app) {}

}
