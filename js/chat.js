/*
 * chat.js
 *
 4.0 copyright © 2009-2012 Gorlum for http://supernova.ws
   [!] Another rewrite
   [+] Chat is now incremental
 3.0 copyright (c) 2009 by Gorlum for http://supernova.ws
   [!] Full rewrite
   [!] Using jQuery for AJAX
   [+] Complies with PCG1
 * @version 2.0 by Gorlum for http://supernova.ws
 * @version 1.2 by Ihor
 * @version 1.0 copyright 2008 by e-Zobar for XNova
*/
var chat_refreshing = false;
var chat_last_message = 0;

function addSmiley(smiley)
{
  document.chat_form.msg.value += smiley;
  document.chat_form.msg.focus();
}

function addMessage()
{
  var message = document.chat_form.msg.value;
  if(!message)
  {
    return;
  }

  document.chat_form.msg.value = '';
  var color = document.getElementById("chat_color");
  color = color.options[color.selectedIndex].value;

  if(color)
  {
    message = "[c="+color+"]" + message + "[/c]";
  }

  jQuery.post("chat_add.php", {'ally': ally_id, 'message': message}, function(data)
    {
      showMessage();
    }
  );
}

function showMessage(norefresh)
{
  if(chat_refreshing)
  {
    return;
  }

  chat_refreshing = true;
  jQuery.post("chat_msg.php", {'ally': ally_id, 'last_message': chat_last_message}, function(data)
    {
      var shoutbox = document.getElementById('shoutbox');
      if(data.html)
      {
        chat_last_message = data.last_message;
        shoutbox.innerHTML += data.html;
        jQuery('#shoutbox').animate({scrollTop: jQuery('#shoutbox').prop('scrollHeight')}, 2000);
      }

      if(data.disable != undefined)
      {
        jQuery('#msg,#send,#chat_color').attr('disabled', 'disabled');
        jQuery('#chat_message_inputs, #chat_message_smiles').hide();
      }
      else
      {
        chat_refreshing = false;
        window.setTimeout(showMessage, 5000);
      }
    }, "json"
  );
}

jQuery(document).ready(function()
  {
    showMessage();
  }
);
