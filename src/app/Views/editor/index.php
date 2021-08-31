<!-- CONTENT -->
<main id="content">

    <h2 class="display-6 text-center mb-4">Compare plans</h2>

    <div class="table-responsive">
		<div id="editor" class="editor">

		</div>

		<div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
			<button id="btn_save" class="btn btn-primary me-md-2" type="button"> 저장 </button>
			<button id="btn_preview" class="btn btn-primary" type="button"> 미리보기 </button>
		</div>

    </div>
</main>

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
			
			$.post("editor/save", {



			}, function(rst){

				console.log(rst);
				
			});

		});

		$content.find("#btn_preview").click(function(){
			
		});

	})($("#content"));

})(jQuery);

</script>