<?php

namespace Karambol\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use DebugBar\StandardDebugBar;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\Bridge as DebugBarBridge;
use Doctrine\DBAL\Logging\DebugStack;

class DebugBarProvider implements ServiceProviderInterface
{

  public function register(Application $app) {
    $app['debug_bar'] = $app->share(function() { return new StandardDebugBar(); });
  }

  public function boot(Application $app) {

    if(!$app['debug']) return;

    $debugBar = $app['debug_bar'];

    // Collecteur Doctrine
    if(!$debugBar->hasCollector('doctrine')) $debugBar->addCollector($this->getDoctrineCollector($app));
    // Collecteur Monolog
    if(!$debugBar->hasCollector('monolog')) $debugBar->addCollector($this->getMonologCollector($app));

  }

  protected function getDoctrineCollector(Application $app) {
    $debugStack = new DebugStack();
    $orm = $app['orm'];
    $orm->getConnection()->getConfiguration()->setSQLLogger($debugStack);
    return new DebugBarBridge\DoctrineCollector($debugStack);
  }

  protected function getMonologCollector(Application $app) {
    $logger = $app['logger'];
    $logLevel = $app['config']['logger']['level'];
    $collector = new DebugBarBridge\MonologCollector($logger);
    return $collector;
  }

}
