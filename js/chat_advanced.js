/*
 * chat_advanced.js
 *
 Copyright © 2009-2020 Gorlum for http://supernova.ws
*/
var chat_refreshing = false;
var chat_disabled = false;
var chat_last_message = 0;

jQuery(document).ready(function () {
  // Натягиваем скины на элементы смайлики
  jQuery("#chat_smile_point, #chat_color_button, #chat_color_select div, #chat_command_menu div:not(:first-child):not(:last-child)").button().addClass('ui-textfield');
  $('#chat_message_smiles2 div').addClass("ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-textfield ui-button-text").attr('role', 'button');

  if (!IS_HISTORY) {
    height = $('#shoutbox_cell').css('height');
    $('#shoutbox').css('height', height);

    $('#online_table').css('height', height);
    $('#chat_online_div').css('height', $('#chat_online_cell').css('height'));

    showMessage(true);

    $('#msg').focus();
  }
});

jQuery(document).on('keypress', '#msg', function (e) {
  if (e.ctrlKey && (e.keyCode == 13 || e.keyCode == 10)) {
    jQuery("#send").click();
  }
});

// Make player visible/invisible
jQuery(document).on('click', '#chat_player_invisible', function () {
  addMessage('/invisible ' + (jQuery('#chat_player_invisible').is(':checked') ? 'on' : 'off'));
});

// Click to show/hide online
jQuery(document).on('click', '#chat_online_dragger', function () {
  if ($("#chat_online_wrapper").is(":visible")) {
    $(".js_chat_side_panel").hide("fast");
    $("#chat_online_dragger").text("<<");
  } else {
    $(".js_chat_side_panel").show("fast");
    // $(".js_chat_side_panel").show("slide", { direction: "right" }, 1000); // Need effects installed
    $("#chat_online_div").css({"height": "100%"});
    $("#chat_online_dragger").text(">>");
  }
});

// On resize we need to show right panel if it was enlarged above limits and hide it if it was shrinked below limits
$(window).resize(function () {
  clearTimeout(window.resizedFinished);
  window.resizedFinished = setTimeout(function () {
    if ($("#chat_online_dragger").is(":visible")) {
      $("#chat_online_dragger").text("<<");
      if ($("#chat_online_wrapper").is(":visible")) {
        $(".js_chat_side_panel").hide();
      }
    } else {
      if ($("#chat_online_wrapper").is(":not(:visible)")) {
        $(".js_chat_side_panel").show("fast");
      }
    }
  }, 250);
});

// Click on simple nick in chat
jQuery(document).on('click', '.chat_nick_msg', function () {
  addSmiley("(" + jQuery(this).text() + ")");
});
// Click to whisper - whisper nick in chat or nick in online list
jQuery(document).on('click', '[safe_name]', function () {
  addSmiley("/w " + (jQuery(this).attr('safe_name') ? jQuery(this).attr('safe_name') : jQuery(this).html()) + " ", true);
});

// Tooltip for muted player - name and ban untils
jQuery(document).on('mouseenter', '[chat_muted]', function () {
  that = jQuery(this);
  tooltip = $('div#chat_muted_tooltip');

  if (tooltip.css('display') == 'none') {
    tooltip
      .html(L_chat_advanced_command_mute
        .replace('%1$s', that.parent().prev().html())
        .replace('%2$s', that.attr('chat_muted'))
        .replace('%3$s', that.attr('chat_mute_reason') ? L_chat_advanced_command_reason.replace('%s', '"' + that.attr('chat_mute_reason')) + '"' : ''))
      .css({
        'display': 'block',
        'position': 'absolute',
        'top': that.position().top + that.height(),
        'left': that.position().left - tooltip.width() - that.width()
      });
  } else {
    tooltip.css('display', 'none');
  }
});

