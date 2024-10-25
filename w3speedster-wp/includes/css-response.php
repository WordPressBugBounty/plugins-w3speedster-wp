<?php 

if(!empty($_POST['response_path']) && strpos($_POST['response_path'],'/critical-css/') !== false){
	$data = json_encode($_POST);
	//echo $_POST['response_path'];
	if(is_writable($_POST['response_path'])){
		file_put_contents($_POST['response_path'].'/'.base64_encode($_POST['url']).'.json',$data);
		echo json_encode(array('w3-status-code'=>1,'success'=>1)); 
		exit;
	}
}
echo json_encode(array('w3-status-code'=>1,'error'=>1));
exit;