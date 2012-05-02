<?php

// ѕридумать какой статус должен быть у глобальных ответов, что бы не перекрывать статусы платежных систем
// ћожет добавить спецстатус "ќтвет системы платежа" и парсить дальше getMessage
define('SN_PAYMENT_REQUEST_ERROR_DM_AMOUNT', 1);
define('SN_PAYMENT_REQUEST_PAYLINK_UNSUPPORTED', 2);

include('common.' . substr(strrchr(__FILE__, '.'), 1));

$template = gettemplate('dark_matter', true);

$dm_amount_list = array(1000, 2000, 5000, 10000, 20000, 50000, 100000, 200000, 500000, 1000000);

$request = array(
  'dark_matter' => sys_get_param_float('dark_matter'),
);
$request['dark_matter'] = in_array($request['dark_matter'], $dm_amount_list) ? $request['dark_matter'] : 0;

$payment_module_valid = false;
$payment_module = sys_get_param_str('payment_module');
foreach($sn_module as $module_name => $module)
{//  continue;
  if(!is_object($module) || $module->manifest['package'] != 'payment' || !$module->manifest['active'])
  {
    continue;
  }

  lng_include($module_name, $module->manifest['root_relative']);

  $template->assign_block_vars('payment_module', array(
    'ID' => $module_name,
    'NAME' => $lang["module_{$module_name}_name"],
    'DESCRIPTION' => $lang["module_{$module_name}_description"],
  ));

  $payment_module_valid = $payment_module_valid || $module_name == $payment_module;
}
// If payment_module invalid - making it empty OR if there is only one payment_module - selecting it
$payment_module = $payment_module_valid ? $payment_module : (count($template->_tpldata['payment_module']) == 1 ? $template->_tpldata['payment_module'][0]['ID'] : '');

//debug($payment_module);

if($request['dark_matter'] && $payment_module && sys_get_param_str('payment_validate'))
{
  try
  {
    // Any possible errors about generating paylink should be raised in module!
    $pay_link = $sn_module[$payment_module]->compile_request($request);

    if(is_string($pay_link) && strpos($pay_link, 'http') === 0)
    {
      $template->assign_vars(array(
        'PAY_LINK' => $pay_link,
        'PAY_LINK_METHOD' => 'LINK',
      ));
    }
    elseif(is_array($pay_link) && in_array($pay_link['METHOD'], array('POST', 'GET')))
    {
      $template->assign_vars(array(
        'PAY_LINK' => $pay_link['URL'],
        'PAY_LINK_METHOD' => $pay_link['METHOD'],
      ));
      
      foreach($pay_link['DATA'] as $key => $value)
      {
        $template->assign_block_vars('pay_link_data', array(
          'FIELD' => $key,
          'VALUE' => $value,
        ));
      }
    }
    else
    {
      throw new exception($lang['pay_msg_request_paylink_unsupported'], SN_PAYMENT_REQUEST_PAYLINK_UNSUPPORTED);
    }
  }
  catch(exception $e)
  {
    $response = array(
      'result'  => $e->getCode(),
      'message' => $e->getMessage(),
    );
  }
}

foreach($dm_amount_list as $dm_amount)
{
  $template->assign_block_vars('dm_amount', array(
    'VALUE' => $dm_amount,
    'TEXT' => pretty_number($dm_amount),
  ));
}

$template->assign_vars(array(
  'URL_DARK_MATTER' => $config->url_dark_matter,
  'PAYMENT_MODULE' => $payment_module,
  'PAYMENT_MODULE_NAME' => $payment_module,
  'PAYMENT_MODULE_DESCRIPTION' => $payment_module,
  'DARK_MATTER' => (float)$request['dark_matter'],
  'DARK_MATTER_TEXT' => pretty_number($request['dark_matter']),
));

display(parsetemplate($template, $parse), $lang['sys_dark_matter']);

?>
