# 项目 "SuperNova.WS"
## 免责声明
警告！该项目处于 alpha 阶段！目前，它不是为生产用途设计的！代码按"原样"提供。使用风险自负！作者不对因使用、不使用、本代码的存在或以任何其他方式造成的物质、道德、业力、精神以及任何其他损害负责。

警告！虽然 Supernova 是 oGame 的克隆版，但它与官方游戏不同！也就是说，游戏的许多方面与官方 oGame 以及 RR 都不同。滑块已更改，以符合我对有趣游戏的理解。在决定是否自己安装此引擎时，请牢记这一点。

警告！"Supernova" 的状态 - alpha 版本。实际上，这意味着下一次更新可能会完全改变游戏的某个方面。

引擎的大致开发计划包含在文件 /docs/changelog.todo 中

代码根据 GNU 通用公共许可证第 2 版（1991 年 6 月）授权。许可证本身位于分发版的 docs/license.txt 文件中。


### 需要培训的事先通知
这些说明假设您具备定制和使用第三方 Web 托管的能力，熟悉 MySQL 和 PHP，能够使用 MySQL 管理和托管工具。如果您没有独立搭建网站的经验 - 这个分发版不适合您。

警告！我无法测试 MySQL、PHP、XCache 和 Web 服务器的所有可能组合和版本！这意味着对于某些组合和环境设置，引擎可能无法工作。这正是需要具备设置和配置 Web 服务器技能的原因。


## 系统要求
- MySQL 5.x - 必须禁用 STRICT_TRANS_TABLES 模式
- PHP >= 5.5
- Web 服务器
- XCache >= 1.2.h - 可选，但非常、非常推荐。没有 XCache，某些功能将无法工作，并且会显著增加 MySQL 的负载。


SuperNova 在 Windows 上使用 WAMP Server 3.0.8 开发。WAMP 配置：
- MySQL 5.7.14
- Apache 2.4.23
- PHP 5.6.25 + xCache 1.3.2
线上服务器在 CentOS + lighttpd + xCache 下运行

在本地主机上，SuperNova 始终使用 display_errors=1 运行


## 引擎位置
引擎可以运行在网站的任何位置，包括子目录。也就是说，它可以安装在以下地址：
* http://domain.com
* http://my.very.deep.domain.on.crappy.hoster.org
* http://hosting.com/ogame/
等等。


## Web 服务器权限
一般来说，引擎可以在 Web 服务器具有"只读"访问权限的情况下运行。但是，为了使各个子系统正常运行，Web 服务器账户必须被允许写入单独的文件/文件夹。以下是子系统列表和相关文件/文件夹：
* 模板缓存子系统 - 目录 /cache。没有这些模板，每次都会重新渲染
* 头像子系统 - 目录 /images/avatar
* 关于黑客攻击尝试警告的子系统 - Web 服务器账户应该被允许写入磁盘文件 /badqrys.txt。没有此文件，"不良"请求将不会被保存


## 自定义修改
如果您使用自己的皮肤、模板或本地化，请勿修改内置的！仓库中的后续更改可能会覆盖您的添加！以不同的名称复制皮肤/模板/本地化，并使用副本进行自定义。可以在服务器配置页面更改皮肤/模板/默认本地化。


## 安装，数据库配置
警告！必须禁用 STRICT_TRANS_TABLES 模式（变量 sql_mode）！

警告！所有表都存储在 InnoDB 中！默认情况下，MySQL 未配置为与 InnoDB 数据库正常运行！默认设置足以让 MySQL 支持最多 50 个在线用户。如果您计划有更多同时在线玩家，则需要根据您的服务器配置 MySQL。

作为示例，引用您的 MySQL 服务器配置（2GB 内存，服务器专门用于 Supernova 和支持论坛）：
```
innodb_additional_mem_pool_size 20971520
innodb_buffer_pool_size 536870912
innodb_flush_log_at_trx_commit 0
innodb_flush_method O_DIRECT
innodb_log_buffer_size 8388608
```
请特别注意变量 innodb_flush_log_at_trx_commit！

此外，为了便于灾难恢复，建议将每个 InnoDB 表保存在单独的文件中。为此，请在配置文件中添加指令：
```
innodb_file_per_table
```

## 安装，配置 Web 服务器
SN 设计用于使用 xCache 操作码缓存。虽然引擎可以在没有它的情况下工作，但此模式不是常规的。没有操作码缓存，引擎的某些功能将被阻止，并且会增加 MySQL 的负载。

