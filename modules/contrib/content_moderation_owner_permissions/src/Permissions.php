<?php

namespace Drupal\content_moderation_owner_permissions;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\workflows\Entity\Workflow;

/**
 * Defines a class for dynamic permissions based on transitions.
 */
class Permissions {

  use StringTranslationTrait;

  /**
   * Returns an array of content moderation own content permissions.
   *
   * @return array
   *   An associative array of permission names and descriptions.
   */
  public function ownerPermissions() {
    $permissions = [];

    /** @var \Drupal\workflows\WorkflowInterface $workflow */
    foreach (Workflow::loadMultipleByType('content_moderation') as $workflow) {
      foreach ($workflow->getTypePlugin()->getTransitions() as $transition) {

        $permissions['use ' . $workflow->id() . ' transition ' . $transition->id() . ' for own content'] = [
          'title' => $this->t('%workflow workflow: Use %transition transition for own content.', [
            '%workflow' => $workflow->label(),
            '%transition' => $transition->label(),
          ]),
        ];
      }
    }

    return $permissions;
  }

}
