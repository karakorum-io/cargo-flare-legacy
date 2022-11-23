<?php

	print_r($_POST);
    print_r(move_uploaded_file($_POST['tmp_name'],'uploads/'.$_POST['name']));
?> 