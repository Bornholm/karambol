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
use Karambol\Entity\Page;
use Karambol\Entity\Ruleset;
use Karambol\Form\Type\RulesetType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Rule controller
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class RulesController extends Controller {
  
  /**
   * Nom du set de regle
   * @var string
 * @author William Petit
   */
  protected $rulesetName;
  
  /**
   * Constructeur de classe
   * @param String $rulesetName
   * @author William Petit
   */
  public function __construct($rulesetName) {
    $this->rulesetName = $rulesetName;
  }
  
  /**
   * Définition des routes
   * @param KarambolApp $app Application
   * @author William Petit
   */
  public function mount(KarambolApp $app) {
    $rulesetName = $this->rulesetName;
    $app->get(sprintf('/admin/rules/%s', $rulesetName), [$this, 'showRules'])
      ->bind(sprintf('admin_rules_%s', $rulesetName))
    ;
    $app->post(sprintf('/admin/rules/%s', $rulesetName), [$this, 'handleRulesetUpsert'])
      ->bind(sprintf('admin_ruleset_upsert_%s', $rulesetName))
    ;
  }
  
  /**
   * Affichage des regles
   * @return View
   * @author William Petit
   */
  public function showRules() {

    $twig = $this->get('twig');
    $orm = $this->get('orm');

    $ruleset = $orm->getRepository('Karambol\Entity\Ruleset')
      ->findOneByName($this->rulesetName)
    ;

    if($ruleset === null) {
      $ruleset = new Ruleset();
      $ruleset->setName($this->rulesetName);
    }

    $rulesetForm = $this->getRulesetForm($ruleset);

    return $twig->render('admin/rules/index.html.twig', [
      'rulesetForm' => $rulesetForm->createView(),
      'ruleset' => $ruleset
    ]);

  }
  
  /**
   * Insert/Update regle
   * @return Redirect
   * @author William Petit
   */
  public function handleRulesetUpsert() {

    $twig = $this->get('twig');
    $orm = $this->get('orm');
    $request = $this->get('request');

    $ruleset = $orm->getRepository('Karambol\Entity\Ruleset')
      ->findOneByName($this->rulesetName)
    ;

    if($ruleset === null) {
      $ruleset = new Ruleset();
      $ruleset->setName($this->rulesetName);
    }

    $rulesetForm = $this->getRulesetForm($ruleset);

    $rulesetForm->handleRequest($request);

    if( !$rulesetForm->isValid() ) {
      return $twig->render('admin/rules/index.html.twig', [
        'rulesetForm' => $rulesetForm->createView(),
        'ruleset' => $ruleset
      ]);
    }

    $ruleset = $rulesetForm->getData();

    if( $ruleset->getId() === null ) {
      $orm->persist($ruleset);
    }

    $orm->flush();

    $urlGen = $this->get('url_generator');
    return $this->redirect($urlGen->generate(sprintf('admin_rules_%s', $this->rulesetName)));

  } 

  /**
   * Renvoi le formulaire de regle
   * @param Ruleset $ruleset
   * @return Form
   * @author William Petit
   */
  public function getRulesetForm(Ruleset $ruleset) {

    $formFactory = $this->get('form.factory');
    $urlGen = $this->get('url_generator');

    $formBuilder = $formFactory->createBuilder(RulesetType::class, $ruleset);
    $action = $urlGen->generate(sprintf('admin_ruleset_upsert_%s', $ruleset->getName()));

    return $formBuilder->setAction($action)
      ->setMethod('POST')
      ->getForm()
    ;

  }

}