与任何操作码缓存一样，xCache 需要特殊的 Web 服务器配置。如何配置 Web 服务器以使用 xCache 可以在互联网上找到（www.google.com）和 xCache 主页（http://xcache.lighttpd.net/）


## 安装，基础
 1. 创建数据库 "supernova" UTF-8
 2. 创建 MySQL 用户 "supernova_user" 并授予其对 "supernova" 数据库的所有权限
 3. 加载数据库文件 /docs/supernova.sql
 4. 将文件 /docs/config.php.sample 复制到 supernova 根目录并重命名为 config.php
 5. 在文件 /config.php 中，将行
    "Pass" => "MYSQL_PASSWORD", // ??访问 MySQL 服务器的密码。
    中的 MYSQL_PASSWORD 替换为数据库密码
 6. 在文件 /config.php 中，将行
    "Secretword" => "SUPERNOVA", // ??创建 Cookie 的关键字
    中的 SUPERNOVA 替换为任意字符序列
 7. 将 supernova 分发版加载到 Web 服务器的根目录
 8. 在数据库中开启游戏：在表 `sn_config` 中将变量 `game_disable` 的值更改为 0
 9. 以管理员权限进入游戏。默认管理员账户：
    用户名：'admin'（不带引号）
    密码：'admin'（不带引号）
10. 必须在页面 /options.php 上更改管理员密码
11. 在页面 /admin/settings.php 上设置游戏
12. Supernova 准备启动！
13. 如果出现问题 - 请参阅下面的"安装/更新故障排除"部分

警告！团队成员（auth_level > 0）的碎片不会掉落！这不是 bug，这是专门为了增强服务器安全性而设计的！游戏和管理需要使用不同的账户！此外，服务器团队成员不参与记录计算（但参与统计计算）


## 安装，高级版本
数据库访问设置在文件 /config.php 中
游戏设置位于表 <db_prefix>config 中。设置名称直观易懂

一些游戏设置也存储在文件 /includes/constants.php 中

所有更改 - 风险自负

警告！如果您更改了表前缀（默认为 'sn_'），那么在倒入文件 /docs/supernova.sql 之前，还需要更改表的名称！

### 单个服务器的多个宇宙
SN 支持在单个服务器上使用多个宇宙。不同的宇宙必须在 xCache 变量空间内具有不同的前缀


## GIT 简短教程
在当前目录中创建 supernova 的本地副本（不要忘记在末尾加点！）：
```
  git clone git://github.com/supernova-ws/SuperNova.git .
```
当前目录中将有来自 master 分支的最新 supernova 副本。

现在您需要将内容复制到服务器根目录。一般来说，建议在服务器上执行此操作一次 - 以避免复制问题（这种情况已经发生过）。但并非所有托管公司都允许在本地运行程序，因此您可以先在本地驱动器上创建副本。

实际上，就这些了 :) 但是，如果您对引擎进行修改，以下命令对您很有用。回滚对本地副本所做的所有更改：
```
  git reset --hard
```

## 升级到最新版本
0. 警告！更新前请备份数据库和引擎文件，以便在更新失败时可以回滚！
1. 通过具有管理员权限的账户登录游戏
2. 警告！升级前服务器必须停止。操作方式如下：在管理员左侧菜单中会出现"管理员"选项 - 点击它。在管理员菜单中，选择"设置"，勾选"关闭游戏"，按页面底部的"保存"按钮。只有这样才可以执行更新 - 否则结果可能与预期相差甚远。
3. 如果启用了模板缓存，必须删除目录 /cache 中的所有文件
4. 现在更新引擎文件
   1. 如果您是从 GIT 仓库安装的游戏，在服务器根目录运行：
        ```
          git pull
        ```
   2. 在任何其他情况下 - 上传更新版本的引擎
5. 等待新版本引擎上传到服务器！在复制文件或从 GIT 仓库下载的过程中尝试更新可能导致不可预测的结果
6. 切换到您以管理员身份登录的浏览器，选择"浏览"。等待页面加载 - 此时会更新数据库
7. 最后，完成所有这些后，您可以再次开启游戏（管理员 -> 选项，取消勾选"关闭游戏"并保存更改）。
8. 如果出现问题 - 请参阅下面的"安装/更新故障排除"部分

### 从 RR 升级
自动从 RR 开发版本的数据库升级。部分升级通过自动启动文件 update.php 完成

关于 RR，Supernova 更改了横幅位置。之前的链接是：
```
 /Scripts/createbanner.php
```
新链接：
```
 /banner.php
```

使用 Web 服务器工具（mod_rewrite）重定向请求。具体来说，lighttpd 的规则如下：

