<?php

namespace Drupal\social_page\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\random_quote\RandomQuote;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Hello' Block.
 *
 * @Block(
 *   id = "social_page",
 *   admin_label = @Translation("Social Page Block"),
 *   category = @Translation("Social Page Block"),
 * )
 */
class SocialPageBlock extends BlockBase implements ContainerFactoryPluginInterface
{
    /**
     * The config factory service.
     *
     * @var \Drupal\random_quote\RandomQuote
     */
    protected $randomquote;


    /**
     * @param array $configuration
     * @param string $plugin_id
     * @param mixed $plugin_definition
     */

    /**
     * The config factory service.
     *
     * @var \Drupal\Core\Database
     */

    protected $database;

    public function __construct(array $configuration, $plugin_id, $plugin_definition, RandomQuote $random_quote, Connection $database)
    {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->randomquote = $random_quote;
        $this->database = $database;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param array $configuration
     * @param string $plugin_id
     * @param mixed $plugin_definition
     *
     * @return static
     */

    public function defaultConfiguration()
    {
        return [
            'quote_api' => $this->t(''),
        ];
    }

    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
    {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('random_quote.service_quote'),
            $container->get('database'),
        );
    }

    public function build()
    {
        $config = $this->getConfiguration();
        $method = 'GET';
        $url = $config['url_api'];
        $bool = false;
        $random_quote_render = $this->randomquote;
        $data_api = $random_quote_render->callAPI($method, $url, $bool);
        $data_convert = json_decode($data_api, true);
        $data_item = $data_convert['data'];
        foreach ($data_item as $item){
            $result = $item['id'];
        }
        $result = $data_convert['data'];


        return [
            '#markup' => 'ABDEFC',
//            '#theme' => 'render_social',
//            '#items' => $result,
        ];
    }

    public function blockForm($form, FormStateInterface $form_state)
    {
        $form['chose_api'] = array(
            '#type' => 'radios',
            '#default_value' => $this->configuration['chose_api'],
            '#options' => [
                'instagram' => $this->t('Instagram'),
                'facebook' => $this->t('Facebook'),
            ],
            '#attributes' => [
                'id' => 'field_colour_select',
            ],
        );

        $form['url_api'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Url'),
            '#default_value' => $this->configuration['url_api'],
            '#states' => [
                'visible' => [
                    ':input[id="field_colour_select"]' => ['value' => 'facebook'],
                ],
            ],
        ];


        return $form;
    }

    public function blockValidate($form, FormStateInterface $form_state)
    {
    }

    public function blockSubmit($form, FormStateInterface $form_state)
    {
        $values = $form_state->getValues();
        $this->configuration['chose_api'] = $values['chose_api'];
        $this->configuration['url_api'] = $values['url_api'];
    }

}