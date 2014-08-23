<?php

// Эти функции будут переписаны по добавлению инфы в БД

function pname_factory_production_field_name($factory_unit_id)
{
  return get_unit_param($factory_unit_id, P_NAME) . '_porcent';
}

function pname_resource_name($resource_id)
{
  return get_unit_param($resource_id, P_NAME);
}