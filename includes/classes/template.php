<?php
/*
    NOTE!!!
    This file contains the code that allows you to use phpBB's Template Engine outside
    of the phpBB framework in your own site. This is allowed because the phpBB code is
    released under the GPL license. Please read said license before using this code in
    any script you plan on releasing to make sure it's legal.

    This was not my idea, all I have done is consolidated all of the code into one file
    to make it easier on everyone else.
    The original creator of this idea was nanothree on phpBB.com
    Here's the topic: http://www.phpbb.com/community/viewtopic.php?f=71&t=1557455

$config->load_tplcompile
$config->tpl_allow_php
*/

/**
* Base Template class.
* @package phpBB3
*/
class template
{
  /** variable that holds all the data we'll be substituting into
  * the compiled templates. Takes form:
  * --> $this->_tpldata[block][iteration#][child][iteration#][child2][iteration#][variablename] == value
  * if it's a root-level variable, it'll be like this:
  * --> $this->_tpldata[.][0][varname] == value
  */
  var $_tpldata = array('.' => array(0 => array()));
  var $_rootref;

  // Root dir and hash of filenames for each template handle.
  var $root = '';
  var $cachepath = '';
  var $files = array();
  var $filename = array();
  var $files_inherit = array();
  var $files_template = array();
  var $inherit_root = '';
  var $orig_tpl_storedb;
  var $orig_tpl_inherits_id;

  // this will hash handle names to the compiled/uncompiled code for that handle.
  var $compiled_code = array();

  // this for compatibility with old templates
  var $parse = array();

  /**
  * Set template location
  * @access public
  */
  function set_template()
  {
    global $phpbb_root_path, $user;

    if (file_exists($phpbb_root_path . 'styles/' . $user->theme['template_path'] . '/template'))
    {
      $this->root = $phpbb_root_path . 'styles/' . $user->theme['template_path'] . '/template';
      $this->cachepath = $phpbb_root_path . 'cache/tpl_' . str_replace('_', '-', $user->theme['template_path']) . '_';

      if ($this->orig_tpl_storedb === null)
      {
        $this->orig_tpl_storedb = $user->theme['template_storedb'];
      }

      if ($this->orig_tpl_inherits_id === null)
      {
        $this->orig_tpl_inherits_id = $user->theme['template_inherits_id'];
      }

      $user->theme['template_storedb'] = $this->orig_tpl_storedb;
      $user->theme['template_inherits_id'] = $this->orig_tpl_inherits_id;

      if ($user->theme['template_inherits_id'])
      {
        $this->inherit_root = $phpbb_root_path . 'styles/' . $user->theme['template_inherit_path'] . '/template';
      }
    }
    else
    {
      trigger_error('Template path could not be found: styles/' . $user->theme['template_path'] . '/template', E_USER_ERROR);
    }

    $this->_rootref = &$this->_tpldata['.'][0];

    return true;
  }

  /**
  * Set custom template location (able to use directory outside of phpBB)
  * @access public
  */
  function set_custom_template($template_path, $template_name, $template_mode = 'template')
  {
    global $phpbb_root_path, $user;

    // Make sure $template_path has no ending slash
    if (substr($template_path, -1) == '/')
    {
      $template_path = substr($template_path, 0, -1);
    }

    $this->root = $template_path;
    $this->cachepath = $phpbb_root_path . 'cache/ctpl_' . str_replace('_', '-', $template_name) . '_';

    // As the template-engine is used for more than the template (emails, etc.), we should not set $user->theme in all cases, but only on the real template.
    if ($template_mode == 'template')
    {
      $user->theme['template_storedb'] = false;
      $user->theme['template_inherits_id'] = false;
    }

    $this->_rootref = &$this->_tpldata['.'][0];

    return true;
  }

  /**
  * Sets the template filenames for handles. $filename_array
  * should be a hash of handle => filename pairs.
  * @access public
  */
  function set_filenames($filename_array)
  {
    if (!is_array($filename_array))
    {
      return false;
    }
    foreach ($filename_array as $handle => $filename)
    {
      if (empty($filename))
      {
        trigger_error("template->set_filenames: Empty filename specified for $handle", E_USER_ERROR);
      }

      $this->filename[$handle] = $filename;
      $this->files[$handle] = $this->root . '/' . $filename;

      if ($this->inherit_root)
      {
        $this->files_inherit[$handle] = $this->inherit_root . '/' . $filename;
      }
    }

    return true;
  }

  /**
  * Destroy template data set
  * @access public
  */
  function destroy()
  {
    $this->_tpldata = array('.' => array(0 => array()));
    $this->_rootref = &$this->_tpldata['.'][0];
  }

  /**
  * Reset/empty complete block
  * @access public
  */
  function destroy_block_vars($blockname)
  {
    if (strpos($blockname, '.') !== false)
    {
      // Nested block.
      $blocks = explode('.', $blockname);
      $blockcount = sizeof($blocks) - 1;

      $str = &$this->_tpldata;
      for ($i = 0; $i < $blockcount; $i++)
      {
        $str = &$str[$blocks[$i]];
        $str = &$str[sizeof($str) - 1];
      }

      unset($str[$blocks[$blockcount]]);
    }
    else
    {
      // Top-level block.
      unset($this->_tpldata[$blockname]);
    }

    return true;
  }

  /**
  * Display handle
  * @access public
  */
  function display($handle, $include_once = true)
  {
    global $user, $phpbb_hook, $lang;

    if (!empty($phpbb_hook) && $phpbb_hook->call_hook(array(__CLASS__, __FUNCTION__), $handle, $include_once))
    {
      if ($phpbb_hook->hook_return(array(__CLASS__, __FUNCTION__)))
      {
        return $phpbb_hook->hook_return_result(array(__CLASS__, __FUNCTION__));
      }
    }

    if (defined('IN_ERROR_HANDLER'))
    {
      if ((E_NOTICE & error_reporting()) == E_NOTICE)
      {
        error_reporting(error_reporting() ^ E_NOTICE);
      }
    }

    if ($filename = $this->_tpl_load($handle))
    {
      ($include_once) ? include_once($filename) : include($filename);
    }
    else
    {
      eval(' ?>' . $this->compiled_code[$handle] . '<?php ');
    }

    return true;
  }

