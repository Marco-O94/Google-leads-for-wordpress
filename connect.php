<?php
function insert_data($fn, $ln, $ph, $em, $co) {
   $servername = DB_HOST;
   $username = DB_USER;
   $password = DB_PASSWORD;
   $dbname = DB_NAME;
   
   // Create connection
   $conn = new mysqli($servername, $username, $password, $dbname);
   // Check connection
   if ($conn->connect_error) {
     die("Connessione fallita: " . $conn->connect_error);
   }
   $sql = "INSERT INTO lead_ads(nome, cognome, email, tel, azienda)
   VALUES ('$fn', '$ln', '$ph', '$em', '$co')";
  $conn->query($sql);
   $conn->close();
   }
   $Google_data = file_get_contents("php://input");
   $decoded_data= json_decode($Google_data, true);


   function get_dbData() {
    $servername = DB_HOST;
     $username = DB_USER;
     $password = DB_PASSWORD;
     $dbname = DB_NAME;
     // Create connection
     $a=mysqli_connect($servername,$username,$password,$dbname);
     $conn = new mysqli($servername, $username, $password, $dbname);
    $sql = ("SELECT google_key FROM credentials WHERE id='main_key'");
    $conn->query($sql);
    $rt=mysqli_query($a,$sql);
    $conn->close();
    return mysqli_fetch_array($rt,MYSQLI_ASSOC);
  }
$result = get_dbData();
   $pass=$result["google_key"];

if ( $pass == $decoded_data["google_key"]){
   
   $firstName = $decoded_data["user_column_data"][0]["string_value"];
   $lastName = $decoded_data["user_column_data"][1]["string_value"];
   $email = $decoded_data["user_column_data"][2]["string_value"];
   $phone = $decoded_data["user_column_data"][3]["string_value"];
   $company = $decoded_data["user_column_data"][4]["string_value"];
   insert_data($firstName, $lastName, $phone, $email, $company);
}
