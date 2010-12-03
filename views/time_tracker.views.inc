<?php
// $Id$
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
  $data['time_tracker_entry']['billable'] = array(
    'title' => t('Billable'),
    'help' => t('Is the Entry Billable?'),
    'field' => array(
      'handler' => 'views_handler_field_boolean',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_boolean',
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
    
    // if($form['group_nid'] && $form['pid']) {
    //   $form['group_nid']['#ahah'] = array(
    //     'event' => 'change',
    //     'path' => 'reports/js',
    //     'wrapper' => 'edit-pid-wrapper',
    //     'method' => 'replace',
    //     'effect' => 'slide',
    //   );
    // }

  }
}

// /**
//  * AHAH Callback for the views filters
//  * Creates a dependency for the 'Project' filter to 
//  * Get the user to choose a Group First.
//  *
//  * Currently building the html from scratch rather than using
//  * a drupal function because views exposed filters uses a different
//  * caching method for it's form build_id etc. and we can't employ
//  * the usual AHAH methods.
//  */
// function report_js() {
//   
//   // Get the chosen group from the POST variable
//   $group_nid = $_POST['group_nid'];
//   
//   $projects = _get_projects_for_group($group_nid);
//   
//   $options = '';
//   foreach ($projects as $nid => $project) {
//     $options .= '<option value="' . $nid . '">' . $project . '</option>';
//   }
//   
//   // Rebuild the element the way views expects it to be built
//   $html = '<select id="edit-pid" class="form-select" name="pid"><option selected="selected" value="All">&lt;Any&gt;</option>'. $options .'</select>';
//   
//   $output = theme('status_messages') . $html;
//   
//   drupal_json(array('status' => TRUE, 'data' => $output));
// }
// 
// /**
//  * Returns the projects for a given Group ID
//  *
//  * NOTE:
//  * Groups and Projects are just node types, all stored in the
//  * node table in the db. However the og_ancestry table
//  * stored the relationship between a project and a group.
//  *
//  * @param
//  * The Group nid for the project.
//  */
// function _get_projects_for_group($group_id = 'All') {
//   $options = array();
//   
//   if ($group_id == 'All') {
//     $sql = "SELECT node.title, node.nid
//             FROM {node}
//             JOIN {og_ancestry} AS oa ON oa.nid = node.nid
//             JOIN {node} AS no ON no.nid = oa.group_nid
//             WHERE node.type = '%s'
//             ORDER BY node.title ASC";
//   }
//   else {
//     $sql = "SELECT node.title, node.nid
//             FROM {node}
//             JOIN {og_ancestry} AS oa ON oa.nid = node.nid
//             JOIN {node} AS no ON no.nid = oa.group_nid
//             WHERE node.type = '%s'
//             AND no.nid = '%s'";
//   }
//           
//   $result = db_query($sql, 'casetracker_basic_project', $group_id);
//   
//   while ($row = db_fetch_object($result)) {
//     $options[$row->nid] = $row->title;
//   }
//   return $options;
// }
