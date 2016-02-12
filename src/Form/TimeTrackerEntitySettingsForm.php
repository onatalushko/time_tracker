<?php
/**
 * @file
 * Contains \Drupal\time_tracker\Form\TimeTrackerEntitySettingsForm.
 */

namespace Drupal\time_tracker\Form;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\time_tracker\Entity\TimeTrackerSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TimeTrackerEntitySettingsForm.
 * @package Drupal\time_tracker\Form
 */
class TimeTrackerEntitySettingsForm extends FormBase {

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a ContentLanguageSettingsForm object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')
    );
  }

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
    $entity_types = $this->entityManager->getDefinitions();
    $labels = array();
    $default = array();

    $bundles = $this->entityManager->getAllBundleInfo();
    $time_tracker_configuration = array();
    foreach ($entity_types as $entity_type_id => $entity_type) {
      if (!$entity_type instanceof ContentEntityTypeInterface || !isset($bundles[$entity_type_id])) {
        continue;
      }
      $labels[$entity_type_id] = $entity_type->getLabel() ?: $entity_type_id;
      $default[$entity_type_id] = FALSE;

      // Check whether we have any custom setting.
      foreach ($bundles[$entity_type_id] as $bundle => $bundle_info) {
        $config = TimeTrackerSettings::loadByEntityTypeBundle($entity_type_id, $bundle);
        $time_tracker_configuration[$entity_type_id][$bundle] = $config;
        if ($config->getActive()) {
          $default[$entity_type_id] = $entity_type_id;
        }
      }
    }

    asort($labels);

    $form = array(
      '#labels' => $labels,
      '#attached' => array(
        'library' => array(
          'language/drupal.language.admin',
        ),
      ),
      '#attributes' => array(
        'class' => 'language-content-settings-form',
      ),
    );

    $form['entity_types'] = array(
      '#title' => $this->t('Custom Time Tracker settings'),
      '#type' => 'checkboxes',
      '#options' => $labels,
      '#default_value' => $default,
    );

    $form['settings'] = array('#tree' => TRUE);

    foreach ($labels as $entity_type_id => $label) {
      $entity_type = $entity_types[$entity_type_id];

      $form['settings'][$entity_type_id] = array(
        '#title' => $label,
        '#type' => 'container',
        '#entity_type' => $entity_type_id,
        '#theme' => 'time_tracker_settings_table',
        '#bundle_label' => $entity_type->getBundleLabel() ?: $label,
        '#states' => array(
          'visible' => array(
            ':input[name="entity_types[' . $entity_type_id . ']"]' => array('checked' => TRUE),
          ),
        ),
      );

      foreach ($bundles[$entity_type_id] as $bundle => $bundle_info) {
        $form['settings'][$entity_type_id][$bundle]['settings'] = array(
          '#type' => 'item',
          '#label' => $bundle_info['label'],
          'time_tracker' => array(
            '#type' => 'time_tracker_configuration',
            '#entity_information' => array(
              'entity_type' => $entity_type_id,
              'bundle' => $bundle,
            ),
            '#default_value' => $time_tracker_configuration[$entity_type_id][$bundle],
          ),
        );
      }
    }

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save configuration'),
      '#button_type' => 'primary',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValue('settings') as $entity_type => $entity_settings) {
      foreach ($entity_settings as $bundle => $bundle_settings) {
        $config = TimeTrackerSettings::loadByEntityTypeBundle($entity_type, $bundle);
        $config->setActive($bundle_settings['settings']['time_tracker']['active'])
          ->save();
      }
    }
    drupal_set_message($this->t('Settings successfully updated.'));
  }

}
