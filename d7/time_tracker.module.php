<?php
/**
 * @file
 * Main module file for time_tracker.
 */

use \Drupal\time_tracker\Entity\TimeTrackerEntryInterface;
use \Drupal\time_tracker\Entity\TimeTrackerActivityInterface;
use Drupal\Core\Url;

/*
 * Define some constants
 */
// Default Date formats
define('TIME_TRACKER_DEFAULT_DATE_FORMAT', 'Y-m-d H:i');
define('TIME_TRACKER_DEFAULT_TIMESTAMP_FORMAT', 'F d, Y');
define('TIME_TRACKER_DEFAULT_INTERVAL_FORMAT', 'h:i A - M d, Y');
// Constants for what we are tracking time on
define('TIME_TRACKER_BOTH', 'both');
define('TIME_TRACKER_ENTITY', 'entity');
define('TIME_TRACKER_COMMENT', 'comment');

/**
 * Access callback; allow access for time tracker entities.
 */
function time_tracker_entity_access($op, $entity, $account, $entity_type) {
  $access = FALSE;
  switch ($entity_type) {
    case 'time_tracker_entry':
      if (empty($entity)) {
        switch ($op) {
          case 'add':
            $access = user_access('add time tracker entries', $account);
          case 'view':
            $access = user_access('view all time tracker entries', $account);
          case 'edit':
            $access = user_access('edit all time tracker entries', $account);
          case 'delete':
            $access = user_access('delete any time tracker entries', $account);
          default:
            break;
        }
        return $access || user_access('administer time tracker entries') || user_access('administer time tracker');
      }
      else {
        return time_tracker_access($op, $entity, $account);
      }

    case 'time_tracker_activity':
      if ($op != 'view') {
        return user_access('administer time tracker', $account);
      }
      else if ($op == 'view') {
        return TRUE;
      }
  }

  return NULL;
}

/**
 * Implements hook_help().
 */
