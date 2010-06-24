<?php
function displayP($template){
  global $lang;

  foreach($template->parse as $key => $data)
    $template->assign_var($key, $data); // This is an example of assigning a template variable

  $template->assign_var('HELLO_WORLD', 'Hello world'); // This is an example of assigning a template variable

  $template->display('body');
  pr();
}

function parsetemplate ($template, $array) {
  if(is_object($template)){
    $template->parse = $array;
    return $template;
  }else{
    global $lang;

    $search[] = '#\{L_([a-z0-9\-_]*?)\[([a-z0-9\-_]*?)\]\}#Ssie';
    $replace[] = '( ( isset($lang[\'\1\'][\'\2\']) ) ? $lang[\'\1\'][\'\2\'] : \'\' );';
    $search[] = '#\{L_([a-z0-9\-_]*?)\}#Ssie';
    $replace[] = '( ( isset($lang[\'\1\']) ) ? $lang[\'\1\'] : \'\' );';
    $search[] = '#\{([a-z0-9\-_]*?)\}#Ssie';
    $replace[] = '( ( isset($array[\'\1\']) ) ? $array[\'\1\'] : \'\' );';

    return preg_replace($search, $replace, $template);
  /*
    $template = preg_replace('#\{L_([a-z0-9\-_]*?)\[([a-z0-9\-_]*?)\]\}#Ssie', '( ( isset($lang[\'\1\'][\'\2\']) ) ? $lang[\'\1\'][\'\2\'] : \'\' );', $template);
    $template = preg_replace('#\{L_([a-z0-9\-_]*?)\}#Ssie', '( ( isset($lang[\'\1\']) ) ? $lang[\'\1\'] : \'\' );', $template);
    return preg_replace('#\{([a-z0-9\-_]*?)\}#Ssie', '( ( isset($array[\'\1\']) ) ? $array[\'\1\'] : \'\' );', $template);
  */
  }
}

function gettemplate ($templatename, $isphpBB = false) {
  global $ugamela_root_path;

  $filename = $ugamela_root_path . TEMPLATE_DIR . TEMPLATE_NAME . '/' . $templatename . ".tpl";

  if($isphpBB){
    $template = new template();
    $template->set_custom_template('templates/'.TEMPLATE_NAME, TEMPLATE_NAME);

    $template->set_filenames(array(
        'body' => $templatename . ".tpl"
    ));

    return $template;
  }else{
    return ReadFromFile($filename);
  }
}
?>