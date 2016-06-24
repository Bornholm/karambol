<?php

namespace Karambol\Controller\Admin;

use Karambol\KarambolApp;
use Karambol\Controller\Controller;
use Karambol\Form\Type\SettingsType;

class SettingsController extends Controller {

  public function mount(KarambolApp $app) {
    $app->get('/admin/settings', array($this, 'showSettings'))->bind('settings');
    $app->post('/admin/settings', array($this, 'handleSettings'))->bind('handle_settings');
  }

  public function showSettings() {
    $twig = $this->get('twig');
    $form = $this->getSettingsForm();
    return $twig->render('admin/settings/index.html.twig', [
      'settingsForm' => $form->createView()
    ]);
  }

  public function handleSettings() {

    $twig = $this->get('twig');
    $request = $this->get('request');
    $form = $this->getSettingsForm();

    $form->handleRequest($request);

    return $twig->render('admin/settings/index.html.twig', [
      'settingsForm' => $form->createView()
    ]);

  }

  public function getSettingsForm() {

    $formFactory = $this->get('form.factory');
    $urlGen = $this->get('url_generator');
    $settings = $this->get('settings');

    $formBuilder = $formFactory->createBuilder(SettingsType::class, [], [ 'settings' => $settings ]);
    $action = $urlGen->generate('handle_settings');

    return $formBuilder->setAction($action)
      ->setMethod('POST')
      ->getForm()
    ;

  }

}
