<?php

namespace Drupal\pfizer_question\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Component\Serialization\Json;


class SendMailForm extends FormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'send_mail_from';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {

        $form['title'] = [
            '#type' => 'item',
            '#title' => $this->t('<h2>Just so you know...<h2>'),
        ];

        $form['description'] = [
            '#type' => 'item',
            '#title' => $this->t('Your email will not be used for any purpose other than for your own use and to whom you send the summary.
Your responses are not saved'),
        ];

        $form['actions'] = [
            '#type' => 'actions',
        ];

        $form['actions']['sendmail'] = [
            '#type' => 'submit',
            '#button_type' => 'primary',
            '#value' => $this->t('Send via Email'),
            // Custom submission handler for page 1.
            // skip validation
        ];

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // TODO: Implement submitForm() method.
    }
}