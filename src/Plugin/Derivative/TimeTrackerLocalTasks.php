<?php
/**
 * @file
 * Contains \Drupal\time_tracker\Plugin\Derivative\TimeTrackerLocalTasks.
 */

namespace Drupal\time_tracker\Plugin\Derivative;


use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\time_tracker\TimeTrackerManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TimeTrackerLocalTasks.
 * @package Drupal\time_tracker\Plugin\Derivative
 */
class TimeTrackerLocalTasks extends DeriverBase  implements ContainerDeriverInterface{
  /**
   * The base plugin ID.
   *
   * @var string
   */
  protected $basePluginId;

  /**
   * The time tracker manager.
   *
   * @var \Drupal\time_tracker\TimeTrackerManagerInterface
   */
  protected $timeTrackerManager;

  /**
   * TimeTrackerLocalTasks constructor.
   */
  public function __construct($base_plugin_id, TimeTrackerManagerInterface $timeTrackerManager) {
    $this->basePluginId = $base_plugin_id;
    $this->timeTrackerManager = $timeTrackerManager;
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    // Create tabs for all possible entity types.
    foreach ($this->timeTrackerManager->getSupportedEntityTypes() as $entity_type_id => $entity_type) {
      // Find the route name for the translation overview.
      $time_tracker_route_name = "entity.$entity_type_id.time_tracker";

      $base_route_name = "entity.$entity_type_id.canonical";
      $this->derivatives[$time_tracker_route_name] = array(
          'entity_type' => $entity_type_id,
          'title' => 'Time entries',
          'route_name' => $time_tracker_route_name,
          'base_route' => $base_route_name,
        ) + $base_plugin_definition;
    }
    return parent::getDerivativeDefinitions($base_plugin_definition);
  }

  /**
   * @inheritDoc
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $base_plugin_id,
      $container->get('time_tracker.manager')
    );
  }

}
