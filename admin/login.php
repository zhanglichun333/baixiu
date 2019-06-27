<?php 
require_once '../config.php';
session_start();
function login(){
  // 1.表单接受和校验
  if(empty($_POST['email']) || empty($_POST['password'])){
    $GLOBALS['message'] = '请输入邮箱和密码';
    return;
  }
  // 数据校验并持久化
  $conn=mysqli_connect(bx_DB_HOST,bx_DB_USER,bx_DB_PASSWORD,bx_DB_NAME);
  if(!$conn){
    exit('连接数据库失败');
  }
  $query=mysqli_query($conn,"select * from users where email='{$_POST['email']}' limit 1;");
  if(!$query){
    $GLOBALS['message'] = '登录失败';
    return;
  }
  $user=mysqli_fetch_assoc($query);
  // var_dump($user);
  if(!$user){
    $GLOBALS['message'] = '请输入正确的邮箱';
    return;
  }
  if($user['password']!==$_POST['password']){
    $GLOBALS['message'] = '请输入正确的密码';
    return;
  }
  // 3.响应
  $_SESSION['current_login_user']=$user;
  header('location: /admin/');
}
if($_SERVER['REQUEST_METHOD']==='POST'){
  login();
}
if($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['action']) && $_GET['action']==='logout'){
  unset($_SESSION['current_login_user']);
}

 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <link rel="stylesheet" href="/static/assets/vendors/animate/animate.css">
</head>
<body>
  <div class="login">
    <form class="login-wrap <?php echo isset($message)?'shake animated':''; ?>" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" novalidate autocomplete="off">
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
        <div class="alert alert-danger">
          <strong>错误！</strong><?php echo $message; ?>
        </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus value="<?php echo isset($_POST['email'])?$_POST['email']:'' ?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block" href="index.html">登 录</button>
    </form>
  </div>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script>
    $(function($) {
      //用户输入邮箱后，页面中显示邮箱对应图像
      // 时机：邮箱文本框失去焦点，能够拿到文本框里的邮箱
      // 事件：获取邮箱相对应的头像地址，展示在img元素
      $('#email').on('blur', function() {
        var value = $(this).val()
        var emailFormat = /^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/
        // 忽略文本框为空或不是一个邮箱
        if (!value || !emailFormat.test(value)) return
        // console.log(value)
        //输入一个合理的邮箱==>JS无法操作数据库，只能通过Ajax向服务端发出请求，得到接口，获取到相对应的头像地址
        $.get('/admin/api/avatar.php', {
          email: value
        }, function(res) {
          //res==>头像地址
          if (!res) return
          //展示到上面img元素
          // console.log(res)
          $('img').fadeOut(function() {
            $(this).on('load', function() {
              $(this).fadeIn()
            }).attr('src', res)
          })
        })

      })
    })
    
  </script>
</body>
</html>
