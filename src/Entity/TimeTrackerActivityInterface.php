<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 11.01.15
 * Time: 23:56
 */
namespace Drupal\time_tracker\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;


/**
 * Provides an interface defining a Time tracker activity entity.
 */
interface TimeTrackerActivityInterface extends ConfigEntityInterface {

  /**
   * Returns the comment type description.
   *
   * @return string
   *   The comment-type description.
   */
  public function getDescription();

  /**
   * Sets the description of the comment type.
   *
   * @param string $description
   *   The new description.
   *
   * @return $this
   */
  public function setDescription($description);

}
