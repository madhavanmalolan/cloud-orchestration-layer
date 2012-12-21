<html>
    <head>
        <title> Madhavan's Cloud </title>
        <style>
            body { 
                background-color:black;
            }
            div.container{
               background-color:white;
               border-radius: 5px;
               margin : 10px;
               padding : 10px;
           }
           table.menu tr td {
               background-color : gray;
               color:white;
               border-radius:5px;
               padding: 3px;
           }
            table.menu tr td:hover {
               background-color : cyan;
               color:white;
               border-radius:5px;
               padding: 3px;
           }                   
           table.menu tr td a {
               text-decoration:none;
               color:black;
           }
            
        </style>
    </head>
    <body>
        <div class=container>
        <h1> Cloud - Admin Panel </h1>
        <p> (Client) </p>
        <table class="menu">
            <tr>

                <td> <a href="create.php">Create A VM </a></td>
                <td> <a href="vm_settings.php"> VM settings </a> </td>
                <td> <a href="pm_settings.php"> Physical Machine Settings </a></td>
                <td> <a href="available_images.php"> Images </a> </td>


            </tr>
        </table>
