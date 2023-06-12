<?php

namespace Drupal\random_quote\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * SendToDestinationsForm class.
 */
class QuoteForm extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'quote_form';
    }

    protected $database;

    public function __construct(Connection $database) {
        $this->database = $database;
    }

    /**
     * {@inheritdoc}
     */

    /**
     * @param ContainerInterface $container
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('database')
        );
    }

    public function buildForm(array $form, FormStateInterface $form_state, $options = NULL)
    {

        //build form
        $form['content'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Content'),
        ];

        $form['author'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Author'),
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => t('Save'),
        ];

        return $form;
    }



    public function validateForm(array &$form, FormStateInterface $form_state)
    {

    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $author = $form_state->getValue('author');
        $content = $form_state->getValue('content');

        $query = $this->database->insert('quote_process');
        $query->fields([
            'content',
            'author',
        ]);
        $query->values([$content, $author]);
        $query->execute();

    }


}
