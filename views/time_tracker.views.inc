<?php
// $Id: time_tracker.views.inc,v 1.10 2010/06/11 23:10:08 codi Exp $
/**
 * @file
 *   Views integration for time_tracker
 */

/**
 * Implements hook_views_data().
 */
function time_tracker_views_data() {
  $data['time_tracker_entry']['table']['group'] = t('Time Tracker');
  $data['time_tracker_entry']['table']['base'] = array(
    'field' => 'teid',
    'title' => t('Time Tracker'),
    'help' => t('Content generated from time tracker entries.'),
  );

  $data['time_tracker_entry']['teid'] = array(
    'title' => t('ID'),
    'help' => t('Time Entry ID.'),
    'field' => array(
      'handler' => 'views_handler_field_numeric',
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
  $data['time_tracker_entry']['timestamp'] = array(
    'title' => t('Timestamp'),
    'help' => t('Timestamp of the Entry.'),
    'field' => array(
      'handler' => 'views_handler_field_date',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort_date',
    ),
    'filter' => array(
      'handler' => 'time_tracker_views_handler_filter_date',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument',
    ), 
  );
  $data['time_tracker_entry']['start'] = array(
    'title' => t('Start Time'),
    'help' => t('Start time of the Entry.'),
    'field' => array(
      'handler' => 'views_handler_field_date',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort_date',
    ),
    'filter' => array(
      'handler' => 'time_tracker_views_handler_filter_date',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument',
    ),
  );
  $data['time_tracker_entry']['end'] = array(
    'title' => t('End Time'),
    'help' => t('End time of the Entry.'),
    'field' => array(
      'handler' => 'views_handler_field_date',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort_date',
    ),
    'filter' => array(
      'handler' => 'time_tracker_views_handler_filter_date',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument',
    ),
  );
  $data['time_tracker_entry']['duration'] = array(
    'title' => t('Duration'),
    'help' => t('Time Entry Duration.'),
    'field' => array(
      'handler' => 'views_handler_field_numeric',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_numeric',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument',
    ),
  );
  $data['time_tracker_entry']['note'] = array(
    'title' => t('Note'),
    'help' => t('Notes for node-based time entries'),
    'field' => array(
      'handler' => 'views_handler_field',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument',
    ),
  );
  // This isn't being used right now so I'm de-exposing it to views
  // $data['time_tracker_entry']['billable'] = array(
  //   'title' => t('Billable'),
  //   'help' => t('Is the Entry Billable?'),
  //   'field' => array(
  //     'handler' => 'views_handler_field_boolean',
  //   ),
  //   'sort' => array(
  //     'handler' => 'views_handler_sort',
  //   ),
  //   'filter' => array(
  //     'handler' => 'views_handler_filter_boolean',
  //   ),
  //   'argument' => array(
  //     'handler' => 'views_handler_argument',
  //   ),
  // );
  $data['time_tracker_entry']['locked'] = array(
    'title' => t('Locked'),
    'help' => t('Is the Entry Locked?'),
    'field' => array(
      'handler' => 'views_handler_field_boolean',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_boolean_operator',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument',
    ),
  );
  $data['time_tracker_entry']['uid'] = array(
    'title' => t('Author'),
    'help' => t('Relate an entry to the user who created it.'),
    'relationship' => array(
      'handler' => 'views_handler_relationship',
      'base' => 'users',
      'base field' => 'uid',
      'label' => t('User'),
    ),
  );
  $data['time_tracker_entry']['nid'] = array(
    'title' => t('Node'),
    'help' => t('Relate an entry to a comments node.'),
    'relationship' => array(
      'handler' => 'views_handler_relationship',
      'base' => 'node',
      'base field' => 'nid',
      'label' => t('Node'),
    ),
  );
  $data['time_tracker_entry']['cid'] = array(
    'title' => t('Comment'),
    'help' => t('Relate an entry to a comment.'),
    'relationship' => array(
      'handler' => 'views_handler_relationship',
      'base' => 'comments',
      'base field' => 'cid',
      'label' => t('Comment'),
    ),
  );
  $data['time_tracker_entry']['activity'] = array(
    'title' => t('Activity'),
    'help' => t('Relate an entry to an Activity Name.'),
    'relationship' => array(
      'handler' => 'views_handler_relationship',
      'base' => 'time_tracker_activity',
      'base field' => 'taid',
      'label' => t('Activity'),
    ),
  );

  $data['time_tracker_activity']['table']['join']['time_tracker_entry'] = array(
    'left_field' => 'activity',
    'field' => 'taid',
  );
  $data['time_tracker_activity']['table']['group'] = t('Time Tracker');

  $data['time_tracker_activity']['name'] = array(
    'title' => t('Activity Name'),
    'help' => t('Activity Name.'),
    'field' => array(
      'handler' => 'views_handler_field',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort_sort',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_string',
    ),
  );

  $data['time_estimate_entry']['table']['join']['time_tracker_entry'] = array(
    'left_field' => 'nid',
    'field' => 'nid',
  );
  $data['time_estimate_entry']['table']['group'] = t('Time Tracker');

  $data['time_estimate_entry']['estimate'] = array(
    'title' => t('Time Estimate'),
    'help' => t('Time Estimate.'),
    'field' => array(
      'handler' => 'views_handler_field',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort_sort',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_numeric',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_numeric',
    ),
  );
  
  // Access Time Tracker Data from the comments base table
  $data['comments']['cid'] = array(
    'relationship' => array(
      'title' => t('Time Tracker'),
      'help' => t('Access time tracker data for comments.'),
      'handler' => 'views_handler_relationship',
      'base' => 'time_tracker_entry',
      'base field' => 'cid',
      'label' => t('Time Tracker'),
    ),
  );

  // Define a "table" called 'time_tracker_totals', which will actually be
  // a subquery that will add up all the time tracked on the node.
  $data['time_tracker_totals'] = array(
    'table' => array(
      'group' => t('Time Tracker'),
      // Join this "table" to the Node table, so this can be used in node views,
      // which is the whole point.
      'join' => array(
        'node' => array(
          'left_field' => 'nid',
          'field' => 'nid',
          // Use a custom join handler, which constructs the query. This is
          // defined below.
          'handler' => 'time_tracker_node_join_handler',
        ),
      ),
    ),

    // Create a field called total_time, to add up all the time tracked on
    // a node. Allow it to be used as a numeric Field, Filter, and Sort.
    'total_time' => array(
      'title' => t('Total time'),
      'help' => t('The total time tracked on the node (not including time tracked on comments)'),
      'field' => array(
        'handler' => 'views_handler_field_numeric',
        'click sortable' => TRUE,
        'float' => TRUE,
      ),
      'filter' => array(
        'handler' => 'views_handler_filter_numeric',
      ),
      'sort' => array(
        'handler' => 'views_handler_sort',
      ),
    ),
  );

  return $data;
}

/**
 * Implementation of hook_views_handlers() to register all of the basic handlers
 * views uses.
 */
function time_tracker_views_handlers() {
  return array(
    'info' => array(
      'path' => drupal_get_path('module', 'time_tracker') .'/views',
      ),
    'handlers' => array(
      'time_tracker_views_handler_filter_date' => array(
        'parent' => 'views_handler_filter_numeric',
      ),
    ),
  );
}


function time_tracker_form_views_exposed_form_alter(&$form, $form_state) {
  
  if ($form['form_id']['#id'] == 'edit-views-exposed-form') {
    if(module_exists('jquery_ui')) {
      jquery_ui_add(array('ui.datepicker'));
      drupal_add_js(drupal_get_path('module', 'time_tracker') .'/js/datepicker.settings.js');
      drupal_add_css(drupal_get_path('module', 'jquery_ui') .'/jquery.ui/themes/default/ui.datepicker.css');
      drupal_add_css(drupal_get_path('module', 'time_tracker') . '/views/css/time_tracker_views.css');
    }
  }
  
  if (isset($form['timestamp']['min']) && isset($form['timestamp']['max']) && arg(0) != 'admin') {
    
    $start_date = urlencode($form['timestamp']['min']['#default_value']);
    $end_date = urlencode($form['timestamp']['max']['#default_value']);
    
    // Whaaaaa-aaaat?
    // This is a simple redirect if there is no ? in the file path
    // It's a hack, I know, but it works...
    if ( !isset($_GET['uid']) && !isset($_GET['timestamp']['min']) && !isset($_GET['timestamp']['max']) &&  !isset($_GET['pid']) && !isset($_GET['group_nid']) ) {
      drupal_goto($_GET['q'], 'timestamp[min]=' . $start_date . '&timestamp[max]=' . $end_date);
    }

  }
}

/**
 * Implementation of hook_date_api_tables
 */
function time_tracker_date_api_tables() {
  return array('time_tracker_entry');
}

/**
 * Implementation of hook_date_api_fields
 * This is required to allow timetracker timestamp
 * as a date argument in views.
 */
function time_tracker_date_api_fields($field) {
  $values = array(
    'sql_type' => DATE_UNIX,
    'tz_handling' => 'site',
    'timezone_field' => '',
    'offset_field' => '',
    'related_fields' => array(),
    'granularity' => array('year', 'month', 'day', 'hour', 'minute', 'second'),
  );
  
  switch($field) {
    case 'time_tracker_entry.timestamp':
    case 'time_tracker_entry.start':
    case 'time_tracker_entry.end': 
      return $values;
  }
}


/**
 * Handles the join between the node table and the time information.
 *
 * Note that this handler is in the views.inc file rather than in its own file,
 * because we need to load it. Views doesn't auto-load join handlers, unlike
 * other handlers (i.e., join handlers are not Views handlers per se, despite
 * the name).
 */
class time_tracker_node_join_handler extends views_join {

  /**
   * Overrides the views_join::join() method.
   *
   * Custom join that makes a sub-query.
   */
  function join($table, &$query) {
    $alias = $table['alias'];
    $left = $query->get_table_info('node');
    $left_field = $left['alias'] . ".nid";

    $output = $this->type . " JOIN (SELECT SUM(`duration`) as total_time, `nid` FROM {time_tracker_entry} GROUP BY nid) as $alias ON $left_field = $alias.nid";
    return $output;
  }
}
