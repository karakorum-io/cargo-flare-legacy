<?php
/**
 * View file for Cron listings and other dashboard information showcasing
 * 
 * @author Chetu Inc.
 * @version 1.0
 * @depends index action of Cron Controller
 */
?>
<html>
    <head>
        <title>Customer Portal | Cron Dashboard</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
    </head>
    <body style="background:#000; color:#fff;">
        <div class="container-fluid">
            <br><br>
            <h1 class="text-center">Cron Dashboard</h1>
            <h4 class="text-center"><?php echo date('M d, Y h:i:s A');?></h4>
            <ul>
                <li><a class="text-danger" href="cron/terminate_no_hit_sessions" target="_blank"><h4>Terminate over timed sessions</h4></a></li>
            </ul>
        </div>
    </body>
</html>