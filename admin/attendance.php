
<?php
include "../dbConnect.php";
session_start();
if(!isset($_SESSION['username'])){
	header('location: ../login.php');
}
include("includes/header.php"); ?>



</section>

<section class="content">
	<div class="container-fluid">

		<div class="block-header">
			<h2><strong>Attendance</strong></h2>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 contentl-xs-12">


			<ol class="breadcrumb">
				<li>
					<a href="index.php">
						<i class="material-icons">dashboard</i> Dashboard
					</a>
				</li>
				<li class="active">
					<i class="material-icons">queue</i> Attendance
				</li>
			</ol>
		</div>



	</div>
			<div class="card">
				<div class="header">
					<h2>
						Current Gym Users
					</h2>
					<h2 class="pull-right" style="margin-top:-18px;">
						Available Towels:
						<?php 
							$pdo = new dbConnect();
							echo $towels = $pdo->previousAvailable();
						?>
					</h2>
				</div>
				<div class="body">
					<form method="POST" action="actions/attendanceAction.php">
						<div class="row clearfix">
							<div class="col-md-3">
								<div class="form-group">
									<div class="form-line">
										<h5 class="pull-left">Client Name</h5>

										<select class="form-control show-tick" data-live-search="true" id="clientName" name="clientName" >
											<?php 
											$pdo = new dbConnect();

											 $clients = $pdo->getRows('client',array('order_by'=>'CLIENT_ID ASC'));
                                                    if(!empty($clients)){ 
                                                        $count = 0; foreach($clients as $client){ 
                                                            $count++;
                                                            $a = $client['CLIENT_ID'];
                                                            ?>



								
											<option id = "myUL" value="<?php echo $client['CLIENT_ID']; ?>">
												<?php $firstname = $client['CLIENT_FirstName']; $midname = $client['CLIENT_MiddleName']; $lastname = $client['CLIENT_LastName']; $fullname=$firstname." ".$midname." ".$lastname; echo $fullname ; ?>
											</option>
											<?php }}?>
										</select>

									</div>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
								  <div class="form-line">
									<h5 class="pull-left">Locker Number </h5>
									<select class="form-control" name="Locker" style="height:50%">
										<?php echo get_locker() ?>
									</select>
								<?php
								function get_locker(){

								//if clientName.Gender = Male $M range is 1-150, if Female 151-300
									$var="";
									for ($m=1; $m<=150; $m++){
										$var .= '<option value="'.$m.'">'.$m.' </option>';
									}
									return $var;
									//if number is selected to time-in. Remove from choices
								}
								?>
									</div>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
							    	<div class="form-line">
										<h5 class="pull-left">Towels</h5>
										<input type="number" class="form-control"  name="towel" value="" required />
										<input type="hidden"   name="sample" value="1"/>

								 	</div>
								 </div>
							</div>

							<div class="col-md-3" style="margin-top: 20px;">
								<input type="hidden" name="action_type" value="add"/>
								<button name="add" type="submit" class="btn bg-green btn-block"><i class="material-icons">access_alarm</i>TIME-IN</button>
							</div>
							
						</div>
						











					   <div class="body table-responsive">
                    	<table class="table table-bordered table-striped table-hover dataTable ">
                        	<thead>
                            <tr>
                                <th class="center" aria-label="Name: activate to sort column descending" >Client Name</th>
                                <th class="center">Time-in</th>
                                <th class="center">Time-out</th>
                                <th class="center">Towels</th>
                                <th class="center">Locker No.</th>
                                <th class="center">Actions</th>
                            </tr>
                        	</thead>
                        	<tbody>
                            	<?php
                            		$pdo = new dbConnect();
                            		$attendances = $pdo->attendance(date("Y-m-d"),array("order_by" => "A_Code ASC"));
                            			if(!empty($attendances)){
                            				$count = 0;
                            				foreach($attendances as $attend){
                            	?>
            
                                 
                                    <tr>
                                        <td> 
                                        	<?php 
                                               	$firstname = $attend['CLIENT_FirstName']; 
                                                $midname = $attend['CLIENT_MiddleName']; 
                                                $lastname = $attend['CLIENT_LastName']; 
                                                $fullname = $firstname ." ". $midname." ". $lastname; 
                                                    echo $fullname ; ?></td>
                                        <td><?php echo date("g:i A", strtotime($attend['A_TimeIn'])); ?></td>
                                        <td><?php echo $attend['A_TimeOut'] ?></td>
                                        <td><?php echo $attend['A_TowelQty']; ?></td>
                                        <td><?php echo $attend['A_LockerKey']; ?></td>
                                        <td class="center"><button type="button" class="btn bg-green" style="height: 30px;" data-toggle="modal"  data-target="#smallModal-<?php echo $attend['A_Code']; ?>">Modify</button>
                                        </td>
                                        <td class="center"><button type="button" data-toggle="modal"  data-target="#timeout-<?php echo $attend['A_Code']; ?>" class="btn bg-red" >Time-Out</button>
									
                                        </td>

                                    </tr>
				  </form>

	                                   	<!-- TIMEOUT MODAL -->
	<div class="modal fade" id="timeout-<?php echo $attend['A_Code']; ?>" tabindex="-1" role="dialog">
				<div class="modal-dialog modal-sm">
					<div class="modal-content">
					<center>
						<div class="modal-header">
							<h3 class="modal-title" id="smallModalLabel"><strong>Client Time-out</strong></h3>
						</div>

						<div class="container-fluid">
							
								<div class="form-group">
									<div class="form-line">
										<h5 class="pull-left">Client Name</h5>
										<input type="text" class="form-control" placeholder="Client Name" value="<?php 
                                               	$firstname = $attend['CLIENT_FirstName']; 
                                                $midname = $attend['CLIENT_MiddleName']; 
                                                $lastname = $attend['CLIENT_LastName']; 
                                                $fullname = $firstname ." ". $midname." ". $lastname; 
                                                    echo $fullname ; ?>" disabled/>
									</div>
								</div>

							

							<form method="POST" action="actions/attendanceAction.php">
								<div class="form-group">
									<div class="form-line">
										<h5 class="pull-left">Returned Towel Quantity</h5>
										<input name="returnedTowel" type="number" class="form-control" value="<?php echo $attend['A_TowelQty']; ?>"/>
									</div>
								</div>

								<div class="form-group">
									<div class="form-line">
										<h5 class="pull-left">Returned Locker Number</h5>
										
										<input type="number" class="form-control" value="<?php echo $attend['A_LockerKey']; ?>"/>
									</div>
								</div>
							
						</div>

					</center>
					<div class="modal-footer">
						<input type="hidden" name="A_Code" value="<?php echo $attend['A_Code'];?>"/>
						<input type="hidden" name="action_type" value="out"/>
						<button type="submit" name="submit" class="btn  bg-green">TIME-OUT</button>
						<button type="button" class="btn  bg-red" data-dismiss="modal">CLOSE</button>
						</form>
					</div>
				</div>
			</div>
		</div>
                                   	<!-- END OF TIMEOUT MODAL -->

                                    <!-- MODIFY MODAL -->

          <div class="modal fade" id="smallModal-<?php echo $attend['A_Code']; ?>" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-sm" role="document">
				<div class="modal-content">
					<center>
						<div class="modal-header">
							<h3 class="modal-title" id="smallModalLabel">Modify Client Amenities</h3>
						</div>
						<form method="post" action="actions/attendanceAction.php">
						<div class="row container-fluid">
							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-line">
										<h5 class="pull-left">Towel Count</h5>
										<input type="number" name="modifiedTowel" class="form-control" placeholder="Towel Count" value="<?php echo $attend['A_TowelQty']; ?>"/>
									</div>
								</div>
								<div class="form-group">
									<div class="form-line">
										<h5 class="pull-left">Locker Number</h5>
										<input type="number" name="modifiedLocker" class="form-control" placeholder="Locker Number" value="<?php echo $attend['A_LockerKey']; ?>"/>
									</div>
								</div>
							</div>
						</div>
					</center>
					<div class="modal-footer">
						<form method="post" action="actions/attendanceAction.php">
						<input type="hidden" name="A_Code" value="<?php echo $attend['A_Code'] ?>"/>
						
						<input type="hidden" name="action_type" value="edit"/>
						<button name="submit" type="submit" class="btn bg-green">SAVE CHANGES</button>
						<button type="button" class="btn  bg-red" data-dismiss="modal">CLOSE</button>
					</form>
					</div>
				</div>
			</div>
		</div>




                                                  

                                                 

			<!-- END OF MODIFY MODAL -->
		
		
							
							<?php } }else{ ?>
									<tr><td colspan="6">No Current Gym User(s) found......</td></tr>
							<?php } ?>
									
                        	</tbody>
                     	</table>
                    </div>						
				</div>
			  </div>	
	<!-- #END# Hover Rows -->





		<!-- Small Size -->

		
	</div>
