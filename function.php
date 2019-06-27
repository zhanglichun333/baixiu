<?php

/**
 * 封装大家公用的函数
 */

require_once 'config.php';

/**
 * 获取当前用户登录的信息，如果没获取到就自动转入登录页面
 *@return[type][description]
 */
session_start();
function bx_get_current_user(){
	if(empty($_SESSION['current_login_user'])){
	  header('location: login.php');
	  exit();
	}
	return $_SESSION['current_login_user'];
}

/**
 * 通过连接数据库查询获取多条数据
 * 索引数组套关联数组
 *@return[type][description]
 */
function bx_fetch_all($sql){
	$conn=mysqli_connect(bx_DB_HOST,bx_DB_USER,bx_DB_PASSWORD,bx_DB_NAME) or die('连接数据库失败'.mysqli_error($conn));
	$query=mysqli_query($conn,$sql);
	if(!$query){
		exit('查询失败');
	}

	/*
	object(mysqli_result)#2 (5) {
	  ["current_field"]=>
	  int(0)
	  ["field_count"]=>
	  int(9)
	  ["lengths"]=>
	  NULL
	  ["num_rows"]=>
	  int(0)
	  ["type"]=>
	  int(0)
	}*/
	  // $result=array();
	while($row=mysqli_fetch_assoc($query)){
		$result[]=$row;
	}
	return $result;
}

/**
 * 通过连接数据库查询获取一条数据
 * 关联数组
 *@return[type][description]
 */
function bx_fetch_one($sql){
	$res=bx_fetch_all($sql);
	return isset($res[0])?$res[0]:null;
}

/**
 * 执行一个增删改语句
 *@return[type][description]
 */
function bx_execute($sql){
	$conn=mysqli_connect(bx_DB_HOST,bx_DB_USER,bx_DB_PASSWORD,bx_DB_NAME) or die('连接数据库失败'.mysqli_error($conn));
	$query=mysqli_query($conn,$sql);
	if(!$query){
		return false;
	}
	$affected_rows=mysqli_affected_rows($conn);
	return $affected_rows;
}