  /**
  * Display the handle and assign the output to a template variable or return the compiled result.
  * @access public
  */
  function assign_display($handle, $template_var = '', $return_content = true, $include_once = false)
  {
    ob_start();
    $this->display($handle, $include_once);
    $contents = ob_get_clean();

    if ($return_content)
    {
      return $contents;
    }

    $this->assign_var($template_var, $contents);

    return true;
  }

  /**
  * Load a compiled template if possible, if not, recompile it
  * @access private
  */
  function _tpl_load(&$handle)
  {
    global $user, $phpEx, $config;

    if (!isset($this->filename[$handle]))
    {
      trigger_error("template->_tpl_load(): No file specified for handle $handle", E_USER_ERROR);
    }

    $filename = $this->cachepath . str_replace('/', '.', $this->filename[$handle]) . '.' . $phpEx;
    //$this->files_template[$handle] = (isset($user->theme['template_id'])) ? $user->theme['template_id'] : 0;

    $recompile = false;
    if (!file_exists($filename) || @filesize($filename) === 0)
    {
      $recompile = true;
    }
    else if ($config->load_tplcompile)
    {
      // No way around it: we need to check inheritance here
      if ($user->theme['template_inherits_id'] && !file_exists($this->files[$handle]))
      {
        $this->files[$handle] = $this->files_inherit[$handle];
        $this->files_template[$handle] = $user->theme['template_inherits_id'];
      }
      $recompile = (@filemtime($filename) < filemtime($this->files[$handle])) ? true : false;
    }

    // Recompile page if the original template is newer, otherwise load the compiled version
    if (!$recompile)
    {
      return $filename;
    }

    global $db, $phpbb_root_path;

    if (!class_exists('template_compile'))
    {
      include($phpbb_root_path . 'includes/functions_template.' . $phpEx);
    }

    // Inheritance - we point to another template file for this one. Equality is also used for store_db
    if (isset($user->theme['template_inherits_id']) && $user->theme['template_inherits_id'] && !file_exists($this->files[$handle]))
    {
      $this->files[$handle] = $this->files_inherit[$handle];
      $this->files_template[$handle] = $user->theme['template_inherits_id'];
    }

    $compile = new template_compile($this);

    // If we don't have a file assigned to this handle, die.
    if (!isset($this->files[$handle]))
    {
      trigger_error("template->_tpl_load(): No file specified for handle $handle", E_USER_ERROR);
    }

    // Just compile if no user object is present (happens within the installer)
    if (!$user)
    {
      $compile->_tpl_load_file($handle);
      return false;
    }

    if (isset($user->theme['template_storedb']) && $user->theme['template_storedb'])
    {
      $rows = array();
      $ids = array();
      // Inheritance
      if (isset($user->theme['template_inherits_id']) && $user->theme['template_inherits_id'])
      {
        $ids[] = $user->theme['template_inherits_id'];
      }
      $ids[] = $user->theme['template_id'];

      foreach ($ids as $id)
      {
        $sql = 'SELECT *
        FROM ' . STYLES_TEMPLATE_DATA_TABLE . '
        WHERE template_id = ' . $id . "
          AND (template_filename = '" . $db->sql_escape($this->filename[$handle]) . "'
            OR template_included " . $db->sql_like_expression($db->any_char . $this->filename[$handle] . ':' . $db->any_char) . ')';

        $result = $db->sql_query($sql);
        while ($row = $db->sql_fetchrow($result))
        {
          $rows[$row['template_filename']] = $row;
        }
        $db->sql_freeresult($result);
      }

      if (sizeof($rows))
      {
        foreach ($rows as $row)
        {
          $file = $this->root . '/' . $row['template_filename'];
          $force_reload = false;
          if ($row['template_id'] != $user->theme['template_id'])
          {
            // make sure that we are not overlooking a file not in the db yet
            if (isset($user->theme['template_inherits_id']) && $user->theme['template_inherits_id'] && !file_exists($file))
            {
              $file = $this->inherit_root . '/' . $row['template_filename'];
              $this->files[$row['template_filename']] = $file;
              $this->files_inherit[$row['template_filename']] = $file;
              $this->files_template[$row['template_filename']] = $user->theme['template_inherits_id'];
            }
            else if (isset($user->theme['template_inherits_id']) && $user->theme['template_inherits_id'])
            {
              // Ok, we have a situation. There is a file in the subtemplate, but nothing in the DB. We have to fix that.
              $force_reload = true;
              $this->files_template[$row['template_filename']] = $user->theme['template_inherits_id'];
            }
          }
          else
          {
            $this->files_template[$row['template_filename']] = $user->theme['template_id'];
          }

          if ($force_reload || $row['template_mtime'] < filemtime($file))
          {
            if ($row['template_filename'] == $this->filename[$handle])
            {
              $compile->_tpl_load_file($handle, true);
            }
            else
            {
              $this->files[$row['template_filename']] = $file;
              $this->filename[$row['template_filename']] = $row['template_filename'];
              $compile->_tpl_load_file($row['template_filename'], true);
              unset($this->compiled_code[$row['template_filename']]);
              unset($this->files[$row['template_filename']]);
              unset($this->filename[$row['template_filename']]);
            }
          }

          if ($row['template_filename'] == $this->filename[$handle])
          {
            $this->compiled_code[$handle] = $compile->compile(trim($row['template_data']));
            $compile->compile_write($handle, $this->compiled_code[$handle]);
          }
          else
          {
            // Only bother compiling if it doesn't already exist
            if (!file_exists($this->cachepath . str_replace('/', '.', $row['template_filename']) . '.' . $phpEx))
            {
              $this->filename[$row['template_filename']] = $row['template_filename'];
              $compile->compile_write($row['template_filename'], $compile->compile(trim($row['template_data'])));
              unset($this->filename[$row['template_filename']]);
            }
          }
        }
      }
      else
      {
        $file = $this->root . '/' . $row['template_filename'];

        if (isset($user->theme['template_inherits_id']) && $user->theme['template_inherits_id'] && !file_exists($file))
        {
          $file = $this->inherit_root . '/' . $row['template_filename'];
          $this->files[$row['template_filename']] = $file;
          $this->files_inherit[$row['template_filename']] = $file;
          $this->files_template[$row['template_filename']] = $user->theme['template_inherits_id'];
        }
        // Try to load from filesystem and instruct to insert into the styles table...
        $compile->_tpl_load_file($handle, true);
        return false;
      }

      return false;
    }

    $compile->_tpl_load_file($handle);
    return false;
  }

  /**
  * Assign key variable pairs from an array
  * @access public
  */
  function assign_vars($vararray)
  {
    foreach ($vararray as $key => $val)
    {
      $this->_rootref[$key] = $val;
    }

    return true;
  }

  /**
  * Assign a single variable to a single key
  * @access public
  */
  function assign_var($varname, $varval)
  {
    $this->_rootref[$varname] = $varval;

    return true;
  }

  /**
  * Assign key variable pairs from an array to a specified block
  * @access public
  */
  function assign_block_vars($blockname, $vararray)
  {
    if (strpos($blockname, '.') !== false)
    {
      // Nested block.
      $blocks = explode('.', $blockname);
      $blockcount = sizeof($blocks) - 1;

      $str = &$this->_tpldata;
      for ($i = 0; $i < $blockcount; $i++)
      {
        $str = &$str[$blocks[$i]];
        $str = &$str[sizeof($str) - 1];
      }

      $s_row_count = isset($str[$blocks[$blockcount]]) ? sizeof($str[$blocks[$blockcount]]) : 0;
      $vararray['S_ROW_COUNT'] = $s_row_count;

      // Assign S_FIRST_ROW
      if (!$s_row_count)
      {
        $vararray['S_FIRST_ROW'] = true;
      }

      // Now the tricky part, we always assign S_LAST_ROW and remove the entry before
      // This is much more clever than going through the complete template data on display (phew)
      $vararray['S_LAST_ROW'] = true;
      if ($s_row_count > 0)
      {
        unset($str[$blocks[$blockcount]][($s_row_count - 1)]['S_LAST_ROW']);
      }

      // Now we add the block that we're actually assigning to.
      // We're adding a new iteration to this block with the given
      // variable assignments.
      $str[$blocks[$blockcount]][] = $vararray;
    }
    else
    {
      // Top-level block.
      $s_row_count = (isset($this->_tpldata[$blockname])) ? sizeof($this->_tpldata[$blockname]) : 0;
      $vararray['S_ROW_COUNT'] = $s_row_count;

      // Assign S_FIRST_ROW
      if (!$s_row_count)
      {
        $vararray['S_FIRST_ROW'] = true;
      }

      // We always assign S_LAST_ROW and remove the entry before
      $vararray['S_LAST_ROW'] = true;
      if ($s_row_count > 0)
      {
        unset($this->_tpldata[$blockname][($s_row_count - 1)]['S_LAST_ROW']);
      }

      // Add a new iteration to this block with the variable assignments we were given.
      $this->_tpldata[$blockname][] = $vararray;
    }

    return true;
  }

  /**
  * Change already assigned key variable pair (one-dimensional - single loop entry)
  *
  * An example of how to use this function:
  * {@example alter_block_array.php}
  *
  * @param  string  $blockname  the blockname, for example 'loop'
  * @param  array $vararray the var array to insert/add or merge
  * @param  mixed $key    Key to search for
  *
  * array: KEY => VALUE [the key/value pair to search for within the loop to determine the correct position]
  *
  * int: Position [the position to change or insert at directly given]
  *
  * If key is false the position is set to 0
  * If key is true the position is set to the last entry
  *
  * @param  string  $mode   Mode to execute (valid modes are 'insert' and 'change')
  *
  * If insert, the vararray is inserted at the given position (position counting from zero).
  * If change, the current block gets merged with the vararray (resulting in new key/value pairs be added and existing keys be replaced by the new value).
  *
  * Since counting begins by zero, inserting at the last position will result in this array: array(vararray, last positioned array)
  * and inserting at position 1 will result in this array: array(first positioned array, vararray, following vars)
  *
  * @return bool false on error, true on success
  * @access public
  */
  function alter_block_array($blockname, $vararray, $key = false, $mode = 'insert')
  {
    if (strpos($blockname, '.') !== false)
    {
      // Nested blocks are not supported
      return false;
    }

    // Change key to zero (change first position) if false and to last position if true
    if ($key === false || $key === true)
    {
      $key = ($key === false) ? 0 : sizeof($this->_tpldata[$blockname]);
    }

    // Get correct position if array given
    if (is_array($key))
    {
      // Search array to get correct position
      list($search_key, $search_value) = @each($key);

      $key = NULL;
      foreach ($this->_tpldata[$blockname] as $i => $val_ary)
      {
        if ($val_ary[$search_key] === $search_value)
        {
          $key = $i;
          break;
        }
      }

      // key/value pair not found
      if ($key === NULL)
      {
        return false;
      }
    }

    // Insert Block
    if ($mode == 'insert')
    {
      // Make sure we are not exceeding the last iteration
      if ($key >= sizeof($this->_tpldata[$blockname]))
      {
        $key = sizeof($this->_tpldata[$blockname]);
        unset($this->_tpldata[$blockname][($key - 1)]['S_LAST_ROW']);
        $vararray['S_LAST_ROW'] = true;
      }
      else if ($key === 0)
      {
        unset($this->_tpldata[$blockname][0]['S_FIRST_ROW']);
        $vararray['S_FIRST_ROW'] = true;
      }

      // Re-position template blocks
      for ($i = sizeof($this->_tpldata[$blockname]); $i > $key; $i--)
      {
        $this->_tpldata[$blockname][$i] = $this->_tpldata[$blockname][$i-1];
        $this->_tpldata[$blockname][$i]['S_ROW_COUNT'] = $i;
      }

      // Insert vararray at given position
      $vararray['S_ROW_COUNT'] = $key;
      $this->_tpldata[$blockname][$key] = $vararray;

      return true;
    }

    // Which block to change?
    if ($mode == 'change')
    {
      if ($key == sizeof($this->_tpldata[$blockname]))
      {
        $key--;
      }

      $this->_tpldata[$blockname][$key] = array_merge($this->_tpldata[$blockname][$key], $vararray);
      return true;
    }

    return false;
  }

  /**
  * Include a separate template
  * @access private
  */
  function _tpl_include($filename, $include = true)
  {
    $handle = $filename;
    $this->filename[$handle] = $filename;
    $this->files[$handle] = $this->root . '/' . $filename;
    if ($this->inherit_root)
    {
      $this->files_inherit[$handle] = $this->inherit_root . '/' . $filename;
    }

    $filename = $this->_tpl_load($handle);

    if ($include)
    {
      global $user;

      if ($filename)
      {
        include($filename);
        return;
      }
      eval(' ?>' . $this->compiled_code[$handle] . '<?php ');
    }
  }

  /**
  * Include a php-file
  * @access private
  */
  function _php_include($filename)
  {
    global $phpbb_root_path;

    $file = $phpbb_root_path . $filename;

    if (!file_exists($file))
    {
      // trigger_error cannot be used here, as the output already started
      echo 'template->_php_include(): File ' . htmlspecialchars($file) . ' does not exist or is empty';
      return;
    }
    include($file);
  }
}

/**
*
* @package phpBB3
* @version $Id: functions_template.php 10064 2009-08-30 11:15:24Z acydburn $
* @copyright (c) 2005 phpBB Group, sections (c) 2001 ispi of Lincoln Inc
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* Extension of template class - Functions needed for compiling templates only.
*
* psoTFX, phpBB Development Team - Completion of file caching, decompilation
* routines and implementation of conditionals/keywords and associated changes
*
* The interface was inspired by PHPLib templates,  and the template file (formats are
* quite similar)
*
* The keyword/conditional implementation is currently based on sections of code from
* the Smarty templating engine (c) 2001 ispi of Lincoln, Inc. which is released
* (on its own and in whole) under the LGPL. Section 3 of the LGPL states that any code
* derived from an LGPL application may be relicenced under the GPL, this applies
* to this source
*
* DEFINE directive inspired by a request by Cyberalien
*
* @package phpBB3
*/
class template_compile
{
  var $template;