```
# 将旧式横幅重定向到新横幅
server.modules += ("mod_rewrite")
url.rewrite-once = (
  "^/Scripts/createbanner.php(.*)" => "/banner.php$1"
)
```

## 安装/更新故障排除

### 消息 "Game is not configured yet"
如果您看到此消息，则表示您当前以非管理员账户登录游戏。要更改账户：
1. 通过打开链接 `(您的服务器 URL)/logout.php` 退出登录
   1. 如果您是以模拟器模式登录游戏的，游戏将立即将您重定向到管理员部分。
   2. 否则，游戏将将您重定向到登录页面。输入管理员登录名和密码
   3. 游戏将将您重定向到管理员部分。
2. 根据您的意愿配置游戏。
3. 在服务器设置页面 /admin/settings.php 的"状态"选项卡上，更改游戏状态并保存更改

### 其他问题
无法预测 PHP、MySQL 和 Web 服务器的所有可能设置组合。SN 侧重于"默认设置"。因此，在某些情况下，升级程序可能会失败。

自动更新算法设计为单次运行。但是，在某些配置中，升级过程中会出现错误。其中一些是致命的，一些可以通过重新启动程序（有或没有编辑数据库）来修复。

通过管理员页面"工具"菜单中的"强制更新"选项再次运行更新。请注意！使用强制更新可能会导致数据库损坏（如果更新本身覆盖之前成功的更新）！只有在标准更新程序未成功时才应使用强制更新！如果没有帮助 - 应该进行故障诊断

## 引擎故障诊断
最新和最当前的引擎版本（包含最新和最相关的错误）可以在此链接下载：
```
https://github.com/supernova-ws/SuperNova/zipball/trunk
```
无需关注文件名。

！警告！诊断应在单独的数据库和单独的引擎副本上进行！不要在实时服务器上进行诊断！

诊断的标准步骤序列如下：
1. 在空数据库中从存档设置干净的数据库转储。此阶段的问题说明：
   1. 最可能的是 - MySQL 服务器设置错误
   2. 数据库转储中的错误。也许您在更新转储时处于"关键时刻"。这里的"关键时刻"是相对概念 - 可能持续数天
2. 在上一步获得的干净服务器和干净数据库上，从存档放置引擎。尝试运行。此阶段的问题说明：
   1. 引擎配置错误。检查文件 /config.php
   2. HTTP 服务器设置错误（如果是第一次运行引擎）
   3. 自动更新程序中的错误
   4. 引擎正在编写过程中，但您遇到了提交"关键时刻"
3. 用您的工作数据库转储替换干净的数据库（不要忘记如果需要再次更改配置 - 表前缀、登录名/密码等）。此阶段的问题说明：
   1. 引擎配置错误。检查文件 /config.php
   2. 自动更新程序中的错误
   3. 数据库本身的错误。然后自己处理，或为我的工作付费
4. 如果没有问题 - 那么实时服务器上有些特别的东西。这里我们需要更多了解 - 要么自己处理，或为我的工作付费

在每个阶段之前，始终需要重新启动 Web 服务器和 MySQL - 否则可能会使用旧版本的旧设置。
只有遵循真正准确的诊断程序，才能有效识别每个单独服务器上 SN 的问题。

## 网址
项目主站：http://supernova.ws

### 论坛
项目论坛：http://forum.supernova.ws

支持论坛：http://forum.supernova.ws/viewforum.php?f=73

错误报告论坛：http://forum.supernova.ws/viewforum.php?f=65

### Supernova 在 github 上
项目页面：http://github.com/supernova-ws/SuperNova

下载最新版本：https://github.com/supernova-ws/SuperNova/zipball/master

仓库：git://github.com/supernova-ws/SuperNova.git

### Supernova 在 sourceforge 上
项目页面：http://sourceforge.net/projects/supernova-ws/

下载引擎：http://sourceforge.net/projects/supernova-ws/files/

仓库：git://supernova-ws.git.sourceforge.net/gitroot/supernova-ws/supernova-ws


# 捐赠
您可以通过向以下钱包发送 WebMoney 来帮助：
  WMZ (WM-USD) Z409323360409
  WMR (WM-RUB) R961266352219
  WMU (WM-UAH) U726314912308

如果您使用 WebMoney 购买各种电子产品，您可以 a) 以优惠价格购买普通电子产品 b) 帮助项目。
http://gorlum.plati.ru - 手机充值、Skype、WoW - 游戏等电子产品，支持 WM。通过此链接进行的每次购买都会给我带来少量佣金。

记住！没有什么比捐赠更能增强对工作的信心！

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
更新日期：2020-07-27 V45d0
