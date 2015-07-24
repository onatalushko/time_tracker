<?php

/**
 * Class TimeTrackerFeedsEntryProcessor
 * Creates Time Tracker entries from feed items.
 */
class TimeTrackerFeedsEntryProcessor extends FeedsProcessor {

  public function configDefaults() {
    return array(
      'bundle' => 'time_tracker_entry',
      'mappings' => array(),
      'update_existing' => FEEDS_SKIP_EXISTING,
      'input_format' => NULL,
      'entity_bundle' => '',
      'skip_hash_check' => FALSE,
    );
  }

  public function configForm(&$form_state) {
    $info = $this->entityInfo();
    $form = array();

    $tokens = array('@entities' => strtolower($info['label plural']));

    $form['update_existing'] = array(
      '#type' => 'radios',
      '#title' => t('Update existing @entities', $tokens),
      '#description' =>
        t('Existing @entities will be determined using mappings that are a "unique target".', $tokens),
      '#options' => array(
        FEEDS_SKIP_EXISTING => t('Do not update existing @entities', $tokens),
        FEEDS_REPLACE_EXISTING => t('Replace existing @entities', $tokens),
        FEEDS_UPDATE_EXISTING => t('Update existing @entities', $tokens),
      ),
      '#default_value' => $this->config['update_existing'],
    );
    global $user;
    $formats = filter_formats($user);
    $format_options = array();
    foreach ($formats as $format) {
      $format_options[$format->format] = $format->name;
    }
    $form['input_format'] = array(
      '#type' => 'select',
      '#title' => t('Text format'),
      '#description' => t('Select the input format for the body field of the nodes to be created.'),
      '#options' => $format_options,
      '#default_value' => isset($this->config['input_format']) ? $this->config['input_format'] : 'plain_text',
      '#required' => TRUE,
    );
    $form['skip_hash_check'] = array(
      '#type' => 'checkbox',
      '#title' => t('Skip hash check'),
      '#description' => t('Force update of items even if item source data did not change.'),
      '#default_value' => $this->config['skip_hash_check'],
    );

    $entity_types = entity_get_info();
    $entity_options = array();
    foreach ($entity_types as $key => $type) {
      foreach ($type['bundles'] as $bkey => $bundle) {
        if (variable_get('time_tracker-' . $key . '-' . $bkey, FALSE)) {
          $entity_options[$key . '-' . $bkey] = $type['label'] . ': ' . $bundle['label'];
        }
      }
    }

    $form['entity_bundle'] = array(
      '#type' => 'select',
      '#title' => t('Entity Type & Bundle'),
      '#description' => t('Select the entity type and bundle for this instance. If you are attaching to comments, select nothing here.'),
      '#options' => $entity_options,
      '#default_value' => $this->config['entity_bundle'],
      '#empty_value' => '',
      '#required' => FALSE,
    );

    return $form;
  }

  /**
   * Define entity type.
   */
  public function entityType() {
    return 'time_tracker_entry';
  }

  /**
   * Implements parent::entityInfo().
   */
  protected function entityInfo() {
    $info = parent::entityInfo();
    $info['label plural'] = t('Entries');
    return $info;
  }

  /**
   * Creates a new entry in memory and returns it.
   */
  protected function newEntity(FeedsSource $source) {
    $entity = entity_get_controller('time_tracker_entry')->create();
    $this->log('import_config', '<pre>' . print_r($this->config, TRUE) . '</pre>');
    if ($this->config['entity_bundle'] != '') {
      list($target_entity, $target_bundle) = explode('-', $this->config['entity_bundle'], 2);
      $entity->entity_type = $target_entity;
      $entity->entity_bundle = $target_bundle;
    }
    return $entity;
  }

  /**
   * @param FeedsSource $source
   * @param $entity_id
   * @return bool|mixed
   */
  public function entityLoad(FeedsSource $source, $entity_id) {
    return time_tracker_time_entry_load($entity_id);
  }

