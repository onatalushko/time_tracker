<?php
/**
 * @file
 * Handler for Time Estimate remaining field.
 */

/**
 * Field handler to calculate the remaining time.
 */
class time_estimate_remaining_time extends views_handler_field_numeric {

  function query() {
    $this->ensure_my_table();
    $this->field_alias = $this->field;

  }

  function pre_render(&$values) {
    $entity_type = $this->table;
    $field = $this->view->base_field;

    foreach($values as $k => $value){
      $id = $value->{$field};

      $entries = db_query('SELECT * FROM {time_tracker_entry}
                         WHERE entity_type = :type AND entity_id = :id',
                         array(':type' => $entity_type, ':id' => $id))->fetchAll();

      $durations = array();
      foreach($entries as $entry){
        $durations[] = $entry->duration;
      }

      $total_duration = array_sum($durations);
      $time_estimate = $value->field_time_estimate[0]['raw']['value'];
      $value = $time_estimate - $total_duration;
      $values[$k]->{$this->field} = $value;
    }
  }
}
