<?php
 include "../dbConnect.php";
 session_start();
 if (!isset($_SESSION['loggedIn'])) {
        $_SESSION['redirectURL'] = $_SERVER['REQUEST_URI'];
        echo "<script>alert('Unauthorized access!Please login! ');window.location.href='../login.php';</script>";
    }
 include("includes/header.php"); ?>
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>Studio Class Registrations</h2>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    
                           
                            <ol class="breadcrumb">
                                <li>
                                    <a href="index.php">
                                        <i class="material-icons">dashboard</i> Dashboard
                                    </a>
                                </li>
                                <li class="active">
                                    Studio Class - Reports - Class Registrations
                                </li>
                            </ol>
            </div>
        </div>
    <?php include("StudioClass-Report-List.php"); ?>
    <div class="card">
        <div class="header">
            <h2>Class Registrations</h2>
        </div>
                        <div class="body">

                         <form method="POST">
                            <div class="row clearfix">
                                <div class="col-md-5">
                                    <div class="form-group">
                                       <div class="form-line">
                                        <div class="col-md-6">
                                         <input type="date" class="form-control"  id="filterstart" name="filter_start"/>
                                        </div>
                                        <div class="col-md-6">
                                         <input type="date" class="form-control" name="filter_end"/>
                                       </div>
                                     </div>
                                    </div>  
                                </div>
                                <div class="col-md-4">
                            <select class="form-control show-tick" data-live-search="true" id="className" name="className">
                                        <option value="null">Choose Class</option>
                                            <?php 
                                            $conn = new mysqli("localhost", "root", "", "eclipse_db") or die(mysqli_error());

                                            $class = $conn->query("SELECT * FROM studioclass") or die(mysql_error());

                                            while($scn = $class->fetch_array()) {
                                                ?>

                                        <option id = "<?php echo $scn['SC_Code']; ?>" value="<?php echo $scn['SC_Code']; ?>">
                                                <?php echo $scn['SC_Name']; ?>
                                        </option>
                                            <?php 
                                                }
                                            ?>
                                </select>

                                </div>

                                <div class="col-md-3">
                                    <input type="hidden" name="action_type" value="filter"/>
                                    <button type="submit" name= "filter" class="btn bg-teal btn-block btn-lg waves-effect">Filter</button>
                                </div>
                            </div>
                        </form>

                        <div id="print">
                         <table class="table table-bordered table-striped table-hover dataTable" id="classreg" name="classreg" role="grid" aria-describedby="classreg">
                                    <thead>
                                        <tr>
                                            <th align="center">Registration Date</th>
                                            <th align="center">Client Name</th>
                                            <th align="center">Class</th>
                                            <th align="center">Start Time</th>
                                            <th align="center">End Time</th>
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                        <?php 

                                         $conn = new mysqli("localhost", "root", "", "eclipse_db") or die(mysqli_error()); 


                                if(isset($_REQUEST['action_type']) && !empty($_REQUEST['action_type'])){
                                        if($_REQUEST['action_type'] == 'filter') {

                                            $filterstart = date('Y-m-d', strtotime($_POST['filter_start']));
                                            $filterend = date('Y-m-d', strtotime($_POST['filter_end']));
                                            $className = $_POST['className'];

                                            if($className != "null" && $filterstart == $_POST['filter_start']) {
                                                $reg = $conn->query("SELECT * FROM `clientassignment` INNER JOIN studioclasssession ON clientassignment.SCS_Code = studioclasssession.SCS_Code INNER JOIN client ON clientassignment.CLIENT_ID = client.CLIENT_ID INNER JOIN studioclass ON studioclasssession.SC_Code = studioclass.SC_Code WHERE CA_RegDate BETWEEN '$filterstart' AND '$filterend' AND studioclasssession.SC_Code = '$className' ") or die(mysql_error());

                                            while($freg = $reg->fetch_array()) {
                                                                    ?>
                                        <tr>
                                            <td align="center"><?php echo date("F j, Y", strtotime($freg['CA_RegDate'])) ?></td>
                                            <td align="center"><?php echo $freg['CLIENT_FirstName'] ?> <?php echo $freg['CLIENT_LastName'] ?></td>
                                            <td align="center"><?php echo $freg['SC_Name'] ?></td>
                                            <td align="center"><?php echo date("g:i A", strtotime($freg['SCS_StartTime'])) ?></td>
                                            <td align="center"><?php echo date("g:i A", strtotime($freg['SCS_EndTime'])) ?></td>
                                        </tr>
                                    <?php
                                        } 
                                            } else if($className == "null" && ($filterstart != $_POST['filter_start'] || $filterend != $_POST['filter_end'])) {

                                                $reg = $conn->query("SELECT * FROM `clientassignment` INNER JOIN studioclasssession ON clientassignment.SCS_Code = studioclasssession.SCS_Code INNER JOIN client ON clientassignment.CLIENT_ID = client.CLIENT_ID INNER JOIN studioclass ON studioclasssession.SC_Code = studioclass.SC_Code") or die(mysql_error());

                                            while($freg = $reg->fetch_array()) {
                                                                    ?>
                                        <tr>
                                            <td align="center"><?php echo date("F j, Y", strtotime($freg['CA_RegDate'])) ?></td>
                                            <td align="center"><?php echo $freg['CLIENT_FirstName'] ?> <?php echo $freg['CLIENT_LastName'] ?></td>
                                            <td align="center"><?php echo $freg['SC_Name'] ?></td>
                                            <td align="center"><?php echo date("g:i A", strtotime($freg['SCS_StartTime'])) ?></td>
                                            <td align="center"><?php echo date("g:i A", strtotime($freg['SCS_EndTime'])) ?></td>
                                        </tr>
                                                <?php
                                                 }
                                                
                                         } else {

                                            $reg = $conn->query("SELECT * FROM `clientassignment` INNER JOIN studioclasssession ON clientassignment.SCS_Code = studioclasssession.SCS_Code INNER JOIN client ON clientassignment.CLIENT_ID = client.CLIENT_ID INNER JOIN studioclass ON studioclasssession.SC_Code = studioclass.SC_Code WHERE CA_RegDate BETWEEN '$filterstart' AND '$filterend' OR studioclasssession.SC_Code = '$className' ") or die(mysql_error());

                                            while($freg = $reg->fetch_array()) {
                                                                    ?>
                                        <tr>
                                            <td align="center"><?php echo date("F j, Y", strtotime($freg['CA_RegDate'])) ?></td>
                                            <td align="center"><?php echo $freg['CLIENT_FirstName'] ?> <?php echo $freg['CLIENT_LastName'] ?></td>
                                            <td align="center"><?php echo $freg['SC_Name'] ?></td>
                                            <td align="center"><?php echo date("g:i A", strtotime($freg['SCS_StartTime'])) ?></td>
                                            <td align="center"><?php echo date("g:i A", strtotime($freg['SCS_EndTime'])) ?></td>
                                        </tr>
                                    <?php
                                        }
                                            }
                                        }

                                    }  else {

                                         $reg = $conn->query("SELECT * FROM `clientassignment` INNER JOIN studioclasssession ON clientassignment.SCS_Code = studioclasssession.SCS_Code INNER JOIN client ON clientassignment.CLIENT_ID = client.CLIENT_ID INNER JOIN studioclass ON studioclasssession.SC_Code = studioclass.SC_Code") or die(mysql_error());

                                            while($freg = $reg->fetch_array()) {
                                                                    ?>
                                        <tr>
                                            <td align="center"><?php echo date("F j, Y", strtotime($freg['CA_RegDate'])) ?></td>
                                            <td align="center"><?php echo $freg['CLIENT_FirstName'] ?> <?php echo $freg['CLIENT_LastName'] ?></td>
                                            <td align="center"><?php echo $freg['SC_Name'] ?></td>
                                            <td align="center"><?php echo date("g:i A", strtotime($freg['SCS_StartTime'])) ?></td>
                                            <td align="center"><?php echo date("g:i A", strtotime($freg['SCS_EndTime'])) ?></td>
                                        </tr>
                                    <?php
                                        }
                                    }
                                        ?>

                                    </tbody>
                                </table>
                            </div>
                            </div>
                        </div>
            <script>

                      $(document).ready(function() {
    $('#classreg').DataTable( {
        dom: 'Bfrtip',
        buttons: [ 'copy', 'csv', 'excel',
            { 
                extend: 'print',
                title: '',
                responsive: true,
                footer: true,
                className: '',
                customize: function ( win ) {
                    $(win.document.body)
                        .prepend('<center><h4>Class Registrations Report</h4></center>')
                        .prepend('<center><h3>Eclipse Healing and Body Design Center</h3></center>')

                    $(win.document.body).find('h3').css('font-family','Impact'); 
 
                    $(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' )

                    $(win.document.body.innerHTML += "<br><br><center><div><label>Printed By: ____________  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Signed By:____________</label></div></center>")
                }

            }
        ]
    } );


} );
            </script>
            </section>
    <?php include("includes/footer.php"); ?>

    <!-- Jquery DataTable Plugin Js -->
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
</body>

</html>