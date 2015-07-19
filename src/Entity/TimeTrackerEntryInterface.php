<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 11.01.15
 * Time: 23:55
 */
namespace Drupal\time_tracker\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;


/**
 * Defines the comment entity class.
 *
 * @ContentEntityType(
 *   id = "time_tracker_entry",
 *   label = @Translation("Time tracker entry"),
 *   bundle_label = @Translation("Content type"),
 *   handlers = {
 *     "storage" = "Drupal\time_tracker\TimeTrackerEntryStorage",
 *     "storage_schema" = "Drupal\time_tracker\TimeTrackerEntryStorageSchema",
 *     "access" = "Drupal\time_tracker\TimeTrackerEntryAccessControlHandler",
 *     "view_builder" = "Drupal\time_tracker\TimeTrackerEntryViewBuilder",
 *     "views_data" = "Drupal\time_tracker\TimeTrackerEntryViewsData",
 *     "form" = {
 *       "default" = "Drupal\time_tracker\TimeTrackerEntryForm",
 *       "delete" = "Drupal\time_tracker\Form\TimeTrackerEntryDeleteForm"
 *     },
 *     "translation" = "Drupal\time_tracker\TimeTrackerEntryTranslationHandler"
 *   },
 *   base_table = "time_tracker",
 *   uri_callback = "time_tracker_entry_uri",
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "teid",
 *     "label" = "title",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "entity.time_tracker_entry.canonical",
 *     "delete-form" = "entity.time_tracker_entry.delete_form",
 *     "edit-form" = "entity.time_tracker_entry.edit_form",
 *   },
 *   field_ui_base_route  = "entity.time_tracker_entry.edit_form",
 * )
 */
interface TimeTrackerEntryInterface extends ContentEntityInterface {
  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type);
}