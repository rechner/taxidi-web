<!DOCTYPE html>
<!-- vim: tabstop=2:expandtab:softtabstop=2 -->
<?php
  
  //get input:
  if (is_numeric($id = $_GET["id"])) { 
    require_once 'config.php';
    require_once 'functions.php';
    
    //internationalisation
    $domain = "details";
    require_once 'locale.php';
    

    /*$connection = pg_connect ("host=$dbhost dbname=$dbname 
                              user=$dbuser password=$dbpass");*/
    $dbh = db_connect();

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
      
      /*$query = "UPDATE data SET " .
                    "   name = '"              . $_POST["name"]         .
                    "', lastname = '"          . $_POST["lastname"]     .
                    "', phone = '"             . $_POST["phone"]        . 
                    "', \"mobileCarrier\" = '" . (isset($_POST["mobileCarrier"]) ? "1" : "0") . 
                    "', grade = '"             . $_POST["grade"]        .
                    "', dob = '"               . $_POST["dob"]          . 
                    "', activity = '"          . (is_numeric($_POST["activity"]) ? $_POST["activity"] : 0 ) . 
                    "', room = '"              . (is_numeric($_POST["room"]) ? $_POST["room"] : 0 )         . 
                    "', medical = '"           . $_POST["medical"]      . 
                    "', parent1 = '"           . $_POST["parent1"]      . 
                    "', parent2 = '"           . $_POST["parent2"]      . 
                    "', \"parentEmail\" = '"   . $_POST["parent_email"] . 
                    "', notes = '"             . $_POST["notes"]        . 
                    "', \"lastModified\" = '"  . date("Y-m-d H:i:s.u", $_SERVER["REQUEST_TIME"]) . 
                  "' WHERE id = $id;"; */
      $sql = "UPDATE data SET name = :name, lastname = :lastname, phone = :phone, \"mobileCarrier\" = :mobilecarrier, grade = :grade, dob = :dob, activity = :activity, room = :room, medical = :medical, parent1 = :parent1, parent2 = :parent2, \"parentEmail\" = :parentemail, notes = :notes, \"lastModified\" = :lastmodified WHERE id = :id;";
      
      $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
      $sth->execute(array(":name" => $_POST["name"], ":lastname" => $_POST["lastname"], ":phone" => $_POST["phone"], ":mobilecarrier" => isset($_POST["mobileCarrier"]) ? "1" : "0", ":grade" => $_POST["grade"], ":dob" => $_POST["dob"], ":activity" => is_numeric($_POST["activity"]) ? $_POST["activity"] : 0, ":room" => is_numeric($_POST["room"]) ? $_POST["room"] : 0, ":medical" => $_POST["medical"], ":parent1" => $_POST["parent1"], ":parent2" => $_POST["parent2"], ":parentemail" => $_POST["parent_email"], ":notes" => $_POST["notes"], ":id" => $id, ":lastmodified" => date("Y-m-d H:i:s.u", $_SERVER["REQUEST_TIME"])));
      /*print("\nfoo\n");
      $temp = $sth->errorInfo();
      print($temp[2]);
      print("\nfoo\n");*/
      
      //TODO check for error
      $modifysuccess = true;
    }
                              
    ///*
    $sql = "SELECT name, lastname, dob, activity, room, grade, phone, 
                     \"mobileCarrier\", paging, parent1, parent2,
                     \"parentEmail\", medical, \"joinDate\", \"lastSeen\",
                     \"lastModified\", count, visitor, expiry, \"noParentTag\",
                     barcode, picture, notes
                FROM data WHERE id = :id;";
    /*$result = pg_query($connection, $query) or 
      die("Error in query: $query." . pg_last_error($connection));*/
    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute(array(":id" => $id));
    $edata = $sth->fetch(PDO::FETCH_ASSOC);
    
    if (!$edata) {
      header("HTTP/1.1 400 Bad Request");
      die("ID " . $id . " does not exist.");
    }
    
    $page_title = "{$edata["name"]} {$edata["lastname"]}";
    
    // The parent search term, if applicable:
    if (array_key_exists('query', $_GET)) {
      $search = $_GET['query'];
    } else {
      $search = '';
    }
    
    $age = date_diff(new DateTime($edata["dob"]), new DateTime("now"));
    if ($age->y >= 1) {
      $agestr = $age->format( _("Age") . ": %y");
    } else {
      $agestr = $age->format( _("Age") , ": %m " . _("months"));
    }

    pg_free_result($result);
  
  } else {
    header("HTTP/1.1 400 Bad Request");
    die("Missing or malformed ID parameter");
  }

  function formatByteSize($bytes) {
    if(!empty($bytes)) {
      $s = array('bytes', 'kiB', 'MiB', 'GiB', 'TiB', 'PiB');
      $e = floor(log($bytes) / log(1024));

      $rounded = round($bytes / pow(1024, floor($e)), 2);
      global $locale_info; //from locale.php
      return number_format($rounded, 1, 
        $locale_info['decimal_point'],
        $locale_info['thousands_sep']) . $s[$e];
    }
  }

  require_once "template/header.php";