  // Various storage arrays
  var $block_names = array();
  var $block_else_level = array();

  /**
  * constuctor
  */
  function template_compile(&$template)
  {
    $this->template = &$template;
  }

  /**
  * Load template source from file
  * @access private
  */
  function _tpl_load_file($handle, $store_in_db = false)
  {
    // Try and open template for read
    if (!file_exists($this->template->files[$handle]))
    {
      trigger_error("template->_tpl_load_file(): File {$this->template->files[$handle]} does not exist or is empty", E_USER_ERROR);
    }

    $this->template->compiled_code[$handle] = $this->compile(trim(@file_get_contents($this->template->files[$handle])));

    // Actually compile the code now.
    $this->compile_write($handle, $this->template->compiled_code[$handle]);

    // Store in database if required...
    if ($store_in_db)
    {
      global $db, $user;

      $sql_ary = array(
        'template_id'     => $this->template->files_template[$handle],
        'template_filename'   => $this->template->filename[$handle],
        'template_included'   => '',
        'template_mtime'    => time(),
        'template_data'     => trim(@file_get_contents($this->template->files[$handle])),
      );

      $sql = 'INSERT INTO ' . STYLES_TEMPLATE_DATA_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
      $db->sql_query($sql);
    }
  }

  /**
  * Remove any PHP tags that do not belong, these regular expressions are derived from
  * the ones that exist in zend_language_scanner.l
  * @access private
  */
  function remove_php_tags(&$code)
  {
    // This matches the information gathered from the internal PHP lexer
    $match = array(
      '#<([\?%])=?.*?\1>#s',
      '#<script\s+language\s*=\s*(["\']?)php\1\s*>.*?</script\s*>#s',
      '#<\?php(?:\r\n?|[ \n\t]).*?\?>#s'
    );

    $code = preg_replace($match, '', $code);
  }

