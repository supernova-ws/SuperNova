    <center>
      <div id="copyright" style="padding-top: 2ex;">
        Project &quot;SuperNova.WS&quot; Release {D_SN_RELEASE} V{D_SN_VERSION} &copy; 2009-2012 Gorlum<br>
        Based on XNova RageRepack v226
      </div>
    </center>

    <script type="text/javascript"><!--
      var localTime = new Date();
      var serverTime = new Date('{SERVER_TIME}' * 1000);
      var timeDiff = serverTime.valueOf() - localTime.valueOf();

      jQuery(document).ready(function()
        {
          jQuery.post("time_probe.php", function(data)
            {
              localTime = new Date();
              serverTime = new Date(data * 1000);
              timeDiff = serverTime.valueOf() - localTime.valueOf();
            }
          );

          sn_timer();
        }
      );
    --></script>
  </body>
</html>