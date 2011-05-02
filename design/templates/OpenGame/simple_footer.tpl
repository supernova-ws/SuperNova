    <center>
      <div id="copyright" style="padding-top: 2ex;">
        Project &quot;SuperNova.WS&quot; Release {D_SN_RELEASE} V{D_SN_VERSION} &copy; 2009-2011 Gorlum<br>
        Based on XNova RageRepack v226
      </div>
    </center>

    <script type="text/javascript"><!--
      var timeDiff = new Date('{SERVER_TIME}' * 1000).valueOf() - new Date().valueOf();

      jQuery(document).ready(function() 
        {
          jQuery.post("time_probe.php", function(data) 
            {
              //var result = jQuery("time", xml).text();
              //timeDiff = new Date(result * 1000).valueOf() - new Date().valueOf();
              timeDiff = new Date(data * 1000).valueOf() - new Date().valueOf();
            } 
          );

          sn_timer();
        }
      );
    --></script> 
  </body>
</html>