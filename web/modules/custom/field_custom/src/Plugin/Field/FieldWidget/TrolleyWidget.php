<?php


namespace Drupal\field_custom\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 *
 * @FieldWidget(
 *   id = "trolley_widget",
 *   label = @Translation("Trolley Widget"),
 *   field_types = {
 *     "trolley",
 *   }
 * )
 */
class TrolleyWidget extends WidgetBase
{
    /**
     * {@inheritdoc}
     */
    public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
    {
        $values = isset($items[$delta]->value) ? $items[$delta]->value : "";
        $element += [
            '#type' => 'textfield',
            '#suffix' => "<span>" . $this->getFieldSetting('moreinfo') . "</span>",
            '#default_value' => $values
        ];
        return ['value' => $element];
    }

    public static function defaultSettings()
    {
        return [
                'trolley_widget' => 'default'
            ] + parent::defaultSettings();
    }

    //it is created form in setting manage display form field
    public function settingsForm(array $form, FormStateInterface $form_state)
    {
        $element['trolley_widget'] = [
            '#type' => 'textfield',
            '#title' => 'Placeholder Text',
            '#default_value' => $this->getSetting('trolley_widget'),
        ];
        return $element;
    }

    // it is created summary to field like a description
    public function settingsSummary()
    {
        $summary = [];
        $summary[] = $this->t("Text Trolley Widget: @trolley_widget", array("@trolley_widget" => $this->getSetting('trolley_widget')));
        return $summary;
    }

}