<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?> · Taxídí</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Web interface for Taxidi check-in system">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="bootstrap/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="bootstrap/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="bootstrap/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="bootstrap/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="bootstrap/ico/apple-touch-icon-57-precomposed.png">
  </head>

  <body>

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">Taxídí</a>
          <div class="nav-collapse">
            <ul class="nav">
              <li><a href="#">Home</a></li>
              <li><a href="search.php">Search</a></li>
              <li><a href="#register">Register</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

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
                } ?>" class="btn btn-danger">Delete</a>
      </div>
    </div>


    <!-- sidebar -->
    <div class="container">
      <div class="container-fluid">
        <div class="row-fluid">
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
            </div><!--/.well -->
          </div><!--/span-->
         <!-- /sidebar -->

