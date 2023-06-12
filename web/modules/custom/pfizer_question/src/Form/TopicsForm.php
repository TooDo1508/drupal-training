<?php

namespace Drupal\pfizer_question\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Component\Serialization\Json;
use Drupal\pfizer_question\Controller\PfizerQuestionController;
use Drupal\printable\PrintableFormatPluginManager;
use Mpdf\Mpdf;
use Dompdf\Dompdf;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class TopicsForm extends FormBase
{

    /**
     * The config factory service.
     *
     * @var \Drupal\pfizer_question\Controller\PfizerQuestionController
     */
    protected $pfizerQuestionController;

    public function __construct(PfizerQuestionController $pfizer_question)
    {
        $this->pfizerQuestionController = $pfizer_question;
    }

    /**
     * {@inheritdoc}
     */

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('pfizer_question.render_pdf'),
        );
    }

    public function getFormId()
    {
        return 'form_topics';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {

        $form['title'] = [
            '#type' => 'item',
            '#title' => $this->t('<h2>Additional Topics to Consider:<h2>'),
        ];

        $form['description'] = [
            '#type' => 'item',
            '#title' => $this->t('Write in your answers in the space below after printing your summary'),
        ];

        $form['topic_1'] = [
            '#type' => 'textarea',
            '#title' => $this->t('<b>1.  What does managing your UC look like to you? Is there anything you
would like to be able to do more of, but can’t, due to your symptoms?</b>'),
            '#default_value' => "",
        ];

        $form['topic_2'] = [
            '#type' => 'textarea',
            '#title' => $this->t('<b>2. What are your priorities for your next visit?</b>'),
            '#default_value' => "",
        ];

        $form['topic_3'] = [
            '#type' => 'textarea',
            '#title' => $this->t('<b>3. Are there any other questions or topics you want to address with your
doctor? This is the time to bring up anything that’s been on your mind.</b>'),
            '#default_value' => "",
        ];

        $form['topic_4'] = [
            '#type' => 'item',
            '#title' => $this->t('<b>You can print this page and show it to your doctor. If you prefer, you can email it to your doctor</b>'),
            '#default_value' => "",
        ];

        $form['actions'] = [
            '#type' => 'actions',
        ];


        $form['actions']['download'] = [
            '#type' => 'submit',
            '#button_type' => 'primary',
            '#value' => $this->t('Download PDF'),
            // Custom submission handler for page 1.
            // skip validation
        ];

        $form['actions']['sendmail'] = [
            '#type' => 'link',
            '#title' => t('Send via Email'),
            '#url' => Url::fromRoute('send_mail_form.path'),
            '#attributes' => [
                'class' => ['use-ajax', 'button'],
                'data-dialog-type' => 'modal',
                'data-dialog-options' => Json::encode([
                    'width' => 700,
                ])
            ]
        ];

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $url = \Drupal\Core\Url::fromRoute('pfizer_question.summary_pdf');
//          ->setRouteParameters($page_values);
        $form_state->setRedirectUrl($url);

//        return $render;

        }

}