  /**
  * The all seeing all doing compile method. Parts are inspired by or directly from Smarty
  * @access private
  */
  function compile($code, $no_echo = false, $echo_var = '')
  {
    global $config;

    if ($echo_var)
    {
      global $$echo_var;
    }

    // Remove any "loose" php ... we want to give admins the ability
    // to switch on/off PHP for a given template. Allowing unchecked
    // php is a no-no. There is a potential issue here in that non-php
    // content may be removed ... however designers should use entities
    // if they wish to display < and >
    $this->remove_php_tags($code);

    // Pull out all block/statement level elements and separate plain text
    preg_match_all('#<!-- PHP -->(.*?)<!-- ENDPHP -->#s', $code, $matches);
    $php_blocks = $matches[1];
    $code = preg_replace('#<!-- PHP -->.*?<!-- ENDPHP -->#s', '<!-- PHP -->', $code);

    preg_match_all('#<!-- INCLUDE (\{\$?[A-Z0-9\-_]+\}|[a-zA-Z0-9\_\-\+\./]+) -->#', $code, $matches);
    $include_blocks = $matches[1];
    $code = preg_replace('#<!-- INCLUDE (?:\{\$?[A-Z0-9\-_]+\}|[a-zA-Z0-9\_\-\+\./]+) -->#', '<!-- INCLUDE -->', $code);

    preg_match_all('#<!-- INCLUDEPHP ([a-zA-Z0-9\_\-\+\./]+) -->#', $code, $matches);
    $includephp_blocks = $matches[1];
    $code = preg_replace('#<!-- INCLUDEPHP [a-zA-Z0-9\_\-\+\./]+ -->#', '<!-- INCLUDEPHP -->', $code);

    preg_match_all('#<!-- ([^<].*?) (.*?)? ?-->#', $code, $blocks, PREG_SET_ORDER);

    $text_blocks = preg_split('#<!-- [^<].*? (?:.*?)? ?-->#', $code);

    for ($i = 0, $j = sizeof($text_blocks); $i < $j; $i++)
    {
      $this->compile_var_tags($text_blocks[$i]);
    }
    $compile_blocks = array();

    for ($curr_tb = 0, $tb_size = sizeof($blocks); $curr_tb < $tb_size; $curr_tb++)
    {
      $block_val = &$blocks[$curr_tb];

      switch ($block_val[1])
      {
        case 'BEGIN':
          $this->block_else_level[] = false;
          $compile_blocks[] = '<?php ' . $this->compile_tag_block($block_val[2]) . ' ?>';
        break;

        case 'BEGINELSE':
          $this->block_else_level[sizeof($this->block_else_level) - 1] = true;
          $compile_blocks[] = '<?php }} else { ?>';
        break;

        case 'END':
          array_pop($this->block_names);
          $compile_blocks[] = '<?php ' . ((array_pop($this->block_else_level)) ? '}' : '}}') . ' ?>';
        break;

        case 'IF':
          $compile_blocks[] = '<?php ' . $this->compile_tag_if($block_val[2], false) . ' ?>';
        break;

        case 'ELSE':
          $compile_blocks[] = '<?php } else { ?>';
        break;

        case 'ELSEIF':
          $compile_blocks[] = '<?php ' . $this->compile_tag_if($block_val[2], true) . ' ?>';
        break;

        case 'ENDIF':
          $compile_blocks[] = '<?php } ?>';
        break;

        case 'DEFINE':
          $compile_blocks[] = '<?php ' . $this->compile_tag_define($block_val[2], true) . ' ?>';
        break;

        case 'UNDEFINE':
          $compile_blocks[] = '<?php ' . $this->compile_tag_define($block_val[2], false) . ' ?>';
        break;

        case 'INCLUDE':
          $temp = array_shift($include_blocks);

          // Dynamic includes
          // Cheap match rather than a full blown regexp, we already know
          // the format of the input so just use string manipulation.
          if ($temp[0] == '{')
          {
            $file = false;

            if ($temp[1] == '$')
            {
              $var = substr($temp, 2, -1);
              //$file = $this->template->_tpldata['DEFINE']['.'][$var];
              $temp = "\$this->_tpldata['DEFINE']['.']['$var']";
            }
            else
            {
              $var = substr($temp, 1, -1);
              //$file = $this->template->_rootref[$var];
              $temp = "\$this->_rootref['$var']";
            }
          }
          else
          {
            $file = $temp;
          }

          $compile_blocks[] = '<?php ' . $this->compile_tag_include($temp) . ' ?>';

          // No point in checking variable includes
          if ($file)
          {
            $this->template->_tpl_include($file, false);
          }
        break;

        case 'INCLUDEPHP':
          $compile_blocks[] = ($config->tpl_allow_php) ? '<?php ' . $this->compile_tag_include_php(array_shift($includephp_blocks)) . ' ?>' : '';
        break;

        case 'PHP':
          $compile_blocks[] = ($config->tpl_allow_php) ? '<?php ' . array_shift($php_blocks) . ' ?>' : '';
        break;

        default:
          $this->compile_var_tags($block_val[0]);
          $trim_check = trim($block_val[0]);
          $compile_blocks[] = (!$no_echo) ? ((!empty($trim_check)) ? $block_val[0] : '') : ((!empty($trim_check)) ? $block_val[0] : '');
        break;
      }
    }

    $template_php = '';
    for ($i = 0, $size = sizeof($text_blocks); $i < $size; $i++)
    {
      $trim_check_text = trim($text_blocks[$i]);
      $template_php .= (!$no_echo) ? (($trim_check_text != '') ? $text_blocks[$i] : '') . ((isset($compile_blocks[$i])) ? $compile_blocks[$i] : '') : (($trim_check_text != '') ? $text_blocks[$i] : '') . ((isset($compile_blocks[$i])) ? $compile_blocks[$i] : '');
    }

    // Remove unused opening/closing tags
    $template_php = str_replace(' ?><?php ', ' ', $template_php);

    // Now add a newline after each php closing tag which already has a newline
    // PHP itself strips a newline if a closing tag is used (this is documented behaviour) and it is mostly not intended by style authors to remove newlines
    $template_php = preg_replace('#\?\>([\r\n])#', '?>\1\1', $template_php);

    // There will be a number of occasions where we switch into and out of
    // PHP mode instantaneously. Rather than "burden" the parser with this
    // we'll strip out such occurences, minimising such switching
    if ($no_echo)
    {
      return "\$$echo_var .= '" . $template_php . "'";
    }

    return $template_php;
  }