  /**
   * @param FeedsSource $source
   * @param $target_item
   * @param $target_element
   * @param $value
   */
  public function setTargetElement(FeedsSource $source, $target_item, $target_element, $value) {
    switch ($target_element) {
      case  'entity_id:url':
        $entity_id = db_select('feeds_item', 'fi')
          ->fields('fi', array('entity_id'))
          ->condition('url', $value, '=')
          ->execute()
          ->fetchField();

        $target_item->entity_id = $entity_id;
        break;

      case 'entity_id:guid':
        $entity_id = db_select('feeds_item', 'fi')
          ->fields('fi', array('entity_id'))
          ->condition('guid', $value, '=')
          ->execute()
          ->fetchField();

        $target_item->entity_id = $entity_id;
        break;
      case 'comment_id:guid':
        $comment_id = db_select('feeds_comment_item', 'fci')
          ->fields('fci', array('cid'))
          ->condition('guid', $value, '=')
          ->execute()
          ->fetchField();

        $target_item->comment_id = $comment_id;
        break;
      default:
        parent::setTargetElement($source, $target_item, $target_element, $value);
        break;
    }
  }


  /**
   * Return available mapping targets.
   */
  public function getMappingTargets() {
    $targets = parent::getMappingTargets() + array(
      'teid' => array(
        'name' => t('Time Tracker Entry ID'),
        'description' => t('The ID of an entry. Use with caution.'),
        'optional_unique' => TRUE,
      ),
      'uid' => array(
        'name' => t('User ID'),
        'description' => t('The Drupal user ID of the activity author.'),
      ),
      'start' => array(
        'name' => t('Start Time'),
        'description' => t('You must use this in conjunction with End Time, and in exclusivity with Duration.'),
      ),
      'end' => array(
        'name' => t('End Time'),
        'description' => t('You must use this in conjunction with Start Time, and in exclusivity with Duration.'),
      ),
      'duration' => array(
        'name' => t('Duration'),
        'description' => t('You must use this in exclusivity with Start Time and End Time. The value saved here will not respect your rounding configuration.'),
      ),
      'timestamp' => array(
        'name' => t('Timestamp'),
        'description' => t('If using Duration, it is the day under which the entry is logged. If using Start & End, it is the time at which the entry was logged.'),
      ),
      'activity' => array(
        'name' => t('Activity'),
        'description' => t('The activity associated with this entry.'),
      ),
      'note' => array(
        'name' => t('Note'),
        'description' => t('The note attached to the entry.'),
      ),
      'locked' => array(
        'name' => t('Locked'),
        'description' => t('Set to TRUE if the entry is locked to prevent further editing.'),
      ),
      'billable' => array(
        'name' => t('Billable'),
        'description' => t('Set to TRUE if the entry is billable.'),
      ),
      'billed' => array(
        'name' => t('Billed'),
        'description' => t('Set to TRUE if the entry has been billed.'),
      ),
      'deductions' => array(
        'name' => t('Deduction Duration'),
        'description' => t('Time deduction to apply to the entry.'),
      ),
      'comment_id:guid' => array(
        'name' => t('Comment reference by Feeds Comment GUID'),
        'description' => t('Comment referenced by the entry matched by Feeds Comment GUID.'),
      ),
      'comment_id' => array(
        'name' => t('Comment referenced by Comment ID'),
        'description' => t('Comment referenced by the entry matched by Comment ID.'),
      ),
      'entity_id' => array(
        'name' => t('Entity referenced by Entity ID'),
        'description' => t('Entity referenced by its ID.'),
      ),
      'entity_id:url' => array(
        'name' => t('Entity referenced by Feeds URL'),
        'description' => t('Entity referenced by the entry matched by Feeds URL.'),
      ),
      'entity_id:guid' => array(
        'name' => t('Entity referencde by Feeds GUID'),
        'description' => t('Entity referenced by the entry matched by Feeds GUID.'),
      ),
    );

    // Let other modules expose mapping targets.
    self::loadMappers();
    feeds_alter('feeds_processor_targets', $targets, 'time_tracker_entry', 'time_tracker_entry');

    return $targets;
  }

  /**
   * Save an entity.
   *
   * @param $entity
   *   Entity to be saved.
   */
  protected function entitySave($entity) {
    $this->log('import_entity', '<pre>' . print_r($entity, TRUE) . '</pre>');
    time_tracker_time_entry_save($entity);
  }

  /**
   * Writes to feeds log.
   */
  public function log($type, $message, $variables = array(), $severity = WATCHDOG_NOTICE) {
    feeds_log($this->id, 0, $type, $message, $variables, $severity);
  }

  /**
   * Delete a series of entities.
   *
   * @param $entity_ids
   *   Array of unique identity ids to be deleted.
   */
  protected function entityDeleteMultiple($entity_ids) {
    entity_delete_multiple('time_tracker_entry', $entity_ids);
  }
}
