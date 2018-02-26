<?php

   //testPage.html
   //messagePage.html
	//Database Info//

   $db_host = "localhost";//dbhost
   $db_name = "fsbhm_entry";//dbname
   $db_username = "root";//dblogin
   $db_password = "";
   $name = strtolower($_POST['name']);
   $area = $_POST['area'];
   $email = $_POST['email'];
   $ipaddress = $_SERVER['REMOTE_ADDR'];

   try{
   	$conn = new PDO("mysql:dbname=$db_name;host=$db_host", $db_username, $db_password);
   	$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO:: FETCH_ASSOC);
   	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   }
   catch(PDOException $e){
   	echo "there seems to be an issue with the database";
   	die();
   }
   //to check whether or not ip is already logged
   $mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);
   $result = $mysqli->query("SELECT id FROM examineers WHERE email = '$email'");
   //to check if there is an entry associated with the email
   //if not accept info into database and send the test page
   if($result->num_rows == 0) {
   $query = "INSERT INTO `examineers` (name, email, location, dbIp) values(:name,:email,:area,:ipaddress)";//:dbIp dbIp
   $stmnt = $conn->prepare($query);
   $stmnt->bindValue(':name', $name);
   $stmnt->bindValue(':area', $area);
   $stmnt->bindValue(':email', $email);
   $stmnt->bindValue(':ipaddress', $ipaddress);
   $stmnt->execute();
   $stmnt->closeCursor();
  header("location:testPage.html");
  exit();}  
   //if there is an entry check date of last time taken and see how many days have past if not been 30 days do not allow to take test
    else {
       $takenDate = $mysqli->query("SELECT test_date FROM examineers WHERE email = '$email'")->fetch_object()->test_date;
       $today = new DateTime();
       $daysLeft = 30 - ((strtotime($today->format('Y-m-d H:i:s')) - strtotime($takenDate)) /86400);
       if($daysLeft <= 0){
         $query = "INSERT INTO `examineers` (name, email, location, dbIp) values(:name,:email,:area,:ipaddress)";//:dbIp dbIp
         $stmnt = $conn->prepare($query);
         $stmnt->bindValue(':name', $name);
         $stmnt->bindValue(':area', $area);
         $stmnt->bindValue(':email', $email);
         $stmnt->bindValue(':ipaddress', $ipaddress);
         $stmnt->execute();
         $stmnt->closeCursor();
         header("location:testPage.html");
         exit();}
      
       else{
         echo round($daysLeft) . " Days Left Until Next Try! Please Retake!";}
     }
       $mysqli->close();
?>
      
