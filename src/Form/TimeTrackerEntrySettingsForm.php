<?php
/**
 * Created by PhpStorm.
 * User: niko
 * Date: 25.07.15
 * Time: 11:05
 */

namespace Drupal\time_tracker\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


/**
 * Class ContentEntityExampleSettingsForm.
 *
 * @package Drupal\time_tracker\Form
 *
 * @ingroup time_tracker
 */
class TimeTrackerEntrySettingsForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'content_entity_example_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Empty implementation of the abstract submit class.
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['contact_settings']['#markup'] = 'Settings form for ContentEntityExample. Manage field settings here.';
    return $form;
  }
}