?> 
<!-- sidebar -->
<div class="span3">
  <div class="well sidebar-nav">
    <ul class="nav nav-list">
      <li class="nav-header"><?php echo _('Search') ?></li>
      <li><a href="search.php"><i class="icon-search"></i><?php echo _('Search') ?></a></li>
      <li><a href="advsearch.php"><i class="icon-filter"></i><?php echo _('Advanced') ?></a></li>
      <li><a href="#"><i class="icon-bookmark"></i><?php echo _('Saved Searches') ?></a></li>
      <li class="nav-header"><?php echo _('Actions') ?></li>
      <li><a href="print.php?id=<?php echo $_GET["id"]; ?>" target="_blank"><i class="icon-print"></i><?php echo _('Print details') ?></a></li>
      <li><a href="#"><i class="icon-tags"></i><?php echo _('Print nametags') ?></a></li>
      <li><a data-toggle="modal" href="#downloadModal"><i class="icon-download"></i><?php echo _('Download details') ?></a></li>
      <li><a href="#"><i class="icon-bullhorn"></i><?php echo _('Create incident report') ?></a></li>
      <li><a data-toggle="modal" href="#deleteModal"><i class="icon-trash"></i><?php echo _('Delete record') ?></a></li>
    </ul>
  </div>
  <input id="realfileinput" type="file" style="height: 0px; width: 0px;">
</div>
<!-- /sidebar -->

<div id="downloadModal" class="modal hide fade">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h3><?php echo _('Download Record Data') ?></h3>
  </div>
  <div class="modal-body">
    <h4><?php echo _('Download format') ?></h4>
    <form class="form-horizontal">
      <fieldset>  
        <div class="control-group">
          <label class="control-label" for="inlineCheckboxes"><?php echo _('Select Format') ?>:</label>
          <div class="controls">
            <label class="radio inline">
              <input type="radio" name="format" id="inlineCheckbox1" value="option1" checked> csv
            </label>
            <label class="radio inline">
              <input type="radio" name="format" id="inlineCheckbox2" value="option2"> xml
            </label>
            <label class="radio inline">
              <input type="radio" name="format" id="inlineCheckbox3" value="option3"> yaml
            </label>
          </div>        
        </div>
      </fieldset>
    </form>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal" ><?php echo _('Close') ?></a>
    <a href="#" class="btn btn-primary"><?php echo _('Download') ?></a>
  </div>
</div>

<div id="deleteModal" class="modal hide fade">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h3><?php echo _('Delete Record') ?></h3>
  </div>
  <div class="modal-body">
    <h4><?php echo _('Are you sure you want to delete this record?') ?></h4>
    <p><?php echo _('This action cannot be undone.') ?></p>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal" ><?php echo _('Close') ?></a>
    <a href="delete.php?id=<?php 
            if ($query != '') {
              echo $id . "&query=" . $search;
            } else {
              echo $id;
            } ?>" class="btn btn-danger"><?php echo _('Delete') ?></a>
  </div>
</div>

