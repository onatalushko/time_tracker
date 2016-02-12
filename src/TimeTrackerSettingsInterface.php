<?php

/**
 * @file
 * Contains \Drupal\time_tracker\TimeTrackerSettingsInterface.
 */

namespace Drupal\time_tracker;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining language settings for content entities.
 */
interface TimeTrackerSettingsInterface extends ConfigEntityInterface {

  /**
   * Gets the entity type ID this config applies to.
   *
   * @return string
   */
  public function getTargetEntityTypeId();

  /**
   * Gets the bundle this config applies to.
   *
   * @return string
   */
  public function getTargetBundle();

  /**
   * Sets the bundle this config applies to.
   *
   * @param string $target_bundle
   *   The bundle.
   *
   * @return $this
   */
  public function setTargetBundle($target_bundle);

}

