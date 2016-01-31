<?php

// Эти функции будут переписаны по добавлению инфы в БД

function pname_factory_production_field_name($factory_unit_id)
{
  return get_unit_param($factory_unit_id, P_NAME) . '_porcent';
}

/**
 * Return unit PNAME (DB field name)
 *
 * @param $resource_id
 *
 * @return mixed
 */
function pname_resource_name($resource_id)
{
  return get_unit_param($resource_id, P_NAME);
}