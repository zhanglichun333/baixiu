<?php  
//接受ajax请求，返回评论数据
require_once('../../function.php');
$page=isset($_GET['page'])?intval($_GET['page']):1;
$length=3;
$offset=($page-1)*$length;
$sql=sprintf('select
comments.*,
posts.title as posts_title
from comments
inner join posts on comments.post_id=posts.id
order by comments.created desc
limit %d,%d;',$offset,$length);
$comments=bx_fetch_all($sql);

$total_counts=bx_fetch_one('select
 count(1) as count from comments
inner join posts on comments.post_id=posts.id')['count'];
$totalPages=ceil($total_counts/$length);

// var_dump($comments);
$json=json_encode(array(
	'comments'=>$comments,
	'totalPages'=>$totalPages
));
header('Content-Type: application/json');
echo $json;