  /**
  * Compile variables
  * @access private
  */
  function compile_var_tags(&$text_blocks)
  {
    // including $lang variable
    global $lang;

    // change template varrefs into PHP varrefs
    $varrefs = array();

    // This one will handle varrefs WITH namespaces
    preg_match_all('#\{((?:[a-z0-9\-_]+\.)+)(\$)?([A-Z0-9\-_]+)\}#', $text_blocks, $varrefs, PREG_SET_ORDER);

    foreach ($varrefs as $var_val)
    {
      $namespace = $var_val[1];
      $varname = $var_val[3];
      $new = $this->generate_block_varref($namespace, $varname, true, $var_val[2]);

      $text_blocks = str_replace($var_val[0], $new, $text_blocks);
    }

    // This will handle the remaining root-level varrefs
    // transform vars prefixed by L_ into their language variable pendant if nothing is set within the tpldata array
    if (strpos($text_blocks, '{L_') !== false)
    {
      $text_blocks = preg_replace('#\{L_([a-zA-Z0-9\-_]+)\[([a-zA-Z0-9\-_]*?)\]\}#', "<?php echo ((isset(\$this->_rootref['L_\\1']['\\2'])) ? \$this->_rootref['L_\\1']['\\2'] : ((isset(\$lang['\\1']['\\2'])) ? \$lang['\\1']['\\2'] : '{ \\1[\\2] }')); ?>", $text_blocks);
      $text_blocks = preg_replace('#\{L_([a-zA-Z0-9\-_]+)\}#', "<?php echo ((isset(\$this->_rootref['L_\\1'])) ? \$this->_rootref['L_\\1'] : ((isset(\$lang['\\1'])) ? \$lang['\\1'] : '{ L_\\1 }')); ?>", $text_blocks);
    }

    // Handle addslashed language variables prefixed with LA_
    // If a template variable already exist, it will be used in favor of it...
    if (strpos($text_blocks, '{LA_') !== false)
    {
      $text_blocks = preg_replace('#\{LA_([a-zA-Z0-9\-_]+)\}#', "<?php echo ((isset(\$this->_rootref['LA_\\1'])) ? \$this->_rootref['LA_\\1'] : ((isset(\$this->_rootref['L_\\1'])) ? addslashes(\$this->_rootref['L_\\1']) : ((isset(\$lang['\\1'])) ? addslashes(\$lang['\\1']) : '{ \\1 }'))); ?>", $text_blocks);
    }

    // Handle remaining varrefs
    $text_blocks = preg_replace('#\{([a-zA-Z0-9\-_]+)\}#', "<?php echo (isset(\$this->_rootref['\\1'])) ? \$this->_rootref['\\1'] : ''; ?>", $text_blocks);
    $text_blocks = preg_replace('#\{\$([a-zA-Z0-9\-_]+)\}#', "<?php echo (isset(\$this->_tpldata['DEFINE']['.']['\\1'])) ? \$this->_tpldata['DEFINE']['.']['\\1'] : ''; ?>", $text_blocks);

    return;
  }

