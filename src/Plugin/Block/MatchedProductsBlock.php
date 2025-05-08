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

    \Drupal::logger('image_tag_analysis')->notice('🔧 MatchedProductsBlock STARTED');

    $current_route = \Drupal::routeMatch()->getRouteName();
    if (strpos($current_route, 'entity.node.canonical') === false) {
      return []; // only render on full node page
    }

    $node = \Drupal::routeMatch()->getParameter('node');
    if (!$node instanceof Node) {
      $nid = \Drupal::routeMatch()->getRawParameter('node');
      if (is_numeric($nid)) {
        $node = Node::load($nid);
      }
    }

    if (!$node instanceof Node || $node->bundle() !== 'article') {
      return [];
    }

    if (!$node->hasField('field_image_product_tags') || $node->get('field_image_product_tags')->isEmpty()) {
      return [];
    }

    $term_ids = array_column($node->get('field_image_product_tags')->getValue(), 'target_id');
    if (empty($term_ids)) {
      return [];
    }

    // Query Product Catalog
    $nids = $this->entityTypeManager->getStorage('node')->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'product_catalog')
      ->condition('status', 1)
      ->condition('field_product_tags.target_id', $term_ids, 'IN')
      ->range(0, 10)
      ->sort('created', 'DESC')
      ->execute();

    if (empty($nids)) {
      return [];
    }

    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
    $items = [];

    foreach ($nodes as $product) {
      $title = $product->label();
      //$url = $product->toUrl()->toString();
      $store = $product->hasField('field_product_store_name') && !$product->get('field_product_store_name')->isEmpty()
        ? $product->get('field_product_store_name')->value
        : 'N/A';

      $image_markup = '';
      if ($product->hasField('field_product_image') && !$product->get('field_product_image')->isEmpty()) {
        $image_file = $product->get('field_product_image')->entity;
        $image_render_array = [
          '#theme' => 'image_style',
          '#style_name' => 'medium',
          '#uri' => $image_file->getFileUri(),
          '#alt' => $title,
        ];
        $image_markup = \Drupal::service('renderer')->renderPlain($image_render_array);
      }

      $link_url = $product->hasField('field_product_external_link') && !$product->get('field_product_external_link')->isEmpty()
        ? $product->get('field_product_external_link')->uri
        : $product->toUrl()->toString();

      $image_render_array = [
        '#theme' => 'image_style',
        '#style_name' => 'medium',
        '#uri' => $image_file->getFileUri(),
        '#alt' => $title,
      ];

      $image_markup = $this->renderer->render($image_render_array);

      $image_link = [
        '#type' => 'link',
        '#title' => [
          '#markup' => $image_markup,
        ],
        '#url' => \Drupal\Core\Url::fromUri($link_url),
        '#options' => ['attributes' => ['target' => '_blank']],
      ];

      $items[] = [
        'image' => $image_link,
        'title' => [
          '#type' => 'link',
          '#title' => $title,
          '#url' => \Drupal\Core\Url::fromUri($link_url),
          '#options' => ['attributes' => ['target' => '_blank']],
        ],
        'store' => ['#markup' => $store],
      ];

    }

    \Drupal::logger('image_tag_analysis')->notice('📦 Prepared slider items: @count', [
      '@count' => count($items),
    ]);

    // Load settings from config
    $config = \Drupal::config('image_tag_analysis.slider');

    $settings = [
      'itemsPerView' => (int) $config->get('items_per_view') ?? 3,
      'effect' => $config->get('slider_effect') ?? 'slide',
      'breakpoints' => [
        1024 => ['slidesPerView' => (int) $config->get('slides_desktop') ?? 3],
        768 => ['slidesPerView' => (int) $config->get('slides_tablet') ?? 2],
        0 => ['slidesPerView' => (int) $config->get('slides_mobile') ?? 1],
      ],
    ];

    return [
      '#theme' => 'matched_products_slider',
      '#items' => $items,
      '#attached' => [
        'library' => ['image_tag_analysis/slider'],
        'drupalSettings' => [
          'image_tag_analysis' => [
            'slider' => $settings,
          ],
        ],
      ],
      '#cache' => [
        'tags' => ['node_list', 'taxonomy_term_list'],
        'contexts' => ['url.path'],
      ],
      'debug_text' => ['#markup' => '✅ Block rendered!'],
    ];
  }
}
