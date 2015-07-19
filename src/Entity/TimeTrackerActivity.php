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
 *     "list_builder" = "Drupal\time_tracker\TimeTrackerActivityListBuilder",
 *     "form" = {
 *       "add" = "Drupal\time_tracker\Form\TimeTrackerActivityForm",
 *       "edit" = "Drupal\time_tracker\Form\TimeTrackerActivityForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *   },
 *   config_prefix = "activity",
 *   admin_permission = "administer time tracker",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "weight" = "weight",
 *   },
 *   links = {
 *     "add-form" = "/admin/structure/time_tracker_activity/add",
 *     "edit-form" = "/admin/structure/time_tracker_activity/manage/{time_tracker_activity}/edit",
 *     "delete-form" = "/admin/structure/time_tracker_activity/manage/{time_tracker_activity}/delete",
 *     "collection" = "/admin/structure/time_tracker_activity",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "weight",
 *   }
 * )
 */
class TimeTrackerActivity extends ConfigEntityBase implements TimeTrackerActivityInterface {

  /**
   * The machine name of the activity.
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
   * The description.
   *
   * @var string
   */
  protected $description;

  /**
   * The activity weight.
   *
   * @var integer
   */
  public $weight;

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->description = $description;
    return $this;
  }

}