  /**
  * Compile blocks
  * @access private
  */
  function compile_tag_block($tag_args)
  {
    $no_nesting = false;

    // Is the designer wanting to call another loop in a loop?
    if (strpos($tag_args, '!') === 0)
    {
      // Count the number if ! occurrences (not allowed in vars)
      $no_nesting = substr_count($tag_args, '!');
      $tag_args = substr($tag_args, $no_nesting);
    }

    // Allow for control of looping (indexes start from zero):
    // foo(2)    : Will start the loop on the 3rd entry
    // foo(-2)   : Will start the loop two entries from the end
    // foo(3,4)  : Will start the loop on the fourth entry and end it on the fifth
    // foo(3,-4) : Will start the loop on the fourth entry and end it four from last
    if (preg_match('#^([^()]*)\(([\-\d]+)(?:,([\-\d]+))?\)$#', $tag_args, $match))
    {
      $tag_args = $match[1];

      if ($match[2] < 0)
      {
        $loop_start = '($_' . $tag_args . '_count ' . $match[2] . ' < 0 ? 0 : $_' . $tag_args . '_count ' . $match[2] . ')';
      }
      else
      {
        $loop_start = '($_' . $tag_args . '_count < ' . $match[2] . ' ? $_' . $tag_args . '_count : ' . $match[2] . ')';
      }

      if (strlen($match[3]) < 1 || $match[3] == -1)
      {
        $loop_end = '$_' . $tag_args . '_count';
      }
      else if ($match[3] >= 0)
      {
        $loop_end = '(' . ($match[3] + 1) . ' > $_' . $tag_args . '_count ? $_' . $tag_args . '_count : ' . ($match[3] + 1) . ')';
      }
      else //if ($match[3] < -1)
      {
        $loop_end = '$_' . $tag_args . '_count' . ($match[3] + 1);
      }
    }
    else
    {
      $loop_start = 0;
      $loop_end = '$_' . $tag_args . '_count';
    }

    $tag_template_php = '';
    array_push($this->block_names, $tag_args);

    if ($no_nesting !== false)
    {
      // We need to implode $no_nesting times from the end...
      $block = array_slice($this->block_names, -$no_nesting);
    }
    else
    {
      $block = $this->block_names;
    }

    if (sizeof($block) < 2)
    {
      // Block is not nested.
      $tag_template_php = '$_' . $tag_args . "_count = (isset(\$this->_tpldata['$tag_args'])) ? sizeof(\$this->_tpldata['$tag_args']) : 0;";
      $varref = "\$this->_tpldata['$tag_args']";
    }
    else
    {
      // This block is nested.
      // Generate a namespace string for this block.
      $namespace = implode('.', $block);

      // Get a reference to the data array for this block that depends on the
      // current indices of all parent blocks.
      $varref = $this->generate_block_data_ref($namespace, false);

      // Create the for loop code to iterate over this block.
      $tag_template_php = '$_' . $tag_args . '_count = (isset(' . $varref . ')) ? sizeof(' . $varref . ') : 0;';
    }

    $tag_template_php .= 'if ($_' . $tag_args . '_count) {';

    /**
    * The following uses foreach for iteration instead of a for loop, foreach is faster but requires PHP to make a copy of the contents of the array which uses more memory
    * <code>
    * if (!$offset)
    * {
    *   $tag_template_php .= 'foreach (' . $varref . ' as $_' . $tag_args . '_i => $_' . $tag_args . '_val){';
    * }
    * </code>
    */

    $tag_template_php .= 'for ($_' . $tag_args . '_i = ' . $loop_start . '; $_' . $tag_args . '_i < ' . $loop_end . '; ++$_' . $tag_args . '_i){';
    $tag_template_php .= '$_'. $tag_args . '_val = &' . $varref . '[$_'. $tag_args. '_i];';

    return $tag_template_php;
  }

