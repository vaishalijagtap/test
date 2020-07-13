<?php

include('classes/api.php');

$campaignApi = new campaignAPI();

// Get User details
if (isset($_GET['userId'])) {
    $output = $campaignApi->getDetails($_GET['userId']);  
} else {
    if (isset($_REQUEST['action']) && $_REQUEST['action']=='create') {
        $output = $campaignApi->createUser($_REQUEST);
    } else if (isset($_REQUEST['action']) && $_REQUEST['action']=='optout') {
        $output = $campaignApi->optOut($_REQUEST);
    } else {
        $output = $campaignApi->read();
    }
}

if (empty($output)) {
    $output = array('message'=>'No Data found.');
}

echo json_encode($output);
?>