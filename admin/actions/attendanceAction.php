
<?php
session_start();
include '../../dbConnect.php';
$pdo = new dbConnect();

$tblName1 = 'attendance';
$tblName2 = 'client';
$tableName3='transaction';
$tableName4 ='towels';
$tableTowel = 'towelinventory';

$firstname = 'CLIENT_FirstName';
$lastname = 'CLIENT_LastName';
$fullname = $firstname. " " .$lastname;
$column1 = $fullname;
$month = date("M", strtotime("+8 HOURS"));
$year = date("Y", strtotime("+8 HOURS"));
$date = date("Y-m-d");
$time=date("H:i:s", strtotime("+7 HOURS"));



  //working
if(isset($_REQUEST['action_type']) && !empty($_REQUEST['action_type'])){
    if($_REQUEST['action_type'] == 'add'){ 
        $regstat = $pdo->regStat($_POST['clientName']);



      
         $userData = array(
            'CLIENT_ID' => $_POST['clientName'],
            'A_TowelQty' => $_POST['towel'],
            'A_LockerKey' => $_POST['Locker'],
            'A_TimeIn' => $time,
            'A_Year' => $year,
            'A_Month' => $month,
            'A_Date' => $date,
            'A_id' => $_POST['sample'],
            'A_status' => $regstat
    

        );
         

        $check = $pdo->checkAttendance($_POST['clientName'],$date);
        if($check <> $_POST['clientName'] || empty($check)){
            
                

                $available = $pdo->previousAvailable($date);
                $borrowed = $pdo->previousBorrowed($date);
                $userData3 = array(
                    'TI_Borrowed' => ($_POST['towel'] + $borrowed),
                    'TI_Available' => ($available - $_POST['towel'])
                );

                if($_POST['towel'] <= $available){
                    $insert = $pdo->insert($tblName1,$userData);
                    echo "<script>alert('Client Successfully Timed-in');window.location.href='../attendance.php';</script>";
                    $condition = array("TI_Date" => $date);
                    $update = $pdo->update($tableTowel,$userData3,$condition);
                }else{
                    echo "<script>alert('Client time-in Failed! Insufficient towels!');window.location.href='../attendance.php';</script>";
                }
                
                

            
            
        $price = $pdo->walkin();
        
        
        $userData2 = array(
            'CLIENT_ID' => $_POST['clientName'],
            'TR_Type' => 'Walk-in',
            'TR_Bill' => $price,
            'TR_Status' => 'unpaid',
            'TR_TransactionDate' => $date,
            'year' => $year,
            'month' => $month
        );
        if($regstat == "Walk-in"){
            $insert1= $pdo->insert($tableName3,$userData2);
            
        }else{

        } 
            
        $inser = $pdo->insert($tableTowel, $userData2);
        $statusMsg = $insert?'Studio Class data has been inserted successfully.':'Some problem occurred, please try again.';
        $_SESSION['statusMsg'] = $statusMsg;
        // header("Location:../attendance.php");
    }else{
        
        echo "<script>alert('Client Timed-in Failed!');window.location.href='../attendance.php';</script>";
         $statusMsg = $insert?'Studio Class data has been inserted successfully.':'Some problem occurred, please try again.';
        $_SESSION['statusMsg'] = $statusMsg;
    }
        
        //working
    }elseif($_REQUEST['action_type'] == 'edit'){
            $userData = array(
            'A_TowelQty' => $_POST['modifiedTowel'],
            'A_LockerKey' => $_POST['modifiedLocker']
            );

            $condition = array('A_Code' => $_POST['A_Code']);
            $update = $pdo->update($tblName1,$userData,$condition);
           
            echo "<script>alert('Client Attendance Information Successfully Modified!');window.location.href='../attendance.php';</script>";
             $statusMsg = $update?'User data has been updated successfully.':'Some problem occurred, please try again.';
            $_SESSION['statusMsg'] = $statusMsg;
        //working
    }elseif($_REQUEST['action_type'] == 'out'){ 
        $userData = array(
            'A_TimeOut' => $time,
            'A_TowelReturn' => $_POST['returnedTowel']
        );

        $borrowed = $pdo->previousBorrowed($date);
        $returned = $pdo->getReturned($date);
        $userData4 = array(
            'TI_Returned' => ($_POST['returnedTowel'] + $returned),
            'TI_Borrowed' => ($borrowed - $_POST['returnedTowel'])

        );

        $condition2 = array('TI_Date' => $date);
        $update4 = $pdo->update($tableTowel,$userData4,$condition2);
            

        $condition1 = array('A_Code' => $_POST['A_Code']);
        $update1 = $pdo->update($tblName1,$userData,$condition1);
        
        echo "<script>alert('Client Successfully timed-out');window.location.href='../attendance.php';</script>";
         $statusMsg = $update?'User data has been updated successfully.':'Some problem occurred, please try again.';
            $_SESSION['statusMsg'] = $statusMsg;


        

    }
}

 