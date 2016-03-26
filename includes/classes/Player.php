<?php

/**
 * Class Player
 *
 * @property string name
 * @property int    authLevel
 * @property int    capitalPlanetId
 * @property float  statPointsTotal
 *
 */
class Player extends UnitContainer {

  /**
   * Type of this location
   *
   * @var int $locationType
   */
  public static $locationType = LOC_USER;


  /**
   * Returns location's player owner ID
   *
   * @return int
   */
  public function getPlayerOwnerId() {
    return $this->_dbId;
  }


  public function setLocatedAt($location) {
    $this->locatedAt = $this;
  }









  // Inherited from DBRow
  /**
   * Table name in DB
   *
   * @var string
   */
  protected static $_table = 'users';
  /**
   * Name of ID field in DB
   *
   * @var string
   */
  protected static $_dbIdFieldName = 'id';
  /**
   * DB_ROW to Class translation scheme
   *
   * @var array
   */
  protected static $_properties = array(
    'dbId'            => array(
      P_DB_FIELD => 'id',
//      P_FUNC_INPUT => 'floatval',
    ),
    'name'            => array(
      P_DB_FIELD => 'username',
    ),
    'authLevel'       => array(
      P_DB_FIELD   => 'authlevel',
      P_FUNC_INPUT => 'intval',
    ),
    'statPointsTotal' => array(
      P_DB_FIELD   => 'total_points',
      P_FUNC_INPUT => 'floatval',
    ),
    'capitalPlanetId' => array(
      P_DB_FIELD => 'id_planet',
//      P_FUNC_INPUT => 'floatval',
    ),
  );


  // Innate properties
  /**
   * @var Bonus $player_bonus
   */
  public $player_bonus = null;
  /**
   * @var array
   */
  protected $db_row = array();

  protected $_name = '';
  protected $_authLevel = AUTH_LEVEL_REGISTERED;
  protected $_capitalPlanetId = 0;
  protected $_statPointsTotal = 0;

//  public $avatar = 0;
//  public $vacation = 0;
//  public $banaday = 0;
//  public $dark_matter = 0;
//  public $dark_matter_total = 0;
//  public $player_rpg_explore_xp = 0;
//  public $player_rpg_explore_level = 0;
//  public $ally_id = 0;
//  public $ally_tag = 0;
//  public $ally_name = 0;
//  public $ally_register_time = 0;
//  public $ally_rank_id = 0;
//  public $lvl_minier = 0;
//  public $xpminier = 0;
//  public $player_rpg_tech_xp = 0;
//  public $player_rpg_tech_level = 0;
//  public $lvl_raid = 0;
//  public $xpraid = 0;
//  public $raids = 0;
//  public $raidsloose = 0;
//  public $raidswin = 0;
//  public $new_message = 0;
//  public $mnl_alliance = 0;
//  public $mnl_joueur = 0;
//  public $mnl_attaque = 0;
//  public $mnl_spy = 0;
//  public $mnl_exploit = 0;
//  public $mnl_transport = 0;
//  public $mnl_expedition = 0;
//  public $mnl_buildlist = 0;
//  public $msg_admin = 0;
//  public $deltime = 0;
//  public $news_lastread = 0;
//  public $total_rank = 0;
//  public $password = 0;
//  public $salt = 0;
//  public $email = 0;
//  public $email_2 = 0;
//  public $lang = 0;
//  public $sign = 0;
//  public $galaxy = 0;
//  public $system = 0;
//  public $planet = 0;
//  public $current_planet = 0;
//  public $user_lastip = 0;
//  public $user_last_proxy = 0;
//  public $user_last_browser_id = 0;
//  public $register_time = 0;
//  public $onlinetime = 0;
//  public $que_processed = 0;
//  public $dpath = 0;
//  public $design = 0;
//  public $noipcheck = 0;
//  public $options = 0;
//  public $user_as_ally = 0;
//  public $metal = 0;
//  public $crystal = 0;
//  public $deuterium = 0;
//  public $user_birthday = 0;
//  public $user_birthday_celebrated = 0;
//  public $player_race = 0;
//  public $vacation_next = 0;
//  public $metamatter = 0;
//  public $metamatter_total = 0;
//  public $admin_protection = 0;
//  public $ip_int = 0;
//  public $user_bot = 0;
//  public $gender = 0;
//  public $immortal = 0;
//  public $parent_account_id = 0;
//  public $server_name = 0;
//  public $parent_account_global = 0;


