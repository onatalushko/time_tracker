<?php

/**
 * @file
 * Contains \Drupal\time_tracker\Form.
 */

namespace Drupal\time_tracker\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\language\Entity\ContentLanguageSettings;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Base form controller for category edit forms.
 */
class TimeTrackerEntryForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {

//    $fields['entity_type'] = BaseFieldDefinition::create('string')
//      ->setLabel(t('Entity type'))
//      ->setDescription(t('The entity type to which this entry is attached.'))
//      ->setSetting('max_length', EntityTypeInterface::ID_MAX_LENGTH);
//
////@todo possible remove
////    $property['entity_bundle'] = array(
////      'label' => t('Entity Bundle'),
////      'description' => t('The attached entity\'s bundle.'),
////      'type' => 'text',
////      'setter callback' => 'entity_property_verbatim_set',
////      'schema field' => 'entity_bundle',
////    );
//
//    $fields['entity_id'] = BaseFieldDefinition::create('entity_reference')
//      ->setLabel(t('Entity ID'))
//      ->setDescription(t('The ID of the entity of which this entry is a attached.'))
//      ->setRequired(TRUE);
//
////@todo possible remove
////    $property['comment_id'] = array(
////      'label' => t('Comment ID'),
////      'description' => t('The attached comment\'s id.'),
////      'type' => 'integer',
////      'setter callback' => 'entity_property_verbatim_set',
////      'schema field' => 'comment_id',
////    );
//
//    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
//      ->setLabel(t('User ID'))
//      ->setDescription(t('The user ID of the entry author.'))
//      ->setTranslatable(TRUE)
//      ->setSetting('target_type', 'user')
//      ->setDefaultValue(0);
//
//    $fields['label'] = BaseFieldDefinition::create('string')
//      ->setLabel(t('Label'))
//      ->setDescription(t('The title.'))
//      ->setRequired(TRUE)
//      ->setDefaultValue('')
//      ->setSetting('max_length', 255)
//      ->setDisplayOptions('view', array(
//        'label' => 'hidden',
//        'type' => 'string',
//        'weight' => -5,
//      ))
//      ->setDisplayOptions('form', array(
//        'type' => 'string_textfield',
//        'weight' => -5,
//      ))
//      ->setDisplayConfigurable('form', TRUE);
//
//    $fields['activity'] = BaseFieldDefinition::create('entity_reference')
//      ->setLabel(t('Activity ID'))
//      ->setDescription(t('The activity ID for this entry.'))
//      ->setTranslatable(TRUE)
//      ->setSetting('target_type', 'time_tracker_activity')
//      ->setDefaultValue(0);
//
//    $fields['timestamp'] = BaseFieldDefinition::create('timestamp')
//      ->setLabel(t('Timestamp'))
//      ->setDescription(t('The timestamp recorded for this entry.'));
//
//    $fields['start'] = BaseFieldDefinition::create('timestamp')
//      ->setLabel(t('Start Timestamp'))
//      ->setDescription(t('The start timestamp for this entry.'));
//
//    $fields['end'] = BaseFieldDefinition::create('timestamp')
//      ->setLabel(t('End Timestamp'))
//      ->setDescription(t('The end timestamp for this entry.'));
//
//    $fields['deductions'] = BaseFieldDefinition::create('integer')
//      ->setLabel(t('Deductions'))
//      ->setDescription(t('Deductions to add to this entry.'))
//      ->setSetting('unsigned', TRUE);
//
//    $fields['duration'] = BaseFieldDefinition::create('integer')
//      ->setLabel(t('Duration'))
//      ->setDescription(t('Duration of this entry.'))
//      ->setSetting('unsigned', TRUE);
//
//    $fields['note'] = BaseFieldDefinition::create('text')
//      ->setLabel(t('Note'))
//      ->setDescription(t('Note attached to the entry.'));
//
//    $fields['locked'] = BaseFieldDefinition::create('boolean')
//      ->setLabel(t('Locked'))
//      ->setDescription(t('Is this entry locked?'))
//      ->setTranslatable(TRUE)
//      ->setDefaultValue(TRUE);
//
//    $fields['billable'] = BaseFieldDefinition::create('boolean')
//      ->setLabel(t('Billable'))
//      ->setDescription(t('Is this entry billable?'))
//      ->setTranslatable(TRUE)
//      ->setDefaultValue(TRUE);
//
//    $fields['billed'] = BaseFieldDefinition::create('boolean')
//      ->setLabel(t('Billed'))
//      ->setDescription(t('Has this entry been billed?'))
//      ->setTranslatable(TRUE)
//      ->setDefaultValue(TRUE);


    /* @var $entity \Drupal\time_tracker\Entity\TimeTrackerEntry */
    $form = parent::form($form, $form_state);
//    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entry = $this->entity;
    $status = $entry->save();

    if ($status == SAVED_UPDATED) {
      drupal_set_message(t('Time entry %label has been updated.', array('%label' => $entry->label())));
      $this->logger('time_tracker')->notice('Time entry %label has been updated.', array('%label' => $entry->label()));
    }
    else {
      drupal_set_message(t('Time entry %label has been added.', array('%label' => $entry->label())));
      $this->logger('time_tracker')->notice('Time entry %label has been added.', array('%label' => $entry->label()));
    }

    $form_state->setRedirect('entity.time_tracker_entry.collection');
  }
}