<div id="photouploadModal" class="modal hide fade">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h3><?php echo _('Upload New Photo') ?></h3>
  </div>
  <div class="modal-body">
    <p><?php echo sprintf(_("Uploads are limited to %s and must be in ".
              "jpeg or png format"), formatByteSize($photo_maxsize)); ?></p>
    <div>
      <div class="thumbnail" style="width: 250px; margin: 0 auto;">
        <img id="photopreview" style="width: 250px;" src="photo.php<?php echo "?id=" . $edata["picture"] ?>">
      </div>
      <img id="tarimg"></canvas>  
    </div>
    <br>
    <div id="fileselecterror" class="alert alert-error" style="display: none;">
      <?php echo _('Error: File type not supported.') ?>
    </div>
    <div class="well" id="photodndbox" style="height: 33px">
      <div class="progress progress-striped active" style="display: none; margin: 6px 0 9px;">
        <div id="photoupload_progressbar" style="width: 0%" class="bar"></div>
      </div>
      <div id="drophere" style="width: 100%; height: 100%; display: none;">
        <div style="width: 100%; height: 100%; display: table;">
          <div style="display: table-cell; vertical-align: middle; text-align: center;">
            <?php echo _('Drop file here.') ?>
          </div>
        </div>
      </div>
      <div class="input-append" style="display: table; width: 100%; height: 100%;">
        <input style="cursor: pointer; cursor: hand;" id="fakefileinput" class="input-xlarge" type="text" onclick="document.getElementById('realfileinput').click();" readonly>
        <a class="btn" style="margin-left: -4px; border-radius: 0 3px 3px 0; cursor: pointer; cursor: hand;" onclick="document.getElementById('realfileinput').click();"><?php echo _('Browse') ?></a>
        <div style="display: table-cell; vertical-align: middle; text-align: right;">
          <?php echo _('Drop file here.') ?>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <a class="btn" data-dismiss="modal" ><?php echo _('Close') ?></a>
    <a id="uploadphoto" class="btn btn-primary"><?php echo _('Upload Photo') ?></a>
  </div>
</div>

