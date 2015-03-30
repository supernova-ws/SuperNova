var SN_SOUND_INIT = false;

function sn_sound_play(sound) {
  SN_SOUND_INIT && SN_SOUND_ENABLED ? ion.sound.play(sound) : false;
}

$(document).ready(function() {
  ion.sound({
    sounds: [
      {
        alias: "chat_message",
        name: "button_tiny",
      },
      {
        alias: "key_press",
        name: "snap",
      },
    ],
    path: "sounds/",
    multiplay: true,
    preload: true,
    volume: 1.0
  });

  SN_SOUND_INIT = true;
});
