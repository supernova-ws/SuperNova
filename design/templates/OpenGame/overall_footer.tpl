    <center>
      <div id="copyright" style="padding-top: 2ex;">
        Project &quot;SuperNova.WS&quot; &copy; 2009-2010 Gorlum
      </div>
    </center>

    <script type="text/javascript"><!--
      var timeDiff = new Date('{SERVER_TIME}' * 1000).valueOf() - new Date().valueOf();

      jQuery(document).ready(function() 
        {
          jQuery.post("../../../time_probe.php", function(xml) 
            {
              var result = jQuery("time", xml).text();
              timeDiff = new Date(result * 1000).valueOf() - new Date().valueOf();
            } 
          );

          sn_timer();
        }
      );
    --></script> 
  </body>
</html>