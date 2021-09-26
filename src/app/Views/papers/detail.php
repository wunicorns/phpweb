<div class="container mt-5">
	<main id="content" class="row" data="<?= $papers['paper_id'] ?>">

		<h2 class="display-6 text-center mb-4">문서 작성</h2>

		<div class="table-responsive">
			
			<div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
				<button id="btn_delete" class="btn btn-danger me-md-2" type="button"> 삭제 </button>
				<button id="btn_download" class="btn btn-success" type="button"> 다운로드 </button>
				<button id="btn_cancel" class="btn btn-primary" type="button"> 목록 </button>
			</div>

			<div class="mb-3">
				<label for="inputTitle" class="form-label">문서 제목</label>				
				<div id="inputTitle"><?= $papers['title']; ?></div>
			</div>

			<div class="row">
			<?php
			$numbering = 1;
			foreach($sheets as $sheet){
				$html = "<div class='mb-3'>"
						. "<label for='inputContent' class='form-label'>문서 내용 {$numbering}</label>"
						. "<div id='inputContent'>{$sheet['content']}</div>"
						. "</div>";

				echo $html;

				$numbering++;
			}
			?>
			</div>
		</div>
	</main>
</div>
<script>

(function($){
	const paperId = "<?= $papers['paper_id'] ?>";
	(function($content){

		$content.find("#btn_download").click(function(){
			window.open('/papers/convert/' + paperId);
		});

        $content.find(".btn_delete").click(function(){
            if(confirm("삭제하시겠습니까?")){

                var paper_id = $content.attr('data');
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

		$content.find("#btn_cancel").click(function(){
			history.back();
		});

	})($("#content"));

})(jQuery);

</script>