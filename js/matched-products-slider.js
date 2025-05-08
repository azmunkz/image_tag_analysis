(function (Drupal, once) {
  Drupal.behaviors.matchedProductsSlider = {
    attach: function (context, settings) {
      once('matched-products-slider', '.matched-products-swiper', context).forEach(function (swiperEl) {
        const sliderSettings = settings.image_tag_analysis?.slider || {};

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
})(Drupal, once);
