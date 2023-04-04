$(document).on('pagecreate', '#wishList', function(){
	if (!objPageCreate.wishList) {
		objPageCreate.wishList = true;
		$(document).on('vclick', '#addWish', function () {
			console.debug('addWish');
			$.ajax({
				url: '/assets/code/addWish.php',										/* Save the dog information */
				type: 'POST',
				data: {AddNew : 'y', user: objUser.userid},
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						alert('Request to add wish item failed, try again later');				
					} else {
						$('#wishlist').prepend(htmlWishAdd.replace(/{w_id}/g,result.data).replace(/{w_by}/g,objUser.userid).replace(/{w_date}/g,dateForm)).listview("refresh");						
					}
				},
				error: function (request,error) {
					alert('Network error');
				}
			});
		});	
	}
});
$(document).on('pagecreate', '#wishInfo', function(){
	formChange = false; 	
	$('#wishEditForm').validate({
		rules: {
			w_desc: {required: true, minlength: 4},
			w_sev: {selectcheck: true}			
		},
		messages: {
			w_desc: {required: "Please provide Description", minlength: "Please enter valid Description"},
			w_sev: {selectcheck: "Please select priority"}						
		}
	});
	$('.forms').change(function() {
		formChange = true;
	});
	if (objUser.userid!='azhoffs') {
		$('#w_statusBtn input:radio').attr('disabled',true);
		$('#w_ans').attr('disabled',true);
	}
	if (!objPageCreate.wishInfo) {
		objPageCreate.wishInfo = true;
		$(document).on('vclick', '#wishSaveBtn', function () {
			if (!$('#wishEditForm').valid()) {
				return;
			}
			$.ajax({
				url: '/assets/code/saveWish.php',											/* Save the bug information */
				type: 'POST',
				data: $('#wishEditForm').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						$('#wishErrorText').text(result.errorDetail.error_msg);
						$('#wishError').popup('open', {x: 0, y:0});					
					} else {
						formChange = false;
						$('#wishSuccess').popup('open', {x: 0, y:0});
						listDash();
					}
				},
				error: function (request,error) {
					$('#wishErrorText').text('Network error');
					$('#wishError').popup('open', {x: 0, y:0});
				}
			});		
		});
	}
});


$(document).on('pagecreate', '#user', function(){
	formChange = false; 	
	$('#userEdit').validate({
		rules: {
			LockedReason: {required: '#Locked:checked', minlength: 4}
		},
		messages: {
			LockedReason: {required: "Please provide reason", minlength: "Please enter valid reason"}
		}
	});
	$('.forms').change(function() {
		formChange = true;
	});
	$('#Locked').bind('change', function() {
		if ($(this).is(':checked')) {
			$('.lockdateText').text(dateForm);
			$('#LockedDate').val(dateForm);
			$('.LockHide').show();
		} else {
			$('.LockHide').hide();
			$('#LockedReason').val('');
			$('#LockedDate').val('');			
			$('.lockdateText').text('');
		}
	});
	$('#user').on("pagebeforehide", function ( event, ui ) {						/* This will be deprecated in 1.6.0 */
		if (ui.nextPage[0].id == 'memSelect') {												/* Hard-coded page name 				*/
			arySavedForm = $('#userEdit').serializeArray();
		} else {
			arySavedForm = [];
		}
	});
	if (arySavedForm[0] != null) {															/* Has form data been saved previously? */
		console.debug('form data is present');
		$.each(arySavedForm, function (key, value) {										/* Restore the form with saved data		*/
			var sel = '#'+value.name;
			if (value.value=='Y') {
				$(sel).attr('checked',true);												// if it was a saved checkbox, then set it to checked
			} else {
				$(sel).val(value.value);													// 
				$(sel).val([value.value]);
			}
		});
		$('input[type="checkbox"]').flipswitch("refresh");									/* Refresh the JQM flipswitch inputs */
		arySavedForm = [];																	/* Clear out saved form data			*/
	}
	if (memNew) {																			/* Check to see if memSelect set responsible member */
		memNew = false;
		$('#userEdit #fk_PeopleT_id').val(objMemSelect.memKey);
		$('#userEdit #memSelected').text(objMemSelect.memName);
	}	
	if (!objPageCreate.user) {
		objPageCreate.user = true;
		$(document).on('vclick', '#userSaveBtn', function () {
			if (!$('#userEdit').valid()) {
				return;
			}
			$.ajax({
				url: '/assets/code/saveUser.php',											/* Save the vet information */
				type: 'POST',
				data: $('#userEdit').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						$('#userErrorText').text(result.errorDetail.error_msg);
						$('#userError').popup('open', {x: 0, y:0});					
					} else {
						formChange = false;
						objUser.Role = result.data;
						$('#userSuccess').popup( 'open', {x: 0, y:0});
						listDash();
					}
				},
				error: function (request,error) {
					$('#userErrorText').text('Network error');
					$('#userError').popup('open');
				}
			});		
		});
	}
});
$(document).on('pageshow', '#teamMenu', function(){
	localStorage.setItem("memGroupSel","");
});
$(document).on('pageshow', '#memGroup', function(){
	console.log('memGroup pageshow');
	loadGroup();
});

