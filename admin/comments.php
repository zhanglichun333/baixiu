<?php 
require_once '../function.php';
bx_get_current_user();
 ?>
<!DOCTYPE html>
<html lang="zh-CN" >
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
  <style>
    .box-Loading{
      /*display:none;*/
      display:flex;
      justify-content:center;
      align-items:center;
      position: fixed;
      left:0;
      top:0;
      right:0;
      bottom:0;
      background-color: rgba(0,0,0,.2);
      z-index:999;

    }
    .flip-txt-loading {
      font: 26px Monospace;
      letter-spacing: 5px;
      color: #fff;
    }

    .flip-txt-loading > span {
      animation: flip-txt  2s infinite;
      display: inline-block;
      transform-origin: 50% 50% -10px;
      transform-style: preserve-3d;
    }

    .flip-txt-loading > span:nth-child(1) {
      -webkit-animation-delay: 0.10s;
              animation-delay: 0.10s;
    }

    .flip-txt-loading > span:nth-child(2) {
      -webkit-animation-delay: 0.20s;
              animation-delay: 0.20s;
    }

    .flip-txt-loading > span:nth-child(3) {
      -webkit-animation-delay: 0.30s;
              animation-delay: 0.30s;
    }

    .flip-txt-loading > span:nth-child(4) {
      -webkit-animation-delay: 0.40s;
              animation-delay: 0.40s;
    }

    .flip-txt-loading > span:nth-child(5) {
      -webkit-animation-delay: 0.50s;
              animation-delay: 0.50s;
    }

    .flip-txt-loading > span:nth-child(6) {
      -webkit-animation-delay: 0.60s;
              animation-delay: 0.60s;
    }

    .flip-txt-loading > span:nth-child(7) {
      -webkit-animation-delay: 0.70s;
              animation-delay: 0.70s;
    }

    @keyframes flip-txt  {
      to {
        -webkit-transform: rotateX(1turn);
                transform: rotateX(1turn);
      }
    }
  
  
  </style>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right" id="pagination-demo">
          <li><a href="#" id="page-content"></a></li>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="20"><input type="checkbox"></th>
            <th>作者</th>
            <th width="600">评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="140">操作</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
  <div class="box-Loading">
    <div class="flip-txt-loading">
    <span>L</span><span>o</span><span>a</span><span>d</span><span>i</span><span>n</span><span>g</span>
</div>
  </div>
  
  <?php $current_page='comments'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  <script id="comments_templ" type="text/x-jsrender">
    {{for comments}}
       <tr {{if status=='rejected'}} class="danger" {{else status=='held'}} class="warning" {{else status=='trashed'}} class="success" {{/if}} data-id="{{:id}}">
        <td class="text-center"><input type="checkbox"></td>
        <td>{{:author}}</td>
        <td>{{:content}}</td>
        <td>{{:posts_title}}</td>
        <td>{{:created}}</td>
        <td>{{:status}}</td>
        <td class="text-center">
          {{if status=='held'}}
          <a href="post-add.html" class="btn btn-info btn-xs">批准</a>
          <a href="post-add.html" class="btn btn-warning btn-xs">拒绝</a>
          {{/if}}
          {{if status=='rejected'}}
          <a href="post-add.html" class="btn btn-info btn-xs">申请</a>
          {{/if}}
          <a href="javascript:;" class="btn btn-danger btn-xs btn-delete">删除</a>
        </td>
      </tr> 
    {{/for}}
  </script>
  <script>
    //每一次发出ajax请求
    $(document)
    .ajaxStart(function () {
      NProgress.start()
      // $('.box-Loading').css('display','block')
      $('.box-Loading').fadeIn();
      // $('body').attr('cursor','wait')
    })
    .ajaxStop(function () {
      NProgress.done()
      // $('.box-Loading').css('display','none')
      $('.box-Loading').fadeOut();
      // $('body').attr('cursor');

    })

    //发送Ajax获取列表所需要的数据，通过模板引擎把数据渲染在页面上
    var current_page=1
    function loadPageDate(page){
      $.get('/admin/api/comment.php',{page:page},function (res) {
        // if(page>res['totalPages']){
        //   loadPageDate(res['totalPages'])
        //   return
        // }
        // $('#pagination-demo').twbsPagination('destroy')
        $('#pagination-demo').twbsPagination({
          first:'&laquo',
          last:'&raquo',
          prev:'&lt',
          next:'&gt',
          startPage:page,
          totalPages: res['totalPages'],
          visiblePages: 5,
          initiateStartPageClick: false,
          onPageClick: function (event, page) {
            // console.log(page)//第一次初始化就会触发一次
            loadPageDate(page)
          }
        });
        // console.log(res)
        var html=$('#comments_templ').render({comments:res['comments']})
        console.log(html)
        $('tbody').html(html)
        current_page=page
      })
    }
    loadPageDate(current_page)

    //删除功能
    // 单行删除==注册点击事件，获取单行数据的ID，发出服务端ajax请求删除数据，根据服务端的响应是否重新加载该页面
    $('tbody').on('click','.btn-delete',function () {
      var tr=$(this).parent().parent()
      var id=tr.data('id')
      $.get('/admin/api/comments_delete.php',{id:id},function (res) {
        if(!res) return
        // console.log(res)
        // tr.remove()
        console.log(current_page)
        loadPageDate(current_page)
      })
    })


  </script>
  <script>NProgress.done()</script>
</body>
</html>
