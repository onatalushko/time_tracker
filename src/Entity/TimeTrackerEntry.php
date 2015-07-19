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
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   bundle_entity_type = "time_tracker_activity",
 *   field_ui_base_route  = "entity.time_tracker_activity.add_form",
 * )
 */
class TimeTrackerEntry extends ContentEntityBase implements TimeTrackerEntryInterface {
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

    $fields['entity_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Entity type'))
      ->setDescription(t('The entity type to which this entry is attached.'))
      ->setSetting('max_length', EntityTypeInterface::ID_MAX_LENGTH);

//@todo possible remove
//    $property['entity_bundle'] = array(
//      'label' => t('Entity Bundle'),
//      'description' => t('The attached entity\'s bundle.'),
//      'type' => 'text',
//      'setter callback' => 'entity_property_verbatim_set',
//      'schema field' => 'entity_bundle',
//    );

    $fields['entity_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Entity ID'))
      ->setDescription(t('The ID of the entity of which this entry is a attached.'))
      ->setRequired(TRUE);

//@todo possible remove
//    $property['comment_id'] = array(
//      'label' => t('Comment ID'),
//      'description' => t('The attached comment\'s id.'),
//      'type' => 'integer',
//      'setter callback' => 'entity_property_verbatim_set',
//      'schema field' => 'comment_id',
//    );

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User ID'))
      ->setDescription(t('The user ID of the entry author.'))
      ->setTranslatable(TRUE)
      ->setSetting('target_type', 'user')
      ->setDefaultValue(0);

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Label'))
      ->setDescription(t('The title.'))
      ->setRequired(TRUE)
      ->setDefaultValue('')
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -5,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['activity'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Activity ID'))
      ->setDescription(t('The activity ID for this entry.'))
      ->setTranslatable(TRUE)
      ->setSetting('target_type', 'time_tracker_activity') //@todo check entity type name
      ->setDefaultValue(0);

    $fields['timestamp'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Timestamp'))
      ->setDescription(t('The timestamp recorded for this entry.'));

    $fields['start'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Start Timestamp'))
      ->setDescription(t('The start timestamp for this entry.'));

    $fields['end'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('End Timestamp'))
      ->setDescription(t('The end timestamp for this entry.'));

    $fields['deductions'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Deductions'))
      ->setDescription(t('Deductions to add to this entry.'))
      ->setSetting('unsigned', TRUE);

    $fields['duration'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Duration'))
      ->setDescription(t('Duration of this entry.'))
      ->setSetting('unsigned', TRUE);

    $fields['note'] = BaseFieldDefinition::create('text')
      ->setLabel(t('Note'))
      ->setDescription(t('Note attached to the entry.'));

    $fields['locked'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Locked'))
      ->setDescription(t('Is this entry locked?'))
      ->setTranslatable(TRUE)
      ->setDefaultValue(TRUE);

    $fields['billable'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Billable'))
      ->setDescription(t('Is this entry billable?'))
      ->setTranslatable(TRUE)
      ->setDefaultValue(TRUE);

    $fields['billed'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Billed'))
      ->setDescription(t('Has this entry been billed?'))
      ->setTranslatable(TRUE)
      ->setDefaultValue(TRUE);

    return $fields;
  }
}