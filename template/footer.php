      </div>
    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!-- Set focus to search input -->
    <script src="bootstrap/js/bootstrap-transition.js"></script>
    <script src="bootstrap/js/bootstrap-alert.js"></script>
    <script src="bootstrap/js/bootstrap-dropdown.js"></script>
    <script src="bootstrap/js/bootstrap-scrollspy.js"></script>
    <script src="bootstrap/js/bootstrap-tab.js"></script>
    <script src="bootstrap/js/bootstrap-tooltip.js"></script>
    <script src="bootstrap/js/bootstrap-popover.js"></script>
    <script src="bootstrap/js/bootstrap-button.js"></script>
    <script src="bootstrap/js/bootstrap-collapse.js"></script>
    <script src="bootstrap/js/bootstrap-carousel.js"></script>
    <script src="bootstrap/js/bootstrap-typeahead.js"></script>
    
    <!--<script src="https://github.com/jschr/bootstrap-modal/raw/master/js/bootstrap-modalmanager.js"></script>
    <script src="https://github.com/jschr/bootstrap-modal/raw/master/js/bootstrap-modal.js"></script>-->
    <script src="bootstrap/js/bootstrap-modal.js"></script>
    <script src="resources/js/modalfix.js"></script>

    <script src="resources/js/jquery.ui.core.js"></script>
    <script src="resources/js/jquery.ui.datepicker.js"></script>
    <script src="resources/js/jquery.ui.i18n.js"></script>
    <style type="text/css">
      @import url("themes/<?php echo $theme?>/css/jquery-ui.css");
    </style>
    <script>
      $(function() {
        $.datepicker.setDefaults(
          $.datepicker.regional[ "" ]
        );
        $.datepicker.setDefaults({
          "dateFormat": "yy-mm-dd",
        });
      });
    </script>
  </body>
</html>
