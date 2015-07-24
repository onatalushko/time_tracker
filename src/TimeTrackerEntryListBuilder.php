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
    $header['label'] = t('Label');
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
    $url = Url::fromRoute('entity.time_tracker_entry.canonical', array('time_tracker_entry' => $entity));
    $row['label'] = array(
      'data' => $entity->link(),
    );

//    $row['description'] = array(
//      '#markup' => Xss::filterAdmin($entity->getDescription()),
//    );
    return $row + parent::buildRow($entity);
  }

}
