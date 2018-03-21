<?php
/**
 * Created by Gorlum 01.10.2017 13:12
 */

namespace Common\Tools;

use \classConfig;
use \SN;

class VersionCheckerDeprecated {
  /**
   * @var classConfig $config
   */
  protected $config;

  /**
   * @var int $versionCheckResult
   */
  public $versionCheckResult = SNC_VER_ERROR_CONNECT;

  /**
   * @var array $registerResult
   */
  public $registerResult = [];

  public static function performCheckVersion() {
    $that = new static();
    $that->checkVersion(SNC_MODE_VERSION_CHECK);
  }

  public static function handleCall() {
    $that = new static();

    $mode = sys_get_param_int('mode');
    $ajax = sys_get_param_int('ajax');

    $that->preventDualRegistration($mode, $ajax);

    $that->checkVersion($mode);

    $that->processRegistration($mode);

    if ($ajax) {
      define('IN_AJAX', true);
      print($that->versionCheckResult);
    }
  }

  public function __construct() {
    $this->config = SN::$config;
  }

  /**
   * @param int $mode - SNC_MODE_xxx constants
   */
  protected function checkVersion($mode) {
    $this->processContent(sn_get_url_contents($this->generateUrl($mode)));
  }

  /**
   * @param int $mode - SNC_MODE_xxx constants
   *
   * @return string
   */
  protected function generateUrl($mode, $debug = false) {
    $rootServerUrl = $debug ? 'http://localhost/supernova_site/' : 'http://supernova.ws/';
    $thisSiteUrl = $debug ? 'http://supernova.ws/' : SN_ROOT_VIRTUAL;

    $url =
      $rootServerUrl . 'version_check.php'
      . '?mode=' . $mode
      . '&db=' . DB_VERSION
      . '&release=' . SN_RELEASE
      . '&version=' . SN_VERSION
      . '&key=' . urlencode($this->config->server_updater_key)
      . '&id=' . urlencode($this->config->server_updater_id)
      . ($mode == SNC_MODE_REGISTER ? "&name=" . urlencode($this->config->game_name) . "&url=" . urlencode($thisSiteUrl) : '');

    return $url;
  }

  /**
   * @param $content
   */
  protected function processContent($content) {
    if (!$content) {
      $this->versionCheckResult = SNC_VER_ERROR_CONNECT;
    } else {
      if (($decoded = json_decode($content, true)) !== null) {
        $this->versionCheckResult = isset($decoded['version_check']) ? $decoded['version_check'] : SNC_VER_UNKNOWN_RESPONSE;
        $this->registerResult = is_array($decoded['site']) ? $decoded['site'] : [];
      } else {
        $this->versionCheckResult = $content;
      }
    }

    $this->config->pass()->server_updater_check_last = SN_TIME_NOW;
    $this->config->pass()->server_updater_check_result = $this->versionCheckResult;
  }

  /**
   * @param $mode
   * @param $ajax
   */
  protected function preventDualRegistration($mode, $ajax) {
    if ($mode == SNC_MODE_REGISTER && ($this->config->server_updater_key || $this->config->server_updater_id)) {
      if ($ajax) {
        print(SNC_VER_REGISTER_ERROR_REGISTERED);
      }
      die();
    }
  }

  /**
   * @param $mode
   */
  protected function processRegistration($mode) {
    if ($mode == SNC_MODE_REGISTER) {
      $this->versionCheckResult = isset($this->registerResult['result']) ? $this->registerResult['result'] : SNC_VER_UNKNOWN_RESPONSE;

      if ($this->registerResult['result'] == SNC_VER_REGISTER_REGISTERED && $this->registerResult['site_key'] && $this->registerResult['site_id']) {
        $this->config->pass()->server_updater_key = $this->registerResult['site_key'];
        $this->config->pass()->server_updater_id = $this->registerResult['site_id'];
      }
    }
  }

}