// -------------------
// Chat Administration
// Unmute player
jQuery(document).on('click', '.chat_unmute', function () {
  addMessage('/unmute id ' + jQuery(this).parent().attr('player_id'));
  $('div#chat_muted_tooltip').css('display', 'none');
});
// Popup menu for ban and mute
jQuery(document).on('click', '.chat_ban,.chat_mute', function () {
  // addMessage('/ban id ' + jQuery(this).parent().attr('player_id') + ' 7d');
  that = jQuery(this);
  tooltip = $('div#chat_command_menu');

  if (tooltip.css('display') == 'none') {
    last_child = tooltip.find('div:last-child');
    last_child
      .find('span')
      .css('display', that.hasClass('chat_ban') ? 'inline' : 'none')
      .find('input:checkbox').attr("checked", "checked");

    last_child
      .find('input:text').val(that.hasClass('chat_ban') ? L_chat_advanced_online_banned_via_chat : '');

    tooltip
      .find('#chat_command')
      .html((that.hasClass('chat_ban') ? L_chat_advanced_online_ban : L_chat_advanced_online_mute).replace('%1$s', that.parent().next().html()));
    tooltip
      .attr('command', that.hasClass('chat_ban') ? 'ban' : 'mute')
      .attr('player_id', that.parent().attr('player_id'))
      .css({
        'display': 'block',
        'position': 'absolute',
        'top': that.position().top + that.height(),
        'left': that.position().left - that.width() - tooltip.width()
      });
  } else {
    tooltip.css('display', 'none');
  }
});
// Make ban or mute when command length selected
jQuery(document).on('click', '#chat_command_menu div:not(:first-child):not(:last-child)', function (e) {
  that = jQuery(this);
  command = that.parent().attr('command');
  menu = $('div#chat_command_menu');
  addMessage(
    '/' + command
    + ' id ' + that.parent().attr('player_id') + ' '
    + that.attr('interval') + (menu.find('input:checkbox').is('[checked]') ? '' : '!')
    + ' ' + menu.find('input:text').val());
  menu.css('display', 'none');
});
// Tooltip for unmute button
jQuery(document).on('mouseenter', '.chat_unmute', function () {
  that = jQuery(this);
  tooltip = $('div#chat_muted_tooltip');

  if (tooltip.css('display') == 'none') {
    tooltip
      .html(L_chat_advanced_online_unmute.replace('%1$s', that.parent().next().html()))
      .css({
        'display': 'block',
        'position': 'absolute',
        'top': that.position().top + that.height(),
        'left': that.position().left - tooltip.width() - that.width()
      });
  } else {
    tooltip.css('display', 'none');
  }
});

if (!SN_GOOGLE) {
  jQuery(document).on('mouseleave', '.chat_unmute, [chat_muted]', function () {
    $('div#chat_muted_tooltip').css('display', 'none');
  });
}

// -------------------------------
// Chat history buttons and select
jQuery(document).on('click change', '[chat_history_go]', function (e) {
  is_select = (that = jQuery(this))[0].tagName == 'SELECT';
  if (e.type == 'change' || !is_select) {
    document.location.assign("index.php?page=chat_msg&ally=" + ally_id + "&history=" + IS_HISTORY + "&sheet=" + (is_select ? that.val() : that.attr('chat_history_go')));
  }
});

// ----------------
// Chat smile popup
$(document).on('click', '#chat_smile_point', function (event) {
  tooltip = $('div#chat_message_smiles2');
  offset = $(this).offset();
  if (tooltip.css('display') == 'none') {
    tooltip.css({
      'display': 'block',
      'position': 'absolute',
      'top': offset.top - tooltip.height() - 15,
      'left': offset.left - tooltip.width() / 3
    });
  } else {
    tooltip.css('display', 'none');
  }
});
// Click on smile
jQuery(document).on('click', '[smile]', function () {
  addSmiley($(this).attr('smile'));
  $('div#chat_message_smiles2').css('display', 'none');
});
// Click NOT on smile
jQuery(document).on('click', '#chat_message_smiles2', function () {
  $(this).css('display', 'none');
});