$(document).on('pagecreate', '#memGroup', function(){
	if (!objPageCreate.memGroup) {
		objPageCreate.memGroup = true;
		$(document).on('change', '#SelId', function () {
			localStorage.setItem("memGroupSel",$('#SelId').val());
			loadGroup();
		});	
	}
});

$(document).on('pagecreate', '#vetInfo', function(){
	if (objUser.Role.Dog=='Y') {$('.DogE').show();}					
	formChange = false; 	
	$('#VetEdit').validate({
		rules: {
			SelText: {required: true, minlength: 4},
			vetClinic: {required: true, minlength: 4},
			vetPhone: {required: true, phoneUS: true},
			vetEmergencyPhone: {phoneUS: true},			
			vetAddress: {required: true, minlength: 2},
			vetContact: {minlength: 4},
			vetEmail: {email: true}
		},
		messages: {
			SelText: {required: "Please enter short name", minlength: "Please enter valid short name"},
			vetClinic: {required: "Please enter clinic name", minlength: "Please enter a clinic name"},
			vetPhone: {required: "Please provide phone no.", phoneUS: "Please provide valid phone"},
			vetEmergencyPhone: {phoneUS: "Please provide valid phone"},					
			vetAddress: {required: "Please enter vet address", minlength: "Please enter a valid address"},
			vetContact: {minlength: "Please enter a valid contact"},
			vetEmail: {email: "Please enter a valid email"}
		}
	});
	$('.forms').change(function() {
		formChange = true;
	});	
	if (!objPageCreate.vetInfo) {
		objPageCreate.vetInfo = true;
		$(document).on('vclick', '#vetSaveBtn', function () {
			if (!$('#VetEdit').valid()) {
				return;
			}			
			$.ajax({
				url: '/assets/code/saveVet.php',											/* Save the vet information */
				type: 'POST',
				data: $('#VetEdit').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						$('#vetErrorText').text(result.errorDetail.error_msg);
						$('#vetError').popup('open', {x: 0, y:0});					
					} else {
						formChange = false;
						$('#vetSuccess').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$('#vetErrorText').text('Network error');
					$('#vetError').popup('open');
				}
			});		
		});
	}
});
$(document).on('pagecreate', '#vetList', function(){
	if (objUser.Role.Dog=='Y') {$('.addVet').show();}					
	if (!objPageCreate.vetList) {
		objPageCreate.vetList = true;
		$(document).on('vclick', '#addVet', function () {
			$.ajax({
				url: '/assets/code/addVet.php',										/* Save the dog information */
				type: 'POST',
				data: {AddNew : 'y'},
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						alert('Request to add member failed, try again later');				
					} else {
						listDash();															/* refresh dashboard counts */						
						$('#vet_list').prepend(vetListHTML.replace(/{x}/g,result.data)).listview("refresh");						
					}
				},
				error: function (request,error) {
					alert('Network error');
				}
			});
		});	
	}
});
$(document).on('pagecreate', '#mediaList', function(){
	formChange = false; 	
	if (objUser.Role.Dog=='Y') {$('.DogE').show();}	
	if (objUser.Role.Mem=='Y') {$('.MemE').show();}	
	if (!objPageCreate.mediaList) {
		objPageCreate.mediaList = true;
	}
});
$(document).on('pagecreate', '#admin', function(){
	var warningDisplayed = false;
	if (!objPageCreate.admin) {
		objPageCreate.admin = true;
		$('#xxxMode').change(function() {
			$.ajax({
				url: '/assets/code/modeToggle.php',
				type: 'POST',
				data: {userid:$('#xxxMode').val()},
				dataType: 'json',
				async: true,
				success: function (result) {
					if (result.error) {
						alert(result.errorDetail.error_msg);
					} else {
						localStorage.setItem("xxxMode",result.data);
						if (!warningDisplayed) {
							$('#xxxModePopup').popup('open', {x: 0, y:0});	
							warningDisplayed = true;						
						}
						console.debug('god mode');
					}
				},
				error: function (request,error) {
					alert('Network error');
				}
			});				
		});			
	}
});
$(document).on('pagecreate', '#mediaEdit', function(){
	formChange = false; 	
	if (objUser.Role.Dog=='Y') {$('.DogE').show();}	
	if (objUser.Role.Mem=='Y') {$('.MemE').show();}		
	$('#mediaEditForm').validate({
		rules: {
			Caption: {required: true, minlength: 4},
			FileDesc: {required: true, minlength: 4},
			attachType: {selectcheck: true}			
		},
		messages: {
			Caption: {required: "Please enter caption", minlength: "Please enter valid caption"},
			FileDesc: {required: "Please enter description", minlength: "Please enter description"},
			attachType: {selectcheck: "Please select type"}					
		}
	});
	$('.forms').change(function() {
		formChange = true;
	});		
	if (!objPageCreate.mediaEdit) {
		objPageCreate.mediaEdit = true;
		$(document).on('vclick', '#saveMedia', function () {
			if (!$('#mediaEditForm').valid()) {
				return;
			}			
			$.ajax({
				url: '/assets/code/saveMediaMeta.php',											/* Save the media information */
				type: 'POST',
				data: $('#mediaEditForm').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						$('#mediaEditErrorText').text(result.errorDetail.error_msg);
						$('#mediaEditError').popup('open', {x: 0, y:0});					
					} else {
						formChange = false;
						$('#mediaEditSuccess').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$('#mediaEditErrorText').text('Network error');
					$('#mediaEditError').popup('open');
				}
			});		
		});	
	}
});
function loadGroup() {
	if (!localStorage.getItem("memGroupSel")=="") {
	$.ajax({
		url: '/assets/code/ListGroup.php',										/* */
		type: 'POST',
		data: {SelId : localStorage.getItem("memGroupSel"), SelMode : $('#memGroup').data('myteam')},
		dataType: 'json',
		async: true,
		success: function (result) {
			if (result.error) {
				$('#memGroupErrorText').text(result.errorDetail.error_msg);						
				$('#memGroupError').popup('open', {x: 0, y:0});					
			} else {
				$('#memGroupMembers').empty();
				if (!result.data.length) {
					if ($('#memGroup').data('myteam')=='team') {
						$('#memGroupMembers').append(htmlTeamNull).listview("refresh");
					} else {
						$('#memGroupMembers').append(htmlMemNull).listview("refresh");
					}
				} else {
					$.each(result.data, function (key, value) {
						$('#memGroupMembers').append(htmlMemGroup.replace(/{key}/g,value.key).replace(/{name}/g,value.name).replace(/{phone}/g,value.phone).replace(/{email}/g,value.email).replace(/{id}/g,value.id)).listview("refresh");
					});	
				}
			}
		},
		error: function (request,error) {
			$('#memGroupErrorText').text('Network error');
			$('#memGroupError').popup('open');
		}
	});
	}
}	
function handleMediaClick(e) {																		// handles the media click event
	console.debug($(e));
	fn = $(e)[0].alt;
	window.open(fn, '_blank');
//	console.debug($('#mediaFrame'));
//	$('#mediaFrame').attr('src', fn);
//	$('#mediaPanel').popup('open', {positionTo: '.imgOver'});	
}

