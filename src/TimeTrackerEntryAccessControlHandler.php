<?php
/**
 * @file
 * Contains \Drupal\time_tracker\TimeTrackerEntryAccessControlHandler
 */

namespace Drupal\time_tracker;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityAccessControlHandlerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Class TimeTrackerEntryAccessControlHandler
 * @package Drupal\time_tracker
 */
class TimeTrackerEntryAccessControlHandler extends EntityAccessControlHandler implements EntityAccessControlHandlerInterface {

  /**
   * {@inheritdoc}
   *
   * Link the activities to the permissions. checkAccess is called with the
   * $operation as defined in the routing.yml file.
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view time tracker entry entity');

      case 'edit':
        return AccessResult::allowedIfHasPermission($account, 'edit time tracker entry entity');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete time tracker entry entity');
    }
    return AccessResult::forbidden();
  }

  /**
   * {@inheritdoc}
   *
   * Separate from the checkAccess because the entity does not yet exist, it
   * will be created during the 'add' process.
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add contact entity');
  }
} 