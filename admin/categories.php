<?php 
require_once '../function.php';
bx_get_current_user();

// 添加
function add_categroy(){
  //校验
  if(empty($_POST['name']) || empty($_POST['slug'])){
    $GLOBALS['success'] = false;
    $GLOBALS['message'] = '请输入名称和别名';
    return;
  }
  //接受并保存
  $name=$_POST['name'];
  $slug=$_POST['slug'];
  $row=bx_execute("insert into categories values (null,'{$slug}','{$name}');");
  $GLOBALS['success'] = $row>0;
  $GLOBALS['message'] = $row<=0?'添加失败':'添加成功';
}
// 修改
function edit_categroy(){
  global $current_edit_category;
  $id=$_GET['id'];
  $name=isset($_POST['name'])?$_POST['name']:$current_edit_category['name'];
  $current_edit_category['name']=$name;
  $slug=isset($_POST['slug'])?$_POST['slug']:$current_edit_category['slug'];
  $current_edit_category['slug']=$slug;
  $rows=bx_execute("update categories set slug='{$slug}', name='{$name}' where id=".$_GET[id]);
}

if(empty($_GET['id'])){
  if($_SERVER['REQUEST_METHOD']==='POST'){
  add_categroy();
  }
}else{
  $current_edit_category=bx_fetch_one('select * from categories where id='.$_GET['id']);
  if($_SERVER['REQUEST_METHOD']==='POST'){
    edit_categroy();
  }
}
// 查询全部的分类数据
$categories=bx_fetch_all("select * from categories;");
// var_dump($categories);
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
  <style>
    #alink{
      display: none;
    }
  </style>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
        <?php if ($success): ?>
          <div class="alert alert-success">
            <strong>成功！</strong><?php echo $message ?>
          </div>
        <?php else: ?>
          <div class="alert alert-danger">
            <strong>错误！</strong><?php echo $message ?>
          </div>
        <?php endif ?>
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">
          <?php if (isset($current_edit_category)): ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $current_edit_category['id'] ?>" method="post" autocomplete="off">
              <h2>编辑《<?php echo $current_edit_category['name'] ?>》</h2>
              <div class="form-group">
                <label for="name">名称</label>
                <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" value="<?php echo $current_edit_category['name'] ?>">
              </div>
              <div class="form-group">
                <label for="slug">别名</label>
                <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $current_edit_category['slug'] ?>">
                <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
              </div>
              <div class="form-group">
                <button class="btn btn-primary" type="submit">保存</button>
              </div>
            </form>
          <?php else: ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">
              <h2>添加新分类目录</h2>
              <div class="form-group">
                <label for="name">名称</label>
                <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
              </div>
              <div class="form-group">
                <label for="slug">别名</label>
                <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
                <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
              </div>
              <div class="form-group">
                <button class="btn btn-primary" type="submit">添加</button>
              </div>
            </form>
          <?php endif ?>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" href="javascript:;" id="alink">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $item): ?>
                <tr>
                <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id'] ?>" id="btn_Delete"></td>
                <td><?php echo $item['name'] ?></td>
                <td><?php echo $item['slug'] ?></td>
                <td class="text-center">
                  <a href="categories.php?id=<?php echo $item['id'] ?>" class="btn btn-info btn-xs">编辑</a>
                  <a href="categories_delete.php?id=<?php echo $item['id'] ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page='categories'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script>
    $(function ($) {
      //给每个input注册change事件，在表格中任意一个CheckBox被选中，存入数组，根据数组的长度来判断批量删除按钮的显示，根据id值向服务器发出请求，删除数据库的相关id的行
      var tbodyCheckboxs=$('tbody input')
      var btnDelete=$('#alink')
      var allCheckeds=[]
      tbodyCheckboxs.on('change',function () {
        // this.data['id']/this.data.id js
        // console.log($(this).attr('data-id')) 字符串类型
        // console.log($(this).data('id')) 数字类型
        var id=$(this).data('id')
        if($(this).prop('checked')){
          // allCheckeds.indexOf(id)===-1 || allCheckeds.push(id)
          allCheckeds.includes(id) || allCheckeds.push(id)
        }else{
          allCheckeds.splice(allCheckeds.indexOf(id),1)
        }
        // console.log(allCheckeds)
        // console.log(allCheckeds.length)
        allCheckeds.length?btnDelete.fadeIn():btnDelete.fadeOut();
        btnDelete.attr('href','/admin/categories_delete.php?id='+allCheckeds)
        //字符串可以跟数组拼接，数组会自动转为字符串.toString
      })
      $('thead input').on('change',function () {
        var checked=$(this).prop('checked')
        tbodyCheckboxs.prop('checked',checked).trigger('change')
      })

      
      //给每个input注册change事件，遍历，在表格中任意一个CheckBox被选中，批量删除按钮结显示，根据id值向服务器发出请求，删除数据库的相关id的行
      // var tbodyCheckboxs=$('tbody input')
      // var btnDelete=$('#alink')
      // tbodyCheckboxs.on('change',function () {
      //   var flag=false
      //   tbodyCheckboxs.each(function (i,item) {
      //     // item返回的是DOM对象
      //     // console.log(item) 
      //     if($(item).prop('checked')){
      //       flag=true
      //     }
      //   })
      //   // for(var i=0;i<tbodyCheckboxs.length;i++){
      //   //   // console.log(tbodyCheckboxs[i])
      //   //   if($(tbodyCheckboxs[i]).prop('checked')){
      //   //     flag=true
      //   //   }
      //   // }
      //   flag?btnDelete.fadeIn():btnDelete.fadeOut();
      // })

// attr访问的是元素属性，prop访问的是元素对应的DOM对象的属性
    })
  </script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
