<?php
function getComplainStatus($status) {
    $config = C('COMPLAIN_STATUS');
    return ($config[$status]);
}

?>