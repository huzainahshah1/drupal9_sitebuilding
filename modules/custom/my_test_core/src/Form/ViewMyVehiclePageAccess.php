<?php

/**
 * @file
 * Contains Drupal\my_test_core\Form\MessagesForm.
 */

namespace Drupal\my_test_core\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ViewMyVehiclePageAccess extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'ViewMyVehiclePageAccess.adminsettings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ViewMyVehiclePageAccess';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ViewMyVehiclePageAccess.adminsettings');

    $form['ViewMyVehiclePageAccess']['elements']['name']['#title']= [
      t('What roles can see this page')
    ];
    $form['ViewMyVehiclePageAccess']['checkboxes_section'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('roles'),
      '#options' => [
        'vehicle_admin' => 'vehicle admin',
        'vehicle_editor' => 'vehicle editor',

    ]
      ];

    $form['ViewMyVehiclePageAccess']['custom_text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Some custom text'),
      '#description' => $this->t('Enter any content'),
      '#default_value' => $config->get('custom_text'),
    ];

    return parent::buildForm($form, $form_state);

  }


}
