
<?php
session_start();
include '../../dbConnect.php';

$pdo = new dbConnect();
$user = $_SESSION['username'];
$pass = $_SESSION['password'];
$tblName1 = 'attendance';
$tblName2 = 'client';
$tableName3='transaction';
$tableName4 ='towels';
$tableTowel = 'towelinventory';

$month = date("M", strtotime("+8 HOURS"));
$year = date("Y", strtotime("+8 HOURS"));
date_default_timezone_set('Asia/Manila');
$date = date("Y-m-d");
$time=date("H:i:s");

$client = $_POST['clientName'];
$userid = $pdo->getUserID($user,$pass);
$first = $pdo->getClientFirst($client);

  //working
if(isset($_REQUEST['action_type']) && !empty($_REQUEST['action_type'])){
    if($_REQUEST['action_type'] == 'add'){
        $regstat = $pdo->regStat($client);
        
            $userData = array(
            'CLIENT_ID' => $client,
            'A_TowelQty' => $_POST['towel'],
            'A_LockerKey' => $_POST['Locker'],
            'A_TimeIn' => $time,
            'A_Year' => $year,
            'A_Month' => $month,
            'A_Date' => $date,
            'A_status' => $regstat
            );

            $desc = ' '.$first. ' has been timed in';
                $log = array (
                    'userID' => $userid,
                    'log_activity' => 'Client Time-in',
                    'log_description' => $desc,
                    'log_date' => $date,
                    'log_time' => $time
                );           
                $insert = $pdo->insert('log',$log);
                
            $check = $pdo->checkAttendance($_POST['clientName'],$date);
            $available = $pdo->previousAvailable();
            $borrowed = $pdo->previousBorrowed();
            $expiry = $pdo->memExpire($_POST['clientName']);

            $date1 = new DateTime(date("Y-m-d"));
            $date2 = new DateTime($expiry);
            $diff = $date2->diff($date1)->format("%a");

            $date3 = new DateTime('2018-02-12');
            $date4 = new DateTime('2018-02-16');
            $testDiff = $date4->diff($date3)->format("%a");

                if($_POST['towel'] > $available){

                    echo "<script>alert('Insufficient towels! Time-in Failed ');window.location.href='../attendance.php';</script>";


                }elseif($check == $_POST['clientName']){

                    echo "<script>alert('Client has already been Timed-in! Time-in Failed ');window.location.href='../attendance.php';</script>";

                }else{

                        //insert time-in information to attendance
                        $insert = $pdo->insert($tblName1,$userData);
                        $insert = $pdo->insert('log',$log);

                        echo "<script>alert('Client Time-in Success! ');window.location.href='../attendance.php';</script>";
                        //update towel information

                        $userData7 = array(
                            'TI_Borrowed' => ($_POST['towel'] + $borrowed),
                            'TI_Available' => ($available - $_POST['towel'])
                        );
                        $condition = array("TI_Date" => $date);
                        $update = $pdo->update($tableTowel,$userData7,$condition);


                        //bill client for walk-in fee
                        $bill = $pdo->checkBill($_POST['clientName'],date("Y-m-d"));
                        $price = $pdo->penaltyPrice('Walk-in');

                        //check if client has already been billed for walk-in in the same date
                        if($bill <> $_POST['clientName'] || empty($bill)){
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

                            }
                        }else{

                        }


                }






        //working

    }elseif($_REQUEST['action_type'] == 'edit'){
        
            $userData = array(
            'A_TowelQty' => $_POST['modifiedTowel'],
            'A_LockerKey' => $_POST['modifiedLocker']
            );
            $locker = $pdo->locker($date,$_POST['modifiedLocker']);
            if($locker <> $_POST['modifiedLocker']){
                $condition = array('A_Code' => $_POST['A_Code']);
                $update = $pdo->update($tblName1,$userData,$condition);
                $desc = ' '.$first.'\'s attendance information has been modified ';
                $log = array (
                    'userID' => $userid,
                    'log_activity' => 'Attendance Information Modification',
                    'log_description' => $desc,
                    'log_date' => $date,
                    'log_time' => $time
                );  
                $insert = $pdo->insert('log',$log);

                echo "<script>alert('Client Attendance Information Successfully Modified!');window.location.href='../attendance.php';</script>";

            }else{
                echo "<script>alert('Client Attendance Information Modification Failed!');window.location.href='../attendance.php';</script>";

            }


        //working
    }elseif($_REQUEST['action_type'] == 'out'){
        $userData = array(
            'A_TimeOut' => $time,
            'A_TowelReturn' => $_POST['returnedTowel'],
            'A_ReturnedKey' => $_POST['key']
        );

        $borrowed = $pdo->previousBorrowed();
        $returned = $pdo->getReturned();
        $userData4 = array(
            'TI_Returned' => ($_POST['returnedTowel'] + $returned),
            'TI_Borrowed' => ($borrowed - $_POST['returnedTowel'])

        );

        $desc = ' '.$first.'\'s has been timed out ';
            $log = array (
                'userID' => $userid,
                'log_activity' => 'Client Time out',
                'log_description' => $desc,
                'log_date' => $date,
                'log_time' => $time
            );  
            $insert = $pdo->insert('log',$log);


        $towels = $pdo->checkTowels($_POST['A_Code']);
        if($towels <> $_POST['returnedTowel'] && $_POST['key'] == 'Unreturned'){
            $lostQty = $towels - $_POST['returnedTowel'];
            $penaltyPrice = $pdo->penaltyPrice('Lost Towel(s)');
                $userData1 = array(
                'CLIENT_ID' => $_POST['clientName'],
                'TR_Type' => 'Lost Towel(s) ',
                'TR_Bill' => $penaltyPrice * $lostQty,
                'TR_Status' => 'unpaid',
                'TR_TransactionDate' => $date,
                'year' => $year,
                'month' => $month
        );

                $fee = $pdo->penaltyPrice('Lost Locker Key');
                $userData5 = array(
                    'CLIENT_ID' => $_POST['clientName'],
                    'TR_Type' => 'Lost Locker Key',
                    'TR_Bill' => $fee,
                    'TR_Status' => 'unpaid',
                    'TR_TransactionDate' => $date,
                    'year' => $year,
                    'month' => $month
                );
                $condition = array('A_Code' => $_POST['A_Code']);
                $update = $pdo->update($tblName1,$userData,$condition);

                $condition2 = array('TI_Date' => $date);
                $update4 = $pdo->update($tableTowel,$userData4,$condition2);



         $insert = $pdo->insert($tableName3, $userData1);
         $insert = $pdo->insert($tableName3, $userData5);
         echo "<script>alert('Client charged for a penalty. Client Successfully Timed-out. ');window.location.href='../attendance.php';</script>";
        }elseif($towels <> $_POST['returnedTowel'] && $_POST['key'] == 'Returned'){
            $lostQty = $towels - $_POST['returnedTowel'];
            $penaltyPrice = $pdo->penaltyPrice('Lost Towel(s)');
                $userData1 = array(
                'CLIENT_ID' => $_POST['clientName'],
                'TR_Type' => 'Lost Towel(s) ',
                'TR_Bill' => $penaltyPrice * $lostQty,
                'TR_Status' => 'unpaid',
                'TR_TransactionDate' => $date,
                'year' => $year,
                'month' => $month
                );

                $insert = $pdo->insert($tableName3, $userData1);

                $condition2 = array('TI_Date' => $date);
                $update4 = $pdo->update($tableTowel,$userData4,$condition2);


                $condition1 = array('A_Code' => $_POST['A_Code']);
                $update1 = $pdo->update($tblName1,$userData,$condition1);
                echo "<script>alert('".$lostQty." towel(s) lost. Client Successfully Timed-out. ');window.location.href='../attendance.php';</script>";



        }elseif($towels == $_POST['returnedTowel'] && $_POST['key'] == 'Unreturned'){
            $fee = $pdo->penaltyPrice('Lost Locker Key');
                $userData5 = array(
                    'CLIENT_ID' => $_POST['clientName'],
                    'TR_Type' => 'Lost Locker Key',
                    'TR_Bill' => $fee,
                    'TR_Status' => 'unpaid',
                    'TR_TransactionDate' => $date,
                    'year' => $year,
                    'month' => $month
                );

                $insert = $pdo->insert($tableName3, $userData5);
                $condition = array('A_Code' => $_POST['A_Code']);
                $update = $pdo->update($tblName1,$userData,$condition);

                $condition2 = array('TI_Date' => $date);
                $update4 = $pdo->update($tableTowel,$userData4,$condition2);


                $condition1 = array('A_Code' => $_POST['A_Code']);
                $update1 = $pdo->update($tblName1,$userData,$condition1);
                echo "<script>alert('Locker key unreturned! Client Successfully timed-out!');window.location.href='../attendance.php';</script>";


        }else{
            $condition2 = array('TI_Date' => $date);
            $update4 = $pdo->update($tableTowel,$userData4,$condition2);


            $condition1 = array('A_Code' => $_POST['A_Code']);
            $update1 = $pdo->update($tblName1,$userData,$condition1);

        echo "<script>alert('Client Successfully timed-out');window.location.href='../attendance.php';</script>";








        }


    }
}
