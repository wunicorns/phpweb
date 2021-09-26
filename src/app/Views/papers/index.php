<div class="container mt-5">
	<main id="content">

		<h2 class="display-6 text-center mb-4">문서 </h2>

		<div class="table-responsive">

			


			<div id="editor" class="editor">

			</div>

			<div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
				<button id="btn_save" class="btn btn-primary me-md-2" type="button"> 저장 </button>
				<button id="btn_preview" class="btn btn-primary" type="button"> 미리보기 </button>
				<button id="btn_cancel" class="btn btn-danger" type="button"> 취소 </button>
			</div>

		</div>
	</main>
</div>
<script>

(function($){

	ClassicEditor
		.create( document.querySelector( '#editor' ), {
			// toolbar: [ 'heading', '|', 'bold', 'italic', 'link' ]
		} )
		.then( editor => {
			window.editor = editor;
		} )
		.catch( err => {
			console.error( err.stack );
		} );


	(function($content){

		$content.find("#btn_save").click(function(){
			
			$.post("papers/save", {



			}, function(rst){

				console.log(rst);
				
			});

		});

		$content.find("#btn_preview").click(function(){
			
		});

		$content.find("#btn_cancel").click(function(){
			location.href = location.href.replace("papers/", "papers/list");
		});

	})($("#content"));

})(jQuery);

</script>