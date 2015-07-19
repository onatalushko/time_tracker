<?php

/**
 * @file
 * Contains \Drupal\menu_ui\MenuListBuilder.
 */

namespace Drupal\time_tracker;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Config\Entity\DraggableListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines a class to build a listing of menu entities.
 *
 * @see \Drupal\system\Entity\Menu
 * @see menu_entity_info()
 */
class TimeTrackerActivityListBuilder extends DraggableListBuilder {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'time_tracker_entity_activity_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = t('Label');
    $header['description'] = array(
      'data' => t('Description'),
      'class' => array(RESPONSIVE_PRIORITY_MEDIUM),
    );
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['title'] = array(
      '#markup' => $this->getLabel($entity),
    );
    $row['description'] = array(
      '#markup' => Xss::filterAdmin($entity->getDescription()),
    );
    return $row + parent::buildRow($entity);
  }

}