<script type="text/javascript">
//TODO move this entire script block to head, after template insertion.
$(function(){
  $(".datepicker").datepicker();
  
  var tempphoto = null;   // photo user has selected
  var photoupload = null; // photo upload xhr request
  var uploadphoto = {
    cropper : null,
    "showerror" : function (errmsg) {
      $("#fakefileinput").val("");
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
      try {
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
      } catch (e) {
        callback(tempphoto);
      }
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
  $("#photodndbox").on({
    "dragenter" : function(e) {
      noop(e);
      uploadphoto.setstate(1);
    },
    "dragover" : noop,
    "dragleave" : function(e) {
      noop(e);
      uploadphoto.setstate(0);
    },
    "dragend" : function(e) {
      noop(e);
      uploadphoto.setstate(0);
    },
    "drop" : function(e) {
      noop(e);
      uploadphoto.setstate(0);
      previewphoto(e.originalEvent.dataTransfer.files[0]);
    }
  });
  $("#realfileinput").on("change", function() {
    previewphoto(this.files[0]);
  });

  var previewphoto = function(file) {
    tempphoto = file;
    uploadphoto.hideerror();
    document.getElementById("fakefileinput").value = file.name;
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
        fd.append("id", /[\\?&]id=([^&#]*)/.exec(window.location.search)[1].replace(/\+/g, " "));
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
      $("#fakefileinput").val("");
      uploadphoto.cropper.setImage($("#photomain").attr("src"));
      uploadphoto.setstate(0);
      tempphoto = null;
    },
  });
  
  try {
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
  } catch(e) { }
  
  $("#cancelbutton").click(function() {
    var service = encodeURIComponent($.getparam("query"));
    window.location.href = "search.php?search=" + encodeURIComponent($.getparam("query")) + (service ? "&service=" + service : "");
  });
  
  $("#checkin").click(function() {
    var but = $(this);
    if (!but.hasClass("disabled")) {
      but.addClass("disabled");
      $.ajax({
        url:  "checkin.php",
        type: "POST",
        data: {
          person:  <?php echo $_GET["id"];?>,
          service: <?php echo $_GET["service"];?>
        },
      }).done(function(data) {
        if (data.success) {
          //TODO
        }
      }).always(function(data) {
        but.removeClass("disabled");
      });
    }
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

<div class="span9">

<?php
  if ($modifysuccess) {
    echo "<div class=\"alert\">
            <h4 class=\"alert-heading\">
              Changes successfully saved.
            </h4>
          </div>";
  }
?>
<div class="well" style="overflow-x: auto;">
  <ul class="thumbnails">
    <li class="span3" style="text-align: center;">
      <a href="photo.php<?php echo "?id=" . $edata["picture"] ?>" class="thumbnail">
        <img id="photomain" src="photo.php<?php echo "?id=" . $edata["picture"] ?>"/>
      </a>
      <a href="#photouploadModal" data-toggle="modal"><?php echo _("Upload New Photo"); ?></a>
    </li>
    <li class="span6">
      <div class="page-header">
        <h1><?php echo "{$edata["name"]}</h1> <h2>{$edata["lastname"]}"; ?></h2>
        <?php echo ($edata["visitor"] == "f" ? _("Member") : _("Visitor")) . "<br>"; 
              echo ($edata["visitor"] == "f" ? "" : sprintf(_("Expiry: %s"), $edata["expiry"])) . "<br>";
              echo sprintf(_("Created: %s"), strftime(_("%e %B %Y"), strtotime($edata["joinDate"]))) . "<br>";
              echo sprintf(_("Last Seen: %s"), strftime(_("%e %B %Y"), strtotime($edata["lastSeen"]))) . "<br>";
              echo "<span id=\"lastmodified\">" . sprintf(_("Modified: %s"),  
                strftime(_("%e %b %Y %H:%M:%S"), strtotime($edata["lastModified"]))) . "</span><br>";
              echo sprintf(_("Count: %s"), $edata["count"]);
        ?>
      </div>
    </li>
    <div class="span3 pull-right">
        <button class="btn btn-success btn-block btn-large" type="button" id="checkin">
          Check in to<br>
          <?php 
            $sql = "SELECT name FROM services WHERE id = :id;";
            $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $sth->execute(array(":id" => $_GET["service"]));
            $result = $sth->fetch(PDO::FETCH_BOTH);
            echo $result[0];
          ?>
        </button>
        <button class="btn btn-block btn-large" type="button" id="checkin">
          Check in to<br>
          Multiple Services
        </button>
    </div>
    <ul class="nav nav-tabs span9" style="margin-top: -18px;">
      <li id="tabselect_main" class="<?php echo ($_POST["tab"] != "extended" ? "active" : ""); ?>">
        <a href="javascript:selecttab('main');"><?php echo _("Main"); ?></a>
      </li>
      <li id="tabselect_extended" class="<?php echo ($_POST["tab"] == "extended" ? "active" : ""); ?>">
        <a href="javascript:selecttab('extended');"><?php echo _("Extended"); ?></a>
      </li>
    </ul>
  </ul>   
    <form class="form-horizontal" action="" method="post">
      <fieldset id="tabpane_extended" style="display:<?php echo ($_POST["tab"] == "extended" ? "block" : "none"); ?>;">
        <div class="control-group">
          <label class="control-label" for="street"><?php echo _("Street"); ?></label>
          <div class="controls">
            <input type="text" class="input-xlarge" name="street" id="street" 
              placeholder="<?php echo _("Street Address"); ?>" value="">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="city"><?php echo _("City"); ?></label>
          <div class="controls">
            <input type="text" class="input" name="city" id="city"
              placeholder="<?php echo _("City"); ?>" value="">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="state"><?php echo _("State"); ?></label>
          <div class="controls">
            <input type="text" class="input-small" name="state" id="state"
              placeholder="<?php echo _("State"); ?>" value="">
          </div>
        </div> 
        <div class="control-group">
          <label class="control-label" for="zip"><?php echo _("ZIP"); ?></label>
          <div class="controls">
            <input type="text" class="input-small" name="zip" id="zip"
              placeholder="<?php echo _("ZIP"); ?>" value="">
          </div>
        </div> 
      </fieldset>
      <fieldset id="tabpane_main" style="display:<?php echo ($_POST["tab"] != "extended" ? "block" : "none"); ?>;">
        <div class="control-group">
          <label class="control-label" for="name"><?php echo _("Name"); ?></label>
          <div class="controls">
            <input type="text" class="input" name="name" id="name" 
              placeholder="<?php echo _("Name"); ?>" value="<?php echo $edata["name"]; ?>">
            <input type="text" class="input" name="lastname" id="lastname" 
              placeholder="<?php echo _("Lastname"); ?>" value="<?php echo $edata["lastname"]; ?>">
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="phone"><?php echo _("Phone"); ?></label>
          <div class="controls">
            <input type="tel" class="input-medium" name="phone" id="phone"
              placeholder="<?php echo _("Phone"); ?>" value="<?php echo $edata["phone"]; ?>">
            <label class="checkbox">
              <input type="checkbox" name="mobileCarrier" 
                <?php echo $edata["mobileCarrier"] ? "checked" : ""?>>
                <?php echo _("Mobile phone"); ?>
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
              <input type="text" class="input-small datepicker" name="dob" 
                id="dob" placeholder="<?php echo _("YYYY-MM-DD"); ?>"
                value="<?php echo $edata["dob"]; ?>"> <?php echo $agestr ?>
            </div>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="activity"><?php echo _("Activity"); ?></label>
          <div class="controls">
            <select name="activity" id="activity">
              <?php
                echo (is_null($edata["activity"]) ? "<option disabled selected>" .
                  _("Activity") . "</option>\n" : "");
                foreach ($dbh->query("SELECT id, name FROM activities;") as $row) {
                  echo "<option value=\"{$row["id"]}\"" . ($row["id"] == $edata["activity"] ? " selected" : "") . ">{$row["name"]}</option>\n";
                }
              ?>
            </select>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="room"><?php echo _("Room"); ?></label>
          <div class="controls">
            <select name="room" id="room">
              <?php
                echo (is_null($edata["room"]) ? "<option disabled selected>Room</option>\n" : "");
                foreach ($dbh->query("SELECT id, name FROM rooms;") as $row) {
                  echo "<option value=\"{$row["id"]}\"" . ($row["id"] == $edata["room"] ? " selected" : "") . ">{$row["name"]}</option>\n";
                }
              ?>
            </select>
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="p1_name"><?php echo _("Medical Info"); ?></label>
          <div class="controls">
            <input type="text" class="input" name="medical" id="medical"
              placeholder="<?php echo _("Medical"); ?>" value="<?php echo $edata["medical"]; ?>">
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="p1_name"><?php echo _("Parent 1"); ?></label>
          <div class="controls">
            <input type="text" class="input" name="parent1" id="parent1"
              placeholder="<?php echo _("Name"); ?>" value="<?php echo $edata["parent1"]; ?>">
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="p2_name"><?php echo _("Parent 2"); ?></label>
          <div class="controls">
            <input type="text" class="input" name="parent2" id="parent2"
              placeholder="<?php echo _("Name"); ?>" value="<?php echo $edata["parent2"]; ?>">
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="p1_name"><?php echo _("Parent's Email"); ?></label>
          <div class="controls">
            <input type="text" class="input" name="parent_email" id="parent_email"
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
            <textarea name="notes" id="notes" placeholder="<?php echo _("Notes"); ?>" 
            style="width: 434px;"><?php echo $edata["notes"]; ?></textarea>
          </div>
        </div>
      </fieldset>
      <div class="form-actions">
        <input id="tabinput" name="tab" type="hidden" value="main" />
        <input type="submit" class="btn btn-primary" name="action"
          value="<?php echo _("Save changes"); ?>" />
        <button id="cancelbutton" class="btn" type="button"><?php echo _("Cancel"); ?></button>
      </div>
    </form>
  </div>
</div>
</div>
<link href="resources/css/Jcrop.min.css" rel="stylesheet">
<script src="https://raw.github.com/tapmodo/Jcrop/master/js/jquery.Jcrop.min.js"> </script>
<script src="resources/js/canvas_toblob.js"> </script>
<script src="resources/js/jquery.getparams.js"> </script>
<?php
  require_once "template/footer.php";
?>
