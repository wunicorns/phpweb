<?php

?>

<div class="container mt-5">
    <div class="row">
        <div class="col-6">
        <button id="btn_new_papers" class="btn btn-outline-secondary" type="button"> 문서 추가 </button>
        </div>
        <div class="col-6">
        <form class="input-group">
            <div class="input-group">            
                <select id="slt_search_type" name="stype" class="form-select">
                    <option value="title" <?php echo $stype == '' || $stype == 'title' ? 'selected' : ''; ?>>문서명</option>
                    <option value="paper_id" <?php echo $stype == 'paper_id' ? 'selected' : ''; ?>>문서번호</option>
                    <option value="username" <?php echo $stype == 'username' ? 'selected' : ''; ?>>작성자</option>
                </select>

                <input type="text" class="form-control" name="svalue" aria-label="Text input with 2 dropdown buttons" value="<?= $svalue; ?>">
                <button class="btn btn-outline-secondary" type="submit" aria-expanded="false"> 검색 </button>
            </div>
        </form>
        </div>

        <div class="mt-2">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">제목</th>
                        <th scope="col">상태</th>
                        <th scope="col">작성자</th>
                        <th scope="col">작성일</th>
                        <th scope="col">기능</th>
                    </tr>
                </thead>
                <tbody>
                <?php 

                $num = $total_count - (($page - 1) * $list_size);
                $row_idx = 0;                               
                foreach ($list as $row) {
                    $numbering = $num - $row_idx++;
                    $html = "<tr id={$row['paper_id']}>"
                            . "<td scope='row'>{$numbering}</td>"
                            . "<td><a href='/papers/detail/{$row['paper_id']}'>{$row['title']}</a></td>"
                            . "<td>{$row['status']}</td>"
                            . "<td>{$row['username']}</td>"                            
                            . "<td>{$row['create_dt']}</td>"
                            . "<td>"
                            . "<button type='button' class='btn btn-primary btn-download' style='margin-right:5px;'>다운로드</button>"
                            . "<button type='button' class='btn btn-warning btn-modify' style='margin-right:5px;'>수정</button>"
                            . "<button type='button' class='btn btn-danger btn-delete'>삭제</button>"
                            . "</td>"
                            . "</tr>";
                    
                    echo $html;

                }
                ?>
                </tbody>
            </table>

            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    <?php
                    $actual_link = $_SERVER["REQUEST_URI"];
                    $current_path = preg_replace("!([&]?)page=(.[0-9&]?)!is", "", $actual_link);
                    $current_path .= strpos($current_path, '?') > -1 ? '' : '?';

                    $max_count = floor($total_count / $list_size) + ($total_count % $list_size == 0 ? 0 : 1);

                    $start_num = floor(($page - 1) / $nav_size);
                    $end_num = $start_num + $nav_size;

                    if($start_num > $nav_size) {
                    ?>
                    <li class="page-item disabled">
                        <a class="page-link" href="<?= $current_path."&page=".($start_num - $nav_size + 1) ?>" tabindex="-1" aria-disabled="true">&laquo;</a>
                    </li>
                    <?php
                    }
                    ?>                  

                    <?php 
                    $last_num = $max_count > $nav_size ? $nav_size : $max_count;
                    for($i = 1 ; $i <= $last_num ; $i++){
                        $num = $start_num + $i;    
                    ?>
                        <li class="page-item"><a class="page-link" href="<?= $current_path."&page=".$num ?>"><?= $num ?></a></li>
                    <?php
                    }
                    ?>

                    <?php
                    if($end_num < $max_count) {
                    ?>                    
                    <li class="page-item">
                        <a class="page-link" href="<?= $current_path."&page=".($end_num + 1) ?>">&raquo;</a>
                    </li>
                    <?php
                    }
                    ?>
                </ul>
            </nav>
        <div>

    <div>
</div>

<script>

(function($){

    (function($doc){
    
        $(".container").find("#btn_new_papers").click(function(){
            location.href = "/papers/write";
        });
        
        $doc.find(".btn-modify").click(function(){
            var paper_id = $(this).closest("tr").attr("id");
            location.href="/papers/modify/" + paper_id;
        });

        $doc.find(".btn-download").click(function(){
            var paper_id = $(this).closest("tr").attr("id");
            // location.href = "/papers/download/" + paper_id;
            window.open("/papers/convert/" + paper_id)
        });

        $doc.find(".btn-delete").click(function(){
            if(confirm("삭제하시겠습니까?")){

                var paper_id = $(this).closest("tr").attr("id");
                $.ajax({
                    url: '/papers/delete/' + paper_id,
                    method: 'delete'
                }).done(function(resp){
                    
                    var $json = $.parseJSON(resp);
                    var err = $json.error;

                    if(err == 1){
                        alert('문제가 발생했습니다.');
                    } else {
                        alert('삭제 되었습니다.');
                        location.href = "/papers/";
                    }
                });

            }
			
        });



    })($(".container"));

})(jQuery);

</script>