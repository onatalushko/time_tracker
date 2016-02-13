<?php
/**
 * @file
 * Contains \Drupal\time_tracker\TimeTrackerManager.
 */

namespace Drupal\time_tracker;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\time_tracker\Entity\TimeTrackerSettings;


/**
 * Class TimeTrackerManager.
 * @package Drupal\time_tracker
 */
class TimeTrackerManager implements TimeTrackerManagerInterface {
  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a TimeTrackerManager object.
   */
  public function __construct(EntityTypeManagerInterface $manager) {
    $this->entityManager = $manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getSupportedEntityTypes() {
    $supported_types = array();
    $entity_types = $this->entityManager->getDefinitions();
    $bundles = $this->entityManager->getAllBundleInfo();
    foreach ($entity_types as $entity_type_id => $entity_type) {
      if (!$entity_type instanceof ContentEntityTypeInterface || !isset($bundles[$entity_type_id])) {
        continue;
      }
      foreach ($bundles[$entity_type_id] as $bundle => $bundle_info) {
        $config = $this->loadTimeTrackerSettings($entity_type_id, $bundle);
        if ($config->getActive()) {
          $supported_types[$entity_type_id] = $entity_type;
        }
      }
    }
    return $supported_types;
  }

  /**
   * {@inheritdoc}
   */
  public function isSupported($entity_type_id) {
    $entity_type = $this->entityManager->getDefinition($entity_type_id);
    $this->entityManager->getAllBundleInfo();
    return $entity_type->isTranslatable() && ($entity_type->hasLinkTemplate('drupal:content-translation-overview') || $entity_type->get('content_translation_ui_skip'));

    // TODO: Implement isSupported() method.
  }

  /**
   * {@inheritdoc}
   */
  public function setEnabled($entity_type_id, $bundle, $value) {
    // TODO: Implement setEnabled() method.
  }

  /**
   * {@inheritdoc}
   */
  public function isEnabled($entity_type_id, $bundle = NULL) {
    $enabled = FALSE;

    if ($this->isSupported($entity_type_id)) {
      $bundles = !empty($bundle) ? array($bundle) : array_keys($this->entityManager->getBundleInfo($entity_type_id));
      foreach ($bundles as $bundle) {
        $config = $this->loadTimeTrackerSettings($entity_type_id, $bundle);
        if ($config->getThirdPartySetting('content_translation', 'enabled', FALSE)) {
          $enabled = TRUE;
          break;
        }
      }
    }

    return $enabled;
  }

  /**
   * Loads a content language config entity based on the entity type and bundle.
   *
   * @param string $entity_type_id
   *   ID of the entity type.
   * @param string $bundle
   *   Bundle name.
   *
   * @return \Drupal\language\Entity\ContentLanguageSettings
   *   The content language config entity if one exists. Otherwise, returns
   *   default values.
   */
  protected function loadTimeTrackerSettings($entity_type_id, $bundle) {
    if ($entity_type_id == NULL || $bundle == NULL) {
      return NULL;
    }
    $config = $this->entityManager->getStorage('time_tracker_settings')->load($entity_type_id . '.' . $bundle);
    if ($config == NULL) {
      $config = $this->entityManager->getStorage('time_tracker_settings')->create(['target_entity_type_id' => $entity_type_id, 'target_bundle' => $bundle]);
    }
    return $config;
  }

}
