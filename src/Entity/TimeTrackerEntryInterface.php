<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 11.01.15
 * Time: 23:55
 */
namespace Drupal\time_tracker\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;

interface TimeTrackerEntryInterface extends ContentEntityInterface, EntityOwnerInterface {

}