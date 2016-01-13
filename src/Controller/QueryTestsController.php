<?php

/**
 * @file
 * Contains \Drupal\core_test\Controller\QueryTestsController.
 */

namespace Drupal\core_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\Query\QueryException;
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
   * Allrevisions test.
   *
   * Demonstrate error when
   * When using Entity Field Query to query a revisionable entity and calling:
   * <code>$query->allRevisions()</code>
   * If you set a condition on an entity reference where the target entity type DOES NOT support revisions then the you get a  fatal error form
   *
   * @return string
   *   Return Hello string.
   */
  public function allRevisionsTest($search_str = 'test') {

    $query = $this->entityTypeManager()->getStorage('node')->getQuery('AND');
    // Yes I know we should use this string directly
    // Since Terms are not revisionable this will currently through an error.
    $query->condition('field_tags.entity.name', $search_str);
    $query->allRevisions();
    $ids = $query->execute();
    debug($ids);
    return [
        '#type' => 'markup',
        '#markup' => $this->t('Revision ids returned: ' . implode(',', array_keys($ids))),
    ];
  }

  /**
   * Demonstrating expected error if allRevisions() is used on non-revisionable entity.
   * 
   * @return array
   */
  public function nonRevisionableTest() {
    $ids = [];
    try {
      $query = $this->entityTypeManager()->getStorage('user')->getQuery('AND');
      $query->condition('status', 1);
      $query->allRevisions();
      $ids = $query->execute();
      debug($ids);
    } catch (QueryException $qe) {
      return [
        '#type' => 'markup',
        '#markup' => $this->t('Expected error: '. $qe->getMessage()),
      ];
    }

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Revision ids returned: ' . implode(',',$ids)),
    ];
  }

}
