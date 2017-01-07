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
namespace Karambol\Account;

use Karambol\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Karambol\KarambolApp;
use Karambol\Account\ChangePasswordEvent;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Doctrine\DBAL\Types\Type;

/**
 * Gestion des comptes
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class AccountService extends EventDispatcher {
  
  /**
   * Application
   * @var KarambolApp
   * @author William Petit
   */
  protected $app;
  
  /**
   * Constructeur de classe
   * @param KarambolApp $app Application
   * @author William Petit
   */
  public function __construct(KarambolApp $app) {
    $this->app = $app;
  }
  
  /**
   * Creation de compte
   * @param string $username Nom d'utilisateur
   * @param string $password Mot de passe
   * @param string $email Adresse email
   * @return User
   * @throws Exception\AccountExistsException
   * @throws Exception\EmailExistsException
   * @author William Petit
   */
  public function createAccount($username, $password, $email = null) {

    if($this->accountExists($username)) {
      throw new Exception\AccountExistsException($username);
    }

    if($email !== null && $this->emailExists($email)) {
      throw new Exception\EmailExistsException($email);
    }

    $orm = $this->app['orm'];

    $salt = $this->generateSalt();
    $hash = $this->hashPassword($password, $salt);

    $user = new User();

    $user->setUsername($username);
    $user->setEmail($email);
    $user->changePassword($hash, $salt);

    $event = new CreateAccountEvent($user, $password);
    $this->dispatch(CreateAccountEvent::BEFORE_CREATE_ACCOUNT, $event);

    $orm->persist($user);
    $orm->flush();

    $this->dispatch(CreateAccountEvent::AFTER_CREATE_ACCOUNT, $event);

    return $user;

  }
  
  /**
   * Test l'existence d'un compte
   * @param string $username Nom d'utilisateur
   * @return boolean
   * @author William Petit
   */
  public function accountExists($username) {
    $orm = $this->app['orm'];
    $qb = $orm->getRepository(User::class)->createQueryBuilder('u');
    $qb->select('count(u)')
      ->where($qb->expr()->eq('u.username', $qb->expr()->literal($username)))
    ;
    return $qb->getQuery()->getSingleScalarResult() != 0;
  }
  
  /**
   * Test l'existence d'un email
   * @param string $email Adresse email
   * @return boolean
   * @author William Petit
   */
  public function emailExists($email) {
    $orm = $this->app['orm'];
    $qb = $orm->getRepository(User::class)->createQueryBuilder('u');
    $qb->select('count(u)')
      ->where($qb->expr()->eq('u.email', $qb->expr()->literal($email)))
    ;
    return $qb->getQuery()->getSingleScalarResult() != 0;
  }
  
  /**
   * Change le mot de passe d'un utilisateur
   * @param User $user
   * @param string $newPassword
   * @return User
   * @author William Petit
   */
  public function changePassword(User $user, $newPassword) {

    $orm = $this->app['orm'];

    $salt = $this->generateSalt();
    $hash = $this->hashPassword($newPassword, $salt);

    $user->changePassword($hash, $salt);

    $orm->flush();

    $event = new ChangePasswordEvent($user, $password);
    $this->dispatch(ChangePasswordEvent::NAME, $event);

    return $user;

  }
  
  /**
   * Trouve un utilisateur a partir d'un token
   * @param string $token
   * @param \DateInterval $validityInterval
   * @return User
   * @author William Petit
   */
  public function findUserForPasswordToken($token, \DateInterval $validityInterval = null) {

    if($validityInterval == null) $validityInterval = new \DateInterval('P1D');

    $validityThreshold = new \DateTime();
    $validityThreshold->sub($validityInterval);

    $orm = $this->app['orm'];

    $qb = $orm->getRepository(User::class)->createQueryBuilder('u');

    $qb->select('u')
      ->andWhere($qb->expr()->eq('u.passwordToken', $qb->expr()->literal($token)))
      ->andWhere($qb->expr()->gt('u.passwordTokenTimestamp', ':validityThreshold'))
      ->setParameter('validityThreshold', $validityThreshold, Type::DATETIME)
    ;

    return $qb->getQuery()->getOneOrNullResult();

  }
  
  /**
   * Envoi un mail pour renouveler son mot de passe
   * @param User $user
   * @return AccountService
   * @author William Petit
   */
  public function sendPasswordResetEmail(User $user) {

    $mailer = $this->app['mailer'];
    $orm = $this->app['orm'];
    $twig = $this->app['twig'];
    $translator = $this->app['translator'];
    $urlGenerator = $this->app['url_generator'];

    $user->resetPasswordToken();
    $orm->flush();

    $passwordResetUrl = $urlGenerator->generate('password_reset', ['token' => $user->getPasswordToken()], UrlGenerator::ABSOLUTE_URL);
    $message = sprintf($translator->trans('email.password_reset_message'), $passwordResetUrl);

    $messageVars = [
      'username' => $user->getUsername(),
      'message' => $message,
      'signature' => 'The Karambol Team'
    ];

    $messageTextContent = $twig->render('email/layout.txt.twig', $messageVars);
    $messageHTMLContent = $twig->render('email/layout.html.twig', $messageVars);

    $message = \Swift_Message::newInstance();
    $message->setTo($user->getEmail());
    $message->setSubject($translator->trans('email.password_reset_subject'));
    $message->setBody($messageTextContent);
    $message->addPart($messageHTMLContent, 'text/html');

    $mailer->send($message);

    return $this;

  }
  
  /**
   * Génère un grain de sel
   * @return string
   * @author William Petit
   */
  protected function generateSalt() {
    return base64_encode(random_bytes(8));
  }
  
  /**
   * Hash un mot de passe
   * @param string $password
   * @param string $salt
   * @return string Mot de passe hashé
   * @author William Petit
   */
  protected function hashPassword($password, $salt) {
    $encoder = $this->app['security.encoder.digest'];
    return $encoder->encodePassword($password, $salt);
  }

}
