(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.imageTaggingTrigger = {
    attach: function (context, settings) {
      const nid = drupalSettings.nid;
      const alreadyTagged = drupalSettings.imageTagAnalysisAlreadyTagged;
      if (!nid || alreadyTagged) return;

      const endpoint = '/image-tag-analysis/run-tagging/' + nid;
      const $message = $('<div class="image-tag-message">🧠 Tagging product image…</div>').prependTo('body');

      $.ajax({
        url: endpoint,
        method: 'GET',
        dataType: 'json',
        success: function (res) {
          if (res.status === 'success') {
            Drupal.message(res.message);
          } else {
            Drupal.message(res.message, 'error');
          }
          $message.remove();
        },
        error: function () {
          Drupal.message('Failed to process image tagging.', 'error');
          $message.remove();
        }
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
