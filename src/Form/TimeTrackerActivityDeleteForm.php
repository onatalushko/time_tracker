<?php
/**
 * Created by PhpStorm.
 * User: niko
 * Date: 19.07.15
 * Time: 14:47
 */

namespace Drupal\time_tracker\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;


class TimeTrackerActivityDeleteForm extends EntityConfirmFormBase {
  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the Activity %name?', array('%name' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.time_tracker_activity.type_list');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->entity->delete();
    $this->logger('time_tracker')->notice('Activity %name has been deleted.', array('%name' => $this->entity->label()));
    drupal_set_message($this->t('Activity %name has been deleted.', array('%name' => $this->entity->label())));
    $form_state->setRedirectUrl($this->getCancelUrl());
  }
}