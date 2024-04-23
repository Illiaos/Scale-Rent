<?php 
    if(isset($_COOKIE['userLevel']))
    {
        echo ($_COOKIE['userLevel']);
    }
    else
    {
        echo ("NOT SET");
    }
?>