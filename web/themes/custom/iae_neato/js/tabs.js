!function(c){"use strict";Drupal.behaviors.azTabs={attach:function(a,t){c(a).find(".az-tabs").once("az-attached").each(function(){var t=c(this).find(".tab");c(t[0]).addClass("active"),t.each(function(){c(this).click(function(a){t.removeClass("active"),c(this).addClass("active")})})})}}}(jQuery);
//# sourceMappingURL=maps/tabs.js.map
