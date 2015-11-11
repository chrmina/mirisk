<?php
// check if a file was submitted
if(!isset($_FILES['userfile'])) {
  echo '<p>Please select a file</p>';
}
else
{
  try {
    upload();
    // give praise and thanks to the php gods
    echo '<p>Thank you for submitting</p>';
  }
  catch(Exception $e) {
    echo $e->getMessage();
    echo 'Sorry, could not upload file';
  }
}
?>
