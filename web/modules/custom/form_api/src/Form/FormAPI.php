<?php
/**
 * @file
 * Contains \Drupal\form_api\Form\FormAPI.
 */
namespace Drupal\form_api\Form;

use Drupal\Core\Form\FormBase;

use Drupal\Core\Form\FormStateInterface;

class FormAPI extends FormBase {
  /**
   * {@inheritdoc}
   */
   
  public function getFormId(){
    return 'mymodule_settings';
  }


  public function buildForm(array $form, FormStateInterface $form_state) {

    if (!\Drupal::currentUser()->hasPermission('access secret notes')) {
      return t('You dont have permission in this page');
    }
//
    $form['site_name']= [
      '#type' => 'textfield',
      '#title' => $this->t('Site Name'),

    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (strlen($form_state->getValue('site_name')) > 6) {
      $form_state->setErrorByName('site_name', $this->t('The sitename too long. Please enter site name again'));
    }
  }



  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::messenger()->addMessage(t('Here is your site name'));
    $this->messenger()->addStatus($this->t('Your site name is @sitename', ['@sitename' => $form_state->getValue('site_name')]));
    \Drupal::configFactory()->getEditable('system.site')->set('name', $form_state->getValue('site_name'))->save();

  }

   
}