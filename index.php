
<?php 
    require_once 'core/init.php';    
    include_once('layouts/header.php'); 
?>
<link href="public/css/bootstrap.css" rel="stylesheet" media="screen">
<link href="public/js/bootstrap.js" rel="stylesheet" media="screen">
<div class="container-fluid">
    <div class="mb-3">
        <label for="formFile" class="form-label">Default file input example</label>
        <input class="form-control" type="file" id="formFile">
    </div>
</div>
<?php 

$file = fopen('Sample Stock Price List.csv', 'r');
while (($line = fgetcsv($file)) !== FALSE) {
  //$line is an array of the csv elements
  echo "<pre>";
  print_r($line);
}
fclose($file);
?>