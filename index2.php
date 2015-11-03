<?php
 header('Access-Control-Allow-Origin: *');  

  $filename = $_FILES['file']['name'];
  $contacts = $_POST['filename'];
  //echo $contact;
  // foreach($contacts as $contact) {
  //   echo "Name: " + $contact['name'] + "\n";
  //   echo "E-mail: " + $contact['email'] + "\n";
  // }
  $destination = 'lookbook/' . $filename;
  move_uploaded_file( $_FILES['file']['tmp_name'] , $destination );

?>