var objPageCreate = {};

$(document).on('pagecreate', '#resumePage', function(){
	if (!objPageCreate.resume) {
		objPageCreate.resume = true;
		var hrefPg1 = $('#applPg1').attr('href');
		var hrefNew = hrefTgt = hrefPg1;
		$(document).on('change', '#resumeId', function () {
			if (aryApplList.indexOf($('#resumeId').val())<0) {											// See if Application ID exists
				$('#resumeErrorText').text($('#resumeId').val());
				$('#resumeError').popup('open');				
			} else {
				$('#applPg1').hrefOpts({x:$('#resumeId').val()});
			}
		});		
		$(document).on('change', '#resume', function () {
			$('#hideresume').toggle();																	// toggle hidden field display attribute
			$('#applPg1').hrefOpts({x:''});																// clear out href past the equal sign		
			if ($('#resume').val()=='Y') {																// check if resume is selected
				$('#applPg1').hrefOpts({x:$('#resumeId').val()});										// append the input field to href url
			}
		});					
	}
});
$(document).on('pagecreate', '#memberPage', function(){
	if (!objPageCreate.member) {
		objPageCreate.member = true;
		var hrefPg1 = $('#applPg1').attr('href');
		var hrefNew = hrefTgt = hrefPg1;
		$(document).on('change', '#Member_id', function () {
			if (aryMemList.indexOf($('#Member_id').val())>0) {
				console.debug('found match');
			} else {
				console.debug('no match');
			}
			$('#applPg1').hrefOpts({m:$('#Member_id').val()});											// clear href then append input field
		});		
		$(document).on('change', '#member', function () {
			$('#hidemem').toggle();																		// toggle hidden field display attribute
			$('#applPg1').hrefOpts({m:''});
			if ($('#member').val()=='Y') {																// check if member is selected
				$('#applPg1').hrefOpts({m:$('#Member_id').val()});										// append the input field to href url
			}
		});					
	}
});

(function ( $ ) {
	$.fn.hrefOpts = function (options) {																// jQuery extension, adjusts querystring	
		var params = {}, aryqry, temp, i, l, url, urllink, urlqrystr;									// options -> {key:value,key:value}
		url = this[0].href;
		urllink = url.substring (0,url.indexOf('?')); 
		urlqrystr = url.substring( url.indexOf('?') + 1 );
		aryqry = urlqrystr.split('&');
		for ( i = 0, l = aryqry.length; i < l; i++ ) {													// parse the url querystring into param obj
				temp = aryqry[i].split('=');
				params[temp[0]] = temp[1];
		}
		$.each(options, function(index,value) {															// substitute values from passed options
			params[index] = value;
		});
		aryqry = [];
		$.each(params, function(index,value) {															// re-assemble the querystring
			aryqry.push(index+'='+value);
		});
		urlqrystr = aryqry.join('&');
		this[0].href = urllink+'?'+urlqrystr;
		return (this);	
	};
}( jQuery));

var objSec1 = {"Household":{"Members":[{"Name":"","Age":0}],"Pets":[{"Type":"","Gender":"","Age":0,"Altered":"","Current":""}],"Questions":[{"Q":"Is anyone in your family allergic to dogs or cats?","A":["Yes","No","Unknown"],"R":""},{"Q":"Are you aware Golden's shed?","A":["Yes","No","Wasn't Aware"],"R":""},{"Q":"What other animals do you have?","R":""}]}};