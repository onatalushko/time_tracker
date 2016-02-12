<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 11.01.15
 * Time: 21:46
 */

namespace Drupal\time_tracker\Entity;

use Drupal\Component\Utility\Number;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\user\UserInterface;

/**
 * Defines the Time Tracker Entry entity class.
 *
 * @ContentEntityType(
 *   id = "time_tracker_entry",
 *   label = @Translation("Time tracker entry"),
 *   bundle_label = @Translation("Time tracker entry bundle"),
 *   base_table = "time_tracker_entry",
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\time_tracker\TimeTrackerEntryListBuilder",
 *     "form" = {
 *       "add" = "Drupal\time_tracker\Form\TimeTrackerEntryForm",
 *       "edit" = "Drupal\time_tracker\Form\TimeTrackerEntryForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *     "access" = "Drupal\time_tracker\TimeTrackerEntryAccessControlHandler",
 *   },
 *   links = {
 *     "canonical" = "/time_entry/{time_tracker_entry}/edit",
 *     "edit-form" = "/time_entry/{time_tracker_entry}/edit",
 *     "delete-form" = "/time_entry/{time_tracker_entry}/delete",
 *     "collection" = "/time_entries"
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid"
 *   },
 *   bundle_entity_type = "time_tracker_activity",
 *   field_ui_base_route  = "entity.time_tracker_entry.admin_form",
 * )
 */
class TimeTrackerEntry extends ContentEntityBase implements TimeTrackerEntryInterface {

  /**
   * {@inheritdoc}
   *
   * When a new entity instance is added, set the user_id entity reference to
   * the current user as the creator of the instance.
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'uid' => \Drupal::currentUser()->id(),
      'timestamp' => REQUEST_TIME,
    );
  }


  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }


  /**
   * {@inheritdoc}
   */
  public function getChangedTime() {
    return $this->get('changed')->value;
  }


  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }


  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }


  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }


  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields['id'] = BaseFieldDefinition::create('integer')
        ->setLabel(t('Entry ID'))
        ->setDescription(t('The primary identifier for an entry.'))
        ->setReadOnly(TRUE)
        ->setSetting('unsigned', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('Entry UUID.'))
      ->setReadOnly(TRUE);

//@todo seems deprecated possible remove
    $fields['entity_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Entity type'))
      ->setDescription(t('The entity type to which this entry is attached.'))
      ->setSetting('max_length', EntityTypeInterface::ID_MAX_LENGTH);

    $fields['entity_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Entity ID'))
      ->setDescription(t('The ID of the entity of which this entry is a attached.'))
      ->setRequired(TRUE)
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User ID'))
      ->setDescription(t('The user ID of the entry author.'))
      ->setTranslatable(TRUE)
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback('Drupal\time_tracker\Entity\TimeTrackerEntry::getCurrentUserId')
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['activity'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Activity ID'))
      ->setDescription(t('The activity ID for this entry.'))
      ->setTranslatable(TRUE)
      ->setSetting('target_type', 'time_tracker_activity')
      ->setRequired(TRUE)
      ->setDefaultValue(0)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ),
      ));

    $fields['timestamp'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Timestamp'))
      ->setDescription(t('The timestamp recorded for this entry.'))
      ->setDefaultValue(REQUEST_TIME)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'hidden',
        'weight' => 5,
      ));

    $fields['start'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Start Timestamp'))
      ->setDescription(t('The start timestamp for this entry.'))
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'datetime_timestamp',
        'weight' => 5,
        'settings' => array(
          'size' => '60',
          'placeholder' => '',
        ),
      ));

    $fields['end'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('End Timestamp'))
      ->setDescription(t('The end timestamp for this entry.'))
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'datetime_timestamp',
        'weight' => 5,
        'settings' => array(
          'size' => '60',
          'placeholder' => '',
        ),
      ));

    $fields['deductions'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Deductions'))
      ->setDescription(t('Deductions to add to this entry.'))
      ->setSetting('unsigned', TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'datetime_timestamp',
        'weight' => 5,
        'settings' => array(
          'size' => '60',
          'placeholder' => '',
        ),
      ));

    $fields['duration'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Duration'))
      ->setDescription(t('Duration of this entry.'))
      ->setSetting('unsigned', TRUE);

    $fields['note'] = BaseFieldDefinition::create('text')
      ->setLabel(t('Note'))
      ->setDescription(t('Note attached to the entry.'))
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textarea',
        'weight' => 5,
        'settings' => array(
          'rows' => 5,
          'placeholder' => '',
        ),
      ));

    $fields['locked'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Locked'))
      ->setDescription(t('Is this entry locked?'))
      ->setTranslatable(TRUE)
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'boolean',
        'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'boolean_checkbox',
        'weight' => 5,
        'settings' => array(
          'display_label' => TRUE,
        ),
      ));

    $fields['billable'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Billable'))
      ->setDescription(t('Is this entry billable?'))
      ->setTranslatable(TRUE)
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'boolean',
        'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'boolean_checkbox',
        'weight' => 5,
        'settings' => array(
          'display_label' => TRUE,
        ),
      ));

    $fields['billed'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Billed'))
      ->setDescription(t('Has this entry been billed?'))
      ->setTranslatable(TRUE)
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'boolean',
        'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'boolean_checkbox',
        'weight' => 5,
        'settings' => array(
          'display_label' => TRUE,
        ),
      ));

    return $fields;
  }

  public static function getCurrentUserId() {
    return array(\Drupal::currentUser()->id());
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    $referenced_entity = $this->get('entity_id')->entity;
    return $referenced_entity->label();
  }

  /**
   * {@inheritdoc}
   */
  public function toUrl($rel = 'canonical', array $options = []) {
    if ($rel == 'canonical') {
      $referenced_entity = $this->get('entity_id')->entity;
      return $referenced_entity->toUrl($rel, $options);
    }
    return parent::toUrl($rel, $options);
  }

}