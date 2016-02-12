<?php

/**
 * @file
 * Contains \Drupal\time_tracker\Entity\TimeTrackerSettings.
 */

namespace Drupal\time_tracker\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\time_tracker\TimeTrackerSettingsException;
use Drupal\time_tracker\TimeTrackerSettingsInterface;

/**
 * Defines the ContentLanguageSettings entity.
 *
 * @ConfigEntityType(
 *   id = "time_tracker_settings",
 *   label = @Translation("Time Tracker Settings"),
 *   admin_permission = "administer time tracker",
 *   config_prefix = "time_tracker_settings",
 *   entity_keys = {
 *     "id" = "id"
 *   },
 * )
 */
class TimeTrackerSettings extends ConfigEntityBase implements TimeTrackerSettingsInterface {

  /**
   * The id. Combination of $target_entity_type_id.$target_bundle.
   *
   * @var string
   */
  protected $id;

  /**
   * The entity type ID (machine name).
   *
   * @var string
   */
  protected $target_entity_type_id;

  /**
   * The bundle (machine name).
   *
   * @var string
   */
  protected $target_bundle;

  /**
   * The bundle (machine name).
   *
   * @var string
   */
  protected $active = FALSE;

  /**
   * Constructs a ContentLanguageSettings object.
   *
   * In most cases, Field entities are created via
   * entity_create('field_config', $values), where $values is the same
   * parameter as in this constructor.
   *
   * @param array $values
   *   An array of the referring entity bundle with:
   *   - target_entity_type_id: The entity type.
   *   - target_bundle: The bundle.
   *   Other array elements will be used to set the corresponding properties on
   *   the class; see the class property documentation for details.
   *
   * @see entity_create()
   */
  public function __construct(array $values, $entity_type = 'time_tracker_settings') {
    if (empty($values['target_entity_type_id'])) {
      throw new TimeTrackerSettingsException('Attempt to create time tracker settings without a target_entity_type_id.');
    }
    if (empty($values['target_bundle'])) {
      throw new TimeTrackerSettingsException('Attempt to create time tracker settings without a target_bundle.');
    }
    parent::__construct($values, $entity_type);
  }

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->target_entity_type_id . '.' . $this->target_bundle;
  }

  /**
   * {@inheritdoc}
   */
  public function getTargetEntityTypeId() {
    return $this->target_entity_type_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getTargetBundle() {
    return $this->target_bundle;
  }

  /**
   * {@inheritdoc}
   */
  public function setTargetBundle($target_bundle) {
    $this->target_bundle = $target_bundle;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getActive() {
    return $this->active;
  }

  /**
   * {@inheritdoc}
   */
  public function setActive($status) {
    $this->active = $status;

    return $this;
  }


  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    $this->id = $this->id();
    parent::preSave($storage);
  }

  /**
   * Loads a content language config entity based on the entity type and bundle.
   *
   * @param string $entity_type_id
   *   ID of the entity type.
   * @param string $bundle
   *   Bundle name.
   *
   * @return $this
   *   The content language config entity if one exists. Otherwise, returns
   *   default values.
   */
  public static function loadByEntityTypeBundle($entity_type_id, $bundle) {
    if ($entity_type_id == NULL || $bundle == NULL) {
      return NULL;
    }
    $config = \Drupal::entityTypeManager()->getStorage('time_tracker_settings')->load($entity_type_id . '.' . $bundle);
    if ($config == NULL) {
      $config = TimeTrackerSettings::create(['target_entity_type_id' => $entity_type_id, 'target_bundle' => $bundle]);
    }
    return $config;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    parent::calculateDependencies();

    // Create dependency on the bundle.
    $entity_type = \Drupal::entityTypeManager()->getDefinition($this->target_entity_type_id);
    $bundle_config_dependency = $entity_type->getBundleConfigDependency($this->target_bundle);
    $this->addDependency($bundle_config_dependency['type'], $bundle_config_dependency['name']);

    return $this;
  }

}
