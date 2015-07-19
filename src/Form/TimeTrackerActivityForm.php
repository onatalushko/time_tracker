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
      '#description' => t('Describe this time tracker activity. The text will be displayed on the <em>time tracker activity</em> administration overview page'),
      '#title' => t('Description'),
    );
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

    if ($status == SAVED_UPDATED) {
      drupal_set_message(t('Activity type %label has been updated.', array('%label' => $activity->label())));
      $this->logger('time_tracker')->notice('Activity type %label has been updated.', array('%label' => $activity->label()));
    }
    else {
      drupal_set_message(t('Activity type %label has been added.', array('%label' => $activity->label())));
      $this->logger('time_tracker')->notice('Activity type %label has been added.', array('%label' => $activity->label()));
    }

    $form_state->setRedirect('entity.time_tracker_activity.type_list');
  }
}
