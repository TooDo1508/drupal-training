<?php

namespace Drupal\pfizer_question\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Component\Serialization\Json;
use Drupal\Tests\system\Functional\Bootstrap\DrupalMessengerServiceTest;

class QuestionForm extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'form_pfizer_question';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        if ($form_state->has('page_num') && $form_state->get('page_num') == 2) {
            //return $this->fapiExamplePageTwo($form, $form_state);
            return $this->subscribePageTwo($form, $form_state);
        }

        if ($form_state->has('page_num') && $form_state->get('page_num') == 3) {
            return $this->subscribePageThree($form, $form_state);
        }

        $form_state->set('page_values', []);
        $form_state->set('page_num', 1);

        $form['#attached']['library'][] = 'pfizer_question/pfizer_question';

        $form['title'] = [
            '#type' => 'item',
            '#title' => $this->t('Do you think you are currently flaring?'),
        ];

        $form['description'] = [
            '#type' => 'item',
            '#title' => $this->t('A flare is when your ulcerative colitis symptoms come back after a period of time when you ve been feeling well. These symptoms may include diarrhea abdominal pain, cramping, urgency, rectal pain and rectal bleeding)'),
        ];

        $form['question_1'] = array(
            '#type' => 'radios',
            '#default_value' => $form_state->getValue('question_1', ''),
            '#options' => [
                'Yes, Im having symptoms' => $this->t('Yes, Im having symptoms'),
                'No, but I just had one' => $this->t('No, but I just had one'),
                'No, I havent had one in a while' => $this->t('No, I havent had one in a while'),
                'Unsure / Other' => $this->t('Unsure / Other'),
            ],
        );

        // Group submit handlers in an actions element with a key of "actions" so
        // that it gets styled correctly, and so that other modules may add actions
        // to the form. This is not required, but is convention.
        $form['actions'] = [
            '#type' => 'actions',
        ];

        $form['action']['next'] = [
            '#type' => 'submit',
            '#button_type' => 'primary',
            '#value' => $this->t('Next'),
            // Custom submission handler for page 1.
            '#submit' => ['::subscribePageNext'],
            '#validate' => ['::fapiExampleMultistepFormNextValidate'],

        ];

        $form['actions']['back'] = [
            '#type' => 'submit',
            '#button_type' => 'primary',
            '#value' => $this->t('Back'),
            // Custom submission handler for page 1.
            // skip validation
            '#submit' => ['::BackFirstPage'],
        ];
        //skip button
        $form['actions']['skip'] = [
            '#type' => 'submit',
            '#button_type' => 'primary',
            '#value' => $this->t('Skip'),
            // Custom submission handler for page 1.
            // skip validation
            '#limit_validation_errors' => array(),
            '#submit' => ['::skipPage'],
            '#attributes' => array('onclick' => 'if(!confirm("Are you really want to skip?")){return false;}')
        ];

        $form['back'] = [
            '#type' => 'link',
            '#title' => t('Prefer not to answer'),
            '#url' => Url::fromRoute('send_mail_form.path'),
            '#attributes' => [
                'class' => ['use-ajax'],
                'data-dialog-type' => 'modal',
                'data-dialog-options' => Json::encode([
                    'width' => 700,
                ])
            ]
        ];

        $form['#prefix'] = $this->getFormPrefix(1);
        return $form;
    }


    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {

        $page_values = $form_state->get('page_values');

        $this->messenger()->addMessage($this->t('The form has been submitted!'));


        $tempstore = \Drupal::service('tempstore.private');
        $store = $tempstore->get('form_pfizer_question ');
        $store->set('page_values', $page_values);

        $url = \Drupal\Core\Url::fromRoute('pfizer_question.summary');
//          ->setRouteParameters($page_values);
        $form_state->setRedirectUrl($url);

    }

    /**
     * Provides custom validation handler for page 1.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     */
    public function fapiExampleMultistepFormNextValidate(array &$form, FormStateInterface $form_state)
    {
        $page_num = $form_state->get('page_num');
//
//        switch ($page_num) {
//            case 1:
        if ($form_state->getValue('question_1') === '') {
            $form_state->setErrorByName('question_1', 'Please make you selection!');
        }
//            case 1:
        if ($form_state->getValue('question_2') === '') {
            $form_state->setErrorByName('question_2', 'Please make you selection!');
        }
//        }
    }

    public function BackFirstPage(array &$form, FormStateInterface $form_state)
    {
        $this->messenger()->addMessage($this->t('Have nothing to Back'));
    }

    /**
     * Provides custom submission handler for page 1.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     */
    public function subscribeFirstNextSubmit(array &$form, FormStateInterface $form_state)
    {
        $form_state
            ->set('page_values', [
                // Keep only first step values to minimize stored data.
                'question_1' => $form_state->getValue('question_1'),
            ])
            ->set('page_num', 2)
            // Since we have logic in our buildForm() method, we have to tell the form
            // builder to rebuild the form. Otherwise, even though we set 'page_num'
            // to 2, the AJAX-rendered form will still show page 1.
            ->setRebuild(TRUE);
    }

    public function skipPage(array &$form, FormStateInterface $form_state)
    {
        $page_num = $form_state->get('page_num');
        switch ($page_num) {
            case 1:
                if ($form_state->getValue('question_1') == NULL) {
                    $form_state->setValue('question_1', "");
                }
                $form_state->set('page_num', $page_num + 1)
                    ->setRebuild(TRUE);
            case 2:
                if ($form_state->getValue('question_2') == NULL) {
                    $form_state->setValue('question_2', "");
                }
                $form_state->set('page_num', $page_num + 1)
                    ->setRebuild(TRUE);
        }

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
    public function subscribePageTwo(array &$form, FormStateInterface $form_state)
    {

        $form['#attached']['library'][] = 'dn_subscribe/dn_subscribe';

        $form['title'] = [
            '#type' => 'item',
            '#title' => $this->t('How often do you have blood in your stool?'),
        ];

        $form['description'] = [
            '#type' => 'item',
            '#title' => $this->t('Select one'),
        ];

        $form['question_2'] = array(
            '#type' => 'radios',
            '#default_value' => $form_state->getValue('question_2', ''),
            '#options' => [
                'Monthly' => $this->t('Monthly'),
                'Weekly' => $this->t('Weekly'),
                'Daily' => $this->t('Daily'),
                'Never' => $this->t('Never'),
            ],
        );


        $form['back'] = [
            '#type' => 'submit',
            '#value' => $this->t('Back'),
            // Custom submission handler for 'Back' button.
            '#submit' => ['::subscribePageBack'],
            // We won't bother validating the required 'color' field, since they
            // have to come back to this page to submit anyway.
            '#limit_validation_errors' => [],
        ];

        $form['actions']['skip'] = [
            '#type' => 'submit',
            '#button_type' => 'primary',
            '#value' => $this->t('Skip'),
            // skip validation
            '#limit_validation_errors' => array(),
            '#submit' => ['::skipPage'],
            '#attributes' => array('onclick' => 'if(!confirm("Are you really want to skip?")){return false;}'),
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#button_type' => 'primary',
            '#value' => $this->t('Next'),
            '#submit' => ['::subscribePageNext'],
            '#validate' => ['::fapiExampleMultistepFormNextValidate'],

        ];
        $form['#prefix'] = $this->getFormPrefix(2);


        return $form;
    }

    public function subscribeSecondNextSubmit(array &$form, FormStateInterface $form_state)
    {
        $step = $form_state->get('page_values');
        $form_state
            ->set('page_values', [
                // Keep only first step values to minimize stored data.
//                'address' => $form_state->getValue('address'),
//                'city' => $form_state->getValue('city'),
                'question_2' => $form_state->getValue('question_2'),
                'question_1' => $step['question_1'],

            ])
            ->set('page_num', 3)
            // Since we have logic in our buildForm() method, we have to tell the form
            // builder to rebuild the form. Otherwise, even though we set 'page_num'
            // to 2, the AJAX-rendered form will still show page 1.
            ->setRebuild(TRUE);
    }

    public function subscribePageFour(array &$form, FormStateInterface $form_state)
    {

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
            '#submit' => ['::subscribePageBack'],
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

    public function subscribePageThree(array &$form, FormStateInterface $form_state)
    {

        $form['#attached']['library'][] = 'dn_subscribe/dn_subscribe';

        $form['title_question_3'] = [
            '#type' => 'item',
            '#title' => $this->t('<h3>Have you had any new UC symptoms within the past 2 weeks?</h3>'),
        ];

        $form['description_question_3'] = [
            '#type' => 'item',
            '#title' => $this->t('Select one.'),
        ];

        $form['question_3'] = array(
            '#type' => 'radios',
            '#default_value' => $form_state->getValue('question_1', ''),
            '#options' => [
                'Yes' => $this->t('Yes'),
                'No' => $this->t('No'),
            ],
            '#validate' => ['::fapiExampleMultistepFormNextValidate'],
            // '#required' => TRUE,
        );

        $form['back'] = [
            '#type' => 'submit',
            '#value' => $this->t('Back'),
            // Custom submission handler for 'Back' button.
            '#submit' => ['::subscribePageBack'],
            // We won't bother validating the required 'color' field, since they
            // have to come back to this page to submit anyway.
            '#limit_validation_errors' => [],
        ];

        $form['actions']['skip'] = [
            '#type' => 'submit',
            '#button_type' => 'primary',
            '#value' => $this->t('Skip'),
            // Custom submission handler for page 1.
            // skip validation
            '#limit_validation_errors' => array(),
            '#submit' => ['::skipPage'],
            '#attributes' => array('onclick' => 'if(!confirm("Are you really want to skip?")){return false;}')
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

    public function subscribePageNext(array &$form, FormStateInterface $form_state)
    {
        $page_num = $form_state->get('page_num');
        switch ($page_num) {
            case 1:
                $form_state->set('page_values', [
                    'question_1' => $form_state->getValue('question_1'),
                ])
                    ->set('page_num', $page_num + 1);
            case 2:
                $page_values = $form_state->get('page_values');
                $form_state->set('page_values', [
                    'question_2' => $form_state->getValue('question_2'),
                    'question_1' => $page_values['question_1'],
                ])
                    ->set('page_num', $page_num + 1);
        }
        $form_state->setRebuild();
    }

    public function subscribePageBack(array &$form, FormStateInterface $form_state)
    {

        $form_state
            // Restore values for the first step.
            ->setValues($form_state->get('page_values'))
            ->set('page_num', $form_state->get('page_num') - 1)
            // Since we have logic in our buildForm() method, we have to tell the form
            // builder to rebuild the form. Otherwise, even though we set 'page_num'
            // to 1, the AJAX-rendered form will still show page 2.
            ->setRebuild(TRUE);
    }

    public function getFormPrefix($step)
    {

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