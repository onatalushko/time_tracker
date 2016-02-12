<?php
/**
 * @file
 * Contains \Drupal\time_tracker\Routing\TimeTrackerRoutes.
 */

namespace Drupal\time_tracker\Routing;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class TimeTrackerRoutes.
 * @package Drupal\time_tracker\Routing
 */
class TimeTrackerRoutes {

  /**
   * {@inheritdoc}
   */
  public function routes() {
    $route_collection = new RouteCollection();

    $route = new Route(
    // Path to attach this route to:
      '/example',
      // Route defaults:
      array(
        '_controller' => '\Drupal\time_tracker\Controller\TimeTrackerRoutesController::content',
        '_title' => 'Time entries',
      ),
      // Route requirements:
      array(
        '_permission'  => 'access content',
      )
    );
    // Add the route under the name 'example.content'.
    $route_collection->add('time_tracker.content', $route);
    return $route_collection;
  }

}
