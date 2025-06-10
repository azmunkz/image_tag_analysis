<?php

namespace Drupal\image_tag_analysis\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;

class FallbackPreviewController extends ControllerBase {

  public function preview($nid) {
    $node = Node::load($nid);
    if ($node && $node->bundle() === 'product_catalog') {
      $image = '';
      if (!$node->get('field_product_image')->isEmpty()) {
        $file = $node->get('field_product_image')->entity;
        if ($file && $file->getFileUri()) {
          $image = \Drupal::service('file_url_generator')->generateAbsoluteString($file->getFileUri());
        }
      }

      return new JsonResponse([
        'nid' => $node->id(),
        'title' => $node->label(),
        'image' => $image,
      ]);
    }

    return new JsonResponse(['error' => 'Product not found'], 404);
  }
}
