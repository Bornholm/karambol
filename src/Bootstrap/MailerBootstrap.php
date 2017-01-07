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
namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Silex\Application;
use Silex\Provider\SwiftmailerServiceProvider;

/**
 * Initialisation mailer
 * @package Karambol
 * @since 1.0.0
 * @license AGPLv3
 * @author William Petit
 */
class MailerBootstrap implements BootstrapInterface {
  
  /**
   * Initialisation
   * @param KarambolApp $app
   * @author William Petit
   */
  public function bootstrap(KarambolApp $app) {

    $app->register(new SwiftmailerServiceProvider());

    $mailerConfig = $app['config']['mailer'];
    $app['swiftmailer.options'] = $mailerConfig['options'];
    $app['swiftmailer.sender_address'] = $mailerConfig['sender_address'];
    $app['swiftmailer.delivery_addresses'] = $mailerConfig['delivery_addresses'];
    $app['swiftmailer.delivery_whitelist'] = $mailerConfig['delivery_whitelist'];

    $mailer = $app['mailer'];

    // Automatically set default sender address
    $senderAddress = $mailerConfig['sender_address'];
    $mailer->registerPlugin(new DefaultSenderPlugin(
      is_array($senderAddress) ? $senderAddress[0] : $senderAddress,
      is_array($senderAddress) ? $senderAddress[1] : ''
    ));

  }

}

class DefaultSenderPlugin implements \Swift_Events_SendListener {

  protected $defaultSenderEmail;
  protected $defaultSenderName;

  public function __construct($defaultSenderEmail, $defaultSenderName = '') {
    $this->defaultSenderEmail = $defaultSenderEmail;
    $this->defaultSenderName = $defaultSenderName;
  }

  /**
   * Invoked immediately before the Message is sent.
   *
   * @param Swift_Events_SendEvent $evt
   */
  public function beforeSendPerformed(\Swift_Events_SendEvent $evt) {

    $message = $evt->getMessage();

    if(!count($message->getFrom())) {
      $message->setFrom($this->defaultSenderEmail, $this->defaultSenderName);
    }

  }

  /**
   * Invoked immediately after the Message is sent.
   *
   * @param Swift_Events_SendEvent $evt
   */
  public function sendPerformed(\Swift_Events_SendEvent $evt) {}

}
