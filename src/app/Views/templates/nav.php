<?php

$current_uri = uri_string();

//echo uniqid();

?>
<style>

header a.nav-link { cursor:pointer; }

</style>

<header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom" style="padding-right: 100px;padding-left: 100px;">
    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
        <svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"></use></svg>
        <span class="fs-4 site_name">Simple header</span>        
    </a>

    <ul class="nav nav-pills">
        <li class="nav-item"><a class="link_user nav-link <?php echo (strpos($current_uri, 'user') !== false ? "active": ""); ?>" aria-current="page"> 사용자관리 </a></li>
        <li class="nav-item"><a class="link_papers nav-link <?php echo (strpos($current_uri, 'papers') !== false ? "active": ""); ?>"> 문서관리 </a></li>
    </ul>
    <div class="vr" style="margin:0px 0px 0px 15px;"> </div>
    <a class="link_papers nav-link" href="/user/logout"> 로그아웃 </a>
</header>

<script>

(function($){
    var link = location.href;
    $("header").find(".link_user").click(function(){
        location.href = link.substring(0, link.indexOf("papers/")) + "user/list";
    });

    $("header").find(".link_papers").click(function(){
        location.href = link.substring(0, link.indexOf("user/")) + "papers/list";
    });

})(jQuery);

</script>