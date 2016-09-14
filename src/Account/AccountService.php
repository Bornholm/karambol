<?php

namespace Karambol\Account;

use Karambol\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Karambol\KarambolApp;
use Karambol\Account\ChangePasswordEvent;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Doctrine\DBAL\Types\Type;

class AccountService extends EventDispatcher {

  protected $app;

  public function __construct(KarambolApp $app) {
    $this->app = $app;
  }

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

  public function accountExists($username) {
    $orm = $this->app['orm'];
    $qb = $orm->getRepository(User::class)->createQueryBuilder('u');
    $qb->select('count(u)')
      ->where($qb->expr()->eq('u.username', $qb->expr()->literal($username)))
    ;
    return $qb->getQuery()->getSingleScalarResult() != 0;
  }

  public function emailExists($email) {
    $orm = $this->app['orm'];
    $qb = $orm->getRepository(User::class)->createQueryBuilder('u');
    $qb->select('count(u)')
      ->where($qb->expr()->eq('u.email', $qb->expr()->literal($email)))
    ;
    return $qb->getQuery()->getSingleScalarResult() != 0;
  }

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

  protected function generateSalt() {
    return base64_encode(random_bytes(8));
  }

  protected function hashPassword($password, $salt) {
    $encoder = $this->app['security.encoder.digest'];
    return $encoder->encodePassword($password, $salt);
  }

}
