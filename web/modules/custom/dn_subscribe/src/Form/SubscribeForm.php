<?php

namespace Drupal\dn_subscribe\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


class SubscribeForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'form_subscribe_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    if ($form_state->has('page_num') && $form_state->get('page_num') == 2) {
      //return $this->fapiExamplePageTwo($form, $form_state);
      return $this->subscribePageTwo($form, $form_state);
    }

    if ($form_state->has('page_num') && $form_state->get('page_num') == 3) {
      return $this->subscribePageThree($form, $form_state);
    }

    $form_state->set('page_num', 1);

    $form['#attached']['library'][] = 'dn_subscribe/dn_subscribe';
    $form['description'] = [
      '#type' => 'item',
      '#title' => $this->t('A basic multistep form (page 1)'),
    ];

    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name'),
      '#description' => $this->t('Enter your first name.'),
      '#default_value' => $form_state->getValue('first_name', ''),
      '#required' => TRUE,
    ];

    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name'),
      '#default_value' => $form_state->getValue('last_name', ''),
      '#description' => $this->t('Enter your last name.'),
    ];


    // Group submit handlers in an actions element with a key of "actions" so
    // that it gets styled correctly, and so that other modules may add actions
    // to the form. This is not required, but is convention.
    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['next'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Next'),
      // Custom submission handler for page 1.
      '#submit' => ['::subscribeFirstNextSubmit'],
      // Custom validation handler for page 1.
      //'#validate' => ['::fapiExampleMultistepFormNextValidate'],
    ];

    $form['actions']['skip'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Skip'),
      // Custom submission handler for page 1.
      '#submit' => ['::skipPage'],
      // Custom validation handler for page 1.
      //'#validate' => ['::fapiExampleMultistepFormNextValidate'],
      '#limit_validation_errors' => array(),
    ];

    $form['#prefix'] = $this->getFormPrefix(1);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $page_values = $form_state->get('page_values');

    $this->messenger()->addMessage($this->t('The form has been submitted. name="@first @last", address="@address". city="@city"', [
      '@first' => $page_values['first_name'],
      '@last' => $page_values['last_name'],
      '@address' => $page_values['address'],
      '@city' => $page_values['city'],
    ]));

    //$this->messenger()->addMessage($this->t('And the favorite color is @color', ['@color' => $form_state->getValue('color')]));
  }

  /**
   * Provides custom validation handler for page 1.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function fapiExampleMultistepFormNextValidate(array &$form, FormStateInterface $form_state) {
    $first_name = $form_state->getValue('first_name');

    if (strlen($first_name) < 2 ){
      $form_state->setErrorByName('first_name', 'Enter valid first name- length should be more than 2');
    }

  }

  /**
   * Provides custom submission handler for page 1.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function subscribeFirstNextSubmit(array &$form, FormStateInterface $form_state) {
    $form_state
      ->set('page_values', [
        // Keep only first step values to minimize stored data.
        'first_name' => $form_state->getValue('first_name'),
        'last_name' => $form_state->getValue('last_name'),
      ])
      ->set('page_num', 2)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 2, the AJAX-rendered form will still show page 1.
      ->setRebuild(TRUE);
  }

  public function skipPage(array &$form, FormStateInterface $form_state) {
      if($form_state->getValue('first_name') == NULL){
          $form_state->setValue('first_name',"")
          ->set('page_values', [
              // Keep only first step values to minimize stored data.
              'first_name' => $form_state->getValue('first_name'),
              'last_name' => $form_state->getValue('last_name'),
              //'birth_year' => $form_state->getValue('birth_year'),
          ]);
      }
      $form_state->set('page_num', 2)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 2, the AJAX-rendered form will still show page 1.
      ->setRebuild(TRUE);

  }

  /**
   * Builds the second step form (page 2).
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The render array defining the elements of the form.
   */
  public function subscribePageTwo(array &$form, FormStateInterface $form_state) {

    $form['#attached']['library'][] = 'dn_subscribe/dn_subscribe';
    $form['description'] = [
      '#type' => 'item',
      '#title' => $this->t('A basic multistep form (page 2)'),
    ];

    $form['address'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Address'),
      '#required' => TRUE,
      '#default_value' => $form_state->getValue('address', ''),
    ];
    $form['city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('City'),
      '#required' => TRUE,
      '#default_value' => $form_state->getValue('city', ''),
    ];
    $form['back'] = [
      '#type' => 'submit',
      '#value' => $this->t('Back'),
      // Custom submission handler for 'Back' button.
      '#submit' => ['::subscribePageTwoBack'],
      // We won't bother validating the required 'color' field, since they
      // have to come back to this page to submit anyway.
      '#limit_validation_errors' => [],
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Next'),
      '#submit' => ['::subscribeSecondNextSubmit']
    ];
    $form['#prefix'] = $this->getFormPrefix(2);


    return $form;
  }

  public function subscribeSecondNextSubmit(array &$form, FormStateInterface $form_state) {
    $name = $form_state->get('page_values');
    //print_r($fname);exit;
    $form_state
      ->set('page_values', [
        // Keep only first step values to minimize stored data.
        'address' => $form_state->getValue('address'),
        'city' => $form_state->getValue('city'),
        'first_name' => $name['first_name'],
        'last_name' => $name['last_name'],

      ])
      ->set('page_num', 3)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 2, the AJAX-rendered form will still show page 1.
      ->setRebuild(TRUE);
  }

  public function subscribePageThree(array &$form, FormStateInterface $form_state) {

    $form['#attached']['library'][] = 'dn_subscribe/dn_subscribe';
    $form['description'] = [
      '#type' => 'item',
      '#title' => $this->t('A basic multistep form (page 3)'),
    ];


    $form['declaration'] = [
      '#type' => 'checkboxes',
      '#required' => TRUE,
      '#options' => array('option-1' => t('Confirm details provided')),

    ];
    $form['back'] = [
      '#type' => 'submit',
      '#value' => $this->t('Back'),
      // Custom submission handler for 'Back' button.
      '#submit' => ['::subscribePageThreeBack'],
      // We won't bother validating the required 'color' field, since they
      // have to come back to this page to submit anyway.
      '#limit_validation_errors' => [],
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Submit'),
    ];
    $form['#prefix'] = $this->getFormPrefix(3);


    return $form;
  }

  /**
   * Provides custom submission handler for 'Back' button (page 2).
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function subscribePageTwoBack(array &$form, FormStateInterface $form_state) {
    $form_state
      // Restore values for the first step.
      ->setValues($form_state->get('page_values'))
      ->set('page_num', 1)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 1, the AJAX-rendered form will still show page 2.
      ->setRebuild(TRUE);
  }

  public function subscribePageThreeBack(array &$form, FormStateInterface $form_state) {
    $form_state
      // Restore values for the first step.
      ->setValues($form_state->get('page_values'))
      ->set('page_num', 2)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 1, the AJAX-rendered form will still show page 2.
      ->setRebuild(TRUE);
  }

  public function getFormPrefix($step){

      switch ($step) {
        case 1:
         return '<div class="my-form-wrapper">
              <ul id="progressbar">
                <li class="active" id="account"><span><strong>Account</strong></span></li>
                <li id="personal"><span><strong>Personal</strong></span></li>
                <li id="confirm"><span><strong>Finish</strong></span></li>
              </ul>
          </div>';
          break;
        case 2:
          return '<div class="my-form-wrapper">
            <ul id="progressbar">
              <li  id="account"><span><strong>Account</strong></span></li>
              <li class="active" id="personal"><span><strong>Personal</strong></span></li>
              <li id="confirm"><span><strong>Finish</strong></span></li>
          </ul>
          </div>';
          break;
        case 3:
          return '<div class="my-form-wrapper">
            <ul id="progressbar">
              <li  id="account"><span><strong>Account</strong></span></li>
              <li id="personal"><span><strong>Personal</strong></span></li>
              <li id="confirm" class="active"><span><strong>Finish</strong></span></li>
          </ul>
          </div>';
          break;
        default:
           return '';


  }
}

}

/*
https://codepen.io/monbrielle/pen/dyYRgPm


<ul id="progressbar">
                        <li class="active" id="account"><strong>Account</strong></li>
                        <li id="personal"><strong>Personal</strong></li>
                        <li id="payment"><strong>Image</strong></li>
                        <li id="confirm"><strong>Finish</strong></li>
                    </ul>

*/