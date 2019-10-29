$(document).ready(function(){
	var elal = `
	<div id="__ajaxLoading" class="uk-flex-top" uk-modal>
	    <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical uk-border-rounded">
	        <span uk-spinner="ratio: 2"></span>
	        <p>Loading . . .</p>
	    </div>
	</div>
	`;
	
	var domal = $("#__ajaxLoading");
	if(!domal.length){
		$('body').append(elal);
	}
	
	__ajaxLoading = UIkit.modal($("#__ajaxLoading"), {escClose: false, bgClose : false});
	/*
	$(document).ajaxSend(function(){__ajaxLoading.show()});
	$(document).ajaxComplete(function(){__ajaxLoading.hide()});
	*/
});