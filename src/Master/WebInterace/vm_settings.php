<?php include("headers/header.php"); ?>
<?php
   $ch = curl_init();

   // set the url to fetch
   curl_setopt($ch, CURLOPT_URL, 'localhost:9001/pm/list');

   // don't give me the headers just the content
   curl_setopt($ch, CURLOPT_HEADER, 0);

   // return the value instead of printing the response to browser
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

   $content = curl_exec($ch);

   // remember to always close the session and free all resources
   curl_close($ch);
   $types = $content;
   $json_a = json_decode($types,true);
   $pms = $json_a['pmids'];
   
?>


<h1> VM Settings </h1>
<?php foreach($pms as $pm){
       $ch = curl_init();
   echo "<h2> Physical Machine ".$pm."</h2>";

   // set the url to fetch
   $url = 'localhost:9001/pm/'.$pm."/listvms";
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
   $vms = $json_b['vmids'];
   echo "<ul>";
   foreach($vms as $vm){
      echo "<li><a href='get_vm_details.php?vmid=$vm'>VM ($vm)</a></li>";
   }
   echo "</ul>";
   echo "<hr />";


}
?>

<?php include("headers/footer.php"); ?>
