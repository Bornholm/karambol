<?php

  $app->get('/', function() use ($app){
    return $app['twig']->render('home/index.html.twig', [
      'test' => 'hello world !'
    ]);
  });
