<?php
include("headers/header.php");

echo "<h1> Physical Machines </h1>";
   $ch = curl_init();
   $url = "localhost:9001/pm/list";

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
   echo "<ul>";
   foreach($json_b['pmids'] as $pm){
       echo "<li><a href='pm_details.php?pmid=$pm'> $pm </a></li>";
      
   }
   echo "</ul>";


include("headers/footer.php");
?>
