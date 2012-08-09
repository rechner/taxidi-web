<!DOCTYPE html>
<!-- vim: tabstop=2:softtabstop=2 -->
<?php
/* vim: tabstop=2:expandtab:softtabstop=2 */
  /* TODO list
<<<<<<< HEAD
    * Phone mask
    * Proper error message for bad id
    *
    * Changes: Added a few info displays, removed explicit script reference
=======
    * Phone mask
    * Proper error message for bad id
>>>>>>> 504372f9a1f08ca08795f106eaec9ed0e497da6e
  */
  
  //get input:
  if (is_numeric($id = $_GET["id"])) { 
    require_once 'config.php';

    $connection = pg_connect ("host=$dbhost dbname=$dbname 
                              user=$dbuser password=$dbpass");

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
      
      $query = "UPDATE data SET " .
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
                  "' WHERE id = $id;";
      
      $result = pg_query($connection, $query) or 
        die("Error in query: $query." . pg_last_error($connection));
      pg_free_result($result);
			$modifysuccess = true;
    }
                              
    ///*
    $query = "SELECT name, lastname, dob, activity, room, grade, phone, 
                     \"mobileCarrier\", paging, parent1, parent2,
                     \"parentEmail\", medical, \"joinDate\", \"lastSeen\",
                     \"lastModified\", count, visitor, expiry, \"noParentTag\",
                     barcode, picture, notes
                FROM data WHERE id = $id;";
    $result = pg_query($connection, $query) or 
      die("Error in query: $query." . pg_last_error($connection));
    $edata = pg_fetch_assoc($result);
    
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
      $agestr = $age->format("Age: %y");
    } else {
      $agestr = $age->format("Age: %m months");
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

			return round($bytes / pow(1024, floor($e)), 2) .$s[$e];
		}
	}

  require_once "template/header.php";
?> 
<!-- sidebar -->
<div class="span3">
	<div class="well sidebar-nav">
	  <ul class="nav nav-list">
	    <li class="nav-header">Search</li>
	    <li><a href="search.php"><i class="icon-search"></i>Search</a></li>
	    <li><a href="#"><i class="icon-filter"></i>Advanced</a></li>
	    <li><a href="#"><i class="icon-bookmark"></i>Saved searches</a></li>
	    <li class="nav-header">Actions</li>
	    <li><a href="print.php?id=<?php echo $_GET["id"]; ?>" target="_blank"><i class="icon-print"></i>Print details</a></li>
	    <li><a data-toggle="modal" href="#downloadModal"><i class="icon-download"></i>Download details</a></li>
	    <li><a href="#"><i class="icon-bullhorn"></i>Create incident report</a></li>
	    <li><a data-toggle="modal" href="#deleteModal"><i class="icon-trash"></i>Delete record</a></li>
	  </ul>
	</div>
</div>
<!-- /sidebar -->

<div id="downloadModal" class="modal hide fade">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h3>Download Record Data</h3>
	</div>
	<div class="modal-body">
		<h4>Download format</h4>
		<form class="form-horizontal">
		  <fieldset>	
		    <div class="control-group">
		      <label class="control-label" for="inlineCheckboxes">Select Format:</label>
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
		<a href="#" class="btn" data-dismiss="modal" >Close</a>
		<a href="#" class="btn btn-primary">Download</a>
	</div>
</div>

<div id="deleteModal" class="modal hide fade">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h3>Delete Record</h3>
	</div>
	<div class="modal-body">
		<h4>Are you sure you want to delete this record?</h4>
		<p>This action cannot be undone.</p>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal" >Close</a>
		<a href="delete.php?id=<?php 
					  if ($query != '') {
					    echo $id . "&query=" . $search;
					  } else {
					    echo $id;
					  } ?>" class="btn btn-danger">Upload</a>
	</div>
</div>

<div id="photouploadModal" class="modal hide fade">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h3>Upload New Photo</h3>
	</div>
	<div class="modal-body">
		<p>Uploads are limited to <?php echo formatByteSize($photo_maxsize); ?> and must be in jpeg or png format</p>
		<div>
			<div class="thumbnail" style="width: 175px; margin: 0 auto;">
				<img id="photopreview" style="width: 175px;" src="photo.php<?php echo "?id=" . $edata["picture"] ?>">
			</div>
		</div>
		<br>
		<div id="fileselecterror" class="alert alert-error" style="display: none;">
			Error: File type not supported.
		</div>
		<div class="well" id="photodndbox" style="height: 33px">
			<input id="realfileinput" type="file" style="display: none;">
			<div class="progress progress-striped active" style="display: none; margin: 6px 0 9px;">
      	<div id="photoupload_progressbar" style="width: 0%" class="bar"></div>
    	</div>
			<div id="drophere" style="width: 100%; height: 100%; display: none;">
				<div style="width: 100%; height: 100%; display: table;">
		    	<div style="display: table-cell; vertical-align: middle; text-align: center;">
						Drop file here.
					</div>
				</div>
    	</div>
			<div class="input-append" style="display: table; width: 100%; height: 100%;">
				<input style="cursor: pointer; cursor: hand;" id="fakefileinput" class="input-xlarge" type="text" onclick="document.getElementById('realfileinput').click();" readonly>
				<a class="btn" style="margin-left: -4px; border-radius: 0 3px 3px 0; cursor: pointer; cursor: hand;" onclick="document.getElementById('realfileinput').click();">Browse</a>
				<div style="display: table-cell; vertical-align: middle; text-align: right;">
					Or drop file here.
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<a class="btn" data-dismiss="modal" >Close</a>
		<a id="uploadphoto" class="btn btn-primary">Upload Photo</a>
	</div>
