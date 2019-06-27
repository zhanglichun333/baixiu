<?php  
// var_dump($_FILES['avatar']);
// response
// array(5) {
//   ["name"]=>
//   string(5) "5.jpg"
//   ["type"]=>
//   string(10) "image/jpeg"
//   ["tmp_name"]=>
//   string(27) "C:\Windows\Temp\phpA2D4.tmp"
//   ["error"]=>
//   int(0)
//   ["size"]=>
//   int(130211)
// }
// ================================
// 接受文件
if(empty($_FILES['avatar'])){
	exit('缺少必要参数');
}
$avatar=$_FILES['avatar'];
if($avatar['error']!==UPLOAD_ERR_OK){
	$GLOBALS['message'] = "上传失败";
}
// 保存文件
$ext=pathinfo($avatar['name'],PATHINFO_EXTENSION);
$target='../../static/uploads/img-'.uniqid().'.'.$ext;
if(!move_uploaded_file($avatar['tmp_name'], $target)){
	$GLOBALS['message'] = '文件移动失败';
}
// 服务端的反馈
echo substr($target,5);