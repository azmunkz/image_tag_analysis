<?php

namespace Drupal\image_tag_analysis\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SliderSettingsForm extends ConfigFormBase
{

  public function getFormId()
  {
    return 'image_tag_analysis_slider_settings';
  }

  protected function getEditableConfigNames()
  {
    return ['image_tag_analysis.slider'];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('image_tag_analysis.slider');

    $form['items_per_view'] = [
      '#type' => 'number',
      '#title' => $this->t('Default items per view'),
      '#default_value' => $config->get('items_per_view') ?? 3,
      '#min' => 1,
      '#max' => 10,
      '#required' => TRUE,
    ];

    $form['slider_effect'] = [
      '#type' => 'select',
      '#title' => $this->t('Slider effect'),
      '#options' => [
        'slide' => 'Slide',
        'fade' => 'Fade',
        'cube' => 'Cube',
        'coverflow' => 'Coverflow',
        'flip' => 'Flip',
      ],
      '#default_value' => $config->get('slider_effect') ?? 'slide',
    ];

    $form['responsive'] = [
      '#type' => 'details',
      '#title' => $this->t('Responsive Breakpoints'),
      '#open' => TRUE,
    ];

    $form['responsive']['slides_desktop'] = [
      '#type' => 'number',
      '#title' => $this->t('Desktop (≥1024px)'),
      '#default_value' => $config->get('slides_desktop') ?? 3,
      '#min' => 1,
      '#max' => 10,
    ];

    $form['responsive']['slides_tablet'] = [
      '#type' => 'number',
      '#title' => $this->t('Tablet (≥768px)'),
      '#default_value' => $config->get('slides_tablet') ?? 2,
      '#min' => 1,
      '#max' => 10,
    ];

    $form['responsive']['slides_mobile'] = [
      '#type' => 'number',
      '#title' => $this->t('Mobile (<768px)'),
      '#default_value' => $config->get('slides_mobile') ?? 1,
      '#min' => 1,
      '#max' => 10,
    ];

    return parent::buildForm($form, $form_state) + $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('image_tag_analysis.slider')
      ->set('items_per_view', $form_state->getValue('items_per_view'))
      ->set('slider_effect', $form_state->getValue('slider_effect'))
      ->set('slides_desktop', $form_state->getValue('slides_desktop'))
      ->set('slides_tablet', $form_state->getValue('slides_tablet'))
      ->set('slides_mobile', $form_state->getValue('slides_mobile'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
