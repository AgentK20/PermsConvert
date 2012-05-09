<?php
$id = $_GET[id];
$t = $_GET[t];
if($t=="g"){
    $type = "groups";
} elseif ($t=="u"){
    $type = "users";
} else {
    echo "<html><title>Failure!</title><body>Failure of epic proportions has occured. You probably clicked the link after the file had been deleted from our servers. <a href='http://araeosia.com/windperms/'>Click here to try again.</a></body></html>";
}
$fileloc = "/var/www/araeosia/windperms/output/" . $id . "." . $type . ".yml";
if (file_exists($fileloc)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . $type . '.yml');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($fileloc));
    ob_clean();
    flush();
    readfile($fileloc);
    exit;
} else {
    echo "<html><title>Failure!</title><body>Failure of epic proportions has occured. You probably clicked the link after the file had been deleted from our servers. <a href='http://araeosia.com/windperms/'>Click here to try again.</a></body></html>";
}
?>