</section>

			
				
				</form>
		</div>
	</div>




	<?php include("includes/footer.php"); ?>
	<!-- Custom Js -->
	<script src="../assets/plugins/jquery-datatable/jquery.dataTables.js"></script>
	<script src="../assets/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js"></script>
	<script src="../assets/plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js"></script>
	<script src="../assets/plugins/jquery-datatable/extensions/export/buttons.flash.min.js"></script>
	<script src="../assets/plugins/jquery-datatable/extensions/export/jszip.min.js"></script>
	<script src="../assets/plugins/jquery-datatable/extensions/export/pdfmake.min.js"></script>
	<script src="../assets/plugins/jquery-datatable/extensions/export/vfs_fonts.js"></script>
	<script src="../assets/plugins/jquery-datatable/extensions/export/buttons.html5.min.js"></script>
	<script src="../assets/plugins/jquery-datatable/extensions/export/buttons.print.min.js"></script>

	<!-- Bootstrap Material Datetime Picker Plugin Js -->
	<script src="../assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>

	<!-- Custom Js -->
	<script src="../assets/js/admin.js"></script>
	<script src="../assets/js/pages/tables/jquery-datatable.js"></script>
	<script src="../assets/js/pages/forms/basic-form-elements.js"></script>

	<!-- Demo Js -->
	<script src="../assets/js/demo.js"></script> 
<script >
	
</script>






	</body>

</html>