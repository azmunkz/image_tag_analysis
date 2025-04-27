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

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('image_tag_analysis.settings')
      ->set('ai_prompt', $form_state->getValue('ai_prompt'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
