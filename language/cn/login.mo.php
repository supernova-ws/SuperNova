<?php

/*
#############################################################################
#  Filename: login.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Strategy Game
#
#  Copyright © 2009-2018 Gorlum for Project "SuperNova.WS"
#  Copyright © 2008 Aleksandar Spasojevic <spalekg@gmail.com>
#  Copyright © 2005 - 2008 KGsystem
#############################################################################
*/

/**
*
* @package language
* @system [Chinese Simplified]
* @version 45d0
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

global $config;

$a_lang_array = (array(
  'Login' => '登录',
  'User_name' => '用户名:',
  'Authorization' => '授权',
  'Please_Login' => '欢迎，请 <a href="login.php" target="_main">登录...</a>',
  'Please_Wait' => '请稍候',
  'Remember_me' => '记住我',
  'Register' => '错误信息',
  'Login_Error' => '登录错误',
  'PleaseWait' => '请稍候',
  'PasswordLost' => '忘记密码？',
  'Login_Ok' => '连接成功，<a href="./"><blink>正在重定向...</blink></a><br><center><img src="design/images/progressbar.gif"></center>',
  'Login_FailPassword' => '用户名或密码错误<br /><a href="login.php" target="_top">返回</a>',
  'Login_FailUser' => '该玩家不存在。<br><a href=login.php>返回</a>',
  'log_univ' => '宇宙日志！',
  'log_reg' => '注册',
  'log_reg_main' => '规则！',
  'log_menu' => '菜单',
  'log_stat_menu' => '统计',
  'log_enter' => '登录',
  'log_news' => '服务器公告',
  'log_cred' => '关于服务器',
  'log_faq' => 'FAQ',
  'log_forums' => '论坛',
  'log_contacts' => '管理团队',
  'log_desc' => '<strong>Supernova 是一款基于浏览器的多人在线太空策略游戏。</strong> 成千上万的玩家同时在线对抗。你只需要一个浏览器即可开始游戏。',
  'log_toreg' => '立即注册！',
  'log_online' => '在线玩家',
  'log_lastreg' => '新成员',
  'log_numbreg' => '总账号数',
  'log_welcome' => '欢迎来到',
  'vacation_mode' => '你处于假期模式<br>你可以关闭假期模式 ',
  'hours' => ' 小时',
  'vacations' => '假期模式',
  'log_scr1' => '船厂截图，在这里建造飞船。点击图片放大。',
  'log_scr2' => '统计截图，显示你在各种参数上的排名。点击图片放大。',
  'log_scr3' => '宇宙截图，在这里你可以看到你的星球，并寻找其他玩家的星球。点击图片放大。',
  'log_rules' => '游戏规则',
  'log_banned' => '封禁名单',
  'log_see_you' => '希望能再次在我们的宇宙中见到你。祝你好运！<br><a href="login.php">前往游戏登录页面</a>',
  'log_session_closed' => '会话已关闭。',
  'registry' => '注册',
  'form' => '注册表单',
  'Undefined' => '- 未定义 -',
  'Male' => '男',
  'Female' => '女',
  'Multiverse' => 'XNova',
  'E-Mail' => '电子邮箱',
  'MainPlanet' => '你的星球名称',
  'GameName' => '游戏名称',
  'gender' => '性别',
  'accept' => '接受',
  'reg_i_agree' => '我已阅读并同意',
  'reg_with_rules' => '游戏规则',
  'signup' => '注册',
  'Languese' => '语言',
  'log_reg_text0' => '注册前请阅读',
  'log_reg_text1' => '注册意味着你已阅读并完全同意所有规则条款。如果你不同意任何一条规则，请不要注册。',
  'thanksforregistry' => '恭喜你注册成功！如果10秒内没有自动跳转，请点击此<a href=overview.php><u>链接！</u></a>！',
  'welcome_to_universe' => '欢迎来到 OGame！！！',
  'please_click_url' => '为了使用该账号，你必须点击此链接激活它',
  'regards' => '祝你好运！',
  'error_lang' => '不支持此语言！<br />',
  'error_mail' => '错误的电子邮箱！<br />',
  'error_planet' => '已有相同名称的星球！<br />',
  'error_hplanetnum' => '星球名称只能包含拉丁字母！<br />',
  'error_character' => '错误的名称！<br />',
  'error_charalpha' => '你只能使用字母！<br />',
  'error_password' => '密码至少包含4个字符！<br />',
  'error_rgt' => '你必须遵守规则！<br />',
  'error_userexist' => '此名称已被使用！<br />',
  'error_emailexist' => '此邮箱已被使用！<br />',
  'error_sex' => '性别选择错误！<br />',
  'error_mailsend' => '发送邮件错误，你的密码：',
  'reg_welldone' => '注册完成！你的密码已发送到注册邮箱。为了以防万一，这里再次显示：',
  'error_captcha' => '错误的验证码！<br/>',
  'error_v' => '请重试！<br />',
  'log_login_page' => '进入游戏',
  'log_reg_already' => '已有账号？ ',
  'log_reg_already_lost' => '忘记密码？',

  'log_lost_header' => '找回密码',
  'log_lost_code' => '确认码',
  'log_lost_description1' => '输入你注册账号时的邮箱地址。我们将发送一封带有验证码的邮件以重置密码。',

  'log_lost_send_mail' => '发送确认码',
  'log_lost_description2' => '如果你已有确认码，请在下方输入并点击“重置密码”。新密码将发送到你的邮箱。<br /><br />
    如果你已请求确认码但在收件箱找不到邮件 - 请检查垃圾邮件文件夹。某些邮件服务器可能会将我们的邮件标记为“垃圾邮件”<br /><br />
    如果你确定没有收到邮件 - 请发送邮件至管理员邮箱 <span class="ok">' . $config->server_email . '</span>',
  'log_lost_reset_pass' => '重置密码',
  'log_lost_sent_code' => '邮件已发送，包含重置密码的进一步说明。',
  'log_lost_sent_pass' => '新密码已发送到你的邮箱。',
  'log_lost_err_email' => '此邮箱未在数据库中注册。这可能意味着：<br>你输错了邮箱。请返回上一页重试<br>你的账号因长期未活动已被删除。请重新注册<br>你正尝试进入错误的游戏宇宙。请仔细检查当前宇宙名称，如有错误请前往正确的宇宙',
  'log_lost_err_sending' => '发送邮件错误。请通知管理员。',
  'log_lost_err_code' => '验证码未在数据库中注册。这可能意味着：<br>你输错了确认码。请返回上一页重新输入<br>你尝试在错误的宇宙输入确认码。请检查宇宙名称<br>你的账号因长期未活动已被删除。请重新注册<br>确认码已过期。请检查邮件中的有效期。如果已过期，请请求新的确认码',
  'log_lost_err_admin' => '服务器管理团队（版主、管理员等）不能使用密码重置功能。请联系服务器管理员更改密码。',
  'log_lost_err_change' => '数据库密码更改错误。请通知管理员。',

  'login_register_offer' => '点击这里注册',
  'login_password_restore_offer' => '点击这里重置密码',

  'login_register_email_hint' => '请提供有效的电子邮箱 - 该邮箱的所有者被视为账号所有者<br />
    尽量避免使用 mail.ru 邮箱',

  'login_account_name_or_email' => '电子邮箱',

));
