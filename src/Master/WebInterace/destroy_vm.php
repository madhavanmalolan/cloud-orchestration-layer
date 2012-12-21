<?php 
include("headers/header.php");

   $ch = curl_init();
   $url = "localhost:9001/vm/destroy?vmid=".$_GET['id'];
   
   echo "<p> URI : $url </p>";
   curl_setopt($ch, CURLOPT_URL, $url);

   // don't give me the headers just the content
   curl_setopt($ch, CURLOPT_HEADER, 0);

   // return the value instead of printing the response to browser
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

   $content = curl_exec($ch);

   // remember to always close the session and free all resources
   curl_close($ch);
   $types = $content;
   $json_b = json_decode($types,true);
   if($json_b['status'] == 1 ){
       echo  "<h1> This VM is no more :( </h1>";
       echo "<p> Destroy VM successful </p>";

   }
   else echo "<h1> Unable to destroy VM </h1>";
   echo "<p> Server Reply </p><pre>";
   echo $content;
   echo "</pre>";


include("headers/footer.php");
?>
