<?php
  /* vim: tabstop=2:expandtab:softtabstop=2 */
  require_once 'config.php';
  
  function resize_image($file, $w, $h, $crop=FALSE) {
    // @param file, width, height, crop
    // Resize an image using Imagick
    $img = new Imagick($file);
    if ($crop) {
        $img->cropThumbnailImage($w, $h);
    } else {
        $img->thumbnailImage($w, $h, TRUE);
    }

    return $img;
  }

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
    
    $photo_ref = "";
    if (array_key_exists("photo", $_FILES)) {
      if ($_FILES["photo"]["type"] == "image/png" or 
        $_FILES["photo"]["type"] == "image/jpeg") {
        if ($_FILES["photo"]["size"] < $photo_maxsize) {
          // resize and save photo+thumbnail
          
          /* python analogue of this bit checks the file system for the
           * next image in the sequence, not the database */
          $result = pg_query($connection, "SELECT max(picture::integer) FROM data WHERE picture != '';") or 
            die("Error in query: $query." . pg_last_error($connection));
          $rdata = pg_fetch_assoc($result);
          pg_free_result($result);
          $nextImage = intval($rdata["max"]) + 1;
          
          $parts = explode("/", $_FILES["photo"]["type"]);
          $target_path = $photo_path . str_pad($nextImage, 6, "0", 0) . 
                         "." . $parts[1];
          $target_thumb = $photo_path . "/thumbs/" . 
                          str_pad($nextImage, 6, "0", 0) . "." . $parts[1];
          $photo_ref = str_pad($nextImage, 6, "0", 0);
          //resize to 480x480
          $tmp = resize_image($_FILES["photo"]['tmp_name'], 480, 480);
          if ($tmp->writeImage($target_path)) {
            //resize to 128x128
            $thumb = resize_image($_FILES["photo"]['tmp_name'], 128, 128);
            if ($thumb->writeImage($target_thumb)) {
              unlink($_FILES["photo"]['tmp_name']);
              $photoSuccess = TRUE;
            } else {
              $photoSuccess = FALSE;
              $reason = "Unable to save thumbnail";
            } 
          } else {
            $photoSuccess = FALSE;
            $reason = "Unable to resize image";
          }
        } else {
          $photoSuccess = FALSE;
          $reason = "Image too large";
        }
      } else {
        $photoSuccess = FALSE;
        $reason = "Unsupported image type";
      }
    } else { $photoSuccess = TRUE; }
			
    $query = "INSERT INTO data " .
                "(name, lastname, phone, \"mobileCarrier\", paging, grade, " .
                "dob, activity, room, medical, parent1, parent2, "   .
                "\"parentEmail\", notes, visitor, \"joinDate\", "    .
                "\"lastSeen\", \"lastModified\", count, picture) " . 
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
              "', '0', '" . $photo_ref . "') RETURNING id;";
          
    $result = pg_query($connection, $query) or 
      die("Error in query: $query." . pg_last_error($connection));
    $id = pg_fetch_result($result, 0, 0);
    pg_free_result($result);
    
    $register = TRUE;
    
    header("Content-Type: application/json");
    if ($register == TRUE) {
      echo "{\"success\":true,\"id\":".$id.",\"name\":\"" . $_POST["name"] . " " . $_POST["lastname"] . "\"}";
      exit;
    } else {
      echo "{\"success\":false}";
      exit;
    }
    
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
        <div class="page-header" style="margin-bottom: 0px;">
          <h1><?php echo _("Registration"); ?></h1>
        </div>
      </li>
    </ul>   
    
    <div id="successalert" class="alert alert-success" style="display:none;">
    <a class="close" data-dismiss="alert" href="#">×</a>
      <h4 class="alert-heading"> <?php echo _("Registration Complete"); ?> </h4>
      <!-- sprintf(_("Successfully registered <a href=\"details.php?id=" . $id . "\" >%s.</a>"),
            $_POST["name"] . " " . $_POST["lastname"]) .-->
      <span> </span>
    </div>
       
    <input type="file" accept="image/*" id="photoinput" name="origphoto" style="height: 0px; width: 0px;">
    <form enctype="multipart/form-data" class="form-horizontal" action="" method="post" name="details">
      <fieldset>
        <div class="control-group">
          <label class="control-label">Photo</label>
          <div class="controls">
            <div style="display: table; width: 100%; height: 100%;" class="input-append">
              <input type="text" readonly="" onclick="document.getElementById('photoinput').click();" class="input-xlarge" id="fakefileinput" style="cursor: pointer; cursor: hand;">
              <a onclick="document.getElementById('photoinput').click();" style="margin-left: -4px; border-radius: 0 3px 3px 0; cursor: pointer; cursor: hand;" class="btn">Browse</a>
              <div style="display: table-cell; vertical-align: middle; text-align: right;">
                Drop file here.        </div>
            </div>
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
                pattern="^\([2-9]\d\d\)\ [2-9]\d\d-\d\d\d\d$"
                data-validation-pattern-message="Must be a valid North-American telephone number."
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
                $query = "SELECT id, name FROM rooms;";
                $result = pg_query($connection, $query) or
                  die("Error in query: $query." . pg_last_error($connection));
                while ($data = pg_fetch_assoc($result)) {
                  echo "<option value=\"{$data["id"]}\">{$data["name"]}</option>\n";
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
                placeholder="<?php echo _("Medical"); ?>">
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="parent1"><?php echo _("Parent 1"); ?></label>
          <div class="controls">
            <input type="text" class="input" name="parent1" id="parent1"
              placeholder="<?php echo _("Parent 1"); ?>" required>
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="parent2"><?php echo _("Parent 2"); ?></label>
          <div class="controls">
            <input type="text" class="input" name="parent2" id="parent2" 
              placeholder="<?php echo _("Parent 2"); ?>">
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="parent_email"><?php echo _("Parent's Email"); ?></label>
          <div class="controls">
            <input type="email" class="input" name="parent_email" id="parent_email">
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
<script src="bootstrap/js/jqBootstrapValidation.js"></script>
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
$(function(){
  //$("input,select,textarea,checkbox").not("[type=submit]").jqBootstrapValidation();
  
  var cropper = null; // jcropper instance
  
  var selectcenter = function() {
    var bounds = cropper.getBounds(); // [width, height]
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
  
  var selectcenter = function() {
      var bounds = cropper.getBounds(); // [width, height]
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
  
  var getcroppedphoto = function(callback) {
    var canvas = $("<canvas>")[0];
    var selection = cropper.tellSelect();
    var x = Math.floor(selection.x);
    var y = Math.floor(selection.y);
    var s = Math.floor(selection.w);
    if (s == 0) { //extra safety
      var ns = selectcenter();
      x = ns[0];
      y = ns[1];
      s = ns[2];
    }
    canvas.width = 480;
    canvas.height = 480;
    canvas.getContext("2d").drawImage($("#photopreview")[0], x, y, s, s, 0, 0, 480, 480);
    canvas.toBlob(callback);
  }
  
  var resetjcrop = function() {
    cropper && cropper.destroy();
    $("#photopreview").attr("src", "photo.php")
    $('#photopreview').Jcrop({
      "aspectRatio" : 1,
      "boxWidth"    : 250,
      "boxWidth"    : 250,
      "onRelease"   : function() {
        this.setSelect(selectcenter());
      },
    }, function() {
      cropper = this;
      this.setSelect(selectcenter());
    });
  }
  resetjcrop();

  $("#photoinput").on("change", function() {
    var filereader = new FileReader();
    filereader.onload = function(e) {
      $("#photopreview")[0].src = e.target.result;
      cropper.setImage(e.target.result, function() {
        this.setSelect(selectcenter());
      });
    }
    $("#fakefileinput").val(this.files[0].name);
    filereader.readAsDataURL(this.files[0]);
  });
  
  //append resized image blob to formdata before submitting
  //https://developer.mozilla.org/en-US/docs/DOM/XMLHttpRequest/FormData/Using_FormData_Objects
  var disablesubmit = false;
  $("form[name=details]").submit(function(e) {
    if (!disablesubmit) {
      disablesubmit = true;
      $("form[name=details] input[type=submit]").attr("disabled", true);
      var fd = new FormData(document.forms.namedItem("details"));
      getcroppedphoto(function(file) {
        fd.append("photo", file);
        $.ajax({
          url: $("form[name=details]").attr("action"),
          type: "POST",
          data: fd,
          processData: false,
          contentType: false
        }).done(function(data) {
          if (data.success) {
            $("#successalert span").html("Successfully registered <a href=\"details.php?id=" + data.id + "\"> " + data.name + "</a>");
            $("#successalert").show();
            $("form[name=details]")[0].reset();
            resetjcrop();
            document.body.scrollTop = document.documentElement.scrollTop = 0;
          }
        }).always(function(data) {
          disablesubmit = false;
          $("form[name=details] input[type=submit]").attr("disabled", false);
        });
      });
    }
    e.preventDefault();
    return false;
  });
});
</script>

<?php
  require_once "template/footer.php" ;
  pg_close($connection);
?>


