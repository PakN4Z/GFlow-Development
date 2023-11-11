<?php
// Include the library file
require_once 'lib\drv.vlsv2HH.lib.php';
include('lib\millMachDrv.cltmpl.php');


// Replace 'PLC_IP_ADDRESS' and 'PORT' with actual values
$connection = connectToPLC('192.168.1.228', 4444);

// Check the connection
if (!$connection) {
    echo "Failed to connect to PLC.";
} else {
    echo "Connection successful!";
}
?>