function handleDocClick(e) {																		// handles the media click event
	console.debug($(e));
	fn = $(e)[0].alt;
	fn = fn.replace('../../','');
	console.debug(fn);
window.open('https://view.officeapps.live.com/op/view.aspx?src=http://db.southern-az-golden-retriever-rescue.org/'+fn, '_blank');	
	
// window.open('https://view.officeapps.live.com/op/embed.aspx?src=http://db.southern-az-golden-retriever-rescue.org/uploads/files/FN5d314d2aba60b.doc', '_content', 'fullscreen=yes');	
//	console.debug($('#mediaFrame'));
//	$('#mediaFrame').attr('src', fn);
//	$('#mediaPanel').popup('open', {positionTo: '.imgOver'});	
}


function handleImgClick(e) {																		// handles the img click event
	fn = $(e)[0].src;
	fn = '/uploads/files/'+fn.split('/')[fn.split('/').length-1];
window.open(fn, '_blank');	
//	$('#imgContainer').empty();
//	$('#imgContainer').append('<img src="'+fn+'" class="my-imgFull" style="width:100%">');
//	$('#imgPanel').popup('open', {positionTo: '.imgOver'});	
}

vetListHTML =	'<li>'+
				'<a href="/assets/pages/vet.php?x={x}&l={l}&q={q}" style="padding-bottom:0.7em;padding-top:0" data-transition="slide">'+
				'<h3>New Clinic</h3>'+
				'<p>Phone: (___)___-____'+
				'<br>'+
				'</p></a></li>';

htmlMemGroup = 	'<li><a href="/assets/pages/member.php?x={key}&l=all&q=" style="padding:0.5em 1em;" data-id="{key}" >'+
				'<h3><span class="memName">{name}</span>&nbsp; #: <span class="memId">{id}</span></h3>'+
				'<p>Phone: {phone} &nbsp; email: {email}</p>'+
				'</a>'+
				'</li>';
htmlMemNull =	'<li><p style="font-size: 1em;font-style: italic;">No Members found with this interest.</p></li>';
htmlTeamNull =	'<li><p style="font-size: 1em;font-style: italic;">No Members assigned to this team.</p></li>';

htmlWishAdd =	'<li style="padding-bottom:0.1em;padding-top:0.2em">'+
				'<a href="/assets/pages/wish.php?x={w_id}&l=&q=&s=" style="padding-bottom:0;padding-top:0" data-transition="slide">'+
				'<h3>{w_by}&nbsp;&nbsp; {w_date}</h3>'+
				'<p>Priority: 4&nbsp;&nbsp;Status: Open</p>'+
				'<p>NEW ENTRY...</p>'+
				'</a></li>';						
