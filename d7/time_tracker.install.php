<?php
/**
 * @file
 * Install file for time_tracker.
 */
/**
 * Implements hook_schema().
 */
function time_tracker_schema() {
  $schema = array();

  $schema['time_tracker_entry'] = array(
    'fields' => array(
      'teid' => array(
        'type' => 'serial',
        'not null' => TRUE,
        'unsigned' => TRUE,
      ),
      'entity_type' => array(
        'type' => 'varchar',
        'length' => 100,
      ),
      'entity_bundle' => array(
        'type' => 'varchar',
        'length' => 100,
      ),
      'entity_id' => array(
        'type' => 'int',
        'not null' => TRUE,
        'unsigned' => TRUE,
        'default' => 0,
      ),
      'comment_id' => array(
        'type' => 'int',
        'not null' => TRUE,
        'unsigned' => TRUE,
        'default' => 0,
      ),
      'uid' => array(
        'type' => 'int',
        'not null' => TRUE,
        'unsigned' => TRUE,
        'default' => 0,
      ),
      'activity' => array(
        'type' => 'int',
        'unsigned' => TRUE,
        'default' => 0,
      ),
      'timestamp' => array(
        'type' => 'int',
        'not null' => TRUE,
        'default' => 0,
      ),
      'start' => array(
        'type' => 'int',
        'unsigned' => TRUE,
        'default' => 0,
      ),
      'end' => array(
        'type' => 'int',
        'unsigned' => TRUE,
        'default' => 0,
      ),
      'deductions' => array(
        'type' => 'float',
        'unsigned' => TRUE,
        'default' => 0,
      ),
      'duration' => array(
        'type' => 'float',
        'not null' => TRUE,
        'unsigned' => FALSE,
        'default' => 0,
      ),
      'note' => array(
        'type' => 'text',
      ),
      'locked' => array(
        'type' => 'int',
        'size' => 'tiny',
      ),
      'billable' => array(
        'type' => 'int',
        'size' => 'tiny',
      ),
      'billed' => array(
        'type' => 'int',
        'size' => 'tiny',
      ),
    ),
    'primary key' => array('teid'),
    'indexes' => array(
      'time_tracker_entry_entity_id' => array('entity_id'),
      'time_tracker_entry_uid' => array('uid'),
    ),
  );

  $schema['time_tracker_activity'] = array(
    'fields' => array(
      'taid' => array(
        'type' => 'serial',
        'not null' => TRUE,
        'unsigned' => TRUE,
      ),
      'name' => array(
        'type' => 'varchar',
        'length' => 100,
      ),
      'weight' => array(
        'type' => 'int',
        'not null' => TRUE,
      ),
      'status' => array(
        'type' => 'int',
        'size' => 'tiny',
      ),
    ),
    'primary key' => array('taid'),
  );

  return $schema;
}

/**
 * @file
 * Implements hook_install().
 */
function time_tracker_install() {

  db_update('system')
    ->fields(array(
      'weight' => -1,
    ))
    ->condition('name', 'time_tracker', '=')
    ->execute();

  drupal_set_message(t("Time Tracker has been installed successfully. To begin tracking time, you first must enable time tracking on at least one entity type. You can do so on time tracker's entity settings page at admin/config/time_tracker/entity_settings"));
}

/*
 * Implements hook_uninstall().
 *
 * At uninstall time we'll notify field.module that the entity was deleted
 * so that attached fields can be cleaned up.
 */
function time_tracker_uninstall() {
  field_attach_delete_bundle('time_tracker_entry', 'time_entry');
  field_attach_delete_bundle('time_tracker_activity', 'activity');
}

/**
 * Add new fields to table.
 */
function time_tracker_update_7000() {
  db_add_field('time_tracker_entry', 'entity_type', array(
    'type' => 'varchar',
    'length' => 100,
    'not null' => TRUE,
    'default' => ''
  ));

  db_add_field('time_tracker_entry', 'entity_bundle', array(
    'type' => 'varchar',
    'length' => 100,
    'not null' => TRUE,
    'default' => ''
  ));

  // Rename the nid column to entity_id.
  db_change_field('time_tracker_entry', 'nid', 'entity_id', array(
    'type' => 'int',
    'not null' => TRUE,
    'default' => 0,
  ));

  // Rename the nid column to entity_id.
  db_change_field('time_tracker_entry', 'cid', 'comment_id', array(
    'type' => 'int',
    'not null' => TRUE,
    'default' => 0,
  ));

  db_query('UPDATE {time_tracker_entry} SET entity_type = :node', array(':node' => 'node'));

  db_query('UPDATE {time_tracker_entry} tte SET tte.entity_bundle =(SELECT n.type
            FROM {node} n
            WHERE n.nid=tte.entity_id AND n.nid=tte.entity_id)');

  // Update the variables.
  $entity_types = entity_get_info();
  foreach ($entity_types as $key => $type) {
    foreach ($type['bundles'] as $bkey => $bundle) {
      if (variable_get('time_tracker_nodes_' . $bkey, 0)) {
        variable_set('time_tracker-' . $key . '-' . $bkey, 1);
      }
      if (variable_get('time_tracker_comments_' . $bkey, 0)) {
        variable_get('time_tracker_comments-' . $key . '-' . $bkey);
      }
    }
  }
}

/**
 * Add indexes to the time_tracker_entry table (to speed up queries on time entries).
 */
function time_tracker_update_7100() {
  db_add_index('time_tracker_entry', 'time_tracker_entry_entity_id', array('entity_id'));
  db_add_index('time_tracker_entry', 'time_tracker_entry_uid', array('uid'));
}
