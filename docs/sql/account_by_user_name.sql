SELECT
  gu.id,
  gu.username,
  gu.email,
  gac.*,
  ga.*
FROM `sn_users` AS gu
  LEFT JOIN sn_account_translate AS gac ON gac.user_id = gu.id
  LEFT JOIN sn_account AS ga ON ga.account_id = gac.provider_account_id
WHERE username = 'admin';