<?php
require 'MacToM3U.php';


header('Content-Type: application/json; charset=utf-8');
$ISCOMMANDLINE = false;


$obj = null;
if($ISCOMMANDLINE){
    if(count($argv) < 3){print "requires 2 arguments"; exit();}
    $obj = new MacToM3U($argv[1],$argv[2]);
}else if($ISCOMMANDLINE === false && $_SERVER['REQUEST_METHOD'] === 'POST'){
    $data = json_decode(file_get_contents('php://input'));
    $host = isset($data->host)?substr($data->host,0,-3):'';
    $mac = isset($data->mac)?$data->mac:'';
    $obj = new MacToM3U($host,$mac);
}

if($obj == null){
    echo json_encode(array('msg'=>'Invalid Request', 'data'=>'','error'=> 1));
    exit(1);
}



if(!$obj->getToken()){
    echo json_encode(array('msg'=>'Failed to get Token', 'data'=>'','error'=> 1));
    exit(1);
}
if(!$obj->getProfile()){
    echo json_encode(array('msg'=>'Failed to get Profile Information', 'data'=>'','error'=> 1));
    exit(1);
}
if(!$obj->getOrderedVODList()){
    echo json_encode(array('msg'=>'Failed to get VOD List', 'data'=>'','error'=> 1));
    exit(1);
}
if(!$obj->getLink()){
    echo json_encode(array('msg'=>'Failed to get Link', 'data'=>'','error'=> 1));
    exit(1);
}

echo json_encode(array('msg'=>'Success', 'data'=>$obj->getCredentials(),'error'=> 0),JSON_UNESCAPED_SLASHES);
exit(0);

