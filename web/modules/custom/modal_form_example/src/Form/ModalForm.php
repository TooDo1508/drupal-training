<?php

namespace Drupal\modal_form_example\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Url;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Component\Render\FormattableMarkup;

/**
 * SendToDestinationsForm class.
 */
class ModalForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'modal_form_example_modal_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $lastname = NULL) {
    $form['#prefix'] = '<div id="modal_example_form">';
    $form['#suffix'] = '</div>';

    // $tempstore = \Drupal::service('tempstore.private');
    //   // Get the store collection. 
      
    // $store = $tempstore->get('modal_form_example_form');
    //   // Get the key/value pair.
    // $value = $store->get('page_num');

    // The status messages that will contain any form errors.
    $form['status_messages'] = [
      '#type' => 'status_messages',
      '#weight' => -10,
    ];

    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name'),
      '#default_value' => is_null($lastname) ? '3' : $lastname,
      '#description' => $this->t('Enter your last name.'),
    ];

    // A required checkboxes field.
    $form['select'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Selec t Destination(s)'),
      '#options' => ['random_value' => 'Some random value'],
      // '#required' => TRUE,
    ];

    // $form['actions'] = array('#type' => 'actions');
    $form['actions']['send'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit modal form'),
      // '#attributes' => [
      //   'class' => [
      //     'use-ajax',
      //   ],
      // ],
      '#ajax' => [
        'callback' => [$this, 'submitModalFormAjax'],
        'event' => 'click',
      ],
    ];
  //  $form['submit'] = [
  //        '#type' => 'submit',
  //        '#button_type' => 'primary',
  //        '#value' => $this->t('Submit'),
  //  ];

    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';

    return $form;
  }

  /**
   * AJAX callback handler that displays any errors or a success message.
   */
  public function submitModalFormAjax(array $form, FormStateInterface $form_state) {

    \Drupal::logger('modal_form_example')->notice('message 222');
    // // 2. Get the value somewhere else in the app.
    //   $tempstore = \Drupal::service('tempstore.private');
    //   // Get the store collection. 
    //   $store = $tempstore->get('modal_form_example_form');
    //   // Get the key/value pair.
    //   $value = $store->get('page_num');


    //   $store = $tempstore->get('modal_form_example_modal_form');

    //   $store->set('page_num', $value+1);

    //   $value2 = $store->get('page_num');

    //  $form_state->setRedirect('modal_form_example.form');
    //  $form_state->setRedirect('pfizer_question.subscribe_form');
     
    $response = new AjaxResponse();

//     If there are any form errors, AJAX replace the form.
    if ($form_state->hasAnyErrors()) {
      $response->addCommand(new ReplaceCommand('#modal_example_form', $form));
    }
    else {
     //  $modal_form = \Drupal::formBuilder()->getForm('Drupal\modal_form_example\Form\ExampleForm');
      // $response->addCommand(new ReplaceCommand('#modal_example_form_step', 'Hello, I am here'));
      $response->addCommand(new CloseModalDialogCommand());
     // $response->addCommand(new OpenModalDialogCommand("Success!", 'The modal form has been submitted.'.$value2, ['width' => 700]));
    }
  //  $response->addCommand(new CloseModalDialogCommand());

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // \Drupal::service('messenger')->addMessage("Why won't this message show?");

//
// //
//      $tempstore = \Drupal::service('tempstore.private');
//      // Get the store collection.

//      $store = $tempstore->get('modal_form_example_form');
//      // Get the key/value pair.
//      $value = $store->get('page_num');

    //  $store = $tempstore->get('modal_form_example_modal_form');
    //  $store->set('page_num', $value+1);
    \Drupal::logger('modal_form_example')->notice('message333');
    // $response = new AjaxResponse();
    // $response->addCommand(new CloseModalDialogCommand());
    // return $response;
    //  $form_state->setRedirect('modal_form_example.form');

  }

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return ['config.modal_form_example_modal_form'];
  }

}
