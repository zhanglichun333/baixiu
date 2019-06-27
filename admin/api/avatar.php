<?php
require_once '../../config.php';
// 1.接受和校验上传过来email
if(empty($_GET['email'])){
	exit('缺少必要的参数');
}
$email=$_GET['email'];
// 2.连接数据库，根据email查询头像地址
$conn=mysqli_connect(bx_DB_HOST,bx_DB_USER,bx_DB_PASSWORD,bx_DB_NAME) or die('连接数据库失败'.mysqli_error($conn));
$res=mysqli_query($conn,"select avatar from users where email='{$email}';") or die('查询失败'.mysqli_error($conn));
// 3.echo 头像地址
$row=mysqli_fetch_assoc($res);
echo $row['avatar'];
