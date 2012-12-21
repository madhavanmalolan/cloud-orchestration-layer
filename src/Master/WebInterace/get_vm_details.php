<?php include("headers/header.php")?>
<?php
   $ch = curl_init();
   $url = "localhost:9001/vm/query?vmid=".$_GET['vmid'];
   
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
   echo "<h1>".$json_b['name']."</h1>";
   echo "<p> ID = ".$json_b['vmid']."</p>";
   echo "<p> Instance Type = ".$json_b['vm_type']."</p>";
   echo "<p> <a href='destroy_vm.php?id=".$_GET['vmid']."'>DESTROY THIS VM </a></p>";
   echo "<p> JSON Reply to URI $uri </p>";
   echo "<pre>$content</pre>";
   

?>

<?php include("headers/footer.php")?>


