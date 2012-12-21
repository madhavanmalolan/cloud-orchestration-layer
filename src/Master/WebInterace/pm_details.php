<?php include("headers/header.php");

echo "<h1>Physical Machine Details</h1> ";
   $ch = curl_init();
   $url = "localhost:9001/pm/".$_GET['pmid'];

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
   echo "<h2> RAM </h2>";
   echo "<table style='float:left'>";
       echo "<tr><td>".$json_b['capacity']['ram']."</td></tr>";
       for($i = 0 ; $i<10;$i ++)
           echo "<tr><td style='color:red;background-color:red'>.</td></tr>";
   echo "</table>";
   echo "<table>";
       echo "<tr><td>".$json_b['free']['ram']."</td></tr>";
       for($i = 0 ; $i<($json_b['free']['ram']*10/$json_b['capacity']['ram']);$i ++)
           echo "<tr><td style='color:green;background-color:green'>.</td></tr>";
       for($i = 0 ; $i<10-($json_b['free']['ram']*10/$json_b['capacity']['ram']);$i ++)
           echo "<tr><td style='color:black;background-color:black'>.</td></tr>";
   echo "</table>";

   echo "<h2> HDD </h2>";
   echo "<table style='float:left'>";
       echo "<tr><td>".$json_b['capacity']['disk']."</td></tr>";
       for($i = 0 ; $i<10;$i ++)
           echo "<tr><td style='color:red;background-color:red'>.</td></tr>";
   echo "</table>";
   echo "<table>";
       echo "<tr><td>".$json_b['free']['disk']."</td></tr>";
       for($i = 0 ; $i<($json_b['free']['disk']*10/$json_b['free']['disk']);$i ++)
           echo "<tr><td style='color:green;background-color:green'>.</td></tr>";
       for($i = 0 ; $i<10-($json_b['free']['disk']*10/$json_b['capacity']['disk']);$i ++)
           echo "<tr><td style='color:black;background-color:black'>.</td></tr>";
   echo "</table>";
   echo "<h2> CPU </h2>";
   echo "<table style='float:left'>";
       echo "<tr><td>".$json_b['capacity']['cpu']."</td></tr>";
       for($i = 0 ; $i<10;$i ++)
           echo "<tr><td style='color:red;background-color:red'>.</td></tr>";
   echo "</table>";
   echo "<table>";
       echo "<tr><td>".$json_b['free']['cpu']."</td></tr>";
       for($i = 0 ; $i<($json_b['free']['cpu']*10/$json_b['capacity']['cpu']);$i ++)
           echo "<tr><td style='color:green;background-color:green'>.</td></tr>";
       for($i = 0 ; $i<10-($json_b['free']['cpu']*10/$json_b['capacity']['cpu']);$i ++)
           echo "<tr><td style='color:black;background-color:black'>.</td></tr>";
   echo "</table>";
   echo "<p> Response to the URI <p><pre>";
   echo $content;
   echo "</pre>";


include("headers/footer.php");
?>
