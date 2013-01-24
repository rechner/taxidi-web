<?php
  /* vim: tabstop=2:expandtab:softtabstop=2 */
  require_once 'config.php';

  $connection = pg_connect ("host=$dbhost dbname=$dbname 
                              user=$dbuser password=$dbpass");
                              
  //internationalisation
  $domain = "details";
  require_once 'locale.php';
                              
  $register = FALSE; // placeholder to show success message

  if ($_SERVER['REQUEST_METHOD'] == "POST" and array_key_exists('activity', $_POST))  {
    $result = pg_query($connection, "SELECT prefix FROM activities WHERE id = '".$_POST["activity"]."';") or
        die("Error in query: $query." . pg_last_error($connection));
    
    $prefix = pg_fetch_result($result, 0, 0);
    $paging = $prefix . "-" . substr($_POST["phone"], -4);
    
    $query = "INSERT INTO data " .
                "(name, lastname, phone, \"mobileCarrier\", paging, grade, " .
                "dob, activity, room, medical, parent1, parent2, "   .
                "\"parentEmail\", notes, visitor, \"joinDate\", "    .
                "\"lastSeen\", \"lastModified\", count) " .                  
              " VALUES (" .
                "'"    . $_POST["name"]         .
                "', '" . $_POST["lastname"]     .
                "', '" . $_POST["phone"]        . 
                "', '" . (isset($_POST["mobileCarrier"]) ? "1" : "0") . 
                "', '" . $paging .
                "', '" . $_POST["grade"]        .
                "', '" . $_POST["dob"]          . 
                "', '" . (is_numeric($_POST["activity"]) ? $_POST["activity"] : 0 ) . 
                "', '" . (is_numeric($_POST["room"]) ? $_POST["room"] : 0 )         . 
                "', '" . $_POST["medical"]      . 
                "', '" . $_POST["parent1"]      . 
                "', '" . $_POST["parent2"]      . 
                "', '" . $_POST["parent_email"] . 
                "', '" . $_POST["notes"]        . 
                "', 'f" .
                "', '" . date("Y-m-d", $_SERVER["REQUEST_TIME"]) .
                "', '" . date("Y-m-d", $_SERVER["REQUEST_TIME"]) .
                "', '" . date("Y-m-d H:i:s.u", $_SERVER["REQUEST_TIME"]) . 
              "', '0');";
          
    $result = pg_query($connection, $query) or 
      die("Error in query: $query." . pg_last_error($connection));
    pg_free_result($result);
    
    $register = TRUE;
  }
  
  $page_title = "Register";
  
  require_once "template/header.php";
  
?>

  <!-- sidebar -->
  <div class="span3">
    <div class="well sidebar-nav">
      <ul class="nav nav-list">
        <li class="nav-header"><?php echo _("Search"); ?></li>
        <li><a href="search.php"><i class="icon-search"></i><?php echo _("Search"); ?></a></li>
        <li><a href="#"><i class="icon-filter"></i><?php echo _("Advanced"); ?></a></li>
        <li><a href="#"><i class="icon-bookmark"></i><?php echo _("Saved Searches"); ?></a></li>
      </ul>
      <ul class="nav nav-list">
        <li class="nav-header"><?php echo _("Actions"); ?></li>
        <li class="active"><a href="#"><i class="icon-plus-sign"></i><?php echo _("Register"); ?></a></li>
        <li><a href="#"><i class="icon-user"></i><?php echo _("Register Visitor"); ?></a></li>
      </ul>
    </div>
  </div>
  <!-- /sidebar -->
  
  <div class="span9 well" style="overflow-x: auto;">
    <ul class="thumbnails">
      <li class="span6">
        <div class="page-header">
          <h1><?php echo _("Registration"); ?></h1>
        </div>
      </li>
    </ul>   
    
    <?php
      if ($register == TRUE) {
        echo "<div class=\"alert alert-success\">" .
          "<a class=\"close\" data-dismiss=\"alert\" href=\"#\">×</a>" .
          "<h4 class=\"alert-heading\">" . _("Registration Complete") . "</h4>" .
          sprintf(_("Successfully registered %s."),
            $_POST["name"] . " " . $_POST["lastname"]) .
          "</div>";
      }
    ?>
       
    <form class="form-horizontal" method="post" name="details">
      <fieldset>
        <div class="control-group">
          <label class="control-label" for="photo">Photo</label>
          <div class="controls">
            <input type="file" accept="image/*" id="photoinput" name="photoinput">
            <div>
              <div id="fileselecterror" class="alert alert-error" style="display: none;">
                <?php echo _('Error: File type not supported.') ?>
              </div>
              <div id="photodndbox">
                <div class="progress progress-striped active" style="display: none; margin: 6px 0 9px;">
                  <div id="photoupload_progressbar" style="width: 0%" class="bar"></div>
                </div>
              </div>
              <div class="thumbnail" style="width: 100px; margin: 10px">
                <img id="photopreview" style="width: 100px;" src="photo.php<?php echo "?id=" . $edata["picture"] ?>">
              </div>
              <img id="tarimg"></canvas>  
              <a id="uploadphoto" class="btn btn-primary"><?php echo _('Upload Photo') ?></a>
            </div>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="name"><?php echo _("Name"); ?></label>
          <div class="controls">
            <div class="container-fluid" style="padding: 0;">
              <div class="row-fluid">
                <input type="text" class="input span5" name="name" id="name" 
                    style="max-width: 350px" required
                    placeholder="<?php echo _("Name"); ?>" value="<?php echo $edata["name"]; ?>">
                <div style="display: inline-block;"> </div>
                <input type="text" class="input span5" name="lastname" id="lastname" required
                    placeholder="<?php echo _("Lastname"); ?>" value="<?php echo $edata["lastname"]; ?>">
              </div>
            </div>
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="phone"><?php echo _("Phone"); ?></label>
          <div class="controls">
            <input type="tel" class="input-medium" name="phone" id="phone" required
                onKeyDown="javascript:return dFilter (event.keyCode, this, '<?php echo _("(###) ###-####"); ?>');"
                placeholder="<?php echo _("Phone"); ?>" value="<?php echo $edata["phone"]; ?>">
            <label class="checkbox">
              <input type="checkbox" name="mobileCarrier" 
                <?php echo $edata["mobileCarrier"] ? "checked" : ""?>> <?php echo _("Mobile Phone"); ?>
            </label>
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="grade"><?php echo _("Grade"); ?></label>
          <div class="controls">
            <div style="width: 220px; float: left; margin-right: 4px;">
              <input type="text" class="input-small" name="grade" id="grade"
                placeholder="<?php echo _("Grade"); ?>" value="<?php echo $edata["grade"]; ?>">
              <label for="dob" style="float: right; padding-top: 5px; margin-right: 16px;">
                <?php echo _("Birthdate"); ?></label>
            </div>
            <div>
              <input type="date" class="input-small" name="dob" id="dob"
                 onKeyDown="return dFilter (event.keyCode, this, '####-##-##');"
                 onkeyup="showAge();" oninput="showAge();" required
                 pattern="^[12]\d{3}-(0?[1-9]|1[0-2])-([012]?[0-9]|3[01])$"
                 data-validation-pattern-message="Must be a valid ISO8601 date."
                placeholder=<?php echo _("YYYY-MM-DD"); ?> 
                value="<?php echo $edata["dob"]; ?>"> 
                <span class="help-inline" id="age">Age: ?</span>
            </div>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="activity">
              <?php echo _("Activity"); ?>
          </label>
          <div class="controls">
            <select name="activity" id="activity" required>
              <?php
                echo (is_null($edata["activity"]) ? "<option disabled selected>" .
                    _("Activity") . "</option>\n" : "");
                $query = "SELECT id, name FROM activities;";
                $result = pg_query($connection, $query) or
                    die("Error in query: $query." . pg_last_error($connection));
                while ($data = pg_fetch_assoc($result)) {
                  echo "<option value=\"{$data["id"]}\"" . 
                    ($data["id"] == $edata["activity"] ? " selected" : "") . 
                    ">{$data["name"]}</option>\n";
                }
                pg_free_result($result);
              ?>
            </select>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="room"><?php echo _("Room"); ?></label>
          <div class="controls">
            <select name="room" id="room">
              <?php
                echo (is_null($edata["room"]) ? "<option disabled selected>" .
                    _("Room") . "</option>\n" : "");
                $query = "SELECT id, name FROM rooms;";
                $result = pg_query($connection, $query) or
                  die("Error in query: $query." . pg_last_error($connection));
                while ($data = pg_fetch_assoc($result)) {
                  echo "<option value=\"{$data["id"]}\"" . 
                    ($data["id"] == $edata["room"] ? " selected" : "") .
                    ">{$data["name"]}</option>\n";
                }
                pg_free_result($result);
              ?>
            </select>
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="medical"><?php echo _("Medical"); ?></label>
          <div class="controls">
            <input type="text" class="input" name="medical" id="medical"
                placeholder="<?php echo _("Medical"); ?>"
                value="<?php echo $edata["medical"]; ?>">
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="parent1"><?php echo _("Parent 1"); ?></label>
          <div class="controls">
            <input type="text" class="input" name="parent1" id="parent1"
              placeholder="<?php echo _("Parent 1"); ?>" required
              value="<?php echo $edata["parent1"]; ?>">
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="parent2"><?php echo _("Parent 2"); ?></label>
          <div class="controls">
            <input type="text" class="input" name="parent2" id="parent2" 
              placeholder="<?php echo _("Parent 2"); ?>"
              value="<?php echo $edata["parent2"]; ?>">
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="parent_email"><?php echo _("Parent's Email"); ?></label>
          <div class="controls">
            <input type="email" class="input" name="parent_email" id="parent_email"
              placeholder="<?php echo _("Email"); ?>" 
              value="<?php echo $edata["parentEmail"]; ?>">
              <button class="btn" type="button" 
                onClick="parent.location='mailto:<?php echo $edata["parentEmail"]; ?>'">
              <i class="icon-envelope"></i></button>
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="notes"><?php echo _("Notes"); ?></label>
          <div class="controls">
            <div class="container-fluid" style="padding: 0;">
              <div class="row-fluid">
                <textarea name="notes" id="notes" class="span12"
                  style="max-width: 500px"
                  placeholder="<?php echo _("Notes"); ?>"></textarea>
              </div>
            <div>
          </div>
        </div>
        <div class="form-actions">
          <input type="submit" class="btn btn-primary btn-large" value="<?php echo _("Create Record"); ?>" />
          <button class="btn btn-large" href="register.php"><?php echo _("Cancel"); ?></button>
        </div>
      </fieldset>
    </form>
  </div>
</div>

<script src="resources/js/dFilter.js"></script>
<script src="resources/js/jqBootstrapValidation.js"></script>
<script>
  
  function showAge(ind) {
        
          document.getElementById('age').innerHTML = "Age: "+
            getAge($('#dob').attr('value'));
            
      }
      
      function getAge(a) {
        a = new Date(Date.parse(a.replace(/-/g, "/")));
        var b = new Date, 
          years  = b.getYear () - a.getYear (),
          months = b.getMonth() - a.getMonth(),
          days   = b.getDate () - a.getDate ();
        if (b <= a) return "Invalid DOB."
        months < 0 && (years --, months +  12);
        days   < 0 && (months--, days   += 31);
        months < 0 && (years --, months  = 11);
        
        a = [], b = function(b, c) {
          b > 0 && a.push(b + c + (b > 1 ? "s" : "")) }
        b(years , " year" );
        b(months, " month");
        b(days  , " day"  );
        a.length > 1 && (a[a.length - 1] = "and " + a[a.length - 1]);
        return(!a.length ? "0 days" : "" ) +
          a.join(a.length > 2 ? ", " : " ") + " old."
      }

</script>


<link href="resources/css/Jcrop.min.css" rel="stylesheet">
<script src="https://raw.github.com/tapmodo/Jcrop/master/js/jquery.Jcrop.min.js"> </script>
<script src="resources/js/canvas_toblob.js"> </script>
<script src="resources/js/jquery.getparams.js"> </script>

<script type="text/javascript">
//TODO move this entire script block to head, after template insertion.
$(function(){
  
  var tempphoto = null;   // photo user has selected
  var photoupload = null; // photo upload xhr request
  var uploadphoto = {
    cropper : null,
    "showerror" : function (errmsg) {
      $("#photoinput").val("");
      $("#fileselecterror").html(errmsg).show();
    }, 
    "hideerror" : function () {
      $("#fileselecterror").hide();
    },
    "setprogress" : function(percent) {
      $("#photoupload_progressbar").css("width", Math.round(percent) + "%");
    },
    "setstate" : function(state) {
      //TODO switch all of this to actual css classes
      $("body").css("cursor", "default");
      $("#photodndbox").css("background-color", "#F5F5F5");
      $("#photodndbox > div.progress").hide();
      $("#drophere").hide();
      switch (state) {
        case 0: // browse for file
          $("#photodndbox > div.input-append").show();
          break;
        case 1: // drag and drop hover
          $("#photodndbox").css("background-color", "#5F5F5F");
          $("#photodndbox > div.input-append").hide();
          $("#drophere").show();
          break;
        case 2: // progress bar
          uploadphoto.setprogress(0);
          $("body").css("cursor", "progress");
          $("#photodndbox > div.input-append").hide();
          $("#photodndbox > div.progress").show();
          break;
      }
    }, 
    "getcroppedphoto" : function(callback) {
      var canvas = $("<canvas>")[0];
      var selection = uploadphoto.cropper.tellSelect();
      var x = Math.floor(selection.x);
      var y = Math.floor(selection.y);
      var s = Math.floor(selection.w);
      if (s == 0) { //extra safety
        var ns = uploadphoto.selectcenter();
        x = ns[0];
        y = ns[1];
        s = ns[2];
      }
      canvas.width = s;
      canvas.height = s;
      canvas.getContext("2d").drawImage($("#photopreview")[0], x, y, s, s, 0, 0, s, s);
      canvas.toBlob(callback);
    },
    "selectcenter" : function() {
      var bounds = uploadphoto.cropper.getBounds(); // [width, height]
      var w = Math.floor(bounds[0]);
      var h = Math.floor(bounds[1]);
      if (w < h) { //tall image
        var o = Math.floor((h - w) / 2);
        return [0, o, w, o + w];
      } else if (h < w) { // wide image
        var o = Math.floor((w - h) / 2);
        return [o, 0, o + h, h];
      } else {
        return [0, 0, w, w];
      }
    }
  };

  var noop = function(e) {
      e.stopPropagation();
      e.preventDefault();
  }
  $("#photoinput").on("change", function() {
    previewphoto(this.files[0]);
  });

  var previewphoto = function(file) {
    tempphoto = file;
    uploadphoto.hideerror();
    document.getElementById("photoinput").value = file.name;
    var fileurl = window.URL.createObjectURL(file);
    $("#photopreview")[0].src = fileurl;
    uploadphoto.cropper.setImage(fileurl, function() {
      this.setSelect(uploadphoto.selectcenter());
    });
  }
  
  document.getElementById("uploadphoto").onclick = function() {
    uploadphoto.setstate(2);
    uploadphoto.getcroppedphoto(function(file) {
      if (file.size < <?php echo $photo_maxsize; ?>) {
        var fd = new FormData();
        //fd.append("id", /[\\?&]id=([^&#]*)/.exec(window.location.search)[1].replace(/\+/g, " "));
        fd.append("photo", file);
  
        photoupload = new XMLHttpRequest();
        photoupload.upload.addEventListener("progress", function(evt) {
          if (evt.lengthComputable) {
            uploadphoto.setprogress(evt.loaded * 100 / evt.total)
          } else {
            uploadphoto.setprogress(0);
          }
        }, false);
        photoupload.addEventListener("load",  function() { //TODO
          //TODO redo this entire method
          uploadphoto.setstate(0);
          tempphoto = null;
          var response = JSON.parse(this.responseText);
          if (response.success) {
            $("#photomain").attr("src", "photo.php?id=" + response.newphotoid);
            var date1 = response.modified.split(".")[0].split(" ");
            var date2 = new Date(date1[0]);
            $("#lastmodified").text("<?php echo _("Modified") .": "; ?>" + date2.getUTCDate() + " " + (new Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec")[date2.getUTCMonth()]) + " " + date2.getUTCFullYear() + " " + date1[1]);
            $('#photouploadModal').modal("hide");
          } else {
            //TODO server side error!
            //$("#fileselecterror").text("ERROR TODO!!!");
            //$("#fileselecterror").css("display", "block");
          }
        }, false);
        photoupload.addEventListener("error", function() {
          uploadphoto.setstate(0);
          uploadphoto.showerror("Error uploading file."); //TODO switch to language array
        }, false);
        photoupload.addEventListener("abort", function() {
          uploadphoto.setstate(0);
          uploadphoto.showerror("Error: upload aborted."); //TODO switch to language array
        }, false);
        photoupload.open("POST", "photoupload.php");
        photoupload.send(fd);
      } else {
        uploadphoto.showerror("Error: file too large."); //TODO switch to language array
        uploadphoto.setstate(0);
      }
    });
  };
  
  $("#photouploadModal").on({
    "hide" : function() {
      try {
        photoupload.abort();
        photoupload = null;
      } catch (err) {}
    }, 
    "hidden" : function() {
      uploadphoto.hideerror();
      $("#photoinput").val("");
      uploadphoto.cropper.setImage($("#photomain").attr("src"));
      uploadphoto.setstate(0);
      tempphoto = null;
    },
  });
  
  $('#photopreview').Jcrop({
    "aspectRatio" : 1,
    "boxWidth"    : 250,
    "boxWidth"    : 250,
    "onRelease"   : function() {
      this.setSelect(uploadphoto.selectcenter());
    },
  }, function() {
    uploadphoto.cropper = this;
    this.setSelect(uploadphoto.selectcenter());
  });
  
  $("#cancelbutton").click(function() {
    window.location.href = "search.php?search=" + encodeURIComponent($.getparam("query"));
  });
});

//TODO, this function is dumb, fix it
selecttab = function(tab) {
  var tabs = ["main", "extended"];
  for (var t in tabs) {
    var ct = tabs[t];
    if (ct == tab) {
      document.getElementById("tabselect_" + ct).setAttribute("class", "active");
      document.getElementById("tabpane_" + ct).style.display = "block";
    } else {
      document.getElementById("tabselect_" + ct).setAttribute("class", "");
      document.getElementById("tabpane_" + ct).style.display = "none";
    }
  }
  document.getElementById("tabinput").value = tab;
}
</script>

<?php
  require_once "template/footer.php" ;
  pg_close($connection);
?>


