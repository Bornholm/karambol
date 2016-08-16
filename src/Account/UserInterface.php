<?php

namespace Karambol\Account;

use Symfony\Component\Security\Core\User as CoreUser;
use Karambol\AccessControl\ResourceOwnerInterface;
use Karambol\RuleEngine\RuleEngineVariableViewInterface;

interface UserInterface extends CoreUser\UserInterface, ResourceOwnerInterface, RuleEngineVariableViewInterface {}