  /**
  * Compile IF tags - much of this is from Smarty with
  * some adaptions for our block level methods
  * @access private
  */
  function compile_tag_if($tag_args, $elseif)
  {
    // Tokenize args for 'if' tag.
    preg_match_all('/(?:
      "[^"\\\\]*(?:\\\\.[^"\\\\]*)*"         |
      \'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'     |
      [(),]                                  |
      [^\s(),]+)/x', $tag_args, $match);

    $tokens = $match[0];
    $is_arg_stack = array();

    for ($i = 0, $size = sizeof($tokens); $i < $size; $i++)
    {
      $token = &$tokens[$i];

      switch ($token)
      {
        case '!==':
        case '===':
        case '<<':
        case '>>':
        case '|':
        case '^':
        case '&':
        case '~':
        case ')':
        case ',':
        case '+':
        case '-':
        case '*':
        case '/':
        case '@':
        break;

        case '==':
        case 'eq':
          $token = '==';
        break;

        case '!=':
        case '<>':
        case 'ne':
        case 'neq':
          $token = '!=';
        break;

        case '<':
        case 'lt':
          $token = '<';
        break;

        case '<=':
        case 'le':
        case 'lte':
          $token = '<=';
        break;

        case '>':
        case 'gt':
          $token = '>';
        break;

        case '>=':
        case 'ge':
        case 'gte':
          $token = '>=';
        break;

        case '&&':
        case 'and':
          $token = '&&';
        break;

        case '||':
        case 'or':
          $token = '||';
        break;

        case '!':
        case 'not':
          $token = '!';
        break;

        case '%':
        case 'mod':
          $token = '%';
        break;

        case '(':
          array_push($is_arg_stack, $i);
        break;

        case 'is':
          $is_arg_start = ($tokens[$i-1] == ')') ? array_pop($is_arg_stack) : $i-1;
          $is_arg = implode(' ', array_slice($tokens, $is_arg_start, $i - $is_arg_start));

          $new_tokens = $this->_parse_is_expr($is_arg, array_slice($tokens, $i+1));

          array_splice($tokens, $is_arg_start, sizeof($tokens), $new_tokens);

          $i = $is_arg_start;

        // no break

        default:
          if (preg_match('#^((?:[a-z0-9\-_]+\.)+)?(\$)?(?=[A-Z])([A-Z0-9\-_]+)#s', $token, $varrefs))
          {
            $token = (!empty($varrefs[1])) ? $this->generate_block_data_ref(substr($varrefs[1], 0, -1), true, $varrefs[2]) . '[\'' . $varrefs[3] . '\']' : (($varrefs[2]) ? '$this->_tpldata[\'DEFINE\'][\'.\'][\'' . $varrefs[3] . '\']' : '$this->_rootref[\'' . $varrefs[3] . '\']');
          }
          else if (preg_match('#^\.((?:[a-z0-9\-_]+\.?)+)$#s', $token, $varrefs))
          {
            // Allow checking if loops are set with .loopname
            // It is also possible to check the loop count by doing <!-- IF .loopname > 1 --> for example
            $blocks = explode('.', $varrefs[1]);

            // If the block is nested, we have a reference that we can grab.
            // If the block is not nested, we just go and grab the block from _tpldata
            if (sizeof($blocks) > 1)
            {
              $block = array_pop($blocks);
              $namespace = implode('.', $blocks);
              $varref = $this->generate_block_data_ref($namespace, true);

              // Add the block reference for the last child.
              $varref .= "['" . $block . "']";
            }
            else
            {
              $varref = '$this->_tpldata';

              // Add the block reference for the last child.
              $varref .= "['" . $blocks[0] . "']";
            }
            $token = "sizeof($varref)";
          }
          else if (!empty($token))
          {
            $token = '(' . $token . ')';
          }

        break;
      }
    }

    // If there are no valid tokens left or only control/compare characters left, we do skip this statement
    if (!sizeof($tokens) || str_replace(array(' ', '=', '!', '<', '>', '&', '|', '%', '(', ')'), '', implode('', $tokens)) == '')
    {
      $tokens = array('false');
    }
    return (($elseif) ? '} else if (' : 'if (') . (implode(' ', $tokens) . ') { ');
  }

  /**
  * Compile DEFINE tags
  * @access private
  */
  function compile_tag_define($tag_args, $op)
  {
    preg_match('#^((?:[a-z0-9\-_]+\.)+)?\$(?=[A-Z])([A-Z0-9_\-]*)(?: = (\'?)([^\']*)(\'?))?$#', $tag_args, $match);

    if (empty($match[2]) || (!isset($match[4]) && $op))
    {
      return '';
    }

    if (!$op)
    {
      return 'unset(' . (($match[1]) ? $this->generate_block_data_ref(substr($match[1], 0, -1), true, true) . '[\'' . $match[2] . '\']' : '$this->_tpldata[\'DEFINE\'][\'.\'][\'' . $match[2] . '\']') . ');';
    }

    // Are we a string?
    if ($match[3] && $match[5])
    {
      $match[4] = str_replace(array('\\\'', '\\\\', '\''), array('\'', '\\', '\\\''), $match[4]);

      // Compile reference, we allow template variables in defines...
      $match[4] = $this->compile($match[4]);

      // Now replace the php code
      $match[4] = "'" . str_replace(array('<?php echo ', '; ?>'), array("' . ", " . '"), $match[4]) . "'";
    }
    else
    {
      preg_match('#true|false|\.#i', $match[4], $type);

      switch (strtolower($type[0]))
      {
        case 'true':
        case 'false':
          $match[4] = strtoupper($match[4]);
        break;

        case '.':
          $match[4] = doubleval($match[4]);
        break;

        default:
          $match[4] = intval($match[4]);
        break;
      }
    }

    return (($match[1]) ? $this->generate_block_data_ref(substr($match[1], 0, -1), true, true) . '[\'' . $match[2] . '\']' : '$this->_tpldata[\'DEFINE\'][\'.\'][\'' . $match[2] . '\']') . ' = ' . $match[4] . ';';
  }

  /**
  * Compile INCLUDE tag
  * @access private
  */
  function compile_tag_include($tag_args)
  {
    // Process dynamic includes
    if ($tag_args[0] == '$')
    {
      return "if (isset($tag_args)) { \$this->_tpl_include($tag_args); }";
    }

    return "\$this->_tpl_include('$tag_args');";
  }

  /**
  * Compile INCLUDE_PHP tag
  * @access private
  */
  function compile_tag_include_php($tag_args)
  {
    return "\$this->_php_include('$tag_args');";
  }

  /**
  * parse expression
  * This is from Smarty
  * @access private
  */
  function _parse_is_expr($is_arg, $tokens)
  {
    $expr_end = 0;
    $negate_expr = false;

    if (($first_token = array_shift($tokens)) == 'not')
    {
      $negate_expr = true;
      $expr_type = array_shift($tokens);
    }
    else
    {
      $expr_type = $first_token;
    }

    switch ($expr_type)
    {
      case 'even':
        if (@$tokens[$expr_end] == 'by')
        {
          $expr_end++;
          $expr_arg = $tokens[$expr_end++];
          $expr = "!(($is_arg / $expr_arg) % $expr_arg)";
        }
        else
        {
          $expr = "!($is_arg & 1)";
        }
      break;

      case 'odd':
        if (@$tokens[$expr_end] == 'by')
        {
          $expr_end++;
          $expr_arg = $tokens[$expr_end++];
          $expr = "(($is_arg / $expr_arg) % $expr_arg)";
        }
        else
        {
          $expr = "($is_arg & 1)";
        }
      break;

      case 'div':
        if (@$tokens[$expr_end] == 'by')
        {
          $expr_end++;
          $expr_arg = $tokens[$expr_end++];
          $expr = "!($is_arg % $expr_arg)";
        }
      break;
    }

    if ($negate_expr)
    {
      $expr = "!($expr)";
    }

    array_splice($tokens, 0, $expr_end, $expr);

    return $tokens;
  }

  /**
  * Generates a reference to the given variable inside the given (possibly nested)
  * block namespace. This is a string of the form:
  * ' . $this->_tpldata['parent'][$_parent_i]['$child1'][$_child1_i]['$child2'][$_child2_i]...['varname'] . '
  * It's ready to be inserted into an "echo" line in one of the templates.
  * NOTE: expects a trailing "." on the namespace.
  * @access private
  */
  function generate_block_varref($namespace, $varname, $echo = true, $defop = false)
  {
    // Strip the trailing period.
    $namespace = substr($namespace, 0, -1);

    // Get a reference to the data block for this namespace.
    $varref = $this->generate_block_data_ref($namespace, true, $defop);
    // Prepend the necessary code to stick this in an echo line.

    // Append the variable reference.
    $varref .= "['$varname']";
    $varref = ($echo) ? "<?php echo $varref; ?>" : ((isset($varref)) ? $varref : '');

    return $varref;
  }

  /**
  * Generates a reference to the array of data values for the given
  * (possibly nested) block namespace. This is a string of the form:
  * $this->_tpldata['parent'][$_parent_i]['$child1'][$_child1_i]['$child2'][$_child2_i]...['$childN']
  *
  * If $include_last_iterator is true, then [$_childN_i] will be appended to the form shown above.
  * NOTE: does not expect a trailing "." on the blockname.
  * @access private
  */
  function generate_block_data_ref($blockname, $include_last_iterator, $defop = false)
  {
    // Get an array of the blocks involved.
    $blocks = explode('.', $blockname);
    $blockcount = sizeof($blocks) - 1;

    // DEFINE is not an element of any referenced variable, we must use _tpldata to access it
    if ($defop)
    {
      $varref = '$this->_tpldata[\'DEFINE\']';
      // Build up the string with everything but the last child.
      for ($i = 0; $i < $blockcount; $i++)
      {
        $varref .= "['" . $blocks[$i] . "'][\$_" . $blocks[$i] . '_i]';
      }
      // Add the block reference for the last child.
      $varref .= "['" . $blocks[$blockcount] . "']";
      // Add the iterator for the last child if requried.
      if ($include_last_iterator)
      {
        $varref .= '[$_' . $blocks[$blockcount] . '_i]';
      }
      return $varref;
    }
    else if ($include_last_iterator)
    {
      return '$_'. $blocks[$blockcount] . '_val';
    }
    else
    {
      return '$_'. $blocks[$blockcount - 1] . '_val[\''. $blocks[$blockcount]. '\']';
    }
  }

  /**
  * Write compiled file to cache directory
  * @access private
  */
  function compile_write($handle, $data)
  {
    global $phpEx;

    $filename = $this->template->cachepath . str_replace('/', '.', $this->template->filename[$handle]) . '.' . $phpEx;

    $data = "<?php if (!defined('INSIDE')) exit;" . ((strpos($data, '<?php') === 0) ? substr($data, 5) : ' ?>' . $data);

    if ($fp = @fopen($filename, 'wb'))
    {
      @flock($fp, LOCK_EX);
      @fwrite ($fp, $data);
      @flock($fp, LOCK_UN);
      @fclose($fp);

      //phpbb_chmod($filename, CHMOD_READ | CHMOD_WRITE);
      chmod($filename, 0770);
    }

    return;
  }
}
?>