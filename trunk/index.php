<?php
/////////////////////////////////////////////////////////////////////////////////
////              Z-Blog PHP 坑爹的开始
/////////////////////////////////////////////////////////////////////////////////

//phpinfo();
printf(__LINE__);


echo "creating a databse \n";
try {
     $dbh=new PDO('sqlite:voting.db');
     $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     $dbh->exec('
     CREATE TABLE tally(
     QID varchar(32) NOT NULL,
     AID integer NOT NULL,
     votes integer NOT NULL,
     PRIMARY KEY(QID,AID)
     )');
 } catch (Exception $e) {
     echo "error!!:$e";
     exit;
     
 }
 echo "db created successfully!";
?>