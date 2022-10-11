<?php

namespace Drupal\my_test_core\field_format\random\Plugin\Field\FieldFormatter;

use Drupal\my_test_core\Core\Field\FormatterBase;
use Drupal\my_test_core\Core\Field\FieldItemListInterface;
use Drupal\my_test_core\Core\Form\FormStateInterface;


/**
 * Plugin implementation of the 'text_field' formatter.
 *
 * @FieldFormatter(
 *   id = "text_field",
 *   label = @Translation("SOME CUSTOM TEXT"),
 *   field_types = {
 *     "text_field"
 *   }
 * )
 */
class TextField extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('SOME CUSTOM TEXT');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      // Render each element as markup.
      $element[$delta] = ['#markup' => $item->value];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        // Declare a setting named 'text_length', with
        // a default value of 'short'
        'text_length' => 'short',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['text_length'] = [
      '#title' => $this->t('Text length'),
      '#type' => 'select',
      '#options' => [
        'short' => $this->t('Short'),
        'long' => $this->t('Long'),
      ],
      '#default_value' => $this->getSetting('text_length'),
    ];

    return $form;
  }



}
