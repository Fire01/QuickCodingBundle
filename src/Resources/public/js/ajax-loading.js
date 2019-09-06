$(document).ready(function(){
	var elal = `
	<div id="__ajaxLoading" class="uk-flex-top" uk-modal>
	    <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical uk-border-rounded">
	        <span uk-spinner="ratio: 3"></span>
	        <h4>Loading . . .</h4>
	    </div>
	</div>
	`;
	
	var domal = $("#__ajaxLoading");
	if(!domal.length){
		$('body').append(elal);
	}
	
	var __ajaxLoading = UIkit.modal($("#__ajaxLoading"), {escClose: false, bgClose : false});
	
	$(document).ajaxSend(function(){__ajaxLoading.show()});
	$(document).ajaxComplete(function(){__ajaxLoading.hide()});
});