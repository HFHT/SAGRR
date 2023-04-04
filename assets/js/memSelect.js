
$(document).on('pagecreate', '#memSelect', function(){
	memNew = false;
	if (!objPageCreate.memSelect) {
		objPageCreate.memSelect = true;
		$('#DogMedical').validate();
//		$('.my-date').datepicker();
		$(document).on('vclick', '#memSelectlist a', function () {
			memNew = true;
			memSelected = this;
			objMemSelect = {memName : '',memId : '', memKey: 0};
			 
			objMemSelect.memKey = this.getAttribute('data-id');
			objMemSelect.memTeam = this.getAttribute('data-team');
			objMemSelect.memId = $(this.getElementsByClassName('memId')).text();
			objMemSelect.memName = $(this.getElementsByClassName('memName')).text();			
			console.debug(this);
//			nextPage = popPageStack();
//			console.debug(nextPage);
//			$('body').pagecontainer('change',nextPage, {transition : 'slide', reverse : true});
			$.mobile.back();
		});	
	}
});