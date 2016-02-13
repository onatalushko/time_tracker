<?php
/**
 * @file
 * Contains \Drupal\time_tracker\Routing\TimeTrackerRouteSubscriber.
 */

namespace Drupal\time_tracker\Routing;
use Drupal\config_translation\ConfigMapperManagerInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Drupal\time_tracker\TimeTrackerManagerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class TimeTrackerRoutes.
 * @package Drupal\time_tracker\Routing
 */
class TimeTrackerRouteSubscriber extends RouteSubscriberBase {


  /**
   * The content translation manager.
   *
   * @var \Drupal\content_translation\ContentTranslationManagerInterface
   */
  protected $timeTrackerManager;

  /**
   * Constructs a ContentTranslationRouteSubscriber object.
   *
   * @param \Drupal\content_translation\ContentTranslationManagerInterface $content_translation_manager
   *   The content translation manager.
   */
  public function __construct(TimeTrackerManagerInterface $time_tracker_manager) {
    $this->timeTrackerManager = $time_tracker_manager;
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    foreach ($this->timeTrackerManager->getSupportedEntityTypes() as $entity_type_id => $entity_type) {
      // Try to get the route from the current collection.
      $link_template = $entity_type->getLinkTemplate('canonical');
      if (strpos($link_template, '/') !== FALSE) {
        $base_path = '/' . $link_template;
      }
      else {
        if (!$entity_route = $collection->get("entity.$entity_type_id.canonical")) {
          continue;
        }
        $base_path = $entity_route->getPath();
      }

      // Inherit admin route status from edit route, if exists.
      $is_admin = FALSE;
      $route_name = "entity.$entity_type_id.edit_form";
      if ($edit_route = $collection->get($route_name)) {
        $is_admin = (bool) $edit_route->getOption('_admin_route');
      }

      $path = $base_path . '/time_entries';

      $route = new Route(
        $path,
        array(
//          '_controller' => '\Drupal\time_tracker\Controller\TimeTrackerController::overview',
          'entity_type_id' => $entity_type_id,
          '_entity_list' => 'time_tracker_entry',
        ),
        array(
          '_entity_access' => $entity_type_id . '.view',
        ),
        array(
          'parameters' => array(
            $entity_type_id => array(
              'type' => 'entity:' . $entity_type_id,
            ),
          ),
          '_admin_route' => $is_admin,
        )
      );
      $route_name = "entity.$entity_type_id.time_entries";
      $collection->add($route_name, $route);
//
//      $route = new Route(
//        $path . '/add/{source}/{target}',
//        array(
//          '_controller' => '\Drupal\content_translation\Controller\ContentTranslationController::add',
//          'source' => NULL,
//          'target' => NULL,
//          '_title' => 'Add',
//          'entity_type_id' => $entity_type_id,
//
//        ),
//        array(
//          '_entity_access' =>  $entity_type_id . '.view',
//          '_access_content_translation_manage' => 'create',
//        ),
//        array(
//          'parameters' => array(
//            'source' => array(
//              'type' => 'language',
//            ),
//            'target' => array(
//              'type' => 'language',
//            ),
//            $entity_type_id => array(
//              'type' => 'entity:' . $entity_type_id,
//            ),
//          ),
//          '_admin_route' => $is_admin,
//        )
//      );
//      $collection->add("entity.$entity_type_id.time_entries_add", $route);
//
//      $route = new Route(
//        $path . '/edit/{language}',
//        array(
//          '_controller' => '\Drupal\content_translation\Controller\ContentTranslationController::edit',
//          'language' => NULL,
//          '_title' => 'Edit',
//          'entity_type_id' => $entity_type_id,
//        ),
//        array(
//          '_access_content_translation_manage' => 'update',
//        ),
//        array(
//          'parameters' => array(
//            'language' => array(
//              'type' => 'language',
//            ),
//            $entity_type_id => array(
//              'type' => 'entity:' . $entity_type_id,
//            ),
//          ),
//          '_admin_route' => $is_admin,
//        )
//      );
//      $collection->add("entity.$entity_type_id.time_entries_edit", $route);
//
//      $route = new Route(
//        $path . '/delete/{language}',
//        array(
//          '_entity_form' => $entity_type_id . '.content_translation_deletion',
//          'language' => NULL,
//          '_title' => 'Delete',
//          'entity_type_id' => $entity_type_id,
//        ),
//        array(
//          '_access_content_translation_manage' => 'delete',
//        ),
//        array(
//          'parameters' => array(
//            'language' => array(
//              'type' => 'language',
//            ),
//            $entity_type_id => array(
//              'type' => 'entity:' . $entity_type_id,
//            ),
//          ),
//          '_admin_route' => $is_admin,
//        )
//      );
//      $collection->add("entity.$entity_type_id.time_entries_delete", $route);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = parent::getSubscribedEvents();
    // Should run after AdminRouteSubscriber so the routes can inherit admin
    // status of the edit routes on entities. Therefore priority -210.
    $events[RoutingEvents::ALTER] = array('onAlterRoutes', -210);
    return $events;
  }

}
