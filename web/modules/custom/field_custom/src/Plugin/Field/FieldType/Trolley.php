<?php

namespace Drupal\field_custom\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;


/**
 * Define the "custom field type".
 *
 * @FieldType(
 *   id = "trolley",
 *   label = @Translation("Trolley Field Type"),
 *   description = @Translation("Desc for Custom Field Type"),
 *   default_widget = "trolley_widget",
 *   default_formatter = "trolley_formatter"
 * )
 */
class Trolley extends FieldItemBase
{

    /**
     * {@inheritdoc}
     */

    public static function schema(FieldStorageDefinitionInterface $field_definition)
    {
        return [
            'columns' => [
                'value' => [
                    'type' => 'varchar',
                    'length' => $field_definition->getSetting("length"),
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function defaultStorageSettings()
    {
        return [
                'length' => 200,
            ] + parent::defaultStorageSettings();
    }

    /**
     * {@inheritdoc}
     */
    public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data)
    {
        $element = [];

        $element['length'] = [
            '#type' => 'number',
            '#title' => t("Length of your text"),
            '#required' => TRUE,
            '#default_value' => $this->getSetting("length"),
        ];
        return $element;
    }

    /**
     * {@inheritdoc}
     */
    public static function defaultFieldSettings()
    {
        return [
                'moreinfo' => "More info default value",
            ] + parent::defaultFieldSettings();
    }

    /**
     * {@inheritdoc}
     */
    public function fieldSettingsForm(array $form, FormStateInterface $form_state)
    {
        $element = [];
        $element['moreinfo'] = [
            '#type' => 'textfield',
            '#title' => 'More information about this field',
            '#required' => TRUE,
            '#default_value' => $this->getSetting("moreinfo"),
        ];
        return $element;
    }

    /**
     * {@inheritdoc}
     */
    public static function PropertyDefinitions(FieldStorageDefinitionInterface $field_definition)
    {
        $properties['value'] = DataDefinition::create('string')->setLabel(t("Name"));

        return $properties;
    }
}