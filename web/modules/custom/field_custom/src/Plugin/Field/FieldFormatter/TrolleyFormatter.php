<?php

namespace Drupal\field_custom\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'field_example_simple_text' formatter.
 *
 * @FieldFormatter(
 *   id = "trolley_formatter",
 *   label = @Translation("Trolley text-based formatter"),
 *   field_types = {
 *     "trolley"
 *   }
 * )
 */

class TrolleyFormatter extends FormatterBase{



    public function viewElements(FieldItemListInterface $items, $langcode)
    {
        $element = [];
        foreach ($items as $delta => $item) {
            // Render each element as markup.
            $element[$delta] = ['#markup' => $item->value];
        }

        return $element;
    }

    public static function defaultSettings()
    {
        return [
                'trolley_formatter' => 'Trolley Formatter Default Settings'
            ] + parent::defaultSettings();
    }

    public function settingsForm(array $form, FormStateInterface $form_state)
    {
        $element['trolley_formatter'] = [
            '#type' => 'textfield',
            '#title' => 'It Trolley Formatter',
            '#default_value' => $this->getSetting('trolley_formatter'),
        ];
        return $element;
    }

//    public function settingsSummary() {
//        $summary = [];
//        $summary[] = $this->t('Displays Trolley Formatter: @trolley_formatter' , ['@trolley_formatter' => $this->getSetting('trolley_formatter')]);
//        return $summary;
//    }


    // it is created summary to field like a description
    public function settingsSummary()
    {
        $summary = [];
        $summary[] = $this->t("Text Trolley Formatter: @trolley_formatter", array("@trolley_formatter" => $this->getSetting('trolley_formatter')));
        return $summary;
    }
}