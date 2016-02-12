<?php

/**
 * @file
 * Contains \Drupal\menu_ui\MenuListBuilder.
 */

namespace Drupal\time_tracker;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of menu entities.
 *
 * @see \Drupal\system\Entity\Menu
 * @see menu_entity_info()
 */
class TimeTrackerEntryListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = t('Logged in');
//    $header['description'] = array(
//      'data' => t('Description'),
//      'class' => array(RESPONSIVE_PRIORITY_MEDIUM),
//    );
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = array(
      'data' => $entity->toLink(),
    );

//    $row['description'] = array(
//      '#markup' => Xss::filterAdmin($entity->getDescription()),
//    );
    return $row + parent::buildRow($entity);
  }

}
