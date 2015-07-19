<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 11.01.15
 * Time: 21:23
 */

namespace Drupal\time_tracker\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;

class TimeTrackerEntitySettingsForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'time_tracker_entity_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
  //@todo at least replace variable_get with CMI and require rewrite
  //    Example
  //    $exporter = new \SebastianBergmann\Exporter\Exporter();
  //    foreach(\Drupal::entityManager()->getDefinitions() as $id => $definition) {
  //      if (is_a($definition ,'Drupal\Core\Entity\ContentEntityType')) {
  //        $entities_info[$id] = $exporter->toArray($definition);
  //      }
  //    }
    $entity_types = \Drupal::entityManager()->getDefinitions();

    $form['description'] = array(
      '#markup' => '<p>Foreach entity select the bundles that you want to track time on.</p>',
    );
    foreach($entity_types as $key => $type) {
      // We dont want the ability to track time on time tracker activities,
      // entries, or comments.
      if (!in_array($key, array('time_tracker_activity', 'time_tracker_entry', 'comment'))){
        // Basic Settings
        $form[$key] = array(
          '#type' => 'fieldset',
          '#title' => t(":label Settings", array(':label'=> $type['label'])),
          '#collapsible' => TRUE,
          '#collapsed' => TRUE,
        );
        foreach($type['bundles'] as $bkey => $bundle){
          $form[$key][$bkey] = array(
            '#type' => 'fieldset',
            '#title' => t(":label", array(':label'=> $bundle['label'])),
            '#collapsible' => TRUE,
            '#collapsed' => TRUE,
          );

          $form[$key][$bkey]['time_tracker-' . $key . '-' . $bkey] = array(
            '#type' => 'checkbox',
            '#title' => t('Track time on this bundle.'),
            '#default_value' => variable_get('time_tracker-' . $key . '-' . $bkey),
            //'#description' => t("Track time on this bundle."),
          );

          if ($type['label'] == 'Node') {
            $form[$key][$bkey]['time_tracker_comments-' . $key . '-' . $bkey] = array(
              '#type' => 'checkbox',
              '#title' => t("Track time on this bundle's comments."),
              '#default_value' => variable_get('time_tracker_comments-' . $key . '-' . $bkey),
              //'#description' => t("Track time on this bundle's comments."),
            );
          }
        }
      }
    }

    return parent::buildForm($form, $form_state);
  }
} 