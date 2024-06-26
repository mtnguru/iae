/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/
(function ($, Drupal, window) {
  Drupal.behaviors.MediaLibraryItemSelectionClaro = {
    attach: function attach() {
      if (!once('media-library-selection-info-claro_iae-event', 'html').length) {
        return;
      }
      $(window).on('dialog:aftercreate', function (event, dialog, $element, settings) {
        var moveCounter = function moveCounter($selectedCount, $buttonPane) {
          var $moveSelectedCount = $selectedCount.detach();
          $buttonPane.prepend($moveSelectedCount);
        };
        var $buttonPane = $element.closest('.media-library-widget-modal').find('.ui-dialog-buttonpane');
        if (!$buttonPane.length) {
          return;
        }
        var $selectedCount = $buttonPane.find('.js-media-library-selected-count');
        if ($selectedCount.length) {
          moveCounter($selectedCount, $buttonPane);
        } else {
          var selectedCountObserver = new MutationObserver(function () {
            var $selectedCountFind = $buttonPane.find('.js-media-library-selected-count');
            if ($selectedCountFind.length) {
              moveCounter($selectedCountFind, $buttonPane);
              selectedCountObserver.disconnect();
            }
          });
          selectedCountObserver.observe($buttonPane[0], {
            attributes: false,
            childList: true,
            characterData: false,
            subtree: true
          });
        }
      });
    }
  };
})(jQuery, Drupal, window);