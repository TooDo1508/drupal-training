<?php

namespace Drupal\authentication\Form;

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
class AuthenticationForm extends ConfigFormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'authentication_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $options = NULL)
    {

        // $config
        $config = $this->config('authentication.admin_config');

        //build form


        $form['authentication_options'] = array(
            '#type' => 'radios',
            '#default_value' => $config->get('authentication_options'),
            '#options' => [
                'med_ok' => $this->t('MedOK'),
                'doccheck' => $this->t('DocCheck'),
                'confirmation' => $this->t('Confirmation authentication'),
                'form_based' => $this->t('Form Based Authentication'),
                'no_protection' => $this->t('No protection'),
            ],
        );

        $form['bbs_notification_font_color'] = array(
            '#type' => 'color',
            '#title' => t('Font Color'),
            '#default_value' => $config->get('bbs_notification_font_color'),
        );
        return parent::buildForm($form, $form_state);
    }


    public function validateForm(array &$form, FormStateInterface $form_state)
    {
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {


        $this->config('authentication.admin_config')
            ->set('bbs_notification_font_color', $form_state->getValue('bbs_notification_font_color'))
            ->set('authentication_options',$form_state->getValue('authentication_options'))
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
    protected function getEditableConfigNames()
    {
        return [
            'authentication.admin_config',
        ];
    }

}
