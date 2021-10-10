<!DOCTYPE html>
<?php
header("Content-type: text/html; charset=utf-8");
// 网站名称
$web_title = $lister->getConfig('web_title');
// 当前路径
$listed_path = $lister->getListedPath();
// 当前目录中的README文档文件内容
$readme_html = $lister->getReadme();
// 面包屑导航
$breadcrumbs = $lister->listBreadcrumbs();
?>
<html lang="zh-CN">

<head>
    <!-- 网页标题 -->
    <?php if ($listed_path != "") : ?>
        <title><?php echo $web_title . " | " . $listed_path; ?></title>
    <?php else : ?>
        <title><?php echo $web_title; ?></title>
    <?php endif; ?>

    <!-- 网站LOGO -->
    <link rel="shortcut icon" href="resources/themes/bootstrap/img/folder.png" />
    <!-- CSS基本库 -->
    <link rel="stylesheet" href="resources/themes/bootstrap/css/bootstrap.min.css" />
    <!-- 网站图标CSS式样 从版本4升级到5 https://fontawesome.com/how-to-use/on-the-web/setup/upgrading-from-version-4 -->
    <link rel="stylesheet" href="resources/themes/bootstrap/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="resources/themes/bootstrap/fontawesome/css/v4-shims.min.css">
    <!-- 网站主要式样 -->
    <link rel="stylesheet" href="resources/themes/bootstrap/css/style.css" />
    <!-- 代码高亮样式 -->
    <link rel="stylesheet" href="resources/themes/bootstrap/css/prism.css" />

    <!-- JS基本库 -->
    <script src="resources/themes/bootstrap/js/jquery.min.js"></script>
    <!-- JS基本库 -->
    <script src="resources/themes/bootstrap/js/bootstrap.min.js"></script>
    <!-- 代码高亮JS依赖 -->
    <script src="resources/themes/bootstrap/js/prism.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/markdown-it/9.1.0/markdown-it.js"></script> -->

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <?php file_exists('analytics.inc') ? include('analytics.inc') : false; ?>

    <!-- header start -->
    <?php file_exists('header.php') ? include('header.php') : include($lister->getThemePath(true) . "/default_header.php"); ?>
    <!-- header end -->
</head>