// ----------------
// Chat color popup
$(document).on('click', '#chat_color_button', function () {
  tooltip = $('div#chat_color_select');
  offset = $(this).offset();
  if (tooltip.css('display') == 'none') {
    tooltip.css({
      'display': 'block',
      'position': 'absolute',
      'top': offset.top - tooltip.height() - 15,
      'left': offset.left - tooltip.width() / 3
    });
  } else {
    tooltip.css('display', 'none');
  }
});
//// Click on coloring button
jQuery(document).on('click', '#chat_color_select div', function () {
  $('#chat_color').val($(this).attr('color'));
  $("#chat_message_inputs #msg").css('color', $(this).attr('color'));
  $('div#chat_color_select').css('display', 'none');
});
// Click NOT on coloring button
jQuery(document).on('click', '#chat_color_select', function () {
  $(this).css('display', 'none');
});


function addSmiley(smiley, onStart) {
  jQuery('#chat_box #msg').val((onStart ? smiley : '') + jQuery('#chat_box #msg').val() + (onStart ? '' : smiley));
  document.chat_form.msg.focus();
}

function addMessage(message) {
  !message ? message = jQuery("#msg").val() : false;
  if (!message) {
    return;
  }

  // jQuery("#msg").focus().val(''); // focus

  jQuery
    .post(
      "index.php?page=chat_add",
      {'ally': ally_id, 'message': message, 'color': jQuery("#chat_color").val()}
    )
    .always(function () {
      showMessage();
    });

  jQuery("#msg").val('').focus();
}

function showMessage(initial) {
  if (chat_refreshing || chat_disabled) {
    return;
  }

  chat_refreshing = true;
  jQuery.post("index.php?page=chat_msg", {
      'page': 'chat_msg',
      'ally': ally_id,
      'last_message': chat_last_message
    }, function (data) {
      var focused_element = $("*:focus").get(0);

      // var return_focus = $("#msg:focus").length; // focus
      if (data.html) {
        // var shoutbox = document.getElementById('shoutbox');
        chat_last_message = data.last_message;
        // shoutbox.innerHTML += data.html;
        var shoutbox = jQuery('#shoutbox');
        shoutbox.html(shoutbox.html() + data.html);
        shoutbox.animate({scrollTop: shoutbox.prop('scrollHeight')}, 2000);
        if (initial !== true) {
          sn_sound_play("chat_message");
        }
      }

      if (data.users_total) {
        jQuery('.js_global_users_total').html(data.users_total);
      }
      if (data.users_online) {
        jQuery('.js_global_users_online').html(data.users_online);
      }

      if (data.online) {
        var onlinebox = document.getElementById('onlinebox');
        onlinebox.innerHTML = data.online;
        // TODO !!!!
        // jQuery('#onlinebox').animate({scrollTop: jQuery('#shoutbox').prop('scrollHeight')}, 0);
      }

      jQuery('#online_players').html(data.online_players);
      jQuery('#online_invisibles').html(data.online_invisibles);
      if (data.chat_player_invisible == 1) {
        jQuery('#chat_player_invisible').attr('checked', 'checked');
      } else {
        jQuery('#chat_player_invisible').removeAttr('checked');
      }

      if (data.disable != undefined) {
        jQuery('#msg,#send,#chat_color').attr('disabled', 'disabled');
        jQuery('#chat_message_inputs, #chat_message_smiles, #chat_online_wrapper').hide();
        jQuery('#chat_message_refresh').css('display', 'table-row');
        chat_disabled = true;
      } else {
        jQuery('#msg,#send,#chat_color').removeAttr('disabled');

        // if(return_focus) {
        //   $('#msg').focus();
        // }
        if(focused_element) {
          focused_element.focus();
        }
        chat_refreshing = false;
        window.setTimeout(showMessage, chat_refresh_rate);
      }
    },
    "json")
    .always(function (data) {
      if (!chat_disabled) {
        window.setTimeout(showMessage, chat_refresh_rate);
      }
    });
}
