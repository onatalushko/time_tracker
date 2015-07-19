<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 12.01.15
 * Time: 0:49
 */

namespace Drupal\time_tracker\Entity;


use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TimeTrackerEntryStorage extends SqlContentEntityStorage implements TimeTrackerEntryStorageInterface {

} 