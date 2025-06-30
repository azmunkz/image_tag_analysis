<?php

namespace Drupal\image_tag_analysis\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Render\RendererInterface;

/**
 * Provides a 'Matched Products' block.
 *
 * @Block(
 *   id = "matched_products_block",
 *   admin_label = @Translation("Matched Products Block"),
 *   category = @Translation("Custom")
 * )
 */
class MatchedProductsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $entityTypeManager;
  protected $renderer;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entityTypeManager, RendererInterface $renderer) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
    $this->renderer = $renderer;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('renderer')
    );
  }

  public function build() {
    $route_match = \Drupal::routeMatch();
    $node = $route_match->getParameter('node');

    if (!$node instanceof Node || $node->bundle() !== 'article') {
      return [];
    }

    $items = [];

    // Try matched product tags
    $term_ids = [];
    if ($node->hasField('field_image_product_tags') && !$node->get('field_image_product_tags')->isEmpty()) {
      $term_ids = array_column($node->get('field_image_product_tags')->getValue(), 'target_id');
    }

    if (!empty($term_ids)) {
      // Load matched products
      $nids = $this->entityTypeManager->getStorage('node')->getQuery()
        ->accessCheck(TRUE)
        ->condition('type', 'product_catalog')
        ->condition('status', 1)
        ->condition('field_product_tags.target_id', $term_ids, 'IN')
        ->range(0, 10)
        ->sort('created', 'DESC')
        ->execute();

      $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
      \Drupal::logger('image_tag_analysis')->notice('🎯 Injecting matched_slider with @count matched items', ['@count' => count($nodes)]);
    }
    else {
      // Fallback products
      $fallback_ids = \Drupal::config('image_tag_analysis.settings')->get('fallback_products') ?? [];
      $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($fallback_ids);
      \Drupal::logger('image_tag_analysis')->notice('🎯 Injecting matched_slider with @count fallback items', ['@count' => count($nodes)]);
    }

    // Format for Twig
    foreach ($nodes as $product) {
      $title = $product->label();
      $store = $product->get('field_product_store_name')->value ?? 'N/A';
      $link_url = $product->get('field_product_external_link')->uri ?? $product->toUrl()->toString();
      $price = $product->hasField('field_product_price') && !$product->get('field_product_price')->isEmpty() ? number_format($product->get('field_product_price')->value, 2) : NULL;

      $image_markup = '';
      if (!$product->get('field_product_image')->isEmpty()) {
        $image_file = $product->get('field_product_image')->entity;
        $image_render_array = [
          '#theme' => 'image_style',
          '#style_name' => 'medium',
          '#uri' => $image_file->getFileUri(),
          '#alt' => $title,
        ];
        $image_markup = $this->renderer->render($image_render_array);
      }

      $items[] = [
        'image' => [
          '#type' => 'link',
          '#title' => ['#markup' => $image_markup],
          '#url' => \Drupal\Core\Url::fromUri($link_url),
          '#options' => ['attributes' => ['target' => '_blank']],
        ],
        'title' => [
          '#type' => 'link',
          '#title' => $title,
          '#url' => \Drupal\Core\Url::fromUri($link_url),
          '#options' => ['attributes' => ['target' => '_blank']],
        ],
        'store' => ['#markup' => $store],
        'price' => $price ? ['#markup' => 'RM' . $price] : NULL,
      ];
    }

    // Slider settings
    $config = \Drupal::config('image_tag_analysis.slider');
    $settings = [
      'itemsPerView' => (int) $config->get('items_per_view') ?? 3,
      'effect' => $config->get('slider_effect') ?? 'slide',
      'breakpoints' => [
        0 => ['slidesPerView' => (int) $config->get('slides_mobile') ?? 1],
        768 => ['slidesPerView' => (int) $config->get('slides_tablet') ?? 2],
        1024 => ['slidesPerView' => (int) $config->get('slides_desktop') ?? 3],
      ],
    ];

    return [
      '#theme' => 'matched_products_slider',
      '#items' => $items,
      '#attached' => [
        'library' => ['image_tag_analysis/slider'],
        'drupalSettings' => [
          'image_tag_analysis' => [
            'matched_slider_config' => $settings,
          ],
        ],
      ],
      '#cache' => [
        'tags' => [
          'node:' . $node->id(),
          'taxonomy_term_list',
          'config:image_tag_analysis.slider',
        ],
        'contexts' => ['url.path'],
        'max-age' => 0,
      ],
    ];
  }
}
