// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

define([
  "jquery",
  "core/modal_factory",
  "core/str",
  "core/modal_events",
  "core/templates",
  "core/ajax"
], function (
  $,
  ModalFactory,
  Str,
  ModalEvents,
  Templates,
  Ajax
) {
  return {
    init: function () {
      var icon_pickerlink = $("[data-key=icon_picker]");
      var title = Str.get_string("iconselection", "local_dboard");
      var fetchMap = '';
      fetchMap = Ajax.call([
        {
          methodname: "core_output_load_fontawesome_icon_map",
          args: [],
        },
      ], true, false)[0];

      fetchMap.then(function (map) {
        var iconArray = [];
        var toarray = [];
        map.forEach(function (value) {
          if (toarray.indexOf(value.to) === -1) {
            Templates.renderPix(
              value.pix,
              value.component,
              value.component + "/" + value.pix
            ).then(function (result) {
              iconArray.push(result);
            });
          }
          toarray.push(value.to);
        });

        ModalFactory.create(
          {
            title: title,
            body: Templates.render("local_dboard/icon_picker_modal", {
              icons: iconArray,
            }),
            type: "SAVE_CANCEL",
          },
          icon_pickerlink
        ).then(function (modal) {
          modal.setLarge();
          modal.attachToDOM();
          var modalBody = $(".modal-body");
          var icons = modalBody.find(".icon");
          icons.on("click", function () {
            $(".choosed-icon").removeClass("choosed-icon");
            $(this).addClass("choosed-icon");
          });

          modal.getRoot().on(ModalEvents.save, function () {
            var selectedname = $(".choosed-icon").attr("title");
            var clone = $(".choosed-icon").clone();
            $(".selected_icon").replaceWith(clone);
            clone.addClass("selected_icon");
            clone.removeClass("choosed-icon");

            $('[name="icon"]').val(selectedname);
          });
        });
      });
    },
  };
});
