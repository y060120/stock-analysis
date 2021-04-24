
<?php 
    require_once 'core/init.php';    
    include_once('layouts/header.php'); 
    $errorList = '';
?>
<div class="container-fluid">
    <div class="row">
        <div class="col" style="background-color: aliceblue;border-radius: 10px;">
            <div class="mb-3">
                <label for="formFile" class="form-label"></label>       
                    <form action="" method="post" enctype="multipart/form-data">  
<?php 
                        $function = new Functions();
                            try{                                
                                    if(isset($_FILES['formFile']['tmp_name']) && is_uploaded_file($_FILES['formFile']['tmp_name'])){
                                        $data = input::get('formFile');                        
                                        $result = $function->csv_extraction($data['tmp_name']);  // csv extraction and form array
                                        $errorList = $function->create($result);                //check existing datas in database and insert into the db   
                                    }
                                }
                            catch (Exception $error){
                                    die($error->getMessage());
                                }
?>       
                        <input class="form-control" type="file" name="formFile" id="formFile">
                        <br />
                        <input type="submit" value="Upload CSV" name="submit" class="btn btn-success">
                    </form>
            </div>
        </div>
        <div class="col" style="background-color: antiquewhite;border-radius: 10px;">
            <div class="mb-3" style="padding: 50px;">
                <div class="container">
                    <div class="row">
                        <div class="col">  
                            <form>
<?php 
                            try{
                                $function = new Functions();
                                $count = $function->checkEnableClearData();
                                if(isset($count['total']) && $count['total'] > 0){
                                    echo "<input type='button' onclick='clearAllDataform()' value='Clear Data' name='submit' class='btn btn-danger'>";                                   

                                }else{
                                    echo "<input type='button' value='Clear Data' name='submit' class='btn btn-danger' disabled>";
                                }  
                            }catch(Exception $error){
                                die($error->getMessage());
                            }   
                              
?>                                
                            </form>
                        </div>      
                        <div class="col">  
                            
<?php
                            if(isset($errorList['error']) && $errorList['error'] != ''){    // error messages and success messages

                                echo "<div class='alert alert-danger'>". $errorList['error']."</div>" ;

                            }else if(isset($errorList['success']) && $errorList['success'] != ''){

                                echo "<div class='alert alert-success'>". $errorList['success']."</div>" ;

                            }
?>
                            </div>
                        </div>       
                    </div>
                </div>    
            </div>    
        </div>
    </div>
</div>
<br>
<div class="container">
<?php 

 if(isset($count['total']) && $count['total'] > 0){
    $distinctValues = $function->distinctValues();   
?>
    <form method="post" action="">
        <div class="jumbotron" style="background-color: #eee; border-radius:10px; height:550px;">
            <h1>&nbsp;Select Stock Range</h1>   
            <div class="row" style="padding: 30px;">
                <div class="col">      
                <label>Select</label>               
                    <select name="selectStock" class="form-select" aria-label="Default select example">
                        <option selected>Select Stock</option>
<?php 
                        while($row = $distinctValues->fetch_assoc()) {                        
                            $stockName = $row['stock_name'];
                            echo "<option value=$stockName>$stockName</option>";
                        }
?>                   
                    </select>
                </div> 
<?php
                $stockName      = input::get('selectStock');
                $fromRange      = input::get('datepickerFrom');
                $toRange        = input::get('datepickerTo'); 

                if($stockName && $fromRange && $toRange){
                    try{                        
                        $result = $function->calculateRange($stockName,$fromRange,$toRange);
                        echo "<pre>";
                        print_r($result);
                        die();
                    }catch(Exception $error){
                        die($error->getMessage());
                    }                    
                }
?>                
                <div class="col">
                    <label>From</label>
                    <input id="datepickerFrom" name="datepickerFrom" type="text" class="form-control datepicker" data-zdp_readonly_element="false">
                </div>   
                <div class="col"> 
                    <label>To</label>
                    <input id="datepickerTo" name="datepickerTo" type="text" class="form-control datepicker" data-zdp_readonly_element="false">              
                </div>  
                <div class="col">
                    <br>
                    <button type="submit" class="btn btn-primary">Calculate</button>
                </div>
            </div>  
            <br>
            <div class="row" style="padding: 30px;">
                <div class="col">
                    <label>Stock Should Have Purchased On(Purchase Date): </label>
                </div>
                <div class="col">
                    <span class="badge bg-primary">Primary Date</span>
                </div>
                <div class="col">   
                    <label> Stock Price on Purchased Date  </label>                 
                </div>
                <div class="col">         
                    <span class="badge bg-primary">Min Price</span>           
                </div>
            </div> 
            <div class="row" style="padding: 30px;">
                <div class="col">
                    <label>Stock Should Have Sold On(Sold Date): </label>
                </div>
                <div class="col">
                    <span class="badge bg-primary">Primary Date</span>
                </div>
                <div class="col">   
                    <label> Stock Price on Sold Date  </label>                 
                </div>
                <div class="col">         
                    <span class="badge bg-primary">Max Price</span>           
                </div>
            </div> 
        </div>
    </form>  
<?php }
else{
    echo "<div class='alert alert-danger'>No Stock List Found To Calculate Range</div>";
}
?>    
</div>

<script>
$(document).ready(function() {
    $('#datepickerFrom').datepick();
    $('#datepickerTo').datepick();
});
    // function for clearing data ajax call
    function clearAllDataform(){
        var host        = "<?php echo Config::get('mysql/host'); ?>";
        var username    = "<?php echo Config::get('mysql/username'); ?>";
        var password    = "<?php echo Config::get('mysql/password'); ?>";
        var db          = "<?php echo Config::get('mysql/db'); ?>";      

                $.ajax({
                        url: 'classes/Functions.php',
                        type: 'POST',
                        data: {class: 'clearDataWhole',hostName:host,uname:username,pwd:password, database:db},
                        success:function(res){
                            var result = JSON.parse(res);
                            if(result.success){
                                alert(result.success);                                
                            }                           
                        }
                    });
    }
</script>




  