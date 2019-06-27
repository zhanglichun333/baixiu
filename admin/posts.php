<?php 
require_once '../function.php';
bx_get_current_user();

//接受刷选参数
//===========================================
$where='1=1';
$search='';
if(isset($_GET['category']) && $_GET['category']!=='-1'){
  $where.=' and posts.category_id='.$_GET['category'];
  $search.='&category='.$_GET['category'];
}
if(isset($_GET['status']) && $_GET['status']!=='-1'){
  $where.=" and posts.status='{$_GET['status']}'";
  $search.='&status='.$_GET['status'];
}

//处理分页参数
//===========================================
//1<$page<$max_page
$page=isset($_GET['page'])?(int)$_GET['page']:1;
$size=20;
// $page=$page<1?1:$page;
if($page<1){
  header('location: posts.php?page=1'.$search);
}
$total_rows=(int)bx_fetch_one("select count(1) as num from posts
inner join users on posts.user_id=users.id
inner join categories on posts.category_id=categories.id
where {$where};")['num'];
$max_page=(int)ceil($total_rows/$size);
// $page=$page>$max_page?$max_page:$page;
if($page>$max_page){
  header('location: posts.php?page='.$max_page.$search);
}

//获取全部数据
//============================================
$offset=($page-1)*$size;
$posts=bx_fetch_all("select
posts.id,
posts.title,
categories.name as category_name,
users.nickname as user_name,
posts.created,
posts.status
from posts
inner join users on posts.user_id=users.id
inner join categories on posts.category_id=categories.id
where {$where}
order by posts.created desc
limit {$offset}, {$size}");
// var_dump($posts);

//获取分类和用户数据
$categories=bx_fetch_all('select * from categories;');

//数据类型转换
//============================================
/**
 * 转换状态显示
 * @param  string $status 英文状态
 * @return string         中文状态
 */
function convert_status($status){
  // switch ($status) {
  //   case 'published':
  //     return '已发布';
  //     break;
  //   case 'drafted':
  //     return '草稿';
  //     break;
  //   case 'trashed':
  //     return '回收站';
  //     break;
  // }
  $dict=array(
    'published'=>'已发布',
    'drafted'=>'草稿',
    'trashed'=>'回收站'
  );
  return isset($dict[$status])?$dict[$status]:'未知';
} 
function convert_date($created){
$timestamp=strtotime($created);
return date('Y年m月d日<b\r>H:i:s',$timestamp);
}


// 处理分类页码==从1页到最大的页码数=$total_rows/$size
// ========================================
$visiables=5;
$begin=$page-($visiables-1)/2;
$end=$begin+$visiables;
//$begin>1
if($begin<1){
$begin=1;
$end=$begin+$visiables;
}
//$end<最大页码数
if($end>$max_page+1){
$end=$max_page+1;
$begin=$end-$visiables;
if($begin<1){
  $begin=1;
}
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有文章</h1>
        <a href="post-add.html" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF'] ?>">
          <select name="category" class="form-control input-sm">
            <option value="-1">所有分类</option>
            <?php foreach ($categories as $item): ?>
              <option value="<?php echo $item['id']; ?>"<?php echo isset($_GET['category']) && $_GET['category']===$item['id']?'selected':''; ?>><?php echo $item['name'] ?></option>
            <?php endforeach ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="-1">所有状态</option>
            <option value="drafted" <?php echo isset($_GET['status']) && $_GET['status']==='drafted'?'selected':''; ?>>草稿</option>
            <option value="published" <?php echo isset($_GET['status']) && $_GET['status']==='published'?'selected':''; ?>>已发布</option>
            <option value="trashed" <?php echo isset($_GET['status']) && $_GET['status']==='trashed'?'selected':''; ?>>回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <li><a href="#">上一页</a></li>
            <?php for($i=$begin;$i<$end;$i++): ?>
              <li class="<?php echo $page===$i?'active':''; ?>"><a href="posts.php?page=<?php echo $i.$search; ?>"><?php echo $i; ?></a></li>
            <?php endfor ?>
          <li><a href="#">下一页</a></li>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $item): ?>
          <tr>
            <td class="text-center"><input type="checkbox"></td>
            <td><?php echo $item['title']; ?></td>
            <td><?php echo $item['user_name']; ?></td>
            <td><?php echo $item['category_name']; ?></td>
            <td class="text-center"><?php echo convert_date($item['created']); ?></td>
            <td class="text-center"><?php echo convert_status($item['status']); ?></td>
            <td class="text-center">
              <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
              <a href="posts_delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>
  
  <?php $current_page='posts'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
