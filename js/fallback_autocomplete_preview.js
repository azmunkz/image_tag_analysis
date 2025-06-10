// (function ($, Drupal, drupalSettings, once) {
//   Drupal.behaviors.fallbackPreview = {
//     attach: function (context, settings) {
//       const inputElements = once('fallback-autocomplete-init', 'input.fallback-autocomplete', context);
//       inputElements.forEach(function (element) {
//         const $input = $(element);
//         const $container = $('#fallback-preview-container');
//         const $hidden = $('#fallback-product-ids');
//
//         // Helper: update hidden input with selected IDs
//         const updateHiddenField = () => {
//           const ids = [];
//           $container.find('.fallback-card').each(function () {
//             ids.push($(this).data('id'));
//           });
//           $hidden.val(ids.join(','));
//         };
//
//         $input.on('autocompleteselect', function (e, item) {
//           const nidMatch = item.item.value.match(/\((\d+)\)$/);
//           const nid = nidMatch ? nidMatch[1] : null;
//
//           if (!nid) return;
//
//           if ($container.find('[data-id="' + nid + '"]').length === 0) {
//             if ($container.find('.fallback-card').length >= 5) {
//               alert('Maximum 5 fallback products allowed.');
//               return;
//             }
//
//             $.get('/image-tag-analysis/fallback-preview/' + nid, function (data) {
//               const card = `
//                 <div class="fallback-card" data-id="${data.nid}">
//                   <img src="${data.image}" class="fallback-thumb" />
//                   <div class="fallback-title">${data.title}</div>
//                 </div>`;
//               $container.append(card);
//               updateHiddenField();
//             });
//           }
//         });
//
//         // On page load, make sure we sync hidden field
//         updateHiddenField();
//       });
//     }
//   };
// })(jQuery, Drupal, drupalSettings, once);

(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.fallbackPreview = {
    attach: function (context, settings) {
      const inputElements = once('fallback-autocomplete-init', 'input.fallback-autocomplete', context);
      const $container = $('#fallback-preview-container');
      const $hiddenInput = $('#fallback-product-ids');

      function updateFinalIds() {
        const allCards = $container.find('.fallback-card');
        const finalIds = [];

        allCards.each(function () {
          const $card = $(this);
          const nid = $card.data('id');
          const checkbox = $card.find('.fallback-remove-checkbox');

          if (!checkbox.is(':checked')) {
            finalIds.push(nid);
          }
        });

        $hiddenInput.val(finalIds.join(','));
      }

      inputElements.forEach(function (element) {
        const $input = $(element);

        // Autocomplete selection
        $input.on('autocompleteselect', function (e, item) {
          const nidMatch = item.item.value.match(/\((\d+)\)$/);
          const nid = nidMatch ? nidMatch[1] : null;
          if (!nid) return;

          // Prevent duplicates
          if ($container.find('[data-id="' + nid + '"]').length > 0) return;

          // Limit to 5
          const currentCount = $container.find('.fallback-card:not(:has(.fallback-remove-checkbox:checked))').length;
          if (currentCount >= 5) {
            alert('You can only select up to 5 fallback products.');
            return;
          }

          // Fetch preview
          $.get('/image-tag-analysis/fallback-preview/' + nid, function (data) {
            const card = `
              <div class="fallback-card" data-id="${data.nid}">
                <label class="fallback-remove-label">
                  <input type="checkbox" class="fallback-remove-checkbox" data-nid="${data.nid}" />
                  <span>Remove</span>
                </label>
                <img src="${data.image}" class="fallback-thumb" />
                <div class="fallback-title">${data.title}</div>
              </div>`;
            $container.append(card);
            updateFinalIds();
          });

          // Clear input after selection
          $input.val('');
        });
      });

      // Event: On checkbox change
      $(context).on('change', '.fallback-remove-checkbox', function () {
        updateFinalIds();
      });

      // First-time sync
      updateFinalIds();
    }
  };
})(jQuery, Drupal, drupalSettings, once);
