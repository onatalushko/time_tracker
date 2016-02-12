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