</div>

<script type="text/javascript">
$(function(){
  new JsDatePick({
    useMode:2,
    target:"dob",
    dateFormat:"%Y-%m-%d",
    imgPath:"resources/img/datepicker"
    /* weekStartDay:1*/
  });
	
	var tempphoto = null;		// photo user has selected
	var photoupload = null; // photo upload xhr request
	var uploadphoto = {
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
		"setstate" : function (state) {
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
		if (file.type == "image/png" || file.type == "image/jpeg") { //TODO loop through accepted mime types
			if (file.size < <?php echo $photo_maxsize; ?>) {
				tempphoto = file;
				uploadphoto.hideerror();
				document.getElementById("fakefileinput").value = file.name;
				var fileurl = window.URL.createObjectURL(file);
				document.getElementById("photopreview").src = fileurl;
			} else {
				uploadphoto.showerror("Error: file too large.");
			}
		} else {
			uploadphoto.showerror("Error: file type not supported.");
		}
	}
	
	document.getElementById("uploadphoto").onclick = function() {
		var file = tempphoto;
		if (file.type == "image/png" || file.type == "image/jpeg") {
			if (file.size < <?php echo $photo_maxsize; ?>) {
				uploadphoto.setstate(2);
				
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
						$("#lastmodified").text("Modified: " + date2.getUTCDate() + " " + (new Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec")[date2.getUTCMonth()]) + " " + date2.getUTCFullYear() + " " + date1[1]);
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
			}
		} else {
			uploadphoto.showerror("Error: file type not supported."); //TODO switch to language array
		}
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
			$("#photopreview").attr("src", $("#photomain").attr("src"));
			uploadphoto.setstate(0);
			tempphoto = null;
  	},
		"show" : function() {
			
		}
	});
});
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
		  <a href="#" class="thumbnail">
		  	<img id="photomain" src="photo.php<?php echo "?id=" . $edata["picture"] ?>"/>
		  </a>
			<a href="#photouploadModal" data-toggle="modal">Upload new photo</a>
		</li>
		<li class="span6">
		  <div class="page-header">
		    <h1><?php echo "{$edata["name"]}</h1> <h2>{$edata["lastname"]}"; ?></h2>
		    <?php echo ($edata["visitor"] == "f" ? "Member" : "Visitor") . "<br>"; 
		          echo ($edata["visitor"] == "f" ? "" : "Expiry: " . $edata["expiry"]) . "<br>";
		          echo "Created: " . date("j M Y", strtotime($edata["joinDate"])) . "<br>";
		          echo "Last Seen: " . date("j M Y", strtotime($edata["lastSeen"])) . "<br>";
		          echo "<span id=\"lastmodified\">Modified: " . date("j M Y H:i:s", strtotime($edata["lastModified"])) . "</span><br>";
		          echo "Count: " . $edata["count"];
		    ?>
		  </div>
		</li>
		<ul class="nav nav-tabs span9" style="margin-top: -18px;">
		  <li id="tabselect_main" class="<?php echo ($_POST["tab"] != "extended" ? "active" : ""); ?>">
		  	<a href="javascript:selecttab('main');">Main</a>
		  </li>
		  <li id="tabselect_extended" class="<?php echo ($_POST["tab"] == "extended" ? "active" : ""); ?>">
				<a href="javascript:selecttab('extended');">Extended</a>
			</li>
    </ul>
	</ul>   
    <form class="form-horizontal" action="" method="post">
			<fieldset id="tabpane_extended" style="display:<?php echo ($_POST["tab"] == "extended" ? "block" : "none"); ?>;">
				<div class="control-group">
          <label class="control-label" for="street">Street</label>
          <div class="controls">
            <input type="text" class="input-xlarge" name="street" id="street" placeholder="Street Address" value="">
          </div>
        </div>
				<div class="control-group">
          <label class="control-label" for="city">City</label>
          <div class="controls">
            <input type="text" class="input" name="city" id="city" placeholder="City" value="">
          </div>
        </div>
				<div class="control-group">
          <label class="control-label" for="state">State</label>
          <div class="controls">
            <input type="text" class="input-small" name="state" id="state" placeholder="State" value="">
          </div>
        </div> 
				<div class="control-group">
          <label class="control-label" for="zip">ZIP</label>
          <div class="controls">
            <input type="text" class="input-small" name="zip" id="zip" placeholder="ZIP" value="">
          </div>
        </div> 
			</fieldset>
      <fieldset id="tabpane_main" style="display:<?php echo ($_POST["tab"] != "extended" ? "block" : "none"); ?>;">
        <div class="control-group">
          <label class="control-label" for="name">Name</label>
          <div class="controls">
            <input type="text" class="input" name="name" id="name" placeholder="Name" value="<?php echo $edata["name"]; ?>">
            <input type="text" class="input" name="lastname" id="lastname" placeholder="Lastname" value="<?php echo $edata["lastname"]; ?>">
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="phone">Phone</label>
          <div class="controls">
            <input type="tel" class="input-medium" name="phone" id="phone" placeholder="Phone" value="<?php echo $edata["phone"]; ?>">
            <label class="checkbox">
              <input type="checkbox" name="mobileCarrier" <?php echo $edata["mobileCarrier"] ? "checked" : ""?>> Mobile phone
            </label>
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="grade">Grade</label>
          <div class="controls">
            <div style="width: 220px; float: left; margin-right: 4px;">
              <input type="text" class="input-small" name="grade" id="grade" placeholder="Grade" value="<?php echo $edata["grade"]; ?>">
              <label for="dob" style="float: right; padding-top: 5px; margin-right: 16px;">Birthdate</label>
            </div>
            <div>
              <input type="text" class="input-small" name="dob" id="dob" placeholder="YYYY-MM-DD" value="<?php echo $edata["dob"]; ?>"> <?php echo $agestr ?>
            </div>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="activity">Activity</label>
          <div class="controls">
            <select name="activity" id="activity">
              <?php
                echo (is_null($edata["activity"]) ? "<option disabled selected>Activity</option>\n" : "");
                $query = "SELECT id, name FROM activities;";
                $result = pg_query($connection, $query) or
                    die("Error in query: $query." . pg_last_error($connection));
                while ($data = pg_fetch_assoc($result)) {
                  echo "<option value=\"{$data["id"]}\"" . ($data["id"] == $edata["activity"] ? " selected" : "") . ">{$data["name"]}</option>\n";
                }
                pg_free_result($result);
              ?>
            </select>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="room">Room</label>
          <div class="controls">
            <select name="room" id="room">
              <?php
                echo (is_null($edata["room"]) ? "<option disabled selected>Room</option>\n" : "");
                $query = "SELECT id, name FROM rooms;";
                $result = pg_query($connection, $query) or
                  die("Error in query: $query." . pg_last_error($connection));
                while ($data = pg_fetch_assoc($result)) {
                  echo "<option value=\"{$data["id"]}\"" . ($data["id"] == $edata["room"] ? " selected" : "") . ">{$data["name"]}</option>\n";
                }
                pg_free_result($result);
              ?>
            </select>
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="p1_name">Medical Info</label>
          <div class="controls">
            <input type="text" class="input" name="medical" id="medical" placeholder="Medical" value="<?php echo $edata["medical"]; ?>">
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="p1_name">Parent 1</label>
          <div class="controls">
            <input type="text" class="input" name="parent1" id="parent1" placeholder="Name" value="<?php echo $edata["parent1"]; ?>">
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="p2_name">Parent 2</label>
          <div class="controls">
            <input type="text" class="input" name="parent2" id="parent2" placeholder="Name" value="<?php echo $edata["parent2"]; ?>">
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="p1_name">Parent's Email</label>
          <div class="controls">
            <input type="text" class="input" name="parent_email" id="parent_email" placeholder="Email" 
              value="<?php echo $edata["parentEmail"]; ?>">
              <button class="btn" type="button" onClick="parent.location='mailto:<?php echo $edata["parentEmail"]; ?>'">
              <i class="icon-envelope"></i></button>
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="notes">Notes</label>
          <div class="controls">
            <textarea name="notes" id="notes" placeholder="Notes" style="width: 434px;"><?php echo $edata["notes"]; ?></textarea>
          </div>
        </div>
      </fieldset>
      <div class="form-actions">
        <input id="tabinput" name="tab" type="hidden" value="main" />
        <input type="submit" class="btn btn-primary" value="Save changes" />
        <button class="btn">Cancel</button>
      </div>
    </form>
  </div>
</div>
</div>
<script>
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
