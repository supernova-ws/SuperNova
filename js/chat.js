/*
 * chat.js
 *
 3.0 copyright (c) 2009 by Gorlum for http://supernova.ws
   [!] Full rewrite
   [!] Using jQuery for AJAX
   [+] Complies with PCG1
 * @version 2.0 by Gorlum for http://supernova.ws
 * @version 1.2 by Ihor
 * @version 1.0 copyright 2008 by e-Zobar for XNova
*/
// Définition du pseudo
var chat_refreshing = false;

// Raccourcis des smileys
function addSmiley(smiley)
{
  document.chat_form.msg.value = document.chat_form.msg.value + smiley;
  document.chat_form.msg.focus();
}

function addMessage()
{
  var message = document.chat_form.msg.value;//.replace("&","%26");
  document.chat_form.msg.value = '';
  if(message)
  {
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
}

function showMessage(norefresh)
{
  if(!chat_refreshing)
  {
    chat_refreshing = true;
    jQuery.post("chat_msg.php", {'ally': ally_id}, function(data)
      {
        var shoutbox = document.getElementById('shoutbox');
        if(data == 'disable')
        {
          shoutbox.innerHTML = language['chat_timeout'];
          return;
        }
        else if(data)
        {
          shoutbox.innerHTML = data;
          shoutbox.scrollTop = shoutbox.scrollHeight - shoutbox.offsetHeight;
        }
        chat_refreshing = false;
        window.setTimeout(showMessage, 5000);
      }
    );
  }
}

jQuery(document).ready(function()
  {
    showMessage();
  }
);
