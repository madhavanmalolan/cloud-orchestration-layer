<?php include("headers/header.php"); ?>
<?php
   $ch = curl_init();

   // set the url to fetch
   curl_setopt($ch, CURLOPT_URL, 'localhost:9001/vm/types');

   // don't give me the headers just the content
   curl_setopt($ch, CURLOPT_HEADER, 0);

   // return the value instead of printing the response to browser
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

   $content = curl_exec($ch);

   // remember to always close the session and free all resources
   curl_close($ch); 
   $types = $content;
   $json_a = json_decode($types,true);
   $ch = curl_init();

   // set the url to fetch
   curl_setopt($ch, CURLOPT_URL, 'localhost:9001/image/list');

   // don't give me the headers just the content
   curl_setopt($ch, CURLOPT_HEADER, 0);

   // return the value instead of printing the response to browser
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

   $content = curl_exec($ch);

   // remember to always close the session and free all resources
   curl_close($ch); 
   $json_b = json_decode($content,true);
   echo $json_b;
?>
    <br /><br />
    <form action="create_status.php" method="get">
        <table>
            <tr>
              <td>Enter Name for the new VM </td><td><input name="name"></td>
            </tr>
            <tr>
              <td>Instance Type (<a href="types.php">Guide</a>)</td><td>
                 <select name="vm_type">
          
                  <?php
                  $types = $json_a['types'];
                  foreach($types as $type){
                      echo "<option value=".$type['tid'].">CPU : ".$type['cpu'].",RAM : ".$type['ram']."MB, HDD : ".$type['disk']."GB</option>";
                  }
                  ?>    
                  </select>
              </td> 
           
            </tr>
            <tr>
                 <td> Image Type </td><td>
                 <select name = "image_type">
                     <?php
                     $images = $json_b['images'];
                     foreach($images as $image){
                         echo "<option value=".$image['id']."> ".$image['name']."</option>";
                     }
                     ?>
                 </select>
                  </td>


            </tr>
            </table>
         <input type=submit value="Add new VM!"/>
    </form>
<?php include("headers/footer.php") ?>
