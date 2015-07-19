<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 11.01.15
 * Time: 21:47
 */

namespace Drupal\time_tracker\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\comment\CommentTypeInterface;

/**
 * Defines the Time Tracker Activity entity.
 *
 * @ConfigEntityType(
 *   id = "time_tracker_activity",
 *   label = @Translation("Time tracker activity"),
 *   handlers = {
 *     "list_builder" = "Drupal\time_tracker\Controller\TimeTrackerActivityListBuilder",
 *     "form" = {
 *       "add" = "Drupal\time_tracker\Form\TimeTrackerActivityForm",
 *       "edit" = "Drupal\time_tracker\Form\TimeTrackerActivityForm",
 *       "delete" = "Drupal\time_tracker\Form\TimeTrackerActivityDeleteForm"
 *     },
 *   },
 *   config_prefix = "activity",
 *   admin_permission = "administer time tracker",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   links = {
 *     "edit-form" = "entity.time_tracker_activity.edit_form",
 *     "delete-form" = "entity.time_tracker_activity.delete_form"
 *   },
 * )
 */
class TimeTrackerActivity extends ConfigEntityBase implements TimeTrackerActivityInterface {

  /**
   * The ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Description.
   *
   * @var string
   */
  protected $description;


  /**
   * {@inheritdoc}
   * @todo Add to interface.
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   * @todo Add to interface.
   */
  public function setDescription($description) {
    $this->description = $description;
    return $this;
  }

//@todo possible remove
//  /**
//   * The activity weight.
//   *
//   * @var integer
//   */
//  public $weight;
//
//  /**
//   * Is this activity enabled?
//   *
//   * @var integer
//   */
//  public $status;

  // Your specific configuration property get/set methods go here,
  // implementing the interface.

} 
