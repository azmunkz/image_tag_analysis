(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.fallbackThumbnails = {
    attach: function (context, settings) {
      const images = drupalSettings.image_tag_analysis?.fallback_images || {};
      $('.fallback-product-list .form-type-checkbox', context).once('fallback-rendered').each(function () {
        const input = $(this).find('input');
        const nid = input.val();
        const label = $(this).find('label');

        if (images[nid]) {
          const html = `
            <img src="${images[nid].image}" alt="${images[nid].title}" />
            <span>${images[nid].title}</span>
          `;
          label.html(html);
        }
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
