<?php

/**
 * Created by Gorlum 14.02.2017 11:18
 */
class BBCodeParser {

  /**
   * @var classConfig $gameConfig
   */
  protected static $gameConfig;

  /**
   * @var string[][] $bbCodeArray
   */
  protected static $bbCodeArray;
  /**
   * @var string[][] $smilesArray
   */
  protected static $smilesArray;

  public function __construct(\Common\GlobalContainer $gc) {

  }

  public static function _constructorStatic() {
    self::$bbCodeArray = &classSupernova::$design['bbcodes'];
    self::$smilesArray = &classSupernova::$design['smiles'];
    self::$gameConfig = classSupernova::$config;

    // Prefix faq:// resolves to FAQ's URL - if configured
    if (is_object(self::$gameConfig) && !empty(self::$gameConfig->url_faq)) {
      self::$bbCodeArray[AUTH_LEVEL_REGISTERED]['#faq://#isU'] = self::$gameConfig->url_faq;
    }
    self::$bbCodeArray = array_merge_recursive(self::$bbCodeArray, array(
      AUTH_LEVEL_REGISTERED => array(
        // Prefix sn:// resolves to current server URL
        '#sn://#isU'                                                                        => SN_ROOT_VIRTUAL,

        // [ube=ID] resolves to link to battle report
        '#\[ube\=([0-9a-zA-Z]{32})\]#isU'                                                     => "<a href=\"index.php?page=battle_report&cypher=$1\"><span class=\"battle_report_link link\">($1)</span></a>",
        // Battle report's URL from current server also resolves to special link
        "#" . SN_ROOT_VIRTUAL . "index.php?page=battle_report&cypher=([0-9a-zA-Z]{32})#isU"   => "<a href=\"index.php?page=battle_report&cypher=$1\"><span class=\"battle_report_link link\">($1)</span></a>",

        '#\[(c|color)=(white|cyan|yellow|green|pink|red|lime|maroon|orange)\](.+)\[/\1\]#isU' => "<span style=\"color: $2\">$3</span>",
        '#\[b\](.+)\[/b\]#isU'                                                                => "<b>$1</b>",
        '#\[i\](.+)\[/i\]#isU'                                                                => "<i>$1</i>",
        '#\[u\](.+)\[/u\]#isU'                                                                => '<span class="underline">$1</span>',
        '#\[s\](.+)\[/s\]#isU'                                                                => '<span class="strike">$1</span>',
      ),

      AUTH_LEVEL_ADMINISTRATOR => array(
        // Plain URL on string start
        "#^((?:ftp|https?|sn|faq)://[^\s\[]+)#i"                => "<a href=\"$1$2\" target=\"_blank\" class=\"link_external\">$1$2</a>",
        // Plain URL in the string
        "#([\s\)\]\}])((?:ftp|https?|sn|faq)://[^\s\[]+)#i"     => "$1<a href=\"$2$3\" target=\"_blank\" class=\"link_external\">$2$3</a>",

        // [urlw=URL]DESCRIPTION[urlw] - opens link in current window
        "#\[urlw=(ft|https?://)(.+)\](.+)\[/urlw\]#isU"         => "<a href=\"$1$2\" class=\"link\">$3</a>",
        // [url=URL]DESCRIPTION[url] - opens link in new window
        '#\[url=(ft|https?://)(.+)\](.+)\[/url\]#isU'           => "<a href=\"$1$2\" target=\"_blank\" class=\"link_external\">$3</a>",
        // Admins can use color codes and special PURPLE color
        '#\[(c|color)=(\#[0-9A-Fa-f]+|purple)\](.+)\[/\1\]#isU' => "<span style=\"color: $2\">$3</span>",
      ),
    ));

    self::$smilesArray = array_merge_recursive(self::$smilesArray, array(
      AUTH_LEVEL_REGISTERED => array(
        ':)'       => 'smile',
        ':p:'      => 'tongue',
        //':D' => 'lol',
        'rofl'     => 'rofl',
        ':wink:'   => 'wink',
        ':clap:'   => 'clapping',
        ':good:'   => 'good',
        ':yu:'     => 'yu',
        ':yahoo:'  => 'yahoo',
        ':diablo:' => 'diablo',
        ':angel:'  => 'angel',
        ':rose:'   => 'give_rose',

        ':blush:'   => 'blush',
        ':sorry:'   => 'sorry',
        ':cool:'    => 'cool',
        ':cool2:'   => 'dirol',
        ':quote:'   => 'pleasantry',
        ':shout:'   => 'shout',
        ':unknw:'   => 'unknw',
        ':ups:'     => 'pardon',
        ':nea:'     => 'nea',
        ':sarcasm:' => 'sarcasm',
        ':shok:'    => 'shok',
        ':blink:'   => 'blink',

        ':huh:' => 'huh',
        ':('    => 'mellow',
        ':sad:' => 'sad',
        ':c:'   => 'cray',

        ':bad:'   => 'bad',
        ':eye:'   => 'blackeye',
        ':bomb:'  => 'bomb',
        ':crz:'   => 'crazy',
        ':fool:'  => 'fool',
        //  ':wink:' => 'wink',
        ':tease:' => 'tease',

        ':spiteful:' => 'spiteful',
        ':agr:'      => 'aggressive',
        // ':tratata:' => 'mill',
        ':wall:'     => 'wall',
        ':suicide:'  => 'suicide',
        ':plushit:'  => 'plushit',

        ':fr:'      => 'friends',
        ':dr:'      => 'drinks',
        ':popcorn:' => 'popcorn',
        ':coctail:' => 'coctail',
        ':coffee:'  => 'coffee',

        ':accordion:' => 'accordion',
        ':hmm:'       => 'hmm',
        ':facepalm:'  => 'facepalm',
        ':ban:'       => 'ban',
        // ':bayan:' => 'bayan',
        ':censored:'  => 'censored',
        ':contract:'  => 'contract',
        ':help:'      => 'help',
        // ':maniac:' => 'maniac',
        ':panic:'     => 'panic',
        ':poke:'      => 'poke',
        ':pray:'      => 'pray',
        ':whistle:'   => 'whistle',
      ),
    ));
  }

  /**
   * Back-compatible parse function
   *
   * @param      $msg
   * @param int  $author_auth
   * @param int  $encodeOptions - HTML_ENCODE_xxx constants. HTML_ENCODE_MULTILINE by default
   *
   * @return mixed
   */
  public static function parseStatic($msg, $author_auth = AUTH_LEVEL_REGISTERED, $encodeOptions = HTML_ENCODE_MULTILINE) {
    $msg = HelperString::htmlEncode($msg, $encodeOptions);

    foreach (self::$bbCodeArray as $auth_level => $replaces) {
      if ($auth_level > $author_auth) {
        continue;
      }

      foreach ($replaces as $key => $html) {
        $msg = preg_replace('' . $key . '', $html, $msg);
      }
    }

    foreach (self::$smilesArray as $auth_level => $replaces) {
      if ($auth_level > $author_auth) {
        continue;
      }

      foreach ($replaces as $key => $imgName) {
        $msg = preg_replace("#" . addcslashes($key, '()[]{}') . "#isU", "<img src=\"design/images/smileys/" . $imgName . ".gif\" align=\"absmiddle\" title=\"" . $key . "\" alt=\"" . $key . "\">", $msg);
      }
    }

    return $msg;
  }

}
