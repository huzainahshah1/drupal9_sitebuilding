<?php

namespace Drupal\content_moderation_owner_permissions;

use Drupal\content_moderation\StateTransitionValidation;
use Drupal\content_moderation\StateTransitionValidationInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\workflows\StateInterface;
use Drupal\workflows\Transition;
use Drupal\workflows\WorkflowInterface;

/**
 * Validates whether a certain state transition is allowed.
 */
class OwnerStateTransitionValidation  extends StateTransitionValidation implements StateTransitionValidationInterface {

  /**
   * {@inheritdoc}
   */
  public function getValidTransitions(ContentEntityInterface $entity, AccountInterface $user) {
    $workflow = $this->moderationInfo->getWorkflowForEntity($entity);
    $current_state = $entity->moderation_state->value ? $workflow->getTypePlugin()->getState($entity->moderation_state->value) : $workflow->getTypePlugin()->getInitialState($entity);
    $valid_transitions = [];

    // Check if entity is owned by user and user has required permissions.
    if ($entity->hasField('uid') && $entity->get('uid')->entity->id() == $user->id()) {
      $valid_transitions = array_filter($current_state->getTransitions(), function (Transition $transition) use ($workflow, $user) {
        return $user->hasPermission('use ' . $workflow->id() . ' transition ' . $transition->id() . ' for own content');
      });
    }

    return array_merge($valid_transitions, parent::getValidTransitions($entity, $user));
  }

  /**
   * {@inheritdoc}
   */
  public function isTransitionValid(WorkflowInterface $workflow, StateInterface $original_state, StateInterface $new_state, AccountInterface $user, ContentEntityInterface $entity = NULL) {
    $transition = $workflow->getTypePlugin()->getTransitionFromStateToState($original_state->id(), $new_state->id());

    if ($user->hasPermission('use ' . $workflow->id() . ' transition ' . $transition->id() . ' for own content')) {
      return $user->hasPermission('use ' . $workflow->id() . ' transition ' . $transition->id() . ' for own content');
    }

    return parent::isTransitionValid($workflow, $original_state, $new_state, $user,  $entity);
  }

}
