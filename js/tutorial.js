/**
 * Created by Gorlum on 12.02.2017.
 */

const TUTORIAL_BLOCK = "tutorial";
const TUTORIAL_ID = "id";
const TUTORIAL_CONTENT = "content";
const TUTORIAL_TITLE = "title";
const TUTORIAL_NEXT = "next";
const TUTORIAL_PREV = "prev";
const TUTORIAL_COOKIE_NAME = "tutorial";

var tutorial = [];
var tutorial_window;
var tutorial_windowed = Cookies.getJSON(TUTORIAL_COOKIE_NAME);
if (typeof tutorial_windowed === "undefined") {
  Cookies.set(TUTORIAL_COOKIE_NAME, tutorial_windowed = {windowed: 0}, {expires: 365});
}

var tutorialClose = function (e) {
  if (tutorial_windowed.windowed) {
    tutorial_window.dialog("close");
  } else {
    jQuery("#tutorial_block").addClass("hide");
  }
};


function tutorialError(errorText) {
  jQuery("#tutorial_footer").addClass("error").text(errorText).removeClass("hide");
}

function tutorialMakeAction(action, handler) {
  jQuery.get(
    SN_ROOT_VIRTUAL + "index.php?page=ajax&mode=tutorial&action=" + action + "&id=" + jQuery("#tutorial_current").val(),
    handler,
    "json"
  );
}

function tutorialGet(action, errorEmpty) {
  jQuery.get(
    SN_ROOT_VIRTUAL + "index.php?page=ajax&mode=tutorial&action=" + action + "&id=" + jQuery("#tutorial_current").val(),
    function (data) {
      if (data.hasOwnProperty(TUTORIAL_BLOCK) && data[TUTORIAL_BLOCK]) {
        tutorial = data[TUTORIAL_BLOCK];
        if (tutorial.hasOwnProperty(TUTORIAL_ID) && Math.intVal(tutorial[TUTORIAL_ID])) {
          tutorial_change();
          jQuery("#tutorial_footer").addClass("hide").removeClass("error");
        } else {
          tutorialError(errorEmpty);
        }
      } else {
        tutorialError(language.tutorial_error_load);
      }
    },
    "json"
  );
}

function tutorial_store_postion(element) {
  tutorial_windowed = {
    windowed: 1,
    position: {
      top: $(element).css("top"),
      left: $(element).css("left")
    }
  };
  Cookies.set(TUTORIAL_COOKIE_NAME, tutorial_windowed, {expires: 365});
}

function tutorial_change() {
  jQuery("#tutorial_current").val(tutorial[TUTORIAL_ID]);
  jQuery("#tutorial_text").html(tutorial[TUTORIAL_CONTENT]);
  jQuery("#tutorial_header_additional").html(" - " + tutorial[TUTORIAL_TITLE]);

  if (Math.intVal(tutorial[TUTORIAL_NEXT])) {
    jQuery("#tutorial_button_next").removeClass("hide");
    jQuery("#tutorial_button_finish").addClass("hide");
  } else {
    jQuery("#tutorial_button_next").addClass("hide");
    jQuery("#tutorial_button_finish").removeClass("hide");
  }

  if (Math.intVal(tutorial[TUTORIAL_PREV])) {
    jQuery("#tutorial_button_prev").removeClass("hide");
  } else {
    jQuery("#tutorial_button_prev").addClass("hide");
  }
}

function tutorial_window_switch(windowed, reload) {
  if (typeof windowed !== "undefined") {
    tutorial_windowed.windowed = Math.intVal(windowed);
  }

  tutorial_window = jQuery("#tutorial_block");

  if (tutorial_windowed.windowed) {
    // Removing border from tutorial block
    jQuery("#tutorial_container").removeClass("border_image_small");
    // Hiding in-navbar header
    jQuery("#tutorial_header").addClass("hide").removeClass("contFJ");
    // Switching buttons
    jQuery("#tutorial_button_window").addClass("hide");
    jQuery("#tutorial_button_window_off").removeClass("hide");

    var aPosition = {my: "right bottom", at: "right bottom"};
    // Moving dialog window - if there are stored coordinates
    if (typeof tutorial_windowed.position !== "undefined") {
      aPosition = {
        my: "left top",
        // at: "top+" + tutorial_windowed.position.top + " left+" + tutorial_windowed.position.left
        at: "left+" + tutorial_windowed.position.left + " top+" + tutorial_windowed.position.top
      };
    }

    // Dialog class
    tutorial_window.dialog({
      resizable: false,
      width: "auto",
      height: "auto",
      position: aPosition,
      "create": function (event) {
        $(event.target).dialog("widget")
        // Making window position stuck on screen
          .css({"position": "fixed"})
          // Making titlebar visible
          .find(".ui-dialog-titlebar").addClass("ui-dialog-titlebar-show tutorial_dialog_title");
      },
      "open": function (event) {
        var dialogElement = $(event.target).parent();

        // Moving tutorial header text to dialog title
        $("#tutorial_header_text").detach().appendTo(
          // Removing &nbsp; from header
          dialogElement.find(".ui-dialog-title").html("")
        );

        tutorial_store_postion($(event.target).parent());
      },
      "dragStop": function (event, ui) {
        tutorial_store_postion($(event.target).parent());
      }
    });
  } else {
    jQuery("#tutorial_button_window_off").addClass("hide");
    jQuery("#tutorial_button_window").removeClass("hide");
    tutorial_windowed.windowed = 0;
    Cookies.set(TUTORIAL_COOKIE_NAME, tutorial_windowed, {expires: 365});
    if (reload) {
      sn_reload();
    }
  }
}

jQuery(document).on("click", "#tutorial_close", tutorialClose);

jQuery(document).on("click", "#tutorial_button_next", function (e) {
  tutorialGet("ajaxNext", language.tutorial_error_next);
});

jQuery(document).on("click", "#tutorial_button_prev", function (e) {
  tutorialGet("ajaxPrev", language.tutorial_error_prev);
});

jQuery(document).on("click", "#tutorial_button_finish", function (e) {
  tutorialMakeAction("ajaxFinish", tutorialClose);
});

jQuery(document).on("click", "#tutorial_button_window,#tutorial_button_window_off", function (e) {
  tutorial_window_switch($(this).attr("data-windowed"), true);
});
