<?php

namespace Drupal\image_tag_analysis\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImageTaggingController extends ControllerBase {

  /**
   * AJAX endpoint to run tagging.
   */
  public function runTagging($nid) {
    $account = $this->currentUser();
    if (!$account->hasPermission('administer nodes') && !$account->hasPermission('edit any product_catalog content')) {
      throw new AccessDeniedHttpException();
    }

    $node = Node::load($nid);
    if (!$node || $node->bundle() !== 'product_catalog') {
      return new JsonResponse(['status' => 'error', 'message' => 'Invalid node.']);
    }

    // Call tagging logic
    if (function_exists('_image_tag_analysis_process_node')) {
      _image_tag_analysis_process_node($node);
      $node->save();
      return new JsonResponse(['status' => 'success', 'message' => 'Tags generated successfully.']);
    }

    return new JsonResponse(['status' => 'error', 'message' => 'Tagging function not found.']);
  }

}
