<?php

namespace Karambol\AccessControl\Voter;

use Karambol\RuleEngine\Variable\VarsFactory;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\HttpFoundation\Request;
use Karambol\AccessControl\ResourceInterface;
use Karambol\AccessControl\Resource;
use Karambol\AccessControl\BaseActions;
use Karambol\AccessControl\Parser\ResourceSelectorParser;
use Karambol\AccessControl\Permission\PermissionCollection;
use Karambol\AccessControl\ResourceOwnerInterface;
use Karambol\AccessControl\Permission\PermissionChecker;
use Karambol\RuleEngine\RuleEngine;
use Karambol\Entity\User;
use Karambol\RuleEngine\Context\Context;
use Symfony\Component\Security\Core\User\UserInterface;

class RuleEngineAccessControlVoter implements VoterInterface {

  use \Karambol\Util\AppAwareTrait;

  public function vote(TokenInterface $token, $subject, array $attributes) {

    $app = $this->app;
    $logger = $app['logger'];
    $debugBar = $app['debug_bar'];

    $user = $token instanceof AnonymousToken ? new User() : $token->getUser();

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

    $debugBar['time']->startMeasure(
      'access_control_rules',
      sprintf('Access control rules execution - %s',
        $this->getUserActionAsText($user, $action, $resource)
      )
    );

    $logger->debug(sprintf('Checking authorizations - %s', $this->getUserActionAsText($user, $action, $resource)));

    $ruleEngine = $app['rule_engine'];
    $rulesetRepo = $app['orm']->getRepository('Karambol\Entity\Ruleset');

    $ruleset = $rulesetRepo->findOneByName(RuleEngine::ACCESS_CONTROL);

    if(!$ruleset) {
      $debugBar['time']->stopMeasure('access_control_rules');
      return VoterInterface::ACCESS_ABSTAIN;
    }

    $context = new Context();
    $context->expose('user', $user);
    $context->expose('resource', $resource);
    $context->expose('_authorizations', new PermissionCollection());
    $context->expose('_rejections', new PermissionCollection());

    $rules = $ruleset->getRules()->toArray();

    try {
      $ruleEngine->execute(RuleEngine::ACCESS_CONTROL, $rules, $context);
    } catch(\Exception $ex) {
      // TODO Store rule exception and provide the debugging information to the administrator
      $logger->error($ex);
      $debugBar['time']->stopMeasure('access_control_rules');
      return VoterInterface::ACCESS_DENIED;
    }

    $rejections = $context->getVariable('_rejections')->getSource();
    $authorizations =  $context->getVariable('_authorizations')->getSource();

    $permChecker = new PermissionChecker($authorizations, $rejections);
    $isAllowed = $permChecker->isAllowed($action, $resource);

    $debugBar['time']->stopMeasure('access_control_rules');

    if($isAllowed) {
      $logger->debug(sprintf('Authorization granted - %s', $this->getUserActionAsText($user, $action, $resource)));
      return VoterInterface::ACCESS_GRANTED;
    }

    $logger->debug(sprintf('Authorization denied - %s', $this->getUserActionAsText($user, $action, $resource)));
    return VoterInterface::ACCESS_DENIED;

  }

  protected function getUserActionAsText($user, $action, ResourceInterface $resource) {
    return sprintf(
      '"%s" DO "%s" ON "%s")',
      $user instanceof UserInterface && $user->getUsername() ? $user->getUsername() : 'anon.',
      $action,
      $resource
    );
  }

}
