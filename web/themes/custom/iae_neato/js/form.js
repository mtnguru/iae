!function(a){"use strict";function n(t){a.trim(t.val()).length?t.addClass("populated").parent().addClass("populated"):t.removeClass("populated").parent().removeClass("populated")}Drupal.behaviors.az_form={attach:function(t){a(t).find("input");a(t).find("input").once("AzForm").each(function(){var t;n(t=a(this)),t.on("blur",function(){n(a(this))})})}}}(jQuery);
//# sourceMappingURL=maps/form.js.map
