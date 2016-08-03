<?php

namespace Karambol\AccessControl;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\HttpFoundation\Request;
use Karambol\AccessControl\ResourceInterface;
use Karambol\AccessControl\Resource;
use Karambol\AccessControl\BaseActions;
use Karambol\AccessControl\Parser\ResourceSelectorParser;
use Karambol\AccessControl\ResourceOwnerInterface;
use Karambol\RuleEngine\RuleEngine;
use Karambol\Entity\BaseUser;
use Karambol\RuleEngine\RuleEngineVariableViewInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RuleEngineAccessControlVoter implements VoterInterface {

  use \Karambol\Util\AppAwareTrait;

  public function vote(TokenInterface $token, $subject, array $attributes) {

    $app = $this->app;
    $logger = $app['logger'];
    $user = $token instanceof AnonymousToken ? new BaseUser() : $token->getUser();

    $action = null;
    $resource = null;

    if($subject instanceof ResourceInterface) {
      $action = $attributes[0];
      $resource = $subject;
    }

    // Vote sur l'usage d'un rôle
    if(is_array($subject) && count($subject) === 0 && count($attributes) === 1) {
      $resource = new Resource('role', $attributes[0], null);
      $action = BaseActions::ROLE;
    }

    if($subject instanceof Request) {
      $resource = new Resource('url', $subject->getRequestURI(), null);
      $action = BaseActions::ACCESS;
    }

    if($resource === null || $action === null) return VoterInterface::ACCESS_ABSTAIN;

    // dump($subject, $attributes);
    $logger->debug(sprintf(
      'Checking authorizations for user "%s" to use resource "%s" with action "%s"',
      $user instanceof UserInterface ? $user->getUsername() : '???',
      $resource,
      $action
    ));

    $ruleEngine = $app['rule_engine'];
    $rulesetRepo = $app['orm']->getRepository('Karambol\Entity\RuleSet');

    $ruleset = $rulesetRepo->findOneByName(RuleEngine::ACCESS_CONTROL);

    if(!$ruleset) return VoterInterface::ACCESS_ABSTAIN;

    $context = new \stdClass();
    $context->authorizations = [];
    $context->user = $user;

    $vars = [
      '_context' => $context,
      'user' =>  $user instanceof RuleEngineVariableViewInterface ? $user->createRuleEngineView() : $user,
      'resource' => $resource instanceof RuleEngineVariableViewInterface ? $resource->createRuleEngineView() : $resource
    ];

    $rules = $ruleset->getRules()->toArray();

    try {
      $ruleEngine->execute(RuleEngine::ACCESS_CONTROL, $rules, $vars);
    } catch(\Exception $ex) {
      // TODO Store rule exception and provide the debugging information to the administrator
      $logger->error($ex);
    }

    $authorizations =  $context->authorizations;
    $parser = new ResourceSelectorParser();

    // dump($action, $resource, $authorizations);

    foreach($authorizations as $auth) {

      $actionAuthorized = $auth['action'] === '*' || $auth['action'] === $action;
      if(!$actionAuthorized) continue;

      if($auth['resource'] !== null && $auth['resource'] === $resource) return VoterInterface::ACCESS_GRANTED;

      if($auth['selector'] !== null) {
        $selector = $parser->parse($auth['selector']);
        $resourceMatchesSelector = $selector->matches($resource);
        if($resourceMatchesSelector) return VoterInterface::ACCESS_GRANTED;
      }

    }

    return VoterInterface::ACCESS_DENIED;

  }

}
