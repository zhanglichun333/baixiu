<?php
require_once '../../function.php';
if(empty($_GET['id'])){
	exit('缺少必要参数');
}
//id=1 or 1=1 id必须是数字
//方案1： $id=(int)$_GET['id'];
// 方案2：
$id=$_GET['id'];
// if(!is_numeric($id)){//is_numeric()是数字为true
// 	exit('请输入数字的参数');
// }
$row=bx_execute('delete from comments where id in ('.$id.');');
// header('location: /admin/categories.php');
header('Content-Type: application/json');
echo json_encode($row>0);