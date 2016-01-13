<?php

/**
 * @file
 * Contains \Drupal\core_test\Controller\QueryTestsController.
 */

namespace Drupal\core_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManager;

/**
 * Class QueryTestsController.
 *
 * @package Drupal\core_test\Controller
 */
class QueryTestsController extends ControllerBase {

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entity_type_manager;
  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManager $entity_type_manager) {
    $this->entity_type_manager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Allrevisionstest.
   *
   * @return string
   *   Return Hello string.
   */
  public function allRevisionsTest($search_str = 'test') {
    $query = $this->entityTypeManager()->getStorage('node')->getQuery('AND');
    $query->condition('field_tags.entity.name', $search_str);
    $query->allRevisions();
    $ids = $query->execute();
    debug($ids);
    return [
        '#type' => 'markup',
        '#markup' => $this->t('Revision ids returned: ' . implode(',', array_keys($ids))),
    ];
  }

  public function nonRevisionableTest() {
    $query = $this->entityTypeManager()->getStorage('user')->getQuery('AND');
    $query->condition('status', 1);
    $query->allRevisions();
    $ids = $query->execute();
    debug($ids);
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Revision ids returned: ' . implode(',',$ids)),
    ];
  }

}
