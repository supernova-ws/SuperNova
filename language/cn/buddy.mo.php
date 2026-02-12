<?php

/*
#############################################################################
#  Filename: buddy.mo
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
* @system [Chinese]
* @version 43a16.13
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$a_lang_array = (array(
  'buddy_buddies' => '好友',
  'buddy_request_text' => '请求文本',
  'buddy_request_text_default' => '请将我添加到您的好友列表',
  'buddy_request_none' => '没有好友和好友请求',
  'buddy_request_write_header' => '新好友请求',
  'buddy_request_player_name' => '玩家名称',
  'buddy_request_accept' => '将玩家添加到好友列表',

  'buddy_status' => '状态',
  'buddy_status_active' => '这是您的共同好友',
  'buddy_status_incoming_waiting' => '收到的好友请求',
  'buddy_status_incoming_denied' => '您拒绝了好友请求',
  'buddy_status_outcoming_waiting' => '您的请求已发送。等待回复',
  'buddy_status_outcoming_denied' => '您的请求被拒绝',

  // Result messages
  'buddy_err_not_exist' => '请求不存在。可能已被删除或拒绝',

  'buddy_err_accept_own' => '您不能接受自己的请求',
  'buddy_err_accept_alien' => '您不能接受未发送给您的请求',
  'buddy_err_accept_already' => '您已接受此请求，并且已与此玩家成为好友',
  'buddy_err_accept_denied' => '您已拒绝此请求，无法再接受',
  'buddy_err_accept_internal' => '接受请求时出错。请稍后再试。如果错误仍然存在 - 请联系服务器管理员',
  'buddy_err_accept_none' => '好友请求已通过',

  'buddy_err_delete_alien' => '此请求不是由您发起的，也不是发给您的！不要干涉别人的关系！去找您自己的朋友！',
  'buddy_err_unfriend_none' => '您解除了好友关系',
  'buddy_err_delete_own' => '您的请求已删除',

  'buddy_err_deny_none' => '您拒绝了玩家的好友请求。为什么，哦为什么？！',

  'buddy_err_adding_exists' => '您不能向此玩家发送请求 - 您已经是好友或你们之间存在好友请求',
  'buddy_err_adding_none' => '您的好友请求已发送',
  'buddy_err_adding_self' => '您不能向自己发送好友请求',

  // PM messages
  'buddy_msg_accept_title' => '您有新朋友！',
  'buddy_msg_accept_text' => '玩家 %s 将您添加到了好友列表！',
  'buddy_msg_unfriend_title' => '您失去了一位朋友！',
  'buddy_msg_unfriend_text' => '玩家 %s 解除了好友关系并将您从好友列表中移除。真遗憾...',
  'buddy_msg_deny_title' => '无法结交新朋友',
  'buddy_msg_deny_text' => '玩家 %s 拒绝了您的好友请求',
  'buddy_msg_adding_title' => '好友请求',
  'buddy_msg_adding_text' => '玩家 %s 向您发送了好友请求',

  'buddy_hint' => '
    <li>通过菜单项 <a href="search.php">搜索</a> 发送好友请求。</li>
    <li>您可以查看好友的在线或离线状态。但是，您的好友也可以看到您的状态。在接受好友请求之前请考虑这一事实。</li>
    <li>如果您拒绝了好友请求，在对方删除请求之前，您无法与该玩家建立任何好友关系</li>',

));
