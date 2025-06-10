<?php

namespace Drupal\image_tag_analysis\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

class FallbackProductSelectionForm extends ConfigFormBase {

  public function getFormId() {
    return 'image_tag_analysis_fallback_product_form';
  }

  protected function getEditableConfigNames() {
    return ['image_tag_analysis.settings'];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('image_tag_analysis.settings');
    $selected_ids = $config->get('fallback_products') ?? [];

    // Ensure valid node IDs
    $selected_ids = array_map('intval', array_filter($selected_ids));
    $selected_nodes = Node::loadMultiple($selected_ids);

    // Autocomplete input
    $form['fallback_products'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Select fallback products (Max 5)'),
      '#target_type' => 'node',
      '#tags' => TRUE,
      '#selection_settings' => [
        'target_bundles' => ['product_catalog'],
      ],
      '#default_value' => [],
      '#description' => $this->t('You can select up to 5 fallback products.'),
      '#attributes' => [
        'data-limit' => 5,
        'class' => ['fallback-autocomplete'],
      ],
      '#attached' => [
        'library' => ['image_tag_analysis/fallback_autocomplete_preview'],
      ],
    ];

    // Hidden field to store final product IDs (after user removes checked ones)
    $form['fallback_product_ids'] = [
      '#type' => 'hidden',
      '#attributes' => ['id' => 'fallback-product-ids'],
    ];

    // Preview container with checkboxes
    $form['selected_preview'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'fallback-preview-container'],
    ];

    foreach ($selected_nodes as $node) {
      if (!$node->get('field_product_image')->isEmpty()) {
        $file = $node->get('field_product_image')->entity;
        $url = \Drupal::service('file_url_generator')->generateAbsoluteString($file->getFileUri());

        $form['selected_preview']['nid_' . $node->id()] = [
          '#type' => 'container',
          '#attributes' => [
            'class' => ['fallback-card'],
            'data-id' => $node->id(),
          ],
          'checkbox' => [
            '#type' => 'checkbox',
            '#title' => $this->t('Remove'),
            '#attributes' => [
              'class' => ['fallback-remove-checkbox'],
              'data-nid' => $node->id(),
            ],
          ],
          'image' => [
            '#markup' => '<img src="' . $url . '" class="fallback-thumb" />',
          ],
          'title' => [
            '#markup' => '<div class="fallback-title">' . $node->label() . '</div>',
          ],
        ];
      }
    }

    return parent::buildForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $raw_ids = $form_state->getValue('fallback_product_ids');
    $ids = array_filter(explode(',', $raw_ids));
    if (count($ids) > 5) {
      $form_state->setErrorByName('fallback_product_ids', $this->t('You can only save up to 5 fallback products.'));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $raw_ids = $form_state->getValue('fallback_product_ids');
    $ids = array_map('intval', array_filter(explode(',', $raw_ids)));

    \Drupal::logger('image_tag_analysis')->notice('✅ Final fallback product IDs saved: <pre>@ids</pre>', [
      '@ids' => print_r($ids, TRUE),
    ]);

    $this->config('image_tag_analysis.settings')
      ->set('fallback_products', $ids)
      ->save();

    parent::submitForm($form, $form_state);
  }

}
