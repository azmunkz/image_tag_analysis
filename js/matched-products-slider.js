(function (Drupal, once, drupalSettings) {
  Drupal.behaviors.matchedProductsSlider = {
    attach: function (context) {
      once('matched-products-slider', '.matched-products-swiper', context).forEach(function (swiperEl) {
        const sliderSettings = drupalSettings.image_tag_analysis?.matched_slider_config || {};

        console.log('🧩 Loaded slider settings:', sliderSettings);

        new Swiper(swiperEl, {
          slidesPerView: sliderSettings.itemsPerView || 3,
          spaceBetween: 30,
          effect: sliderSettings.effect || 'slide',
          navigation: {
            nextEl: swiperEl.querySelector('.swiper-button-next'),
            prevEl: swiperEl.querySelector('.swiper-button-prev'),
          },
          breakpoints: sliderSettings.breakpoints || {},
        });
      });
    }
  };
})(Drupal, once, drupalSettings);
