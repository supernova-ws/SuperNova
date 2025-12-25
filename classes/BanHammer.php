<?php

/** Created by Gorlum 25.02.2025 13:16 */

/**
 *
 * MySQL: INET_ATON() -> string to int, INET_NTOA() -> int to string
 * PHP: ip2long -> string to int, long2ip() -> int to string
 */
class BanHammer {
  /** Ban record can be of any expirations */
  const EXPIRED_ANY = null;
  /** Ban record should NOT be active */
  const EXPIRED_NOT = false;
  /** Ban record should ALREADY be expired */
  const EXPIRED_ALREADY = true;

  /**
   * Get ban records for specified IPv4 address from ban table with appropriate status
   *
   * @param int|string $ipV4 IPv4 address to check. Can be ether string like `192.168.1.1` or integer like 3232235777 (int unsigned)
   * @param ?bool      $isExpired
   *
   * @return array[]|object[]
   */
  public static function getByIpV4($ipV4, $isExpired = self::EXPIRED_NOT) {
    $ipV4 = !is_numeric($ipV4) ? ip2long($ipV4) : (integer)$ipV4;

    $sqlArray = [
      "SELECT *, INET_NTOA(`ipv4_from`) as ipv4_from_string, INET_NTOA(`ipv4_to`) as ipv4_to_string
       FROM {{ban_ip}}
       WHERE ipv4_from <= $ipV4 AND ipv4_to >= $ipV4",
      $isExpired === self::EXPIRED_ANY ? ''
        : ('AND `expired_at` ' . ($isExpired === self::EXPIRED_NOT ? '>=' : '<') . ' NOW()'),
    ];

    return SN::$db->dbGetAll(implode(' ', $sqlArray));
  }

  /**
   * Check if specified IPv4 address is currently fit specified status
   *
   * @param int|string $ipV4      IPv4 address to check. Can be ether string like `192.168.1.1` or integer like 3232235777 (int unsigned)
   * @param ?bool      $isExpired @see BanHammer::EXPIRED_ANY EXPIRED_XXX class constants
   *
   * @return bool
   */
  public static function checkIpV4($ipV4, $isExpired = self::EXPIRED_NOT) {
    return !empty(static::getByIpV4($ipV4, $isExpired));
  }

}
