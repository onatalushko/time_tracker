<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 11.01.15
 * Time: 20:12
 */

namespace Drupal\time_tracker\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\time_tracker\TimeTrackerManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TimeTrackerController extends ControllerBase {

  /**
   * The content translation manager.
   *
   * @var \Drupal\content_translation\ContentTranslationManagerInterface
   */
  protected $manager;

  /**
   * Initializes a content translation controller.
   *
   * @param \Drupal\content_translation\ContentTranslationManagerInterface
   *   A content translation manager instance.
   */
  public function __construct(TimeTrackerManagerInterface $manager) {
    $this->manager = $manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('time_tracker.manager'));
  }

  /**
   * Builds the time tracker overview page.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param string $entity_type_id
   *   (optional) The entity type ID.
   * @return array Array of page elements to render.
   * Array of page elements to render.
   */
  public function overview(RouteMatchInterface $route_match, $entity_type_id = NULL) {
    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    $entity = $route_match->getParameter($entity_type_id);
    $account = $this->currentUser();

    $entity_ids = \Drupal::entityQuery('time_tracker_entry')
      ->condition('entity_id', $entity->id())
      ->range(0, 100)
      ->execute();
//    $handler = $this->entityManager()->getHandler($entity_type_id, 'translation');
    $manager = $this->manager;
    $entity_type = $entity->getEntityType();

    // Start collecting the cacheability metadata, starting with the entity and
    // later merge in the access result cacheability metadata.
    $cacheability = CacheableMetadata::createFromObject($entity);

//    $languages = $this->languageManager()->getLanguages();
//    $original = $entity->getUntranslated()->language()->getId();
//    $translations = $entity->getTranslationLanguages();
//    $field_ui = $this->moduleHandler()->moduleExists('field_ui') && $account->hasPermission('administer ' . $entity_type_id . ' fields');
    return $build;
  }
}
