<?php
/**
 * @file
 * Contains \Drupal\time_tracker\Form\TimeTrackerSettingsForm.
 */

namespace Drupal\time_tracker\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;


class TimeTrackerSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'time_tracker_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['time_tracker.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('time_tracker.settings');
    // Basic Settings.
    $form['time_tracker_settings'] = array(
      '#type' => 'fieldset',
      '#title' => t("General Settings"),
    );
    $form['time_tracker_settings']['hide_comments'] = array(
      '#type' => 'checkbox',
      '#title' => t("Hide comments with time tracker data entirely if user does not have 'view all time tracker entries' or 'view own time tracker entries' permission"),
      '#description' => t("Checking this setting will hide any comments that have time tracking data on them from any users without the 'view all time tracker entries' or 'view own time tracker entries' permission"),
      '#default_value' => $config->get('hide_comments'),
    );
    $form['time_tracker_settings']['allow_locked_time_entries'] = array(
      '#type' => 'checkbox',
      '#title' => t("Allow locking of time entries"),
      '#description' => t("Checking this setting will allow users with the 'administer time entries' permission to lock time entries, preventing them from being editied."),
      '#default_value' => $config->get('allow_locked_time_entries'),
    );
    $form['time_tracker_settings']['enable_billable_field'] = array(
      '#type' => 'checkbox',
      '#title' => t("Enable the 'Billable' field"),
      '#description' => t("Checking this setting will enable a checkbox to flag time entries as billable"),
      '#default_value' => $config->get('enable_billable_field'),
    );
    $form['time_tracker_settings']['enable_billed_field'] = array(
      '#type' => 'checkbox',
      '#title' => t("Enable the 'Billed' field"),
      '#description' => t("Checking this setting will enable a checkbox to flag time entries as billed"),
      '#default_value' => $config->get('enable_billed_field'),
    );
    $form['time_tracker_settings']['enable_deductions_field'] = array(
      '#type' => 'checkbox',
      '#title' => t("Enable the 'Deductions' field"),
      '#description' => t("Checking this setting will enable a text field for logging time entry deductions. An example usage of this field would be for a time entry from 9:00am to 5:00pm with a deduction of 30 minutes for a lunch break."),
      '#default_value' => $config->get('enable_deductions_field'),
    );

    // Default settings for time tracker time entry and time display fieldsets.
    $form['time_tracker_fieldset_settings'] = array(
      '#type' => 'fieldset',
      '#title' => t("Fieldset Settings"),
    );
    $form['time_tracker_fieldset_settings']['time_tracker_default_collapsed'] = array(
      '#type' => 'checkbox',
      '#title' => t("Collapse time entry form by default"),
      '#description' => t("Checking this setting will collapse the time tracker time entry form by default"),
      '#default_value' => $config->get('time_tracker_default_collapsed'),
    );
    $form['time_tracker_fieldset_settings']['time_entry_table_default_collapsed'] = array(
      '#type' => 'checkbox',
      '#title' => t("Collapse time entry table by default"),
      '#description' => t("When tracking time on nodes, checking this setting will collapse the time tracker time entry table by default"),
      '#default_value' => $config->get('time_entry_table_default_collapsed'),
    );

    $form['time_tracker_fieldset_settings']['time_entry_disable_total_table'] = array(
      '#type' => 'checkbox',
      '#title' => t("Disable the table of time entries on the time entry form"),
      '#description' => t("Not displaying this table could improve page load times for systems with a large number of time entries"),
      '#default_value' => $config->get('time_entry_disable_total_table'),
    );

    $form['time_tracker_fieldset_settings']['time_entry_table_sort'] = array(
      '#type' => 'select',
      '#options' => array(
        'asc' => t('Ascending'),
        'desc' => t('Descending'),
      ),
      '#title' => t("Sort the list of time entries on an entity"),
      '#description' => t("This tell the list of entries on an entity how to sort."),
      '#default_value' => $config->get('time_entry_table_sort'),
    );

    $form['time_tracker_fieldset_settings']['time_entry_list_position'] = array(
      '#type' => 'select',
      '#options' => array(
        'above' => t('Above'),
        'below' => t('Below'),
      ),
      '#title' => t("Position of the entry list in relation to the entry form."),
      '#description' => t("Where does the list of entries show in relation to the actual form."),
      '#default_value' => $config->get('time_entry_list_position'),
    );

    // Default settings for time tracker time entry and time display fieldsets.
    $form['time_tracker_userfield_settings'] = array(
      '#type' => 'fieldset',
      '#title' => t("User Selection Field Settings"),
    );
    $form['time_tracker_userfield_settings']['time_tracker_user_field_type'] = array(
      '#type' => 'radios',
      '#title' => t('User field type'),
      '#description' => t('The type of field for the "user" field when logging time. Only users with the "administer time tracker" permission can view the user field'),
      '#options' => array(
        'textfield' => t('Autocomplete textfield'),
        'select' => t('Select box'),
      ),
      '#default_value' => $config->get('time_tracker_user_field_type'),
    );

    // Time entry settings (duration or interval).
    $form['time_entry_settings'] = array(
      '#type' => 'fieldset',
      '#title' => t("Time Entry Settings"),
    );
    $form['time_entry_settings']['time_entry_method'] = array(
      '#type' => 'radios',
      '#title' => t('Time Tracker Time Entry Method'),
      '#default_value' => $config->get('time_entry_method'),
      '#options' => array(
        'duration' => t('Duration'),
        'interval' => t('Time Interval (Start and End Times)'),
      ),
    );

    // Warn the user about switching between time duration and time interval time entries.
    $msg = $this->t('Note that any entries that have been entered using the <em>duration</em> method will not have start and end times saved in the database. Switching from <em>duration</em> to <em>interval</em> will not retroactively create start and end times for past time entires.');
    $form['time_entry_settings']['time_entry_message'] = array(
      '#markup' => $msg,
      '#prefix' => '<div class="description">',
      '#suffix' => '</div>',
    );
    $form['time_tracker_date_formats'] = array(
      '#type' => 'fieldset',
      '#title' => t('Time Tracker Date Formats'),
    );

    // Date formats.
    $msg = $this->t('Dates will be output using format_date(). Below, please specify PHP date format strings as required by <a href="http://php.net/manual/en/function.date.php">date()</a>. A backslash should be used before a character to avoid interpreting the character as part of a date format.');
    $form['time_tracker_date_formats']['time_entry_message'] = array(
      '#markup' => $msg,
      '#prefix' => '<div class="description">',
      '#suffix' => '</div>',
    );
    $form['time_tracker_date_formats']['time_interval_date_format'] = array(
      '#type' => 'textfield',
      '#title' => t('Time Interval Date Formats'),
      '#description' => t('The date format for displaying time interval start and end time entries. Default is @date', array('@date' => format_date(time(), 'custom', 'h:i A - M d, Y'))),
      '#default_value' => $config->get('time_interval_date_format'),
    );
    $form['time_tracker_date_formats']['timestamp_date_format'] = array(
      '#type' => 'textfield',
      '#title' => t('Timestamp Date Format'),
      '#description' => t('The date format for displaying time entry timestamps. Usually would be displayed without time, although time information is available if desired. Default is @date', array('@date' => format_date(time(), 'custom', 'F d, Y'))),
      '#default_value' => $config->get('timestamp_date_format'),
    );

    // Settings for time rounding.
    $form['time_rounding_settings'] = array(
      '#type' => 'fieldset',
      '#title' => t('Time Rounding Settings'),
    );
    $form['time_rounding_settings']['time_tracker_rounding_operation'] = array(
      '#type' => 'select',
      '#title' => t('Rounding Operation'),
      '#description' => t('The rounding operation to perform.'),
      '#default_value' => $config->get('time_tracker_rounding_operation'),
      '#options' => array(
        'auto' => 'Auto',
        'up' => 'Round Up',
        'down' => 'Round Down',
      ),
    );
    $form['time_rounding_settings']['time_tracker_rounding_interval'] = array(
      '#type' => 'select',
      '#title' => t('Rounding Interval'),
      '#description' => t('The interval (in minutes) to round time entries to. Choose 0 for no rounding'),
      '#default_value' => $config->get('time_tracker_rounding_interval'),
      '#options' => array(
        0 => '0',
        5 => '5',
        10 => '10',
        15 => '15',
        20 => '20',
        30 => '30',
        60 => '60 (hour)',
      ),
    );
    $form['time_rounding_settings']['time_tracker_rounding_message'] = array(
      '#type' => 'checkbox',
      '#title' => t("Display Time Rounding message on Time Entry Form"),
      '#description' => t("Checking this setting will show a message on the time entry form, informing users that their entries will be rounded upon saving."),
      '#default_value' => $config->get('time_tracker_rounding_message'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }
} 
