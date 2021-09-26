<style>
	.ck.ck-editor {
		width:100%;
	}
	.page-link {
		cursor:pointer;
	}
</style>
<div class="container mt-5">
	
	<main id="content" class="row">

		<h2 class="display-6 text-center mb-4"> 문서 작성</h2>

		<div class="table-responsive">
			<form method="post" id="frm" name="frm">
				<?= csrf_field() ?>
				<input type="hidden" id="paper_id" name="paper_id" value="<?= $papers['paper_id']; ?>" />
				<input type="hidden" id="status" name="status" value="<?= $papers['status'] ?? 1; ?>" />
				
				<div class="mb-3">
					<label for="inputTitle" class="form-label">문서 제목</label>
					<input type="text" class="form-control" id="inputTitle" placeholder="" value="<?= $papers['title']; ?>">
				</div>

				<div class="mb-3 row">

					<div class="col-auto">
						<button id="btn_add_page" type="button" class="btn btn-primary"> 페이지 추가 </button>
					</div>
					<div class="col-auto">
						<nav>
							<ul class="pagination pagination-sm">
							<?php
							if(isset($sheets) && count($sheets)> 0){
								$num = 1;							
								foreach($sheets as $sheet) {
								?>
									<li id="nav_<?= $num?>" class="page-item"><a idx="<?= $num?>" class="page-link"><?= $num?></a></li>
								<?php
									$num++;
								}
							}
							?>
							</ul>
						</nav>
					</div>

				</div>

				<div id="editor_area" class="mb-3">
					<label class="form-label">문서 내용</label>
					<?php
					if(isset($sheets) && count($sheets)> 0){
						$num = 1;
						foreach($sheets as $sheet) {
					?>
					<div id="row_<?= $num ?>" class="row" style="display:none;">
						<div class="editor"><?= $sheet['content'] ?></div>
					</div>
					<?php
						$num++;
						}
					}
					?>
				</div>
				
				<div class="row">
					<div class="col-sm-4"><button id="btn_delete" class="btn btn-danger" type="button"> 삭제 </button></div>
					<div class="col-sm-8" style="text-align:right;">
						<button id="btn_save" class="btn btn-primary me-md-2" type="button"> 저장 </button>
						<!-- <button id="btn_preview" class="btn btn-primary" type="button"> 미리보기 </button> -->
						<button id="btn_cancel" class="btn btn-primary" type="button"> 목록 </button>
					</div>
				</div>
				
			</form>
		</div>
	</main>
</div>

<script id="row_template" type="text/template">
<div id="row_{{idx}}" class="row" style="display:none;">
	<div class="editor"></div>
</div>
</script>

<script>
(function($){

	(function($content){

		window.editors = [];

		$content.find("#btn_save").click(function(){
			
			var paper_id = $("#paper_id").val();

			$content.find("#editor_area > div").each(function(i, el){
				// $(el).
			});

			var sheets = window.editors.map(function(el, i) { 
				return {
					ordering: i + 1,
					content: el.getData()
				}; 
			});

			$.post("/papers/save", {

				paper_id: paper_id,
				title: $("#inputTitle").val(),
				status: $("#status").val(),
				sheets: sheets,
				csrf_test_name: $("input[name='csrf_test_name']").val()

			}, function(resp){
				
				var $json = $.parseJSON(resp);
				var err = $json.error;

				if(err == 1){
					alert('문제가 발생했습니다.');
				} else {
					alert('저장 되었습니다.');
					location.href = "/papers/";
				}
				
			});

		});

		$content.find("#btn_preview").click(function(){
			var paper_id = $content.find("#paper_id").val();
			window.open('/papers/convert/' + paper_id);
		});

		$content.find("#btn_cancel").click(function(){
			location.href = "/papers/";
		});

		$content.on('row:remove', function(event, idx){
			
			delete window.editors[idx - 1];

			window.editors = window.editors.filter(function(el) { return !!el; });

			$content.find('ul.pagination').find("#nav_" + idx).remove();
			$content.find('#editor_area').find("#row_" + idx).remove();

			$content.find('ul.pagination li').each(function(i, el){
				$(el).attr('id', 'nav_'+(i+1))
				.find("a")
				.attr('idx', i+1)
				.text(i+1);
			});

			$content.find('#editor_area > div').each(function(i, el){
				$(el).attr('id', 'row_'+(i+1))
			});

			if(idx - 1 < 1){
				$content.trigger('row:active', [1]);
			} else {
				$content.trigger('row:active', [idx - 1]);
			}

		});

		function createEditor(el){
			ClassicEditor
				.create( el, { } )
				.then( editor => {
					window.editors.push(editor);
				} )
				.catch( err => {
					console.error( err.stack );
				} );
		}

		$content.on('row:add', function(event, idx){

			$content.find('ul.pagination').append('<li id="nav_'+idx+'" class="page-item"><a idx="'+idx+'" class="page-link">' + idx + '</a></li>');

			var html = $("#row_template").html().replaceAll('{{idx}}', idx);

			$content.find("#editor_area").append(html);
			
			createEditor(document.querySelector( '#row_' + idx + ' .editor' ));

		});

		$content.on('row:active', function(event, idx){
			$content.find("#editor_area > div").hide();
			$content.find('ul.pagination li').removeClass('active');

			$content.find('ul.pagination').find("#nav_" + idx).addClass('active');
			$content.find("#row_" + idx).show();
		});

		$content.delegate('ul.pagination .page-link', 'click', function(){
			
			var idx = parseInt($(this).attr("idx"));

			$content.trigger('row:active', [idx]);

		});

		
		$content.find("#btn_delete").click(function(){
			var idx = $content.find('ul.pagination li.active .page-link').attr("idx");
			$content.trigger('row:remove', [idx]);
		});

		$content.find("#btn_add_page").click(function(){

			var idx = $content.find('ul.pagination li').length + 1;

			$content.trigger('row:add', [idx])

		});

		if($content.find('ul.pagination li').length < 1){
			$content.trigger('row:add', [1]);			
		} else {
			$content.find("#editor_area > div").each(function(i, el){			
				createEditor( $(el).find("div").get(0));
			});
		}

		$content.trigger('row:active', [1]);

	})($("#content"));

})(jQuery);

</script>