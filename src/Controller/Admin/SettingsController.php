<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Karambol\Controller\Admin;

use Karambol\KarambolApp;
use Karambol\Controller\Controller;
use Karambol\Form\Type\SettingsType;

/**
 * Setting controller
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class SettingsController extends Controller {

  /**
   * Définition des routes
   * @param KarambolApp $app Application
   * @author William Petit
   */
  public function mount(KarambolApp $app) {
    $app->get('/admin/settings', [$this, 'showSettings'])->bind('settings');
    $app->post('/admin/settings', [$this, 'handleSettings'])->bind('handle_settings');
  }
  
  /**
   * Affichage des parametres
   * @return View
   * @author William Petit
   */
  public function showSettings() {

    $twig = $this->get('twig');
    $form = $this->getSettingsForm();
    return $twig->render('admin/settings/index.html.twig', [
      'settingsForm' => $form->createView()
    ]);
  }
  
  /**
   * Enregistre les parametres
   * @return Redirect
   * @author William Petit
   */
  public function handleSettings() {

    $twig = $this->get('twig');
    $request = $this->get('request');
    $form = $this->getSettingsForm();

    $form->handleRequest($request);

    if(!$form->isValid()) {
      return $twig->render('admin/settings/index.html.twig', [
        'settingsForm' => $form->createView()
      ]);
    }

    $data = $form->getData();
    $settings = $this->get('settings');

    $settings->save($data);

    $urlGen = $this->get('url_generator');
    return $this->redirect($urlGen->generate('settings'));

  }
  
  /**
   * Renvoi le formulaire de parametre
   * @return Form
   * @author William Petit
   */
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
