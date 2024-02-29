<?php

$conn = new mysqli("localhost","crisenri_intranet","].wKbv44W4LW8b","crisenri_intranet"); 
$conn->set_charset("utf8mb4");
if(mysqli_connect_errno()){
echo 'Conexión Fallida : ', mysqli_connect_error();
exit();
}

