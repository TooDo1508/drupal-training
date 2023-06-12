<?php

namespace Drupal\modal_form_example\Form;


use Drupal\Core\Form\ConfirmFormBase;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Url;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\ReplaceCommand;

/**
 * ExampleForm class.
 */
class ExampleForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $options = NULL) {
    $form['#prefix'] = '<div id="modal_example_form_step">';
    $form['#suffix'] = '</div>';

      $tempstore = \Drupal::service('tempstore.private');
      // Get the store collection.

      $store = $tempstore->get('modal_form_example_modal_form');
      // Get the key/value pair.
      $value = $store->get('page_num');

    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name'),
      '#default_value' => '',
      '#description' => $this->t('Enter your last name.'),
    ];

    $form['open_modal'] = [
      '#type' => 'link',
      '#title' => $this->t('Open Modal'),
      '#url' => Url::fromRoute('modal_form_example.open_modal_form'),
      '#attributes' => [
        'class' => [
          'use-ajax',
          'button',
        ],
      ],
    ];

    $form['actions']['skip'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Skip'),
      
      // Custom submission handler for page 1.
      // skip validation
        // '#attributes' => array('onclick' => 'if(!confirm("Really Delete?")){return false;}')
     '#ajax' => [
       'callback' => '::ajaxSubmitForm',
       'event' => 'click',
     ],
    ];

    

    // Attach the library for pop-up dialogs/modals.
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';

    return $form;
  }

  public function getQuestion() {
    return $this->t('Do you want to delete %id?', ['%id' => $this->id]);
  }
  

  public function ajaxSubmitForm(array &$form, FormStateInterface $form_state) {

      $last_name = $form_state->getValue('last_name');
      $step = 10;


      $tempstore = \Drupal::service('tempstore.private');
      $store = $tempstore->get('modal_form_example_form');
      $store->set('page_num', $step);
      // We begin building a new ajax reponse.

    $response = new AjaxResponse();

    // If the user submitted the form and there are errors, show them the
    // input dialog again with error messages. Since the title element is
    // required, the empty string wont't validate and there will be an error.
    // if ($form_state->getErrors()) {
      // If there are errors, we can show the form again with the errors in
      // the status_messages section

      // Get the modal form using the form builder.
       $modal_form = \Drupal::formBuilder()->getForm('Drupal\modal_form_example\Form\ModalForm', $last_name);

//     $modal_form = \Drupal::formBuilder()->getForm('Drupal\modal_custom\Form\ModalCustom');

      // Add an AJAX command to open a modal dialog with the form as the content.
      $response->addCommand(new OpenModalDialogCommand('My Modal Form', $modal_form, ['width' => '800']));

    return $response;
  }

  

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'modal_form_example_form';
  }

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return ['config.modal_form_example_form'];
  }

}
