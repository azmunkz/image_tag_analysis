<?php

namespace Drupal\image_tag_analysis\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ImageTagAnalysisSettingsForm extends ConfigFormBase {

  public function getFormId() {
    return 'image_tag_analysis_settings_form';
  }

  protected function getEditableConfigNames() {
    return ['image_tag_analysis.settings'];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('image_tag_analysis.settings');

    $form['ai_prompt'] = [
      '#type' => 'textarea',
      '#title' => $this->t('AI Prompt'),
      '#default_value' => $config->get('ai_prompt'),
      '#description' => $this->t('Custom prompt to send to the AI image analysis model.'),
      '#rows' => 15,
    ];

    $form['cdn_domain'] = [
      '#type' => 'textfield',
      '#title' => $this->t('CDN Domain'),
      '#default_value' => $config->get('cdn_domain'),
      '#description' => $this->t('Enter the base URL of your CDN, e.g., https://d19352o69xmbxa.cloudfront.net'),
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('image_tag_analysis.settings')
      ->set('ai_prompt', $form_state->getValue('ai_prompt'))
      ->set('cdn_domain', $form_state->getValue('cdn_domain'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
