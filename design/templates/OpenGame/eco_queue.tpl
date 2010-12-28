<script type="text/javascript"><!--
<!-- DEFINE $QUE_NOT_EMPTY = false -->
<!-- IF .que -->
sn_timers.unshift(
{
  id: 'ov_{$QUE_ID}', 
  type: 3, 
  active: true, 
  start_time: {TIME_NOW}, 
  options: 
  { 
    msg_done: language['eco_que_empty'], 
    template: '<div class="que_item fl" title="[UNIT_NAME]">\
      <span class="unit_picture"><img src="{-path_prefix-}{dpath}gebaeude/[UNIT_ID].gif" align="top" width="100%" height="100%"></span>\
      <span style="position: absolute; top: 18px; left: 0px; width: 100%; height: 2ex; font-size: 100%;" class="icon_alpha">[UNIT_LEVEL]</span>\
      <span style="position: absolute; bottom: 0px; left: 0px; width: 100%; font-size: 100%;" class="icon_alpha">[UNIT_TIME]</span>\
    </div>',
    que: [
      <!-- BEGIN que -->
        <!-- IF $QUE_ID == que.QUE  -->
          ['{que.ID}', '{que.NAME}', {que.TIME}, {que.AMOUNT}, '{que.LEVEL}'],
          <!-- DEFINE $QUE_NOT_EMPTY = true -->
        <!-- ENDIF -->
      <!-- END que -->
    ]
  }
});
<!-- ENDIF -->
--></script>
