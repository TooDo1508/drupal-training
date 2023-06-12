<?php

namespace Drupal\notification_bar\Form;

use Drupal\Core\Ajax\AjaxResponse;

use Drupal\Core\Ajax\RedirectCommand;

use Drupal\Core\Url;

use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Form\FormBase;

use Drupal\Core\Form\FormStateInterface;


use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\HtmlCommand;

use Drupal\Core\Ajax\ReplaceCommand;


use Drupal\Core\Form\ConfigFormBase;
use Drupal\Component\Render\FormattableMarkup;


/**
 * SendToDestinationsForm class.
 */
class NotificationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'notification_bar';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $options = NULL) {

    // $config
    $config = $this->config('notification_bar.admin_config');

    //build form

    $form['notification_message'] = array(
      '#type' => 'textarea',
      '#title' => t('Notification message text'),
      '#default_value' => $config->get('notification_message'),
      '#description' => t('Enter a notification message for the users'),
    );

    $form['notification_message_allowed'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable notification message'),
      '#description' => t('Alows site administrators to enable the notification message'),
      '#default_value' => $config->get('notification_message_allowed'),
    );



    $form['bbs_bgcolor'] = array(
      '#type' => 'color',
      '#title' => t('Background Color'),
      '#default_value' => $config->get('bbs_bgcolor'),
    );


    $form['bbs_notification_font_color'] = array(
      '#type' => 'color',
      '#title' => t('Font Color'),
      '#default_value' => $config->get('bbs_notification_font_color'),
    );
    return parent::buildForm($form, $form_state);
  }

 

  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {



    $this->config('notification_bar.admin_config')
    ->set('notification_message', $form_state->getValue('notification_message'))
    ->set('notification_message_allowed', $form_state->getValue('notification_message_allowed'))
    ->set('bbs_bgcolor', $form_state->getValue('bbs_bgcolor'))
    ->set('bbs_notification_font_color', $form_state->getValue('bbs_notification_font_color'))
      ->save();
    parent::submitForm($form, $form_state);


  }

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return [
      'notification_bar.admin_config',
    ];
  }

}
