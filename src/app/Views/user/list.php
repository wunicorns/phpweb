<?php

?>

<div class="container mt-5">
    <div class="row">
        <div class="col-6">
        <button id="btn_new" class="btn btn-outline-secondary" type="button"> 사용자 추가 </button>
        </div>
        <div class="col-6">
            <?= csrf_field() ?>
            <form class="input-group">                
                <select id="slt_search_type" name="stype" class="form-select">
                    <option value="username" <?php echo $stype == '' || $stype == 'username' ? 'selected' : ''; ?>>사용자명</option>
                    <option value="id" <?php echo $stype == 'id' ? 'selected' : ''; ?>>아이디</option>
                    <option value="email" <?php echo $stype == 'email' ? 'selected' : ''; ?>>이메일</option>
                </select>

                <input name="svalue" type="text" class="form-control" value="<?php echo $svalue; ?>" aria-label="Text input with 2 dropdown buttons" />
                <button class="btn btn-outline-secondary" type="submit" aria-expanded="false"> 검색 </button>
            </form>                
        </div>

        <div class="mt-2">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">아이디</th>
                        <th scope="col">사용자명</th>
                        <th scope="col">이메일</th>
                        <th scope="col">상태</th>
                        <th scope="col">등록일</th>
                        <th scope="col">기능</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $num = $total_count - (($page - 1) * $list_size);
                $row_idx = 0;                               
                foreach ($list as $row) {
                    $numbering = $num - $row_idx++;
                    $html = "<tr id={$row['seq']}>"
                            . "<td scope='row'>{$numbering}</td>"
                            . "<td><a class='link' href='#' >{$row['id']}</a></td>"
                            . "<td>{$row['username']}</td>"
                            . "<td>{$row['email']}</td>"
                            . "<td>{$row['status']}</td>"
                            . "<td>{$row['create_dt']}</td>"
                            . "<td>"
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

        <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">사용자 정보</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        
                        <form id="userForm" name="userForm">

                        <input type="hidden" id="input_seq" value="" />

                        <div class="mb-3 row">
                            <label for="input_username" class="col-sm-2 col-form-label"> 사용자명 </label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="input_username" placeholder="사용자명" value="">
                            </div>                            
                        </div>

                        <div class="mb-3 row update_user">
                            <label for="input_id_update" class="col-sm-2 col-form-label"> 아이디 </label>
                            <div class="col-sm-10">
                                <input type="text" readonly class="form-control-plaintext" id="input_id_update" value="">
                            </div>                            
                        </div>

                        <div class="mb-3 row create_user">
                            <label for="input_id_create" class="col-sm-2 col-form-label">아이디</label>
                            <div class="col-auto">
                                <input type="text" class="form-control" id="input_id_create" placeholder="아이디">
                            </div>
                            <div class="col-sm-4">
                                <button type="button" class="btn btn-primary" id="btn_duplicate">중복확인</button>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="inputPassword" class="col-sm-2 col-form-label">비밀번호</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" id="inputPassword" value="">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="inputPasswordConfirm" class="col-sm-2 col-form-label">비밀번호 확인</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" id="inputPasswordConfirm" value="">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="input_email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="input_email" placeholder="Email">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="slt_status" class="col-sm-2 col-form-label"> 상태 </label>
                            <div class="col-sm-10">
                                <select class="form-select" aria-label="Default select example" id="slt_status">
                                    <option value="1"> 사용 </option>
                                    <option value="2"> 미사용 </option>
                                </select>
                            </div>
                        </div>
                        
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button id="btn_cancel" type="button" class="btn btn-secondary" data-bs-dismiss="modal"> 취소 </button>
                        <button id="do_save" type="button" class="btn btn-success"> 저장 </button>
                    </div>
                </div>
            </div>
        </div>
    <div>
<script>

(function($){

    var userModalEl = document.getElementById('userModal');

    var userObject = {};
    var idChecked = false;
    var pwChecked = false;

    userModalEl.addEventListener('show.bs.modal', function (event) {

    });

    userModalEl.addEventListener('shown.bs.modal', function (event) {

    });

    userModalEl.addEventListener('hide.bs.modal', function (event) {

    });

    userModalEl.addEventListener('hidden.bs.modal', function (event) {
        $(".update_user").show();
        $(".create_user").show();

        $("#input_seq").val("");
        $("#input_username").val("");
        $("#inputPassword").val("");
        $("#inputPasswordConfirm").val("");        
        $("#input_id_update").val("");
        $("#input_email").val("");
        $("#slt_status").val(1);
    });    


    $("#btn_new").click(function(){

        idChecked = false;
        pwChecked = false;

        var userModal = new bootstrap.Modal(document.getElementById('userModal'), {
            keyboard: false
        });

        $(".update_user").hide();

        userModal.show();

    });

    $(".btn-modify").click(function(){

        idChecked = true;
        pwChecked = true;

        var userModal = new bootstrap.Modal(document.getElementById('userModal'), {
            keyboard: false
        });

        var seq = $(this).closest('tr').attr("id");

        $.get('/user/detail/' + seq, function(resp){

            $(".create_user").hide();

            var $json = $.parseJSON(resp);
            var err = $json.error;
            var userData = $json.result;

            $("#input_seq").val(userData.seq);
            $("#input_username").val(userData.username);
            $("#input_id_update").val(userData.id);
            $("#input_email").val(userData.email);

            $("#slt_status").val(userData.status);            

            userModal.show();

        });

    });

    $(".btn-delete").click(function(){
        if(confirm("삭제하시겠습니까?")){
            var seq = $(this).closest('tr').attr("id");

            $.post('/user/delete', {
                seq: seq,
                csrf_test_name: $("input[name='csrf_test_name']").val()
            }, function(resp){
                
                var $json = $.parseJSON(resp);
                var err = $json.error;

                if(err == 0){
                    alert("삭제되었습니다.");
                } else {
                    alert("삭제시 문제가 발생하였습니다.");
                }
                location.href=location.href;
                
            });
        }

    });



    $("#do_save").click(function(){

        if(!pwChecked) {
            $("#inputPassword").focus();
            return;
        }

        if(!idChecked) {
            $("#input_id_create").focus();
            return;
        }


        var inputSeq = $("#input_seq").val();
        var inputId = inputSeq ? $("#input_id_update").val() : $("#input_id_create").val();

        var userData = {
            seq: inputSeq,
            username: $("#input_username").val(),
            id: inputId,
            email: $("#input_email").val(),
            status: $("#slt_status").val(),
            password: $("#inputPassword").val(),
            password_confirm: $("#inputPasswordConfirm").val(),            
            csrf_test_name: $("input[name='csrf_test_name']").val()
        } 

        $.post('/user/save', userData, function(resp){

            var $json = $.parseJSON(resp);
            var err = $json.error;

            if(err == 0){
                alert("저장되었습니다.");
            } else {
                alert("저장시 문제가 발생하였습니다.");
            }
            location.href=location.href;

        });

    });

    $("#btn_duplicate").click(function(){

        var id = $("#input_id_create").val().trim();

        if(!id) {
            alert("아이디를 확인해주세요.");
            $("#input_id_create").focus();
            return;
        }

        $.get('/user/idcheck', {
            id: id
        }, function(resp){

            var $json = $.parseJSON(resp);
            var err = $json.error;

            if(err == 1){
                alert('이미 사용중인 아이디 입니다.');
                $("#input_id_create").val("");
                idChecked = false;
            } else {
                alert('사용 할 수 있는 아이디 입니다.');
                idChecked = true;
            }

        });

    });

    $("#inputPasswordConfirm").keyup(function(){
        var value = $("#inputPassword").val();

        if(value == this.value){
            pwChecked = true;
        } else {
            pwChecked = false;
        }
    });

})(jQuery);

</script>
</div>