  /**
   * Player constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->locatedAt = $this;
    $this->player_bonus = new Bonus();
  }

  public function isEmpty() {
    return false;
  }

  /**
   * @param $player_id
   */
  public function dbLoad($player_id) {
//    parent::dbLoad($dbId); // TODO: Uncomment when the stars be right
    $this->db_row = db_user_by_id($player_id, true);
    // Парсим инфу и загружаем юниты
    $this->dbRowParse($this->db_row);

//    // Загружаем юниты
//    $this->unitList->setLocatedAt($this);
//    $this->unitList->dbLoad($this->_dbId);

    // Высчитываем бонусы
    $this->player_bonus->add_unit_by_snid(MRC_ADMIRAL, mrc_get_level($this->db_row, null, MRC_ADMIRAL));
    $this->player_bonus->add_unit_by_snid(TECH_WEAPON, mrc_get_level($this->db_row, null, TECH_WEAPON));
    $this->player_bonus->add_unit_by_snid(TECH_SHIELD, mrc_get_level($this->db_row, null, TECH_SHIELD));
    $this->player_bonus->add_unit_by_snid(TECH_ARMOR, mrc_get_level($this->db_row, null, TECH_ARMOR));
  }

  /**
   * Меняет активную планету игрока на столицу, если активная планета равна $captured_planet_id
   *
   * @param int $captured_planet_id
   *
   * @return array|bool|mysqli_result|null
   */
  public function db_user_change_active_planet_to_capital($captured_planet_id) {
    $user_id = $this->_dbId;

    return doquery("UPDATE {{users}} SET `current_planet` = `id_planet` WHERE `id` = {$user_id} AND `current_planet` = {$captured_planet_id};");
  }

  public function calcColonyCount() {
    return $this->db_row[UNIT_PLAYER_COLONIES_CURRENT] = isset($this->db_row[UNIT_PLAYER_COLONIES_CURRENT]) ? $this->db_row[UNIT_PLAYER_COLONIES_CURRENT] : max(0, db_planet_count_by_type($this->db_row['id']) - 1);
  }

  /**
   * @param int $astrotech
   *
   * @return int|mixed
   */
  public function calcColonyMaxCount($astrotech = -1) {
    global $config;

    if($astrotech == -1) {
      if(!isset($this->db_row[UNIT_PLAYER_COLONIES_MAX])) {

        $expeditions = get_player_max_expeditons($this->db_row);
        $astrotech = mrc_get_level($this->db_row, null, TECH_ASTROTECH);
        $colonies = $astrotech - $expeditions;

        $this->db_row[UNIT_PLAYER_COLONIES_MAX] = classSupernova::$config->player_max_colonies < 0 ? $colonies : min(classSupernova::$config->player_max_colonies, $colonies);
      }

      return $this->db_row[UNIT_PLAYER_COLONIES_MAX];
    } else {
      $expeditions = get_player_max_expeditons($this->db_row, $astrotech);
      $colonies = $astrotech - $expeditions;

      return classSupernova::$config->player_max_colonies < 0 ? $colonies : min(classSupernova::$config->player_max_colonies, $colonies);
    }
  }


  /**
   * @return array
   */
  public function getDbRow() {
    return $this->db_row;
  }


  /**
   * @return string
   */
  protected function getName() {
    return $this->db_row['username'];
//    $player_name = $this->db_row['username'];
//
//    return $html_encoded ? htmlentities($player_name, ENT_COMPAT, 'UTF-8') : $player_name;
  }

  /**
   * @param string $name
   */
  protected function setName($name) {
    $this->db_row['username'] = $name;
    $this->_name = $name;
  }


}
