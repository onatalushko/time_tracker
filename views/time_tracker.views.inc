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
      'handler' => 'views_handler_sort_numeric',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_date',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_date',
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
  $data['time_tracker_entry']['taid'] = array(
    'title' => t('Activity'),
    'help' => t('Time Entry Activity.'),
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
      'handler' => 'views_handler_argument_numeric',
    ),
    'relationship' => array(
      'handler' => 'views_handler_relationship',
      'base' => 'time_tracker_activity',
      'base field' => 'taid',
      'label' => t('Time Tracker Activity'),
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

function time_tracker_form_views_exposed_form_alter(&$form, $form_state) {
  if($form['#id'] == 'views-exposed-form-time-tracker-reports-page-1' ||
     $form['#id'] == 'views-exposed-form-time-tracker-user-page-1') {
       
    $start_date = urlencode(date('F d, Y', strtotime(date('m').'/01/'.date('Y'))));
    $end_date = urlencode(date('F d, Y', strtotime('-1 second',strtotime('+1 month',strtotime(date('m').'/01/'.date('Y'))))));
    
    // Whaaaaa-aaaat?
    // This is a simple redirect if there is no ? in the file path
    // It's a hack, I know, but it works...
    if ( !isset($_GET['uid']) && !isset($_GET['timestamp']['min']) && !isset($_GET['timestamp']['max']) &&  !isset($_GET['pid']) && !isset($_GET['group_nid']) ) {
      drupal_goto('reports', 'timestamp[min]=' . $start_date . '&timestamp[max]=' . $end_date);
    }
       
    if(module_exists('jquery_ui')) {
      jquery_ui_add(array('ui.datepicker'));
      drupal_add_js(drupal_get_path('module', 'time_tracker') .'/js/datepicker.settings.js');
      drupal_add_css(drupal_get_path('module', 'jquery_ui') .'/jquery.ui/themes/default/ui.datepicker.css');
    }
    
    //$form['timestamp']['min']['#default_value'] = date('F 01, Y');
    //$form['timestamp']['max']['#default_value'] = date('F d, Y');
    //$form['uid']['#default_value'] = "Chris";
    //$form['pid']['#options'];
    
    //Get the group list
    // $options = array('<choose>' => '<choose...>');
    // 
    // $sql = "SELECT node.title, node.nid 
    //         FROM {node} AS node 
    //         WHERE node.type = '%s' 
    //         ORDER BY node.title ASC";
    //         
    // $result = db_query($sql, 'group');
    // 
    // while ($row = db_fetch_object($result)) {
    //   $options[$row->nid] = $row->title;
    // }
    // 
    // $default_group_nid = 548; // Active State: Alphabetically first
    
    //$_GET['group_nid'] ? $group_nid = $_GET['group_nid'] : $group_nid = $default_group_nid;
    
    // $form['group_nid'] = array(
    //   '#type' => 'select',
    //   '#title' => t('Group'),
    //   '#options' => $options,
    //   '#default_value' => $_GET['group_nid'] ? node_load($group_nid)->title : '<choose...>',
    //   '#weight' => -10000,
    //   '#ahah' => array(
    //     'event' => 'change',
    //     'path' => 'reports/js',
    //     'wrapper' => 'edit-pid-wrapper',
    //     'method' => 'replace',
    //     'effect' => 'slide',
    //   ),
    // );
    
    //$form['pid']['#title'] = "Project";
    //$form['pid']['#weight'] = 10;
    //$form['pid']['#options'] = _get_projects_for_group($group_nid);
    
    //$form['#info']['filter-pid']['label'] = "";
    
    //dpm($form);
  }
}

/**
 * AHAH Callback for the views filters
 * Creates a dependency for the 'Project' filter to 
 * Get the user to choose a Group First.
 *
 * Right now this is disabled because the groups filter has been
 * disabled. Trying to figure out an elegant way of doing this without getting
 * the illegal choice error.
 */
function report_js() {
  
  $group_nid = $_POST['group_nid'];
  
  if ($group_nid == '<choose...>') {
    exit();
  }
  
  $options = _get_projects_for_group($group_nid);
  
  //dpr($options);
  $form_element = array(
    '#type' => 'select',
    '#title' => t('Project'),
    '#default_value' => '',
    '#options' => $options,
  );
  
  // Build the new form.
  $form_state = array('submitted' => FALSE);
  $form_build_id = $_POST['form_build_id'];
  // Add the new element to the stored form. Without adding the element to the
  // form, Drupal is not aware of this new elements existence and will not
  // process it. We retreive the cached form, add the element, and resave.
  if (!$form = form_get_cache($form_build_id, $form_state)) {
    exit();
  }
  $form['pid'] = $form_element;
  form_set_cache($form_build_id, $form, $form_state);
  $form += array(
    '#post' => $_POST,
    '#programmed' => FALSE,
  );

  // Rebuild the form.
  $form = form_builder('views_exposed_form', $form, $form_state);

  // Render the new output.
  $choice_form = $form['pid'];
  unset($choice_form['#prefix'], $choice_form['#suffix']);
  $output = theme('status_messages') . drupal_render($choice_form);
  
  //dpm($form);
  
  drupal_json(array('status' => TRUE, 'data' => $output));
}

/**
 * Returns the projects for a given Group ID
 *
 * NOTE:
 * Groups and Projects are just node types, all stored in the
 * node table in the db. However the og_ancestry table
 * stored the relationship between a project and a group.
 *
 * @param
 * The Group nid for the project.
 */
function _get_projects_for_group($group_id) {
  $options = array('any' => '<Any>');
  
  $sql = "SELECT node.title, node.nid
          FROM {node}
          JOIN {og_ancestry} AS oa ON oa.nid = node.nid
          JOIN {node} AS no ON no.nid = oa.group_nid
          WHERE node.type = '%s'
          AND no.nid ='%s'";
          
  $result = db_query($sql, 'casetracker_basic_project', $group_id);
  
  while ($row = db_fetch_object($result)) {
    $options[$row->nid] = $row->title;
  }
  return $options;
}
