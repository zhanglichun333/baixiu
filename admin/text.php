<?php 
$conn=mysqli_connect(bx_DB_HOST,bx_DB_USER,bx_DB_PASSWORD,bx_DB_NAME) or die('连接数据库失败'.mysqli_error($conn));
	$query=mysqli_query($conn,'select
comments.*,
posts.title as posts_title
from comments
inner join posts on comments.post_id=posts.id
order by comments.created desc
limit 0,6;');
	if(!$query){
		exit('查询失败');
	}
	echo $query;