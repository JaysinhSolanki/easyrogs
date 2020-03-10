<?php

include_once("common.php");



$dbtables	=	$_POST['dbtables'];

?>

<form name="form_field" id="form_field" method="post" >



        <div class="row-fluid">



            <div class="span12">      

                <table class="table table-striped table-bordered table-hover">

                    <thead>

                        <tr>

                            <th class="center">

                                SNO#

                            </th>

                            <th>

                            	Table Name

                            </th>

                            <th>

                            	Fields Name

                            </th>

                            <th>

                            	Fields Label

                            </th>

                            <th>

                            	Fields Type

                            </th>

                            <th>

                            	Input Type

                            </th>

                            <th>

                            	Show

                            </th>

                             <th>

                            	Auto

                            </th>

                             <th>

                            	Source

                            </th>

                             <th>

                             	Query

                             </th>

                             <th>

                             	Json

                             </th>

                            <th>

                               Field Key

                            </th>

                            <th>

                            	Extra

                            </th>

                            <th colspan="2">

                            	Target

                            </th>

                        </tr>

                    </thead>



                    <tbody>

<?php

	foreach($dbtables as $dbtable)

	{

		$query			=	"SHOW columns FROM $dbtable";	

		$tablefields	=	$AdminDAO->queryresult($query);



 

                 foreach($tablefields as $counter=>$tablefield)

                 {  

                    $counter++;	 	

        ?>

                              <tr>     

                                    <td class="center">

										<?php echo $counter;?>

                                    </td>



                                    <td>

                                        <?php echo $dbtable;?>

                                    </td>

                                    <td>

                                    	<?php echo $tablefield['Field'];?>

                                    </td>

                                    <td>

                                    	<input type="text" style="width:67px;"  name="<?php echo $tablefield['Field'];?>" value="<?php echo $tablefield['Field'];?>" />

                                    </td>

                                    <td>

                                    	<?php echo $tablefield['Type'];?>

                                    </td>

                                    <td>

<?php 

									$formfieldtypes	=	$AdminDAO->getrows("system_formfieldtype","*");										

?>

									<select name="fkfieldtypeid" style="width:67px;" >

<?php 

									foreach($formfieldtypes as $formfieldtype)

									{

?>

										<option value="<?php echo $formfieldtype['pkfieldtypeid'];?>"><?php echo $formfieldtype['fieldtypename'];?></option>

<?php																			

									}

?>                                   

                                    </select>

                                    </td>

                                    <td>

                                    	<input type="checkbox"   name="show" value="1"  />

                                    </td>

                                    <td>

                                    	<input type="checkbox"   name="auto" value="1"  />

                                    </td>

                                    <td>

                                    	<select name="source" style="width:67px;" >

                                        	<option value="0">None</option>

                                            <option value="1">Query</option>

                                            <option value="2">Json</option>

                                            <option value="3">Options</option>

                                        </select>

                                    </td>

                                    <td>

                                    	<textarea name="query" style="width:70px; height:18px;"></textarea>

                                    </td>

                                    <td>

                                    	<textarea name="Json" style="width:70px; height:18px;"></textarea>

                                    </td>

                                    <td>

                                    	<?php echo $tablefield['Key'];?>

                                    </td>



                                    <td>

                                      	 <?php echo $tablefield['Extra'];?>

                                    </td>

                                    <td>									                                        

								<select name="selectedtablename" class="selectedtablename" onblur="changetable('<?php echo $counter.$dbtable; ?>');" id="selectedtablename<?php echo $counter.$dbtable; ?>"  style="width:80px;">

<?php 

								foreach($dbtables as $systemtable)

								{		

?>                                

                                	<option value="<?php echo $systemtable;?>"><?php echo $systemtable;?></option>

<?php 

								}

?>                                

                                </select>

                            

									</td>

                                    <td>

                              			 <span id="fieldsdiv<?php echo $counter.$dbtable; ?>">

											<?php											

                                           		 require_once("selecttablefields.php");

                                            ?>

                						 </span>          

            		                </td>

                        </tr>

<?php 

		 }

	}

?>   

                    </tbody>

                </table> 

                    <div style="text-align:left;" class="form-actions">                 

                              <button class="btn btn-success" type="submit" name="submit" value="Submit"> <i class="icon-ok bigger-110"></i>Save</button>

                                  <a class="btn btn-danger" onclick="hidediv('formfields');" href="javascript:void(0);"> <i class="icon-undo bigger-110"></i> Cancel </a> </div>



      </div><!--/span-->

    </div><!--/row-->

</form>              

<script type="text/javascript">



function changetable(id)

{

	selectedtablename	=	$("#selectedtablename"+id).val();

	$("#fieldsdiv"+id).load("selecttablefields.php?selectedtablename="+selectedtablename);

}

</script>

<script type="text/javascript">

        $(document).ready(function(){

            $("#form_field").submit( function () {    

              $.post(

               'formfieldsaction.php',

                $(this).serialize(),

                function(data){

                  $("#results").html(data)

                }

              );

              return false;   

            });   

        });

</script>

