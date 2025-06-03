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
    $form['cdn_domain'] = [
      '#type' => 'textfield',
      '#title' => $this->t('CDN Domain'),
      '#default_value' => $config->get('cdn_domain'),
      '#description' => $this->t('Enter the base URL of your CDN, e.g., https://d19352o69xmbxa.cloudfront.net'),
    ];

    $form['assistant_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('OpenAI Assistant ID'),
      '#default_value' => $config->get('assistant_id'),
      '#description' => $this->t('Enter the Assistant ID from your OpenAI dashboard (e.g., asst_abc123...).'),
      '#required' => TRUE,
    ];

    $form['article_assistant_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('OpenAI Assistant ID for Article Tagging'),
      '#default_value' => $config->get('article_assistant_id'),
      '#description' => $this->t('Paste your Assistant ID for article tagging (e.g., asst_abc123...).'),
      '#required' => TRUE,
    ];

    $form['max_tags'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum number of tags'),
      '#default_value' => $config->get('max_tags') ?? 5,
      '#min' => 1,
      '#max' => 50,
      '#description' => $this->t('Set the maximum number of tags to generate per image.'),
    ];

//    $form['ai_prompt'] = [
//      '#type' => 'textarea',
//      '#title' => $this->t('AI Prompt Product Image Analysis'),
//      '#default_value' => $config->get('ai_prompt'),
//      '#description' => $this->t('Custom prompt to send to the AI image analysis model.'),
//      '#rows' => 15,
//    ];
//
//    $form['article_prompt'] = [
//      '#type' => 'textarea',
//      '#title' => $this->t('AI Prompt for Article'),
//      '#default_value' => $config->get('article_prompt'),
//      '#description' => $this->t('Prompt used when analyzing article content to extract relevant product-related tags.'),
//      '#rows' => 10,
//    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('image_tag_analysis.settings')
      ->set('ai_prompt', $form_state->getValue('ai_prompt'))
      ->set('article_prompt', $form_state->getValue('article_prompt'))
      ->set('cdn_domain', $form_state->getValue('cdn_domain'))
      ->set('assistant_id', $form_state->getValue('assistant_id'))
      ->set('article_assistant_id', $form_state->getValue('article_assistant_id'))
      ->set('max_tags', $form_state->getValue('max_tags'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
