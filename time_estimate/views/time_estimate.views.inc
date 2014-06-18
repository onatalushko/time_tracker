<?php
/**
 * @file
 *   Views integration for time_estimate
 */

/**
 * Implements hook_views_data().
 */
function time_estimate_views_data() {
  $data = array();
  $entity_types = entity_get_info();
  foreach($entity_types as $key => $type){
    $bundles = $type['bundles'];
    foreach($bundles as $bkey => $bundle){
      $tracking = time_tracker_is_tracking_time($key, $bkey);
      if($tracking){
        $table = $type['base table'];
        $data[$table]['time_estimate_remaining'] = array(
          'title' => t('Remaining Time on ' . $type['label']),
          'help' => t('The time estimate minus the accumulated time on the entity'),
          'field' => array(
            'handler' => 'time_estimate_remaining_time',
          ),
          'sort' => array(
            'handler' => 'views_handler_sort_numeric',
          ),
          'filter' => array(
            'handler' => 'views_handler_filter_numeric',
          ),
          'argument' => array(
            'handler' => 'views_handler_argument',
          ),
        );
      }
    }
  }

  return $data;
}


