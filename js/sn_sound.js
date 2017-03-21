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
      {
        alias: "halloween_success",
        name: "bell_ring",
        volume: 0.2,
      },
      {
        alias: "halloween_fail",
        name: "light_bulb_breaking",
        volume: 0.05,
      },
    ],
    path: "sounds/",
    multiplay: true,
    preload: true,
    allow_caching: true,
    volume: 1.0
  });

  SN_SOUND_INIT = true;
});
