<?php
/**
 * Implementation of hook_form().
 */
function time_sheet_settings_form($form, &$form_state) {
  $form = array();
  $form['time_sheet_settings']['time_sheet_week_start'] = array(
    '#type' => 'select',
    '#title' => t('First day of week'),
    '#default_value' => variable_get('time_sheet_week_start', 'monday'),
    '#options' => array(
      'sunday' => t('Sunday'),
      'monday' => t('Monday'),
      'tuesday' => t('Tuesday'),
      'wednesday' => t('Wednesday'),
      'thursday' => t('Thursday'),
      'friday' => t('Friday'),
      'saturday' => t('Saturday'),
    ),
  );

  $form['time_sheet_settings']['time_sheet_days_of_week'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Days of the week'),
    '#default_value' => variable_get('time_sheet_days_of_week', array('monday', 'tuesday', 'wednesday', 'thursday', 'friday')),
    '#options' => array(
      'sunday' => t('Sunday'),
      'monday' => t('Monday'),
      'tuesday' => t('Tuesday'),
      'wednesday' => t('Wednesday'),
      'thursday' => t('Thursday'),
      'friday' => t('Friday'),
      'saturday' => t('Saturday'),
    ),
  );

  return system_settings_form($form);

}