<body>
    <header class="path-announcement navbar navbar-default navbar-fixed-top">
        <div class="path-announcement2 container">
            <!-- 顶部公告栏 start -->
            <p style="color:red">
                <i class="fa fa-volume-down"></i>
                <?php file_exists('bulletin.php') ? include('bulletin.php') : include($lister->getThemePath(true) . "/default_bulletin.php"); ?>
            </p>
            <!-- 顶部公告栏 end -->
        </div>
    </header>

    <div class="page-content container">
        <!-- 面包屑导航栏 start -->
        <nav aria-label="breadcrumb" class="d-none d-md-block d-md-none">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/">
                        <i class="fa fa-home"></i>
                        <?php echo $web_title; ?>
                    </a>
                </li>
                <?php foreach ($breadcrumbs as $breadcrumb) : ?>
                    <?php /** 取第一个元素reset*/ if ($breadcrumb == reset($breadcrumbs)) : ?>

                    <?php /** 取最后一个元素end*/ elseif ($breadcrumb == end($breadcrumbs)) : ?>
                        <li class="breadcrumb-item active">
                            <?php echo $breadcrumb['text']; ?>
                        </li>
                    <?php else : ?>
                        <li class="breadcrumb-item">
                            <a href="<?php echo $breadcrumb['link']; ?>">
                                <?php echo $breadcrumb['text']; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ol>
        </nav>
        <!-- 面包屑导航栏 end -->

        <!-- 系统错误消息  -->
        <?php if ($lister->getSystemMessages()) : ?>
            <?php foreach ($lister->getSystemMessages() as $message) : ?>
                <div class="alert alert-<?php echo $message['type']; ?>">
                    <?php echo $message['text']; ?>
                    <a class="close" data-dismiss="alert" href="#">&times;</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- content -->
        <div id="directory-list-header">
            <div class="row">
                <div class="col-md-7 col-sm-6 col-xs-10">文件</div>
                <div class="col-md-2 col-sm-2 col-xs-2 text-right">大小</div>
                <div class="col-md-3 col-sm-4 hidden-xs text-right">最后修改时间</div>
            </div>
        </div>
        <ul id="directory-listing" class="nav nav-pills nav-stacked">
            <?php foreach ($dirArray as $name => $fileInfo) : ?>
                <li data-name="<?php echo $name; ?>" data-href="<?php echo $fileInfo['url_path']; ?>">
                    <a href="<?php echo $fileInfo['url_path']; ?>" class="clearfix" data-name="<?php echo $name; ?>">
                        <div class="row">
                            <span class="file-name col-md-7 col-sm-6 col-xs-9">
                                <i class="fa <?php echo $fileInfo['icon_class']; ?> fa-fw"></i>
                                <?php echo $name; ?>
                            </span>
                            <span class="file-size col-md-2 col-sm-2 col-xs-3 text-right">
                                <?php echo $fileInfo['file_size']; ?>
                            </span>
                            <span class="file-modified col-md-3 col-sm-4 hidden-xs text-right">
                                <?php echo $fileInfo['mod_time']; ?>
                            </span>
                        </div>
                    </a>
                    <?php if (is_file($fileInfo['file_path'])) : ?>
                    <?php else : ?>
                        <?php if ($lister->containsIndex($fileInfo['file_path'])) : ?>
                            <a href="<?php echo $fileInfo['file_path']; ?>" class="web-link-button" <?php if ($lister->externalLinksNewWindow()) : ?>target="_blank" <?php endif; ?>>
                                <i class="fa fa-external-link"></i>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- READMNE start -->
    <?php
    if ($readme_html != "") {
        // 多行字符串开始
        $readme_top = '
                    <div class="container readme-background" id="readmeTop">
                        <div class="Box-header px-2 clearfix">
                            <h3 class="Box-title pr-3">
                                <svg class="octicon octicon-book" viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true">
                                    <path fill-rule="evenodd" 
                                        d="M3 5h4v1H3V5zm0 3h4V7H3v1zm0 2h4V9H3v1zm11-5h-4v1h4V5zm0 2h-4v1h4V7zm0 2h-4v1h4V9zm2-6v9c0 .55-.45 1-1 1H9.5l-1 1-1-1H2c-.55 0-1-.45-1-1V3c0-.55.45-1 1-1h5.5l1 1 1-1H15c.55 0 1 .45 1 1zm-8 .5L7.5 3H2v9h6V3.5zm7-.5H9.5l-.5.5V12h6V3z">
                                    </path>
                                </svg> README
                            </h3>
                        </div>
                        <div class="readme" id="readme">
                        ';
        echo $readme_top . $readme_html . "</div></div>";
    }
    ?>
    <!-- READMNE end -->

    <!-- 留言 -->
    <!-- Valine -->
    <div class="container" id="vcomments"></div>

    <!-- 来必力 -->
    <!-- <div class="container" id="lv-container" data-id="city" data-uid="MTAyMC80NTE3MC8yMTY4OA=="></div> -->

    <!-- Gitalk -->
    <!-- <div class="container" id="gitalk-container"></div> -->

    <!-- Gitment -->
    <!-- <div class="container" id="gitment-container"></div> -->


    <!-- footer start -->
    <footer>
        <div class="footer container">
            <?php file_exists('footer.php') ? include('footer.php') : include($lister->getThemePath(true) . "/default_footer.php"); ?>
        </div>
    </footer>
    <!-- footer end -->

    <script type="text/javascript">
        // 在html全部加载完了才执行
        window.onload = function() {
            anchorPositioning();
        }
        // onresize 事件会在窗口或框架被调整大小时发生
        window.onresize = function() {
            anchorPositioning();
        }
        // onhashchange 事件在当前 URL 的锚部分(以 '#' 号为开始) 发生改变时触发 。
        window.onhashchange = function() {
            anchorPositioning();
        }

        function anchorPositioning() {
            // 获取URL中的锚点标签属性
            var target = $(decodeURIComponent(location.hash));
            // 让当前的元素滚动到浏览器窗口的可视区域内
            // document.getElementById(location.hash).scrollIntoView(true);
            // 判断锚点是否存在
            if (target.length == 1) {
                $('html,body').animate({
                    scrollTop: target.offset().top - 50
                }, 500);
            }
            // PHP赋值给js变量
            // var mdText="<?php //echo $md_text; ?>";
            // var md = window.markdownit();
            // var result = md.render(mdText);
            // $("#readme").html(result);
        }
        // 点击锚点时跳转
        $(".header-anchor").click(function() {
            // https://developer.mozilla.org/zh-CN/docs/Web/API/Element/scrollTop
            var scrollTop = window.pageYOffset || document.body.scrollTop || document.documentElement.scrollTop;
            document.body.scrollTop = document.documentElement.scrollTop = scrollTop - 50;
        });
    </script>
</body>

</html>
