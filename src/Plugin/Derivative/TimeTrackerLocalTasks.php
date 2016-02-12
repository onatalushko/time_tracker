<?php
/**
 * @file
 * Contains \Drupal\time_tracker\Plugin\Derivative\TimeTrackerLocalTasks.
 */

namespace Drupal\time_tracker\Plugin\Derivative;


use Drupal\Component\Plugin\Derivative\DeriverBase;

/**
 * Class TimeTrackerLocalTasks.
 * @package Drupal\time_tracker\Plugin\Derivative
 */
class TimeTrackerLocalTasks extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    // Implement dynamic logic to provide values for the same keys as in example.links.task.yml.
    $this->derivatives['example.task_id'] = $base_plugin_definition;
    $this->derivatives['example.task_id']['title'] = "I'm a tab";
    $this->derivatives['example.task_id']['route_name'] = 'example.route';
    $this->derivatives['example.task_id']['parent_id'] = "example.local_tasks:example.task_id";
    return $this->derivatives;
  }

}