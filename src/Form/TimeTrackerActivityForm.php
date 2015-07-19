<?php

/**
 * @file
 * Contains \Drupal\time_tracker\Form.
 */

namespace Drupal\time_tracker\Form;

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
class TimeTrackerActivityForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {

    $activity = $this->entity;

    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => t('Label'),
      '#maxlength' => 255,
      '#default_value' => $activity->label(),
      '#required' => TRUE,
    );
    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $activity->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\time_tracker\Entity\TimeTrackerActivity::load',
      ),
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#disabled' => !$activity->isNew(),
    );

    $form['description'] = array(
      '#type' => 'textarea',
      '#default_value' => $activity->getDescription(),
      '#description' => t('Describe this time tracker activity. The text will be displayed on the <em>entityComment types</em> administration overview page'),
      '#title' => t('Description'),
    );
//
//    if ($comment_type->isNew()) {
//      $options = array();
//      foreach ($this->entityManager->getDefinitions() as $entity_type) {
//        // Only expose entities that have field UI enabled, only those can
//        // get comment fields added in the UI.
//        if ($entity_type->get('field_ui_base_route')) {
//          $options[$entity_type->id()] = $entity_type->getLabel();
//        }
//      }
//      $form['target_entity_type_id'] = array(
//        '#type' => 'select',
//        '#default_value' => $comment_type->getTargetEntityTypeId(),
//        '#title' => t('Target entity type'),
//        '#options' => $options,
//        '#description' => t('The target entity type can not be changed after the comment type has been created.')
//      );
//    }
//    else {
//      $form['target_entity_type_id_display'] = array(
//        '#type' => 'item',
//        '#markup' => $this->entityManager->getDefinition($comment_type->getTargetEntityTypeId())->getLabel(),
//        '#title' => t('Target entity type'),
//      );
//    }
//
//    if ($this->moduleHandler->moduleExists('content_translation')) {
//      $form['language'] = array(
//        '#type' => 'details',
//        '#title' => t('Language settings'),
//        '#group' => 'additional_settings',
//      );
//
//      $language_configuration = ContentLanguageSettings::loadByEntityTypeBundle('comment', $comment_type->id());
//      $form['language']['language_configuration'] = array(
//        '#type' => 'language_configuration',
//        '#entity_information' => array(
//          'entity_type' => 'comment',
//          'bundle' => $comment_type->id(),
//        ),
//        '#default_value' => $language_configuration,
//      );
//
//      $form['#submit'][] = 'language_configuration_element_submit';
//    }
//
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save'),
    );
    $form = parent::form($form, $form_state, $activity);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $activity = $this->entity;
    $status = $activity->save();

//    $edit_link = $this->entity->link($this->t('Edit'));
    if ($status == SAVED_UPDATED) {
      drupal_set_message(t('Activity type %label has been updated.', array('%label' => $activity->label())));
      $this->logger('time_tracker')->notice('Activity type %label has been updated.', array('%label' => $activity->label(), 'link' => $edit_link));
    }
    else {
      drupal_set_message(t('Activity type %label has been added.', array('%label' => $activity->label())));
      $this->logger('time_tracker')->notice('Activity type %label has been added.', array('%label' => $activity->label(), 'link' =>  $edit_link));
    }

    $form_state->setRedirect('entity.time_tracker_activity.type_list');
  }

}