function time_tracker_help($path, $arg) {
  switch ($path) {
    case 'admin/help#time_tracker':
      // TODO: Remove html from t().
      return t("<h2>Time Tracker</h2>
        <_perm>To begin tracking time go to the Time Tracker Entity Settings form at (admin/config/time_tracker/entity_settings). There, you can choose to track time either on any entity bundle or a node type's comments. Tracking time on entities will give you a time entry sheet and a table of time entries at the bottom of a entities's content, but before the comments (if on a node). Tracking time on comments will add a time entry sheet to the comment form. Keep in mind that if you choose to track time on a node type's node as well as comments, you will only ever see comment time entries in the comment thread, and node time entries in the time entry table. However, if you were to use views to view the time entries, you could see them all with no distinction.</p>
        <p>We recommend deciding ahead of time whether or not you would like to track time on comments or nodes. We've found that people who just want to jot down time entries with minimal notes prefer to track time on nodes. However
        people who like extensive notes along with their time entires, as well as the ability to thread conversations based on time entries, prefer to use comments as their vessel for tracking time.</p>
        <p>Settings for Time Tracker can be found at admin/config/time_tracker/global_settings. They should be mostly self explanatory. We recommend, however, deciding beforehand if you wish to track time using durations (e.g. 2 hours) or time intervals (e.g. 1:00pm to 2:00pm).</p>
        <h3>Optional Fields</h3>
        <p>Some fields are optional. They can be enabled/disabled on the time tracker settings page:</p>
        <ul>
        <li>Billable:   A simple checkbox to flag the time entry as billable</li>
        <li>Billed:     A simple checkbox to flag the time entry as billed</li>
        <li>Deductions: This field can be used to log deductions from the total time</li>
        </ul>
        <h3>Activities</h3>
        <p>Activities are specific classifications for time entries. You can administer activities at admin/config/time_tracker/activities.</p>
        <p>Disabling an activity just makes it so you can't choose it anymore. Past time entries can still reference it will display the activity name.</p>
        <p>Deleting an activity deletes it completely from the db, thus orphaning any
        past time entries that are referencing it.</p>
        <h3>Permissions</h3>
        <p>Double check your permissions before getting started:</p>
        <ul>
        <li><em>add time tracker entries</em><br/>
            Permission to allow users to track time</li>
        <li><em>view all time tracker entries, view own time tracker entries</em><br/>
            Allow users to view time entries</li>
        <li><em>edit time entries</em>  <br/>
            Allow users to edit time entries</li>
        <li><em>delete time entries</em>  <br/>
            Allow users to delete time entries</li>
        <li><em>administer time tracker</em><br/>
          Access the administration pages</li>
        <li><em>administer time entries</em>  <br/>
            Gives access to additional options when editing a time entry:
            <ul>
              <li>Allows locking of time entires (if that particular setting is on)</li>
              <li>Allows editing of locked time entries</li>
              <li>Allows changing of username associated with a time entry</li>
           </ul>
        </li>
        </ul>
        ");
      break;
  }
}
///**
// * Implements hook_menu().
// */
//function time_tracker_menu() {
//
//   // Service settings.
//  $items['admin/config/time_tracker'] = array(
//    'title' => 'Time Tracker',
//    'description' => 'Time Tracker Module Settings.',
//    'position' => 'right',
//    'weight' => 0,
//    'page callback' => 'system_admin_menu_block_page',
//    'access arguments' => array('access administration pages'),
//    'file path' => drupal_get_path('module', 'system'),
//    'file' => 'system.admin.inc',
//  );
//  $items['admin/config/time_tracker/global_settings'] = array(
//    'title' => 'Global Settings',
//    'description' => 'Time Tracker form global settings.',
//    'page callback' => 'drupal_get_form',
//    'page arguments' => array('time_tracker_settings_form'),
//    'access arguments' => array('administer time tracker'),
//    'file' => 'time_tracker.admin.inc',
//  );
//  $items['admin/config/time_tracker/global_settings/manage'] = array(
//    'title' => 'Global Settings',
//    'type' => MENU_DEFAULT_LOCAL_TASK,
//    'weight' => -10,
//  );
//  $items['admin/config/time_tracker/entity_settings'] = array(
//    'title' => 'Entity Settings',
//    'description' => 'Configure time tracking per entity.',
//    'page callback' => 'drupal_get_form',
//    'page arguments' => array('time_tracker_entity_settings_form'),
//    'access arguments' => array('administer time tracker'),
//    'file' => 'time_tracker.admin.inc',
//  );
//  $items['admin/config/time_tracker/entity_settings/manage'] = array(
//    'title' => 'Entity Settings',
//    'type' => MENU_DEFAULT_LOCAL_TASK,
//    'weight' => -10,
//  );
//  $items['admin/config/time_tracker/activities'] = array(
//    'title' => 'Activities',
//    'description' => 'Manage Time Tracker Activity Entities',
//    'page callback' => 'drupal_get_form',
//    'page arguments' => array('time_tracker_activity_table_form'),
//    'access arguments' => array('administer time tracker'),
//    'file' => 'time_tracker.admin.inc',
//  );
//  $items['admin/config/time_tracker/activities/list'] = array(
//    'title' => 'Activities',
//    'type' => MENU_DEFAULT_LOCAL_TASK,
//    'weight' => -10,
//  );
//  $items['admin/config/time_tracker/activity/delete/%'] = array(
//    'title' => 'Delete Activity',
//    'page callback' => 'drupal_get_form',
//    'page arguments' => array('time_tracker_delete_activity_confirm', 5),
//    'access arguments' => array('administer time tracker'),
//    'file' => 'time_tracker.admin.inc',
//  );
//  $items['time_entry/edit/%time_tracker_time_entry'] = array(
//    'title' => 'Time Entry',
//    'description' => 'Edit a Time Entry',
//    'page callback' => 'drupal_get_form',
//    'page arguments' => array('time_tracker_time_entry_form', 1, 2),
//    'access callback' => 'time_tracker_access',
//    'access arguments' => array(1, 2),
//  );
//  $items['time_entry/delete/%time_tracker_time_entry'] = array(
//    'title' => 'Time Entry',
//    'description' => 'Delete a Time Entry',
//    'page callback' => 'drupal_get_form',
//    'page arguments' => array('time_tracker_entry_confirm_delete', 2),
//    'access callback' => 'time_tracker_access',
//    'access arguments' => array(1, 2),
//  );
//  $items['time_entry/add/%node'] = array(
//    'title' => 'Time Entry',
//    'description' => 'Add a Time Entry',
//    'page callback' => 'time_tracker_add_block',
//    'page arguments' => array(2),
//    'access arguments' => array('add time tracker entries'),
//  );
//
//  return $items;
//}

function time_tracker_access($op, $time_entry, $account = NULL){
  if (empty($account)) {
    global $user;
  }
  else {
    $user = $account;
  }
  if ($op == 'edit'){
    if (user_access('edit own time tracker entries') && $user->uid == $time_entry->uid){
      return TRUE;
    }
    elseif(user_access('edit any time tracker entry') || user_access('administer time entries')){
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
  elseif ($op == 'delete'){
    if (user_access('delete own time tracker entries') && $user->uid == $time_entry->uid){
      return TRUE;
    }
    elseif(user_access('delete any time tracker entry') || user_access('administer time entries')){
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
}

/**
 * Implements hook_views_api().
 */
function time_tracker_views_api() {
  return array(
    'api' => 3,
    'path' => drupal_get_path('module', 'time_tracker') . '/views',
  );
}

/**
 * Implements hook_entity_info().
 */
function time_tracker_entity_info() {
  $info['time_tracker_entry'] = array(
    // A human readable label to identify our entity.
    'label' => t('Time Entry'),

    // The controller for our Entity, extending the Drupal core controller.
    'controller class' => 'TimeTrackerEntryController',

    // The table for this entity defined in hook_schema()
    'base table' => 'time_tracker_entry',

    // Returns the uri elements of an entity
    'uri callback' => 'time_tracker_entry_uri',

    // Additional metadata and callbacks for entity api.
    'plural label' => t('Time Tracker Entries'),
    'description' => t('Log entry of time recorded for a given activity'),
    'access callback' => 'time_tracker_entity_access',
    'creation callback' => 'time_tracker_time_entry_create',
    'save callback' => 'time_tracker_time_entry_save',
    'deletion callback' => 'time_tracker_entry_delete',
    'view callback' => '', // TODO
    'form callback' => '', // TODO

    // IF fieldable == FALSE, we can't attach fields.
    'fieldable' => TRUE,

    // entity_keys tells the controller what database fields are used for key
    // functions. It is not required if we don't have bundles or revisions.
    // Here we do not support a revision, so that entity key is omitted.
    'entity keys' => array(
      'id' => 'teid' , // The 'id' (basic_id here) is the unique id.
    ),

    'bundles' => array(
      'time_tracker_entry' => array(
        'label' => t('Time Entry'),
        'admin' => array(
          'path' => 'admin/config/time_tracker/entity_settings',
          'real path' => 'admin/config/time_tracker/entity_settings',
          'access arguments' => array('administer time tracker'),
        ),
      )
    ),

    // FALSE disables caching. Caching functionality is handled by Drupal core.
    'static cache' => TRUE,

  );

  $info['time_tracker_activity'] = array(
    // A human readable label to identify our entity.
    'label' => t('Time Tracker Activities'),

    // The controller for our Entity, extending the Drupal core controller.
    'controller class' => 'TimeTrackerActivityController',

    // The table for this entity defined in hook_schema()
    'base table' => 'time_tracker_activity',

    // Returns the uri elements of an entity
    'uri callback' => 'time_tracker_activity_uri',

    // Additional metadata and callbacks for entity api.
    'plural label' => t('Time Tracker Activities'),
    'description' => t('Activities for use in tracking time with Time Tracker'),
    'access callback' => 'time_tracker_entity_access',
    'creation callback' => '', // TODO
    'save callback' => '', // TODO
    'deletion callback' => '', // TODO
    'view callback' => '', // TODO
    'form callback' => '', // TODO


    // IF fieldable == FALSE, we can't attach fields.
    'fieldable' => FALSE,

    // entity_keys tells the controller what database fields are used for key
    // functions. It is not required if we don't have bundles or revisions.
    // Here we do not support a revision, so that entity key is omitted.
    'entity keys' => array(
      'id' => 'taid' , // The 'id' (basic_id here) is the unique id.
    ),
    'bundle keys' => array(
      'bundle' => 'activity',
    ),

    // FALSE disables caching. Caching functionality is handled by Drupal core.
    'static cache' => TRUE,
    'bundles' => array(
      'activity' => array(
        'label' => 'Activity',
        // 'admin' key is used by the Field UI to provide field and
        // display UI pages.
        'admin' => array(
          'path' => 'admin/config/time_tracker/activities',
          'file' => 'time_tracker_activity.admin.inc',
          'access arguments' => array('administer time tracker'),
        ),
      ),
    ),
  );

  return $info;
}

///**
// *  Implements hook_entity_property_info().
// */
//function time_tracker_entity_property_info() {
//  $info = array();
//
////  $property = &$info['time_tracker_entry']['properties'];
////
////  $property['teid'] = array(
////    'label' => t('Entry ID'),
////    'description' => t('The primary identifier for an entry.'),
////    'type' => 'integer',
////    'schema field' => 'teid',
////  );
////  $property['entity_type'] = array(
////    'label' => t('Entity Type'),
////    'description' => t('The attached entity\'s type.'),
////    'type' => 'text',
////    'setter callback' => 'entity_property_verbatim_set',
////    'schema field' => 'entity_type',
////  );
////  $property['entity_bundle'] = array(
////    'label' => t('Entity Bundle'),
////    'description' => t('The attached entity\'s bundle.'),
////    'type' => 'text',
////    'setter callback' => 'entity_property_verbatim_set',
////    'schema field' => 'entity_bundle',
////  );
////  $property['entity_id'] = array(
////    'label' => t('Entity ID'),
////    'description' => t('The attached entity\'s id.'),
////    'type' => 'integer',
////    'setter callback' => 'entity_property_verbatim_set',
////    'schema field' => 'entity_id',
////  );
////  $property['comment_id'] = array(
////    'label' => t('Comment ID'),
////    'description' => t('The attached comment\'s id.'),
////    'type' => 'integer',
////    'setter callback' => 'entity_property_verbatim_set',
////    'schema field' => 'comment_id',
////  );
////  $property['uid'] = array(
////    'label' => t('User'),
////    'description' => t('The user that submitted this time entry.'),
////    'type' => 'user',
////    'setter callback' => 'entity_property_verbatim_set',
////    'schema field' => 'uid',
////    'required' => TRUE,
////  );
////  $property['activity'] = array(
////    'label' => t('Activity ID'),
////    'description' => t('The activity ID for this entry.'),
////    'type' => 'time_tracker_activity',
////    'setter callback' => 'entity_property_verbatim_set',
////    'schema field' => 'activity',
////  );
////  $property['timestamp'] = array(
////    'label' => t('Timestamp'),
////    'description' => t('The timestamp recorded for this entry.'),
////    'type' => 'date',
////    'setter callback' => 'entity_property_verbatim_set',
////    'schema field' => 'timestamp',
////  );
////  $property['start'] = array(
////    'label' => t('Start Timestamp'),
////    'description' => t('The start timestamp for this entry.'),
////    'type' => 'date',
////    'setter callback' => 'entity_property_verbatim_set',
////    'schema field' => 'start',
////  );
////  $property['end'] = array(
////    'label' => t('End Timestamp'),
////    'description' => t('The end timestamp for this entry.'),
////    'type' => 'date',
////    'setter callback' => 'entity_property_verbatim_set',
////    'schema field' => 'end',
////  );
////  $property['deductions'] = array(
////    'label' => t('Deductions'),
////    'description' => t('Deductions to add to this entry.'),
////    'type' => 'decimal',
////    'setter callback' => 'entity_property_verbatim_set',
////    'schema field' => 'deductions',
////  );
////  $property['duration'] = array(
////    'label' => t('Duration'),
////    'description' => t('Duration of this entry.'),
////    'type' => 'decimal',
////    'setter callback' => 'entity_property_verbatim_set',
////    'schema field' => 'duration',
////  );
////  $property['note'] = array(
////    'label' => t('Note'),
////    'description' => t('Note attached to the entry.'),
////    'type' => 'text',
////    'setter callback' => 'entity_property_verbatim_set',
////    'schema field' => 'note',
////  );
////  $property['locked'] = array(
////    'label' => t('Locked'),
////    'description' => t('Is this entry locked?'),
////    'type' => 'boolean',
////    'setter callback' => 'entity_property_verbatim_set',
////    'schema field' => 'locked',
////  );
////  $property['billable'] = array(
////    'label' => t('Billable'),
////    'description' => t('Is this entry billable?'),
////    'type' => 'boolean',
////    'setter callback' => 'entity_property_verbatim_set',
////    'schema field' => 'billable',
////  );
////  $property['billed'] = array(
////    'label' => t('Billed'),
////    'description' => t('Has this entry been billed?'),
////    'type' => 'boolean',
////    'setter callback' => 'entity_property_verbatim_set',
////    'schema field' => 'billed',
////  );
//
////  $property = &$info['time_tracker_activity']['properties'];
////
////  $property['taid'] = array(
////    'label' => t('Activity ID'),
////    'description' => t('The primary identifier for an activity.'),
////    'type' => 'integer',
////    'schema field' => 'taid',
////  );
////  $property['name'] = array(
////    'label' => t('Name'),
////    'desciption' => t('Name of the activity.'),
////    'type' => 'text',
////    'setter callback' => 'entity_property_verbatim_set',
////    'schema field' => 'name',
////  );
////  $property['weight'] = array(
////    'label' => t('Weight'),
////    'description' => t('The weight of this activity.'),
////    'type' => 'integer',
////    'setter callback' => 'entity_property_verbatim_set',
////    'schema field' => 'weight',
////  );
////  $property['status'] = array(
////    'label' => t('Status'),
////    'description' => t('Is this activity enabled?'),
////    'type' => 'boolean',
////    'setter callback' => 'entity_property_verbatim_set',
////    'schema field' => 'status',
////  );
////
////  return $info;
//}

/**
 * Entity URI callback.
 */
function time_tracker_activity_uri(TimeTrackerActivityInterface $activity) {
  return new Url(
    'entity.time_tracker_activity',
    array(
      'activity' => $activity->id(),
    )
  );
}

/**
 * Implements hook_field_extra_fields().
 */
function time_tracker_field_extra_fields() {
  $form_elements['time_tracker'] = array(
    'label' => t('Time Entry Form'),
    'description' => t('The base time entry form'),
    'weight' => 1,
  );
  $extra_fields = array();
  $entity_types = entity_get_info();
  foreach($entity_types as $key => $type) {
    foreach($type['bundles'] as $bkey => $bundle){
    $enabled = variable_get('time_tracker-' . $key . '-' . $bkey);
      if ($enabled) {
        $extra_fields[$key][$bkey]['display'] = $form_elements;
      }
    }
  }

  // Add the fields to the Time Entry form as well.
  $extra_fields['time_tracker_entry']['time_tracker_entry']['form'] = $form_elements;
  $extra_fields['time_tracker_entry']['time_tracker_entry']['display'] = $form_elements;

  return $extra_fields;
}

///**
// * Fetch a basic object.
// *
// * This function ends up being a shim between the menu system and
// * time_tracker_entry_load_multiple().
// *
// * This function gets its name from the menu system's wildcard
// * naming conventions. For example, /path/%wildcard would end
// * up calling wildcard_load(%wildcard value). In our case defining
// * the path: examples/entity_example/basic/%time_tracker_entry in
// * hook_menu() tells Drupal to call time_tracker_entry_load().
// *
// * @param $teid
// *   Integer specifying the time entry entity id.
// * @return
// *   A fully-loaded $basic object or FALSE if it cannot be loaded.
// *
// * @see time_tracker_entry_load_multiple()
// * @see entity_example_menu()
// */
//function time_tracker_time_entry_load($teid) {
//  $entry = time_tracker_time_entry_load_multiple(array($teid));
//  return isset($entry[$teid]) ? $entry[$teid] : FALSE;
//}
//
///**
// * Loads multiple basic entities.
// *
// * We only need to pass this request along to entity_load(), which
// * will in turn call the load() method of our entity controller class.
// */
//function time_tracker_time_entry_load_multiple($teids) {
//  return entity_load('time_tracker_entry', $teids);
//}

///**
// * Implements the uri callback.
// */
//function time_tracker_entry_uri($time_tracker_entry) {
//  return array(
//    'path' => 'time_entry/edit/' . $time_tracker_entry->teid,
//  );
//}

/**
 * Entity URI callback.
 */
function time_tracker_uri(TimeTrackerEntryInterface $time_tracker_entry) {
  return new Url(
    'entity.time_tracker_entry.edit',
    array(
      'time_tracker_entry' => $time_tracker_entry->id(),
    )
  );
}



///**
// * Implements hook_entity_view().
// */
//function time_tracker_entity_view($entity, $type, $view_mode, $langcode) {
//  global $user;
//
//  $info = entity_extract_ids($type, $entity);
//  $entity_types = entity_get_info();
//  $bundle = $info[2];
//  $entity_id = $info[0];
//  // Redefine our info array.
//  $info = array(
//    'entity_id' => $entity_id,
//    'bundle' => $bundle,
//    'entity_type' => $type,
//    'entity' => $entity,
//  );
//
//  $enabled_entity = variable_get('time_tracker-' . $type . '-' . $bundle);
//  $enabled_entity_comments = variable_get('time_tracker_comment-' . $type . '-' . $bundle);
//
//  if ($view_mode == 'full' && ($enabled_entity || $enabled_entity_comments)) {
//
//    if (variable_get('time_entry_disable_total_table', 0) != true) { /* Hide the Total and Table */
//      //$resource = db_query("SELECT * FROM {time_tracker_entry} WHERE nid = :nid", array(':nid' => $node->nid));
//      if (user_access('view own time tracker entries')) {
//        //$resource = db_query("SELECT * FROM {time_tracker_entry} WHERE nid = :nid", array(':nid' => $node->nid));
//        $query = new EntityFieldQuery();
//
//        $query->entityCondition('entity_type', 'time_tracker_entry')
//          ->propertyCondition('entity_type', $type)
//          ->propertyCondition('entity_id', $entity_id)
//          ->propertyCondition('entity_bundle', $bundle)
//          ->propertyCondition('uid', $user->uid);
//        $result = $query->execute();
//
//        if (isset($result['time_tracker_entry'])) {
//          $entry_ids = array_keys($result['time_tracker_entry']);
//          $entries = entity_load('time_tracker_entry', $entry_ids);
//        }
//      }
//      if (user_access('view all time tracker entries')) {
//        $query = new EntityFieldQuery();
//        $query->entityCondition('entity_type', 'time_tracker_entry')
//          ->propertyCondition('entity_type', $type)
//          ->propertyCondition('entity_id', $entity_id)
//          ->propertyCondition('entity_bundle', $bundle);
//        $result = $query->execute();
//
//        $totals = array();
//
//        if (isset($result['time_tracker_entry'])) {
//          $entry_ids = array_keys($result['time_tracker_entry']);
//          $entries = entity_load('time_tracker_entry', $entry_ids);
//
//          foreach ($entries as $entry) {
//            $totals[] = $entry->duration;
//          }
//          $total_time = array_sum($totals);
//
//          if ($total_time > 0) {
//            $entity->content['time_tracker']['time_tracker_summary'] = array(
//              '#theme' => 'time_tracker_summary',
//              '#total_time' => $total_time,
//            );
//          }
//        }
//      }
//      // Time tracker entry table
//      if (isset($entries)) {
//        $sort = variable_get('time_entry_table_sort', 'desc');
//        if ($sort == 'desc') {
//          usort($entries, "_time_tracker_sort_entries_desc");
//        }
//        else {
//          usort($entries, "_time_tracker_sort_entries_asc");
//        }
//        $list_position = variable_get('time_entry_list_position', 'above');
//        if ($list_position == 'above') {
//          $weight = 2;
//        }
//        else{
//          $weight = 10;
//        }
//        $entity->content['time_tracker']['entries'] = array(
//          '#theme' => 'time_tracker_time_entry_table',
//          '#time_entries' => $entries,
//          '#weight' => $weight,
//        );
//      }
//    }
//
//    if(user_access('add time tracker entries')){
//      $time_entity = entity_get_controller('time_tracker_entry')->create();
//      $entity->content['time_tracker']['entry_form'] = drupal_get_form('time_tracker_time_entry_form', $info, $time_entity);
//      $entity->content['time_tracker']['entry_form']['#weight'] = 5;
//    }
//  }
//  if(isset($entity->content['time_tracker'])){
//    drupal_add_library('system', 'drupal.collapse');
//    $collapsed = variable_get('time_tracker_default_collapsed', 0);
//    $entity->content['time_tracker'] += array(
//      '#type' => 'fieldset',
//      '#title' => t('Time Tracker'),
//      '#collapsible' => TRUE,
//      '#collapsed' => $collapsed,
//      '#attributes' => array(
//        'class' => array(
//          'collapsible',
//        ),
//      ),
//    );
//    if($collapsed){
//      $entity->content['time_tracker']['#attributes']['class'][] = 'collapsed';
//    }
//  }
//}
//
///**
// * Implements hook_form_FORM_ID_alter().
// */
//function time_tracker_form_comment_form_alter(&$form, &$form_state, $form_id) {
//  $node = $form['#node'];
//
//  $info = array(
//    'entity_id' => $node->nid,
//    'bundle' => $node->type,
//    'entity_type' => 'node',
//    'entity' => $node,
//    'comment_node' => $node->nid,
//  );
//  // Store the info array for other modules i.e. timer.
//  $form_state['time_tracker_info'] = $info;
//  if(user_access('add time tracker entries')){
//    if (variable_get('time_tracker_comments-node-' . $node->type)) {
//      // If this a comment edit form check for associated time trackings.
//      if (isset($form['cid']['#value'])) {
//        $query = new EntityFieldQuery();
//        $query->entityCondition('entity_type', 'time_tracker_entry')
//          ->propertyCondition('entity_type', 'node')
//          ->propertyCondition('comment_id', $form['cid']['#value']);
//        $result = $query->execute();
//
//        if (isset($result['time_tracker_entry'])) {
//          $entry_ids = array_keys($result['time_tracker_entry']);
//          $entries = entity_load('time_tracker_entry', $entry_ids);
//        }
//      }
//      if (isset($entries)) {
//        $time_entity = $entries[$entry_ids[0]];
//      }
//      // Otherwise create a new time entry entity.
//      else {
//        $time_entity = entity_get_controller('time_tracker_entry')->create();
//      }
//      // Merge in time entry form.
//      $form += time_tracker_time_entry_form($form, $form_state, $info, $time_entity);
//    }
//  }
//}
//
///**
// * Implements hook_comment_view().
// */
//function time_tracker_comment_view($comment, $view_mode, $langcode) {
//  if (user_access('view all time tracker entries') || (user_access('view own time tracker entries')
//      && $GLOBALS['user']->uid == $comment->uid)) {
//
//    // If this is a preview we won't have a cid yet.
//    if (empty($comment->cid)) {
//      $time_tracker_data = (object)$comment->time_tracker;
//      $node = node_load($comment->nid);
//    }
//    else {
//      $query = new EntityFieldQuery();
//      $query->entityCondition('entity_type', 'time_tracker_entry')
//        ->propertyCondition('entity_type', 'node')
//        ->propertyCondition('comment_id', $comment->cid);
//      $result = $query->execute();
//
//      if (isset($result['time_tracker_entry'])) {
//        $entry_ids = array_keys($result['time_tracker_entry']);
//        $entries = entity_load('time_tracker_entry', $entry_ids);
//        $time_tracker_data = $entries[$entry_ids[0]];
//      }
//    }
//    if (isset($time_tracker_data) && is_object($time_tracker_data)) {
//      // This will flag the comment so it can be hidden
//      // hide flag is triggered in the preprocess function below
//      $comment->status = 2;
//      $comment->content['time_tracker'] = array(
//        '#theme' => 'time_tracker_comment',
//        '#data' => $time_tracker_data,
//        '#weight' => $comment->content['comment_body']['#weight'] - 0.01,
//      );
//    }
//  }
//}
//
///**
// * Implements hook_comment_update().
// */
//function time_tracker_comment_update($comment) {
//  return time_tracker_comment_insert($comment);
//}
//
///**
// * Implements hook_comment_insert().
// */
//function time_tracker_comment_insert($comment) {
//
//  $bundle = isset($comment->entity_info['bundle']) ? $comment->entity_info['bundle'] : '';
//  $tracking = time_tracker_is_tracking_time('node', $bundle);
//  // Check if we are tracking time on this bundles comments. If not return.
//  $trackable = array(TIME_TRACKER_COMMENT, TIME_TRACKER_BOTH);
//  if(!in_array($tracking, $trackable)){
//    return;
//  }
//
//  $values = array(
//    'locked' => isset($comment->locked) ? $comment->locked : 0,
//    'billable' => isset($comment->billable) ? $comment->billable : NULL,
//    'billed' => isset($comment->billed) ? $comment->billed : NULL,
//  );
//
//  $rounding_interval = variable_get('time_tracker_rounding_interval', 0);
//  $rounding_operation = variable_get('time_tracker_rounding_operation', 'auto');
//  // Round the deductions if necessary
//  $entity = new stdClass();
//  $entity->deductions = isset($comment->deductions) ? _time_tracker_round(_time_tracker_parse_duration($comment->deductions), $rounding_interval / 60, $rounding_operation) : 0;
//  // Check First if we are tracking by duration or time interval
//  if (variable_get('time_entry_method', 'duration') == 'duration') {
//    $duration = _time_tracker_round(_time_tracker_parse_duration($comment->duration), $rounding_interval / 60, $rounding_operation);
//    if ($duration > 0) {
//      $entity->duration = $duration;
//      $entity->timestamp = strtotime($comment->time);
//      unset($comment->time);
//    }
//  }
//  elseif (variable_get('time_entry_method', 'duration') == 'interval') {
//    if (strlen($comment->start) && strlen($comment->end)) {
//      $entity->start = _time_tracker_round(strtotime($comment->start), $rounding_interval * 60, $rounding_operation);
//      $entity->end = _time_tracker_round(strtotime($comment->end), $rounding_interval * 60, $rounding_operation);
//      $entity->duration = _time_tracker_parse_duration(_time_tracker_convert_phptime_to_duration($comment->start, $comment->end));
//    }
//  }
//  if (!empty($comment->username)) {
//    $user = user_load_by_name($comment->username);
//  }
//  else {
//    global $user;
//  }
//
//  // The rest of the time entry
//  $entity->activity = $comment->activity;
//  $entity->uid = $user->uid;
//  $entity->entity_id = $comment->entity_info['entity_id'];
//  $entity->entity_type = $comment->entity_info['entity_type'];
//  $entity->entity_bundle = $comment->entity_info['bundle'];
//  $entity->comment_id = $comment->cid;
//  if (!empty($comment->teid)) {
//    $entity->teid = $comment->teid;
//  }
//  // If locked was in the form use it
//  $entity->locked = isset($values['locked']) ? $values['locked'] : 0;
//  // If billable was in the form use it
//  $entity->billable = isset($values['billable']) ? $values['billable'] : NULL;
//  // If billed was in the form use it
//  $entity->billed = isset($values['billed']) ? $values['billed'] : NULL;
//  // If billed was in the form use it
//  $entity = time_tracker_time_entry_save($entity);
//  if(module_exists('rules')){
//    $tracked_entity = $comment->entity_info['entity'];
//    $event = 'time_tracker_new_node-' . $comment->entity_info['bundle'] . '_entry';
//    rules_invoke_event($event, $user, $tracked_entity);
//  }
//}
//
//
///**
// * Implements hook_entity_delete().
// */
//function time_tracker_entity_delete($entity, $type) {
//  if ($type == 'comment') {
//    $query = new EntityFieldQuery();
//    $query->entityCondition('entity_type', 'time_tracker_entry')
//      ->propertyCondition('entity_type', 'node')
//      ->propertyCondition('comment_id', $entity->cid);
//    $result = $query->execute();
//
//    if (isset($result['time_tracker_entry'])) {
//      $entry_ids = array_keys($result['time_tracker_entry']);
//      $entries = entity_load('time_tracker_entry', $entry_ids);
//
//      time_tracker_entry_delete_multiple($entries);
//    }
//  }
//  else {
//    $info = entity_extract_ids($type, $entity);
//
//    $query = new EntityFieldQuery();
//    $query->entityCondition('entity_type', 'time_tracker_entry')
//      ->propertyCondition('entity_type', $type)
//      ->propertyCondition('entity_id', $info[0])
//      ->propertyCondition('entity_bundle', $info[2]);
//
//    $result = $query->execute();
//
//    if (isset($result['time_tracker_entry'])) {
//      $entry_ids = array_keys($result['time_tracker_entry']);
//      $entries = entity_load('time_tracker_entry', $entry_ids);
//
//      time_tracker_entry_delete_multiple($entries);
//    }
//  }
//}

/**
 * Form function to create an time_tracker_entry entity.
 *
 * The pattern is:
 * - Set up the form for the data that is specific to your
 *   entity: the columns of your base table.
 * - Call on the Field API to pull in the form elements
 *   for fields attached to the entity.
 */
function time_tracker_time_entry_form($form, &$form_state, $info = array(), $time_entry) {
   // Current user
  global $user;

  $time_tracker_data = $time_entry;

  // If we are editing a time entry, we need to define our info array.
  if ($info == 'edit') {
    $type = 'edit';
    $entity = $time_entry;

    $info = array(
      'entity_id' => $entity->entity_id,
      'bundle' => $entity->entity_bundle,
      'entity_type' => $entity->entity_type,
      'entity' => $entity,
      'comment_id' => $entity->comment_id,
    );
  }

  $form_state['time_tracker_info'] = $info;

  // Our base css for the form to put form elements side by side
  drupal_add_css(drupal_get_path('module', 'time_tracker') . '/css/time_tracker.css');

  // Our list of possible activities formatted as an option list for the select drop down
  $activities = _time_tracker_get_active_activities_options();

  // Assume it isn't locked for now.
  $lock = FALSE;

  if (isset($info['comment_node']) || isset($type)) {
    $form['time_tracker'] = array(
      '#type' => 'fieldset',
      '#title' => t('Time Tracker'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
  }
  // set up the entity
  $form['time_tracker']['entity_info'] = array(
    '#type' => 'value',
    '#value' => $info,
  );

  // Display a message about the rounding if it's on and messages are set to display
  if (variable_get('time_tracker_rounding_message', 0) && variable_get('time_tracker_rounding_interval', 0)) {
    $rounding_interval = variable_get('time_tracker_rounding_interval', 0);
    $rounding_operation = variable_get('time_tracker_rounding_operation', 'auto');
    if ($rounding_operation == 'auto') {
      $rounding_operation = 'rounded';
    }
    else {
      $rounding_operation = 'rounded ' . $rounding_operation;
    }
    $msg = t('Time rounding is ON. Time will be !rounded to the nearest !minutes minute interval.', array('!rounded' => $rounding_operation, '!minutes' => $rounding_interval));
    // Add it to the time_tracker fieldset
    $form['time_tracker']['#description'] = $msg;
  }

  // If there is a time tracker entry data object
  if (isset($time_tracker_data)) {
    // If it's an existing time entry, we need the teid
    $form['teid'] = array(
      '#type' => 'value',
      '#value' => isset($time_tracker_data->teid) ? $time_tracker_data->teid : '',
    );
    if (variable_get('allow_locked_time_entries', 0)) {
      if (user_access('administer time entries')) {
        $form['time_tracker']['locked'] = array(
          '#title' => t('Locked'),
          '#type' => 'checkbox',
          '#description' => 'Lock this time entry, preventing further editing',
          '#default_value' => isset($time_tracker_data->locked) ? TRUE : FALSE,
          '#weight' => 6,
        );
      }
      elseif (isset($time_tracker_data->locked)) {
        // We will use $lock throughout the form to determine whether or not
        // we should be hiding the form elements. We want to keep them intact,
        // however for the comments form by making their type 'value' because
        // we don't want to prevent the saving of comment forms directly, we
        // just want to prevent the changing of time entry data.
        $lock = TRUE;
        $msg = 'This time entry is locked from editing.';
        $form['time_tracker']['locked_msg'] = array(
          '#value' => $msg,
        );
        unset($form['time_tracker']['delete']);
      }
    }
  }
  if (isset($time_tracker_data->uid)) {
    $user_submit = user_load($time_tracker_data->uid);
  }
  // Auto complete/select user reference but only if you have permission
  $form['time_tracker']['uid'] = array(
    '#title' => t('User'),
    '#access' => user_access('administer time entries'),
    '#type' => $lock ? 'value' : variable_get('time_tracker_user_field_type', 'textfield'),
    '#weight' => 0,
    '#default_value' => isset($user_submit->name) ? $user_submit->name : $user->name,
  );

  if (variable_get('time_tracker_user_field_type', 'textfield') == 'select') {
    $users = db_query("SELECT uid, name FROM {users} WHERE uid > 0");

    foreach ($users as $user_info) {

      $options[$user_info->uid] = $user_info->name;
    }

    $form['time_tracker']['uid'] += array('#options' => $options);
    $form['time_tracker']['uid']['#weight'] -= 1;
  }
  else {
    $form['time_tracker']['uid'] += array(
      '#autocomplete_path' => 'user/autocomplete',
      '#size' => '15',
    );
  }

  // The activity choser
  $form['time_tracker']['activity'] = array(
    '#title' => t('Activity'),
    '#type' => $lock ? 'value' : 'select',
    '#weight' => 0,
    '#options' => $activities,
    '#required' => TRUE,
    '#default_value' => isset($time_tracker_data->activity) ? $time_tracker_data->activity : '',
  );

  // Add some javascript and css for the datepicker
  drupal_add_library('system', 'ui.datepicker');
  drupal_add_js(drupal_get_path('module', 'time_tracker') . '/js/datepicker.settings.js');

  // Insert different form elements depending on the time_entry_method
  if (variable_get('time_entry_method', 'duration') == 'duration') {
    $format = variable_get('timestamp_date_format', 'F d, Y');
    $form['time_tracker']['time_entry']['time'] = array(
      '#title' => t('Date'),
      '#type' => $lock ? 'value' : 'date_popup',
      '#date_format' => $format,
      '#date_label_position' => 'within',
      '#size' => 20,
      '#weight' => 0,
      '#required' => TRUE,
      '#default_value' => isset($time_tracker_data->timestamp) ? date(TIME_TRACKER_DEFAULT_DATE_FORMAT, $time_tracker_data->timestamp) : date(TIME_TRACKER_DEFAULT_DATE_FORMAT, time()) ,
    );
    $form['time_tracker']['time_entry']['duration'] = array(
      '#title' => t('Hours'),
      '#type' => $lock ? 'value' : 'textfield',
      '#size' => '10',
      '#weight' => 1,
      '#required' => TRUE,
      '#default_value' => isset($time_tracker_data->duration) ? _time_tracker_format_hours_to_hours_and_minutes($time_tracker_data->duration, TRUE) : '',
      '#description' => t('eg. 2.5 or 2:30 for two and a half hours'),
    );
  }
  else { // Time entry method is 'interval'
    $format = variable_get('time_interval_date_format', 'h:i A - M d, Y');
    $form['time_tracker']['time_entry']['prefix'] = array(
      '#value' => '<div class="time_entry">',
      '#weight' => 0,
    );
    $form['time_tracker']['time_entry']['start'] = array(
      '#title' => t('Start'),
      '#type' => $lock ? 'value' : 'date_popup',
      '#default_value' => isset($time_tracker_data->start) ? date(TIME_TRACKER_DEFAULT_DATE_FORMAT, $time_tracker_data->start) : date(TIME_TRACKER_DEFAULT_DATE_FORMAT, time()),
      '#date_format' => $format,
      '#date_label_position' => 'within',
      '#weight' => 1,
      '#required' => TRUE,
    );
    $form['time_tracker']['time_entry']['end'] = array(
      '#title' => t('End'),
      '#type' => $lock ? 'value' : 'date_popup',
      '#default_value' => isset($time_tracker_data->end) ? date(TIME_TRACKER_DEFAULT_DATE_FORMAT, $time_tracker_data->end) : date(TIME_TRACKER_DEFAULT_DATE_FORMAT, time()),
      '#date_format' => $format,
      '#date_label_position' => 'within',
      '#weight' => 2,
      '#required' => TRUE,
    );
    $form['time_tracker']['time_entry']['suffix'] = array(
      '#value' => '</div>',
      '#weight' => 3,
    );
    // If duration data exists already we post a note to the user
    if (isset($time_tracker_data)) {
      if (isset($time_tracker_data->duration) && !($time_tracker_data->end) && !($time_tracker_data->start)) {
        $form['time_tracker']['duration_msg'] = array(
          '#prefix' => '<div class="description"><em>',
          '#value' => t("A duration value exists for this time entry, but no Start and End time. <br/> Saving this entry with a Start and End time will overwrite the duration<br/> Logged Duration: <b>!duration</b>", array('!duration' => _time_tracker_format_hours_to_hours_and_minutes($time_tracker_data->duration))),
          '#suffix' => '</em></div>',
          '#weight' => 4,
        );
      }
    }
  }

  // Deductions
  if (variable_get('enable_deductions_field', 0)) {
    $form['time_tracker']['time_entry']['deductions'] = array(
      '#title' => t('Deductions'),
      '#type' => $lock ? 'value' : 'textfield',
      '#size' => '10',
      '#weight' => 3,
      '#default_value' => isset($time_tracker_data->deductions) ? _time_tracker_format_hours_to_hours_and_minutes($time_tracker_data->deductions, TRUE) : '',
      '#description' => t('eg. 2.5 or 2:30 for two and a half hours'),
    );
  }

  // Billable and Billed fields
  if (variable_get('enable_billable_field', 0)) {
    $form['time_tracker']['billable'] = array(
      '#title' => t('Billable'),
      '#type' => $lock ? 'value' : 'checkbox',
      '#size' => '10',
      '#weight' => 6,
      '#default_value' => isset($time_tracker_data->billable) ? $time_tracker_data->billable : '',
    );
  }
  if (variable_get('enable_billed_field', 0)) {
    $form['time_tracker']['billed'] = array(
      '#title' => t('Billed'),
      '#type' => $lock ? 'value' : 'checkbox',
      '#size' => '10',
      '#weight' => 6,
      '#default_value' => isset($time_tracker_data->billed) ? $time_tracker_data->billed : '',
    );
  }

  // If this isn't meant to track time on a comment, we need a submit button and notes field
  if (!isset($info['comment_node'])) {
    $form['time_tracker']['note'] = array(
      '#title' => t('Note'),
      '#type' => $lock ? 'value' : 'textarea',
      '#weight' => 7,
      '#rows' => 2,
      '#resizable' => FALSE,
      '#default_value' => isset($time_tracker_data->note) ? $time_tracker_data->note : '',
    );
  }

  field_attach_form('time_tracker_entry', $time_entry, $form, $form_state);

  if (!$lock) {
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save'),
      '#weight' => 5,
    );
    $form['actions']['delete'] = array(
      '#type' => 'submit',
      '#value' => t('Delete'),
      '#access' => user_access('delete own time tracker entries') || user_access('delete any time tracker entry') || user_access('administer time entries'),
      '#weight' => 10,
      '#submit' => array('time_tracker_entry_edit_delete'),
    );
  }


    //$form['#validate'][] = array('time_tracker_time_entry_form_validate');
    //$form['#submit'][] = array('time_tracker_time_entry_form_submit');

  return $form;
}


/**
 * Validation handler for time_tracker_entry_add_form form.
 * We pass things straight through to the Field API to handle validation
 * of the attached fields.
 */
function time_tracker_time_entry_form_validate($form, &$form_state) {
  $info = $form_state['values']['entity_info'];
  field_attach_form_validate('time_tracker_entry', $info['entity'], $form, $form_state);
  $values = $form_state['values'];
  // Check First if we are tracking by duration or time interval
  if (variable_get('time_entry_method', 'duration') == 'duration') {
    // If a duration is set, but no date
    if (strlen($values['duration']) && !strlen($values['time'])) {
      form_set_error('time', t('Invalid Date Value. You must also set a date if you wish to log time'));
    }
    // If a duration is set, but it is invalid
    if (strlen($values['duration']) && _time_tracker_parse_duration($values['duration']) === FALSE) {
      form_set_error('duration', t('Invalid duration value. You may enter time fractions such as 1.25 or hour and minute values such as 2:30.'));
    }
  }
  elseif (variable_get('time_entry_method', 'duration') == 'interval') {
    // If there is no start time, but there is an end time
    if (!strlen($values['start']) && strlen($values['end'])) {
      form_set_error('start][date', t('Enter an End time, or no time at all'));
    }
    // If there is no end time, but there is an start time
    if (!strlen($values['end']) && strlen($values['start'])) {
      form_set_error('end][date', t('Enter a Start time, or no time at all'));
    }
      // Make sure From date is before To Date
    if (strlen($values['start']) && strlen($values['end'])) {
      $start = strtotime($values['start']);
      $end = strtotime($values['end']);
      if ($start >= $end) {
        form_set_error('start][date', t('Invalid interval value. Start time must come before End time'));
      }
    }
  }
  elseif (isset($values['deductions'])) {
    // If a duration is set, but it is invalid
    if (strlen($values['deductions']) && _time_tracker_parse_duration($values['deductions']) === FALSE) {
      form_set_error('duration', t('Invalid deductions value. You may enter time fractions such as 1.25 or hour and minute values such as 2:30.'));
    }
  }

}

/**
 * Form submit handler: submits basic_add_form information
 */
function time_tracker_time_entry_form_submit($form, &$form_state) {
  // Our submitted form values
  $values = $form_state['values'];
  $entity = $form_state['values']['entity_info']['entity'];
  $info = $form_state['values']['entity_info'];
  field_attach_submit('time_tracker_entry', $entity, $form, $form_state);
  // Need a user
  if (!empty($values['uid'])) {
    if(is_numeric($values['uid'])){
      $user = user_load($values['uid']);
    }
    else{
      $user = user_load_by_name($values['uid']);
    }
  }
  else {
    global $user;
  }

  // Store the rounding data
  $rounding_interval = variable_get('time_tracker_rounding_interval', 0);
  $rounding_operation = variable_get('time_tracker_rounding_operation', 'auto');

  // Special handling based on the time entry method
  if (variable_get('time_entry_method', 'duration') == 'duration') {
    $entity->start = 0;
    $entity->end = 0;
    $entity->duration = _time_tracker_round(_time_tracker_parse_duration($values['duration']), $rounding_interval / 60, $rounding_operation);
    $entity->timestamp = strtotime($values['time']);
  }
  elseif (variable_get('time_entry_method', 'duration') == 'interval') {
    $entity->start = _time_tracker_round(strtotime($values['start']), $rounding_interval * 60, $rounding_operation);
    $entity->end =_time_tracker_round(strtotime($values['end']), $rounding_interval * 60, $rounding_operation);
    $entity->timestamp = time();
    $entity->duration = _time_tracker_parse_duration(_time_tracker_convert_phptime_to_duration($values['start'], $values['end']));
  }

  // The rest of the time entry
  $entity->activity = $values['activity'];
  $entity->uid = $user->uid;
  $entity->entity_id = $info['entity_id'];
  $entity->entity_type = $info['entity_type'];
  $entity->entity_bundle = $info['bundle'];
  if (isset($info['comment_id'])) {
    $entity->comment_id = $info['comment_id'];
  }
  // For comments, notes are not displayed. So we need to check if it exists.
  $entity->note = isset($values['note']) ? $values['note'] : NULL;

  // If locked was in the form use it
  $entity->locked = isset($values['locked']) ? $values['locked'] : 0;
  // If billable was in the form use it
  $entity->billable = isset($values['billable']) ? $values['billable'] : NULL;
  // If billed was in the form use it
  $entity->billed = isset($values['billed']) ? $values['billed'] : NULL;
  // If billed was in the form use it
  $entity->deductions = isset($values['deductions']) ? _time_tracker_round(_time_tracker_parse_duration($values['deductions']), $rounding_interval / 60, $rounding_operation) : 0;

  $entity = time_tracker_time_entry_save($entity);
  if(module_exists('rules')){
    $event = 'time_tracker_new_' . $info['entity_type'] . '-' . $info['bundle'] . '_entry';
    rules_invoke_event($event, $user, $info['entity']);
  }
  //$form_state['redirect'] = 'examples/entity_example/basic/' . $entity->basic_id;
  drupal_set_message(t('Time Entry Recorded'));
}

/**
 * Entry edit form delete button submit handler.
 */
function time_tracker_entry_edit_delete($form , &$form_state ) {
  $entity = $form_state['values']['entity_info']['entity'];
  $form_state['redirect'] = 'time_entry/delete/' . $entity->teid;
}

/**
 * Delete confirmation form.
 */
function time_tracker_entry_confirm_delete($form, &$form_state, $time_entry) {
  $tracked = entity_load($time_entry->entity_type, array($time_entry->entity_id));
  $tracked = array_shift($tracked);
  $user = user_load($time_entry->uid);
  $uri = entity_uri($time_entry->entity_type, $tracked);

  $info = array(
    'user' => $user,
    'tracked' => $tracked,
    'uri' => $uri,
    'entity' => $time_entry,
  );

  $form['info'] = array(
    '#type' => 'value',
    '#value' => $info,
  );
  $question = t('Are you sure you want to delete the time entry for %name on %title.',
    array('%name' => $user->name, '%title' => $tracked->title));
  $confirm = confirm_form($form, $question , $uri, '', t('Delete'));

  return  $confirm;
}

/**
 * Delete confirmation form submit handler.
 */
function time_tracker_entry_confirm_delete_submit($form , &$form_state) {
  $entity = $form_state['values']['info']['entity'];
  $uri = $form_state['values']['info']['uri'];
  $user = $form_state['values']['info']['user'];
  $tracked = $form_state['values']['info']['tracked'];

  time_tracker_entry_delete($entity);

  drupal_set_message(t('The time entry for %name on %title has been deleted.',
    array('%name' => $user->name, '%title' => $tracked->title))
  );
  $form_state['redirect'] = $uri;
}

/**
 * We create the entity by calling the controller.
 */
function time_tracker_time_entry_create() {
  return entity_get_controller('time_tracker_entry')->create();
}

/**
 * We save the entity by calling the controller.
 */
function time_tracker_time_entry_save(&$entity) {
  return entity_get_controller('time_tracker_entry')->save($entity);
}

/**
 * Use the controller to delete the entity.
 */
function time_tracker_entry_delete($entity) {
  entity_get_controller('time_tracker_entry')->delete($entity);
}

/**
 * Use the controller to delete multiple entries.
 */
function time_tracker_entry_delete_multiple($entities) {
  entity_get_controller('time_tracker_entry')->delete_multiple($entities);
}


/***********************************************************************
 * API FUNCTIONS
 ***********************************************************************/

/**
 * Returns all the time tracker entries for a node
 *
 * @param $nid
 *    The nid of the node to retrieve the time entries for
 *
 * @param $type
 *    Optional parameter to specify which time entries to return:
 *    'all': Returns all time entries
 *    'comment': Returns only comment based time entries
 *    'node': Returns only node based time entries
 *
 * @return
 *    Returns an array of time tracker entry objects
 */
function time_tracker_get_time_entries_for_node($nid, $type = 'all') {
  $time_entries = array();
  switch ($type) {
    case 'node':
      $sql = "SELECT * FROM {time_tracker_entry} AS t
              WHERE nid = %d AND cid = 0
              ORDER BY t.timestamp DESC, t.start DESC, t.teid DESC";
      // Get the entries associated with this node
      $resource = db_query($sql, $nid);
      // Store the db objects in an array for theme function
      while ($time_entry = $resource->fetchObject()) {
        $time_entries[] = $time_entry;
      }
      break;
    case 'comment':
      $sql = "SELECT * FROM {time_tracker_entry} AS t
              WHERE nid = %d AND cid <> 0
              ORDER BY t.timestamp DESC, t.start DESC, t.teid DESC";
      // Get the entries associated with this node
      $resource = db_query($sql, $nid);
      // Store the db objects in an array for theme function
      while ($time_entry = $resource->fetchObject()) {
        $time_entries[] = $time_entry;
      }
      breal;
    case 'all':
      $sql = "SELECT * FROM {time_tracker_entry} AS t
              WHERE nid = %d
              ORDER BY t.timestamp DESC, t.start DESC, t.teid DESC";
      // Get the entries associated with this node
      $resource = db_query($sql, $nid);
      // Store the db objects in an array for theme function
      while ($time_entry = $resource->fetchObject()) {
        $time_entries[] = $time_entry;
      }
      break;
  }
  return $time_entries;
}

/**
 * Returns the enabled time tracker fields
 *
 * @param $type
 *    Optional parameter to specify which time entries to return:
 *    'all': Returns all time entries
 *    'comment': Returns only comment based time entries
 *    'node': Returns only node based time entries
 *
 * @return
 *    Returns an array of time tracker entry objects
 */
function time_tracker_get_enabled_fields($type = 'node') {
  $enabled_fields = array();
  return $enabled_fields;
}

/**
 * Returns true if either we are tracking time on the case
 * either on the comments or the node
 *
 * @param $entity_type
 *    The entity type to check if we are tracking time on it
 *
 * @return
 *    Returns FALSE if we're not tracking time on this node
 *    type, otherwise returns a string of either 'both' 'node'
 *    or 'comment'
 */
function time_tracker_is_tracking_time($entity_type, $bundle) {
  $tracking = FALSE;
  if (variable_get('time_tracker-' . $entity_type . '-' . $bundle) && variable_get('time_tracker_comments-' . $entity_type . '-' . $bundle)) {
    $tracking = TIME_TRACKER_BOTH;
  }
  elseif (variable_get('time_tracker-' . $entity_type . '-' . $bundle)) {
    $tracking = TIME_TRACKER_ENTITY;
  }
  elseif (variable_get('time_tracker_comments-' . $entity_type . '-' . $bundle)) {
    $tracking = TIME_TRACKER_COMMENT;
  }
  return $tracking;
}

/**
 * Returns the activity name for a givent activity id
 *
 * @param $activity_id
 *    The id of the activity
 *
 * @return
 *    The name of the activity
 */
function _time_tracker_get_activity_name($activity_id) {
  $result = db_select('time_tracker_activity', 'tta')
      ->fields('tta')
      ->condition('taid', $activity_id, '=')
      ->execute()
      ->fetchAssoc();

  return $result['name'];
}

/**
 * Calculates the total logged time for a particular node
 *
 * @param $nid
 *    The nid of the node to calculate the total logged time
 *
 * @return
 *    Returns the total logged time ona node. Includes both
 *    comment and node based time entries
 */
function _time_tracker_get_total_logged_time($nid) {

  // Initialize the $total_time variable to 0
  $total_time = 0;

  // if this is a node form display the time tracker
  $resource = db_query("SELECT * FROM {time_tracker_entry} WHERE nid = :nid", array(':nid' => $nid));

  while ($time_entry = $resource->fetchObject()) {
    $total_time = $total_time + ($time_entry->duration - $time_entry->deductions);
  }

  return $total_time;
}


/***********************************************************************
 * HELPER FUNCTIONS -- Public
 ***********************************************************************/
/**
 * Formats hours (e.g. 4.5 hours) into an hours and minutes string
 *
 * @param $hours
 *    The amount of time to format in hours
 *
 * @param $clock_time
 *    Optional Parameter to format the output as clock time (hh:mm)
 *
 * @param $abbrev_hrs
 *    Optional parameter to abbreviate the text for 'hours' to 'hrs'
 *    Default is set to un-abbreviated
 *
 * @param $abbrev_mins
 *    Optional parameter to abbreviate the text for 'minutes' to 'mins'
 *    Default is set to abbreviated
 *
 * @param $sep
 *    Optional separator for the hours and minutes. Default is ', '
 *    e.g. 10 hours, 30 minutes
 *
 */
function _time_tracker_format_hours_to_hours_and_minutes($hours, $clock_time = FALSE, $abbrev_hrs = FALSE, $abbrev_mins = TRUE, $sep = ', ') {
  $hrs = floor($hours);
  $mins = round(($hours - $hrs) * 60);
  if ($mins == 60) {
    $mins = 0;
    $hrs++;
  }
  if ($clock_time) {
    if ($mins < 10) {
      $mins = '0' . $mins;
    }
    return $hrs . ':' . $mins;
  }
  else {
    // Format the 'hours' text
    if ($abbrev_hrs == TRUE) {
      $hours_str = format_plural($hrs, '1 hr', '@count hrs');
    }
    else {
      $hours_str = format_plural($hrs, '1 hour', '@count hours');
    }
    // Don't show minutes if there are none
    if ($mins == 0) {
      return $hours_str;
    }
    else {
      if ($abbrev_mins == TRUE) {
        $minutes_str = format_plural($mins, '1 min', '@count mins');
      }
      else {
        $minutes_str = format_plural($mins, '1 minute', '@count minute');
      }
      if ($hrs == 0) {
        return $minutes_str;
      }
      else {
        return $hours_str . ', ' . $minutes_str;
      }
    }
  }
}


/**
 * A function to calculate the difference between a start time
 * and a stop time and return a duration in the hh:mm format
 *
 * @param $start
 *    The start time, in seconds
 *
 * @param $stop
 *    The end time, in seconds
 */
function _time_tracker_convert_phptime_to_duration($start, $stop = 0) {
  if (is_numeric($start) && is_numeric($stop)){
    $start = $start;
    $stop = $stop;
  }
  else{
    $start = strtotime($start);
    $stop = strtotime($stop);
  }
  if ($stop == 0) {
    $duration = $start;
  }
  else {
    $duration = $stop - $start;
  }
  // floor() = Always round down
  if ($duration >= 3600) {
    $hours = sprintf('%02d', floor($duration / 3600));
    $mins = sprintf('%02d', floor(($duration % 3600) / 60));
    return $hours . ":" . $mins;
  }
  else {
    $mins = sprintf('%02d', ($duration/60));
    return '00:' . $mins;
  }
}

/**
 * Rounds numbers to a particular time interval.
 * Can accept any single format of time (i.e. seconds, minutes, hours)
 * However, the interval format should match the value format or the
 * rounding will not work as expected.
 *
 * EXAMPLES:
 *  1) If you are passing in a UNIX timestamp which is measured in seconds
 *     and you want to round it to the neares 15 minute interval, you will
 *     need to pass in 900 as the interval (15 mins * 60 secs/min = 900)
 *  2) If you are passing in time measured in (fracional) hours (e.g. 1.5
 *     for 1 hour and 30 minutes) and you want to round it to the nearest
 *     15 minute interval, you will need to pass in 0.25 as the interval
 *     (15 mins / 60 mins/hr = 0.25)
 *
 * @param $value
 *   The value to round
 *
 * @param $interval
 *   The interval to round to. See explanation above for specifics
 *
 * @param $operation
 *   The rounding operation to use. Accepted values are:
 *    'auto': Automatically rounds up or down, which ever is closest
 *    'down': Rounds down no matter what
 *    'up': Rounds up no matter what
 *
 * @return
 *   The rounded timestamp
 */
function _time_tracker_round($value, $interval, $operation = 'auto') {

  // Make sure it's a numeric value and the interval isn't 0
  if (!is_numeric($value) || $interval <= 0) {
    return $value;
  }

  switch ($operation) {
    case 'auto':
      $value = round($value / $interval) * $interval;
      break;
    case 'down':
      $value = floor($value / $interval) * $interval;
      break;
    case 'up':
      $value = ceil($value / $interval) * $interval;
      break;
  }

  return $value;
}

/**
 * Helper function to get the active activities
 * Formatted as a options list array for a select form element
 */
function _time_tracker_get_active_activities_options() {
  $activities = array();


  $query = new EntityFieldQuery();

  $query->entityCondition('entity_type', 'time_tracker_activity')
    ->propertyCondition('status', 1);
  $result = $query->execute();

  if (isset($result['time_tracker_activity'])) {
    $activity_ids = array_keys($result['time_tracker_activity']);
    $active_activities = entity_load('time_tracker_activity', $activity_ids);

    foreach ($active_activities as $activity) {
      $activities[$activity->taid] = $activity->name;
    }
  }

  return $activities;
}

/***********************************************************************
 * HELPER FUNCTIONS -- Private
 ***********************************************************************/

/**
 * Utility function that parses a duration string and converts it to fractional
 * hours.
 *
 * Durations can be specified as:
 *  * fractions: 1.25 = 1 hour, 15 minutes, or
 *  * clock times: 1:15 = 1 hour, 15 minutes
 *
 * @param string $input
 *   The user-entered duration value.
 * @return mixed
 *  Returns a time fraction, or FALSE if it could not parse the input.
 */
function _time_tracker_parse_duration($input) {
  $input = (string)$input;

  if (!strlen($input)) {
    return 0;
  }
  elseif (preg_match('/^(\d+)?:(\d+)$/', $input, $matches)) {
    list(, $hours, $minutes) = $matches;
    $hours += $minutes / 60;
  }
  elseif (preg_match('/^\d*(?:\.\d+)?$/', $input, $matches)) {
    $hours = $matches[0];
  }
  else {
    return FALSE;
  }

  return $hours;
}


/**
 * Sort array of time entries by timestamp descending
 */
function _time_tracker_sort_entries_desc($a, $b) {
  return strcmp($a->timestamp, $b->timestamp);
}

/**
 * Sort array of time entries by timestamp ascending
 */
function _time_tracker_sort_entries_asc($a, $b) {
  return strcmp($b->timestamp, $a->timestamp);
}


/**
 * Implements hook_action_info().
 */
function time_tracker_action_info() {
  return array(
    'time_tracker_billed_action' => array(
      'label' => t('Mark entries as billed'),
      'type' => 'time_tracker',
      'configurable' => FALSE,
      'triggers' => array('time_tracker_billed'),
    ),
  );
}

function time_tracker_billed_action($teids, $context) {
  foreach ($teids as $teid) {
    db_update('time_tracker_entry')
      ->fields(array(
        'billed' => 1,
      ))
      ->condition('teid', $teid, '=')
      ->execute();
  }
}

/**
 * Implements hook_theme().
 */
function time_tracker_theme() {
  return array(
    'time_tracker_time_entry_table' => array(
      'variables' => array('time_entries' => NULL),
      'file' => 'time_tracker.theme.inc',
    ),
    'time_tracker_comment' => array(
      'variables' => array('data' => NULL),
      'file' => 'time_tracker.theme.inc',
    ),
    'time_tracker_project_summary' => array(), // not in use
    'time_tracker_summary' => array(
      'variables' => array('total_time' => NULL),
      'file' => 'time_tracker.theme.inc',
    ),
    'time_tracker_activity_table' => array(
      'render element' => 'form',
      'file' => 'time_tracker.theme.inc',
    ),
  );
}

/**
 * Implements hook_ctools_plugin_api().
 */
function time_tracker_ctools_plugin_api($owner, $api) {
  if ($owner == 'feeds' && $api == 'plugins') {
    return array('version' => 1);
  }
}

/**
 * Implements hook_feeds_plugins().
 */
function time_tracker_feeds_plugins() {
  $info['TimeTrackerFeedsEntryProcessor'] = array(
    'name' => 'Time Tracker Entry processor',
    'description' => 'Create and update Time Tracker entries.',
    'help' => 'Create and update Time Tracker entries from parsed content.',
    'handler' => array(
      'parent' => 'FeedsProcessor',
      'class' => 'TimeTrackerFeedsEntryProcessor',
      'file' => 'TimeTrackerFeedsEntryProcessor.inc',
      'path' => drupal_get_path('module', 'time_tracker'),
    ),
  );
  return $info;
}
