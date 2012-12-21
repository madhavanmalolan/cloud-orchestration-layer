<?php include("headers/header.php")?>
<?php
   $ch = curl_init();

   // set the url to fetch
   curl_setopt($ch, CURLOPT_URL, 'localhost:9001/vm/create?name='.$_GET['name']."&vm_type=".$_GET['vm_type']."&image_type=".$_GET['image_type']);

   // don't give me the headers just the content
   curl_setopt($ch, CURLOPT_HEADER, 0);

   // return the value instead of printing the response to browser
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

   $content = curl_exec($ch);

   // remember to always close the session and free all resources
   curl_close($ch);
   $types = $content;
   $json_a = json_decode($types,true);
   if($json_a['vmid'] != 0){
       echo "<h1> Woo hoo!</h1>";
       echo "<p> Successfully created a VM with VM-Id = ".$json_a['vmid']."</p>";
   }
   else {
        echo "<h1>Oops! An error occured</h1>";
       echo "<p> Check for duplicates </p>";
   }


include("headers/footer.php");?>
