!function(i){"use strict";Drupal.behaviors.menu={attach:function(s,e){var a="menu-item--expanded";i(s).find("#book-pages").once("menuAttached").each(function(){i("#book-pages .menu-item--children",s).click(function(e){"LI"===e.target.tagName&&(e.stopPropagation(),i(this).hasClass(a)?i(this).removeClass(a):(i(this).siblings().removeClass(a),i(this).addClass(a)))});var e=localStorage.getItem("atomizer_hide_unpublished");e&&"undefined"!=e&&"TRUE"==e&&i("#book-pages").removeClass("hide-unpublished"),i("#book-show-all").click(function(){i("#book-pages").hasClass("hide-unpublished")?(i("#book-pages").removeClass("hide-unpublished"),localStorage.setItem("atomizer_hide_unpublished","TRUE")):(i("#book-pages").addClass("hide-unpublished"),localStorage.setItem("atomizer_hide_unpublished","FALSE"))})})}}}(jQuery);
//# sourceMappingURL=maps/menu.js.map