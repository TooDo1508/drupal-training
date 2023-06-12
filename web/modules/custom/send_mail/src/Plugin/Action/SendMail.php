<?php

namespace Drupal\mail_sender\Plugin\SendMail;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Action\ActionBase;
use Drupal\Core\Mail\MailManager;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A basic example action that does nothing.
 *
 * @Action(
 *   id = "action_example_basic_action",
 *   label = @Translation("Action Example: A basic example action that does nothing"),
 *   type = "system"
 * )
 */
class SendMail
{


    /**
     * A current user instance which is logged in the session.
     * @var \Drupal\Core\Mail\MailManager
     */
    protected $sendmail;

    /**
     * A current user instance which is logged in the session.
     * @var \Drupal\Core\Session\AccountProxyInterface
     */
    protected $loggedUser;

    /**
     * Construct a BasicExample object.
     *
     * @param array $configuration
     *   A configuration array containing information about the plugin instance.
     * @param string $plugin_id
     *   The plugin ID for the plugin instance.
     * @param mixed $plugin_definition
     *   The plugin implementation definition.
     * @param \Drupal\Core\Mail\MailManager $sendmail
     *   Service from mail.
     * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
     *   A currently logged user instance.
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, MailManager $sendmail, AccountProxyInterface $current_user)
    {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->sendmail = $sendmail;
        $this->loggedUser = $current_user;
    }

    /**
     * {@inheritDoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
    {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('plugin.manager.mail'),
            $container->get('current_user'),
        );
    }

    public function sendMail()
    {

//        $mailManager = \Drupal::service('plugin.manager.mail');
        $module = '';
        $key = 'create_article';
//        $to = \Drupal::currentUser()->getEmail();
        $to = $this->loggedUser->getEmail();
        $params['message'] = 'message';
        $params['node_title'] = 'title';
//        $langcode = \Drupal::currentUser()->getPreferredLangcode();
        $langcode = $this->loggedUser->getPreferredLangcode();
        $send = true;

        $result = $this->sendmail->mail($module, $key, $to, $langcode, $params, NULL, $send);
        if ($result['result'] !== true) {
            drupal_set_message(t('There was a problem sending your message and it was not sent.'), 'error');
        } else {
            drupal_set_message(t('Your message has been sent.'));
        }
    }

}
