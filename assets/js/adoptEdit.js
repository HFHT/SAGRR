var valEle = valErr = formData = {};
var dogSel ={};

$(document).on('pagecreate', '#adoptBig', function(){
	$("#bigAdoptTbl").tablesorter(); 
});
$(document).on('pagecreate', '#adoptFilter', function(){
	console.debug('adoptFilter');
	$('.appfilter').css("visibility", "visible");
	$('input[name="Des"]').bind('change', function() {
		objFilterData.des = $(this).val();
		console.debug($(this).val());
		applyFilter();
	});
	$('input[name="Sex"]').bind('change', function() {
		objFilterData.sex = $(this).val();
		console.debug($(this).val());
		applyFilter();		
	});
	$('input[name="child"]').bind('change', function() {
		objFilterData.child = $(this).val();
		console.debug($(this).val());
		applyFilter();		
	});
	$('input[name="Status"]').bind('change', function() {
		objFilterData.Status = $(this).val();
		console.debug($(this).val());
		applyFilter();		
	});
	$('input[name="dogs"]').bind('change', function() {
		objFilterData.dogs = $(this).val();
		console.debug($(this).val());
		applyFilter();		
	});
	$('input[name="cats"]').bind('change', function() {
		objFilterData.cats = $(this).val();
		console.debug($(this).val());
		applyFilter();		
	});
	$('input[name="hours"]').bind('change', function() {
		objFilterData.hours = $(this).val();
		console.debug($(this).val());
		applyFilter();		
	});
	$('input[name="healthminor"]').bind('change', function() {
		objFilterData.healthminor = $(this).val();
		console.debug($(this).val());
		applyFilter();		
	});
	$('input[name="healthmajor"]').bind('change', function() {
		objFilterData.healthmajor = $(this).val();
		console.debug($(this).val());
		applyFilter();		
	});
	$('input[name="spec"]').bind('change', function() {
		objFilterData.spec = $(this).val();
		console.debug($(this).val());
		applyFilter();		
	});
	$('input[name="hb"]').bind('change', function() {
		objFilterData.hb = $(this).val();
		console.debug($(this).val());
		applyFilter();		
	});
	$('input[name="bi"]').bind('change', function() {
		objFilterData.bi = $(this).val();
		console.debug($(this).val());
		applyFilter();		
	});
	$('input[name="age1"]').bind('change', function() {
		objFilterData.age1 = $(this).val();
		console.debug($(this).val());
		applyFilter();		
	});
	$('input[name="age2"]').bind('change', function() {
		objFilterData.age2 = $(this).val();
		console.debug($(this).val());
		applyFilter();		
	});
	$('input[name="fdoor"]').bind('change', function() {
		objFilterData.door = $(this).val();
		console.debug($(this).val());
		applyFilter();		
	});
	$('input[name="fpool"]').bind('change', function() {
		objFilterData.pool = $(this).val();
		console.debug($(this).val());
		applyFilter();		
	});
	$('input[name="ffence"]').bind('change', function() {
		objFilterData.fence = $(this).val();
		console.debug($(this).val());
		applyFilter();		
	});
	
});
function applyFilter() {
	$.ajax({
		url: '/assets/code/filterApp.php',											/* Save application approval */
		type: 'POST',
		data: objFilterData,
		dataType: 'json',
		async: true,
		success: function (result) {	
			if (result.error) {
				alert('Result Error');					
			} else {
				$('#filt_list').empty().append(result.html).listview("refresh");
				$('.filterQty').html(result.Qty);
				console.debug('got result');
			}
		},
		error: function (request,error) {
			console.debug(request);
			console.debug(error);
			alert('Network Error');
		}
	});
}
$(document).on('pagecreate', '#adoptStatus', function(){
	if (objUser.Role.Appl=='Y') {$('.ApplE').show();}					
});
$(document).on('pagecreate', '#adoptAppl', function(){
	if (!objPageCreate.adoptAppl) {
		objPageCreate.adoptAppl = true;
		$(document).on('click', '.addPet', function() {
			if ( ($('#tblPet tr[style="display:none"]:first').removeAttr('style')).length == 0) {
				$('#addPetBtn').hide();
				console.debug('Hide pet');
			}
		});		
	}
});
$(document).on('pagecreate', '#applApprove', function(){
	formChange = false; 	
	$('#apapprEdit').validate({
		rules: {
			applApproved: {required: true}
		},
		messages: {
			applApproved: {required: "Please approve application."}
		}
	});
	$('.forms').change(function() {
		formChange = true;
	});	
	if ($('#applApprove').data('myappr')!='Y' || objUser.userid=='azhoffs') {
		$('#apprBy').text(objUser.name);
		$('#applApprovedBy').val(objUser.name);
	} else {
		$('.myapappr').empty();
		$('#applApproved').attr('disabled', true);
	}
	if (objUser.Role.App=='Y') {
		$('#apapprSaveBtn').show();
	} else {
		$('.applAppDisable').attr('disabled', true);
		$("label[for='applApprovedN'],label[for='applApprovedY']").css('background-color','darkgray');
	}
	if (!objPageCreate.applApprove) {
		objPageCreate.applApprove = true;
		$(document).on('vclick', '#apapprSaveBtn', function () {
			if (!$('#apapprEdit').valid()) {
				return;
			}			
			$.ajax({
				url: '/assets/code/saveApplAppr.php',											/* Save application approval */
				type: 'POST',
				data: $('#apapprEdit').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {	
					if (result.error) {
						$('#apapprErrorText').text(result.errorDetail.error_msg);
						$('#apapprError').popup('open', {x: 0, y:0});					
					} else {
						formChange = false;
						$('#apapprSuccess').popup('open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$('#apapprErrorText').text('Network error');
					$('#apapprError').popup('open', {x: 0, y:0});
				}
			});
		});	
	}
});

$(document).on('pagecreate', '#adoptDog', function(){
	if (objUser.Role.Assign=='Y') {$('.ApplE').show();}					
	$(document).on('click', '.yesDog', function() {
		formChange = true;
		$('#nodogsel').text('');
		$(".myNav").removeClass("ui-btn-active");		
		// Move selected dog from available to assigned section of the web page
		$(this).removeClass('yesDog ui-icon-arrow-u').addClass('noDog ui-icon-arrow-d').parent().detach().insertAfter('#dogAssign');
		($('.noDog').length == 0) ? $('#dogAssign').show() : $('#dogAssign').hide();
		($('.yesDog').length == 0) ? $('#dogAvail').show() : $('#dogAvail').hide();	
		$('.adoptlists').listview("refresh");
	});
	$(document).on('click', '.noDog', function() {
		formChange = true;
		// Move selected dog from assigned to available section of the web page
		$(this).removeClass('noDog ui-icon-arrow-d').addClass('yesDog ui-icon-arrow-u').parent().detach().insertAfter('#dogAvail');
		($('.noDog').length == 0) ? $('#dogAssign').show() : $('#dogAssign').hide();
		($('.yesDog').length == 0) ? $('#dogAvail').show() : $('#dogAvail').hide();	
		$('.adoptlists').listview("refresh");		
	});	
	if (!objPageCreate.adoptDog) {
		objPageCreate.adoptDog = true;
		$(document).on('vclick', '#saveAdoption', function (e) {
			e.preventDefault();
			e.stopPropagation();
//			if ($('.adoptlists').data('applmode')=='okf') {
//				$('.applStatus').text('WaitList');
//			}
			if ($('.yesDog.assigned').length != 0) {
				if ($('.noDog.available').length == 0) {
					$('#reassignConfirm').popup('open', {x: 0, y:0});
				} else {
					$('#swapdogConfirm').popup('open', {x: 0, y:0});
				}
				return;
			}
			if ($('.noDog.available').length == 0) {
				$('#nodogsel').text('Please add one or more dogs');
				return;
			} else {
				$('#adoptionConfirm').popup('open', {x: 0, y:0});
				return;
			}
		});
		$(document).on('vclick', '#adoptBtn,#reassignBtn,#swapBtn', function (e) {
			console.debug(e);
			e.preventDefault();
			e.stopPropagation();
			$('#adoptionConfirm').popup('close');
			if ($('.noDog.available').length == 0 && $('.yesDog.assigned').length == 0) {console.debug('should not happen'); return;}												// This shouldn't happen, but just in case.
			var aryAdoptions = [];
			var aryReturns = [];
			$('.noDog.available').each(function(a,b,c) {
				console.debug($(this).data('dogkey'),$(this).data('dogname'));
				var objtmp = {	dogkey:$(this).data('dogkey'), 
								dogname:$(this).data('dogname')};
				console.debug(objtmp);
				aryAdoptions.push(objtmp);
			});
			$('.yesDog.assigned').each(function(a,b,c) {
				console.debug($(this).data('dogkey'),$(this).data('dogname'));
				var objtmp = {	dogkey:$(this).data('dogkey'), 
								dogname:$(this).data('dogname')};
				console.debug(objtmp);
				aryReturns.push(objtmp);
//				aryReturns.push($(this).data('dogkey'));
			});
			console.debug(aryAdoptions);
			console.debug(aryReturns);
			var adoptForm = {};
			adoptForm.fk_applid = $('.adoptlists').data('fk_applid');
			adoptForm.applmode = $('.adoptlists').data('applmode');
			adoptForm.applcomp = $(this).attr('id');
			adoptForm.fk_PeopleT_id = objUser.fk_PeopleT_id;
			adoptForm.createBy = objUser.name;
			adoptForm.family = $('.adoptlists').data('family');
			adoptForm.state = $('.adoptlists').data('state');
			adoptForm.dogcnt = $('.adoptlists').data('dogcnt');
			adoptForm.memorappl = $('.adoptlists').data('memorappl');
			adoptForm.DogList = aryAdoptions;
			adoptForm.RetList = aryReturns;	
			console.debug(adoptForm);
			$.ajax({
				url: '/assets/code/saveAdoption.php',											/* Save Dog selections */
				type: 'POST',
				data: {formData:JSON.stringify(adoptForm)},
				dataType: 'json',
				async: true,
				success: function (result) {	
					if (result.error) {
						$('#adoptionErrorText').text(result.errorDetail.error_msg);
						$('#adoptionError').popup('open', {x: 0, y:0});					
					} else {
						listDash();						
						formChange = false;
						$('#adoptionSuccess').popup('open', {x: 0, y:0});
						$.mobile.back();						
					}
				},
				error: function (request,error) {
					$('#adoptionErrorText').text('Network error');
					$('#adoptionError').popup('open', {x: 0, y:0});
				}
			});
		});	
	}
});

$(document).on('pagecreate', '#adoptInfo', function(){
	if (objUser.Role.Appl=='Y') {$('.ApplE').show();}					
	formChange = false; 	
	$('#adoptEditForm').validate({
		rules: {
			LName: {required: true, minlength: 2},
			Addr: {minlength: 4},
			City: {minlength: 4},
			Phone: {required: true, phoneUS: true},
			Cell: {phoneUS: true},
			FName: {required: true, minlength: 2},
			Zip: {zipcode: true}
		},
		messages: {
			LName: {required: "Please enter name", minlength: "Please enter valid name"},
			FName: {required: "Please enter name", minlength: "Please enter valid name"},
			Addr: {minlength: "Please enter valid address"},
			City: {minlength: "Please enter valid city"},
			Email: {email: "Please enter a valid email"},
			Phone: {required: "Please provide phone no.", phoneUS: "Please provide valid phone"},
			Cell: {phoneUS: "Please provide valid cell"}
		}
	});
	$('.forms').change(function() {
		formChange = true;
	});	
	if (!objPageCreate.adoptInfo) {
		objPageCreate.adoptInfo = true;
		$(document).on('vclick', '#adoptSaveBtn', function () {
			if (!$('#adoptEditForm').valid()) {
				return;
			}			
			formData = $('#adoptEditForm').serializeObject();									// Create JSON object and get rid of unneeded selects 
			var v = formData.v;
			delete formData.v;
			var applDateTime = formData.applDateTime;
			delete formData.applDateTime;
			var applNote = formData.applNote;
			delete formData.applNote;			
			$.ajax({
				url: '/assets/code/saveApplContact.php',										/* Save the member information */
				type: 'POST',
				data: {v:v,applDateTime:applDateTime,applNote:applNote,formData:JSON.stringify(formData)},
				dataType: 'json',
				async: true,
				success: function (result) {
					if (result.error) {
						$('#adoptErrorText').text(result.errorDetail.error_msg);
						$('#adoptError').popup('open', {x: 0, y:0});					
					} else {
						listDash();						
						formChange = false;
						$('#adoptSuccess').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$('#adoptErrorText').text('Network error');
					$('#adoptError').popup('open');
				}
			});		
		});
	}
});


$(document).on('pagecreate', '#adoptStatusEdit', function(){
	if (objUser.Role.Appl=='Y') {$('.ApplE').show();}					
	formChange = false;
	// Set the starting status to be the latest one, 
//	var sel = '#a_'+applStatus;
//	$(sel).attr('checked', true).checkboxradio( "refresh" );
	// This page uses another page to select a members name, need to capture whatever was in the form before the page closes and restor it upon return
	$('#adoptStatusEdit').on("pagebeforehide", function ( event, ui ) {						/* This will be deprecated in 1.6.0 */
		if (ui.nextPage[0].id == 'memSelect') {												/* Hard-coded page name 				*/
			arySavedForm = $('#adoptStatusEditForm').serializeArray();
		} else {
			arySavedForm = [];
		}
	});
	if (arySavedForm[0] != null) {															/* Has form data been saved previously? */
//		$('input[name=applProcStatus]:checked').removeAttr('checked').checkboxradio( "refresh" );
		$.each(arySavedForm, function (key, value) {										/* Restore the form with saved data		*/
//			console.debug(key,value);
			var sel = '#'+value.name;
			$(sel).val(value.value);
			$(sel).val([value.value]);
		});
//		applProcStatus = arySavedForm[0].value;												/* Hard-coded based on field position on form */
//		sel = '#a_'+applProcStatus;
//		$(sel).attr('checked', true).checkboxradio( "refresh" );
		$('#applProcStatus').selectmenu('refresh');
		$('#applProcState').selectmenu('refresh');
		arySavedForm = [];																	/* Clear out saved form data			*/
	}
	if (memNew) {																			/* Check to see if memSelect set responsible member */
		memNew = false;
		$('#adoptStatusEditForm #DogCurMember').val(objMemSelect.memName);
		$('#adoptStatusEditForm #DogCurMemNo').val(objMemSelect.memId);
		$('#adoptStatusEditForm #DogCurMember').val(objMemSelect.memName);
		if (objMemSelect.memTeam=='InTake') {
			$('#adoptStatusEditForm #applInTake_fk_PeopleT').val(objMemSelect.memKey);
		} else {
			$('#adoptStatusEditForm #applHV_fk_PeopleT').val(objMemSelect.memKey);			
		}	
		$('#adoptStatusEditForm #fk_PeopleT_id').val(objMemSelect.memKey);
		$('#adoptStatusEditForm #memSelected').text(objMemSelect.memName);
	}
	$('#adoptStatusEditForm').validate({
		rules: {
//			applProcStatus: {required: true},
			StatusComment: {required: true,minlength:4},
			StatusDate: {required: true}
		},
		messages: {
//			applProcStatus: {required: "Please select status"},
			StatusComment: {required: "Please provide comment", minlength: "Please provide valid comment"},			
			StatusDate: {required: "Please provide status date"}
		},
		errorLabelContainer: "#adoptErrContainer"
	});
	$('.forms').change(function() {
//		applStatus = $('input[name=applProcStatus]:checked').val()						// Capture the status so it can be set next time
		formChange = true;
	});
	if (!objPageCreate.adoptStatusEdit) {
		objPageCreate.adoptStatusEdit = true;
		$(document).on('vclick', '#applStatusHelpBtn', function() {
			$('#applStatusHelp').popup('open', {x: 0, y:0});
		});
		$(document).on('vclick', '#adoptStatusSaveBtn', function () {
			console.debug('save button');
			$('#adoptErrContainer').empty();													/* Clear error message							*/
			$('#adoptErrContainer1').empty();													/* Clear error message							*/
			console.log($('#applProcStatus').val(),applProcStatus,$('#fk_PeopleT_id').val().length);
			if (($('#applProcStatus').val()=='HomeVisit') && (applProcStatus!='HomeVisit') && ($('#fk_PeopleT_id').val().length == 0)) {									/* Check to see if responsible person is set	*/
				$('#adoptErrContainer1').append('<label class="error" for="fk_PeopleT_id">Select a Home Visit volunteer</label>').show();
				$(".myNav").removeClass("ui-btn-active");				
				return;
			}
			if (!$('#adoptStatusEditForm').valid()) {
				return;
			}
			$('#createBy').val(objUser.name);												// Capture name of person creating status
			$.ajax({
				url: '/assets/code/saveDogStatus.php',										/* Save the dog information 					*/
				type: 'POST',
				data: $('#adoptStatusEditForm').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						$('#adoptStatusErrorText').text(result.errorDetail.error_msg);
						$('#adoptStatusError').popup('open', {x: 0, y:0});					
					} else {
						formChange = false;
//						applStatus = $('input[name=applProcStatus]:checked').val();			// Capture the status so it can be set next time
						listDash();															/* refresh dashboard counts */						
						$('#adoptStatusSuccess').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$('#adoptStatusErrorText').text('Network error');
					$('#adoptStatusError').popup('open', {x: 0, y:0});
				}
			});		
		});	
	}
});
$(document).on('pagecreate', '#adoptPg', function(){
	if (objUser.Role.Appl=='Y') {$('.ApplE').show();}				
	formChange = false; 
	if (memNew) {																			/* Check to see if memSelect set the linked member */
		memNew = false;
		console.debug(objMemSelect);
		$('#showMemSpawn,#showMemberRcd').toggle();
		$.ajax({
			url: '/assets/code/saveApplMemLink.php',										/* Save the member information */
			type: 'POST',
			data: {v:$('#adoptPg').data('applid'), mem:objMemSelect.memKey},
			dataType: 'json',
			async: true,
			success: function (result) {
				if (result.error) {
					alert(result.errorDetail.error_msg);
			} else {
					$( ":mobile-pagecontainer" ).pagecontainer( "change", "/assets/pages/adopt.php?x="+$('#adoptPg').data('applid')+"&l=&q=", { reload: true } );
					console.debug('page reload');
				}
			},
			error: function (request,error) {
				alert('Network error');
			}
		});						
	}
	
	$('#memSpawn').change(function() {
		console.debug($( "input:checked" ).val());
		if ($( "input:checked" ).val()=='link') {
			$(':mobile-pagecontainer').pagecontainer('change','/assets/pages/memSelect.php?m=&n=&l=');
		} else {
			if ($( "input:checked" ).val()=='spawn') {
				$('#memSpawnConfirm').popup('open', {x: 0, y:0});						/* Confirm the creation of Membership record */
			} else {
				alert ("Program error, could not determine action to take for Membership");
			}
		}	
	});
	if (!objPageCreate.adoptPg) {
		objPageCreate.adoptPg = true;
		$(document).on('vclick', '#memSpawnBtn', function () {
			$.ajax({
				url: '/assets/code/memSpawn.php',										/* Clone Applicant into Member */
				type: 'POST',
				data: {v:$('#adoptPg').data('applid')},
				dataType: 'json',
				async: true,
				success: function (result) {
					if (result.error) {
						alert(result.errorDetail.error_msg);
				} else {
						$( ":mobile-pagecontainer" ).pagecontainer( "change", "/assets/pages/adopt.php?x="+$('#adoptPg').data('applid')+"&l=&q=", { reload: true } );
						console.debug('page reload');
					}
				},
				error: function (request,error) {
					alert('Network error');
				}
			});								
		});
	}
});
$(document).on('pagecreate', '#adoptList', function(){
	if (objUser.Role.Appl=='Y') {$('.addAppl').show();}			
	if (!objPageCreate.adoptList) {
		objPageCreate.adoptList = true;
		$(document).on('vclick', '#addAppl', function () {
			$.ajax({
				url: '/assets/code/addAppl.php',											/* Add a new application */
				type: 'POST',
				data: {AddNew : 'y'},
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						alert('Request to add application failed, try again later');				
					} else {
						listDash();															/* refresh dashboard counts */						
						$('#adopt_list').prepend(adoptListHTML.replace(/{x}/g,result.data).replace(/{date}/g,dateForm)).listview("refresh");					
					}
				},
				error: function (request,error) {
					alert('Network error');
				}
			});
		});	
	}
});
$(document).on('pagecreate', '#adoptS0', function(){
	if (objUser.Role.Appl=='Y') {$('.ApplE').show();}					
	formChange = false; 	
	$('#adoptS0Form').validate({
		rules: {
			MemName: {minlength:2},
			MemAge: {range:[0,110]}
//			Ans4: {required: true},
//			Ans5: {required: true} 
		},
		messages: {
			MemName: {minlength:"Enter valid name"},
			MemAge: {range: "Invalid"}
//			Ans4: {required: "Please answer this question."},
//			Ans5: {required: "Please answer this question."}
		},
		errorPlacement: function(error, element) {										// Perform custom placement for radio buttons for JQM
			placement = $(element);														// default to jQuery validator placement
			if ($(element).attr('name')=='Ans5') {										// if its a known radio button then update placement
				placement = $('.Ans5Err');												// jQuery validator disrupts JQM radio button formats!!
			}
			if ($(element).attr('name')=='Ans4') {
				placement = $('.Ans4Err');
			}
			error.insertAfter(placement);
		}
	});
	$('.forms').change(function() {
		console.debug('forms change');
		formChange = true;
	});	
	if (!objPageCreate.adoptS0) {
		objPageCreate.adoptS0 = true;
		htmlPeople = $('#htmlPeople').clone();
		htmlPeople.attr('id','');
		$(document).on('click', '.addPerson', function() {
			console.debug('Add person');
			if ( ($('.htmlPeople[style="display:none"]:first').removeAttr('style')).length == 0) {
				$('#addPerson').hide();
				console.debug('Hide person');
			}
//			htmlTemp = htmlPeople.clone();
//			$('#tblPerson').append(htmlTemp);
//			$('.addPerson').parent().removeClass('ui-btn-active');			
		});
//		$(document).on('click', '.addPet', function() {
//			if ( ($('#tblPet tr[style="display:none"]:first').removeAttr('style')).length == 0) {
//				$('#addPetBtn').hide();
//				console.debug('Hide pet');
//			}
//		});		
		$(document).on('click', '#adoptS0SaveBtn', function () {
			if (!$('#adoptS0Form').valid()) {
				return;
			}	
			formData = $('#adoptS0Form').serializeObject();									// Create JSON object and get rid of unneeded selects 
//			formData.PetType.length=formData.PetAge.length=formData.PetHome.length=formData.PetSex.length=formData.PetType.indexOf('0');
			for (var i = formData.PetType.length-1;i--;) {									// remove empty rows 
				console.debug(formData.PetType);
				if (formData.PetType[i]=='0') {
					formData.PetType.splice(i,1);
					formData.PetAge.splice(i,1);
					formData.PetHome.splice(i,1);
					formData.PetBreed.splice(i,1);
					formData.PetSex.splice(i,1);
				}
			}
			console.debug(formData.MemName,formData.MemName.length-1);
			for (var i = formData.MemName.length-1;i--;) {									// remove empty rows
				console.debug(formData.MemName,i);
				if (formData.MemName[i]=='') {
					formData.MemName.splice(i,1);
					formData.MemAge.splice(i,1);
				}
			}
			var v = formData.v;
			delete formData.v;
			$.mobile.loading('show');
			$.ajax({
				url: '/assets/code/saveAdopt.php',											// Save the application  
				type: 'POST',
				data: {step:0,v:v,formData:JSON.stringify(formData)},
				dataType: 'json',
				async: true,
				success: function (result) {				
					$.mobile.loading('hide');
					$(".myNav").removeClass("ui-btn-active");
					if (result.error) {
						$('#adoptS0ErrorText').text(result.errorDetail.error_msg);
						$('#adoptS0Error').popup('open', {x: 0, y:0});					
					} else {
						listDash();						
						formChange = false;
						$('#adoptS0Success').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$.mobile.loading('hide');
					$(".myNav").removeClass("ui-btn-active");
					$('#adoptS0ErrorText').text('Network error');
					$('#adoptS0Error').popup('open', {x: 0, y:0});
				}
			});		
		});
	}
});
$(document).on('pagecreate', '#adoptS1', function(){
	if (objUser.Role.Appl=='Y') {$('.ApplE').show();}					
	formChange = false; 	
	$('#adoptS1Form').validate({
		rules: {
//			Ans6: {required: true},
//			Ans7: {required: true},
			Ans8: {required: '#Ans7R:checked'},
//			Ans9: {required: true},
//			Ans10: {required: true},
			Ans11: {required: '#Ans10Y:checked'},
			Ans111: {required: '#Ans110Y:checked'},			
//			Ans12: {required: true},
			Ans13: {required: '#Ans12Y:checked'},
//			Ans14: {selectcheck: '#Ans12Y:checked'}
//			Ans15: {required: true}			
		},
		messages: {
//			Ans6: {required: "Please answer this question."},			
//			Ans7: {required: "Please answer this question."},
			Ans8: {required: "Please answer this question."},
//			Ans9: {required: "Please answer this question."},
//			Ans10: {required: "Please answer this question."},
			Ans11: {required: "Please answer this question."},
			Ans111: {required: "Please answer this question."},
//			Ans12: {required: "Please answer this question."},
			Ans13: {required: "Please answer this question."},
			Ans14: {required: "Please answer this question."}
//			Ans15: {required: "Please answer this question."}			
		},
		errorPlacement: function(error, element) {
			valEle = element;
			valErr = error;
			placement = $(element);
			var ansName = $(element).attr('name');
			if (ansName.substr(0,3)=='Ans') {
				var errNo = ansName.substr(3);
				placement = $('.AnsErr'+errNo);
			}
			console.debug(placement);
			error.insertAfter(placement);
		}
	});
	$('#Ans10').find('input').bind('change', function() {
		console.debug($(this).val());
		if ($(this).val()=='Y') {
			$('.Ans10Hide').show();
		} else {
			$('.Ans10Hide').hide();	
			$('.Ans10Clear').find('input').removeAttr("checked").checkboxradio("refresh");
		}
	});
	$('#Ans110').find('input').bind('change', function() {
		console.debug($(this).val());
		if ($(this).val()=='Y') {
			$('.Ans110Hide').show();
		} else {
			$('.Ans110Hide').hide();	
			$('.Ans110Clear').find('input').removeAttr("checked").checkboxradio("refresh");
		}
	});	
	$('#Ans12').find('input').bind('change', function() {
		console.debug($(this).val());
		if ($(this).val()=='Y') {
			$('.Ans12Hide').show();
		} else {
			$('.Ans12Hide').hide();	
			$('.Ans12Clear').find('input').removeAttr("checked").checkboxradio("refresh");
			$('.Ans12Zero').find('select').val('0').selectmenu('refresh',true);
		}
	});	
	$('.forms').change(function() {
		formChange = true;
	});		
	if (!objPageCreate.adoptS1) {
		objPageCreate.adoptS1 = true;	
		$(document).on('click', '#adoptS1SaveBtn', function () {
			if (!$('#adoptS1Form').valid()) {
				return;
			}	
			formData = $('#adoptS1Form').serializeObject();									// Create JSON object and get rid of unneeded selects 
//			formData.PetType.length=formData.PetAge.length=formData.PetHome.length=formData.PetSex.length=formData.PetType.indexOf('0');
			var v = formData.v;
			delete formData.v;
			$.mobile.loading('show');
			$.ajax({
				url: '/assets/code/saveAdopt.php',											// Save the application  
				type: 'POST',
				data: {step:1,v:v,formData:JSON.stringify(formData)},
				dataType: 'json',
				async: true,
				success: function (result) {				
					$.mobile.loading('hide');
					$(".myNav").removeClass("ui-btn-active");
					if (result.error) {
						$('#adoptS1ErrorText').text(result.errorDetail.error_msg);
						$('#adoptS1Error').popup('open', {x: 0, y:0});					
					} else {
						listDash();						
						formChange = false;
						$('#adoptS1Success').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$.mobile.loading('hide');
					$(".myNav").removeClass("ui-btn-active");
					$('#adoptS1ErrorText').text('Network error');
					$('#adoptS1Error').popup('open');
				}
			});		
		});
	}
});
$(document).on('pagecreate', '#adoptS2', function(){
	if (objUser.Role.Appl=='Y') {$('.ApplE').show();}					
	formChange = false; 	
	$('#adoptS2Form').validate({
		rules: {
//			Ans16: {required: true},
//			Ans17: {required: '#Ans16Y:checked'},
//			Ans18: {required: true},
//			Ans19: {required: true},
//			Ans20: {required: true},			
//			Ans21: {required: '#Ans20Y:checked'},
//			Ans22: {required: '#Ans20Y:checked'}					
		},
		messages: {
//			Ans16: {required: "Please answer this question."},			
//			Ans17: {required: "Please answer this question."},
//			Ans18: {required: "Please answer this question."},
//			Ans19: {required: "Please answer this question."},
//			Ans20: {required: "Please answer this question."},
//			Ans21: {required: "Please answer this question."},
//			Ans22: {required: "Please answer this question."}			
		},
		errorPlacement: function(error, element) {
			valEle = element;
			valErr = error;
			placement = $(element);
			var ansName = $(element).attr('name');
			if (ansName.substr(0,3)=='Ans') {
				var errNo = ansName.substr(3);
				placement = $('.AnsErr'+errNo);
			}
			console.debug(placement);
			error.insertAfter(placement);
		}
	});
	$('#adoptS02Form').validate({
		
	});

	$('#Ans16').find('input').bind('change', function() {
		console.debug($(this).val());
		if ($(this).val()=='Y') {
			$('.Ans16Hide').show();
		} else {
			$('.Ans16Hide').hide();	
			$('.Ans16Clear').find('input').removeAttr("checked").checkboxradio("refresh");
		}
	});
	$('#Ans20').find('input').bind('change', function() {
		console.debug($(this).val());
		if ($(this).val()=='Y') {
			$('.Ans20Hide').show();
		} else {
			$('.Ans20Hide').hide();	
			$('.Ans20Clear').val('');
		}
	});	
	$('#Ans112').find('input').bind('change', function() {
		console.debug($(this).val());
		if ($(this).val()=='Y') {
			$('.Ans112Hide').show();
		} else {
			$('.Ans112Hide').hide();	
			$('.Ans112Clear').val('');
		}
	});		
	$('.forms').change(function() {
		formChange = true;
	});		
	if (!objPageCreate.adoptS2) {
		objPageCreate.adoptS2 = true;	
		$(document).on('click', '#adoptS2SaveBtn', function () {
//			if (!$('#adoptS2Form').valid()) {
//				return;
//			}	
			formData = $('#adoptS2Form').serializeObject();									// Create JSON object and get rid of unneeded selects 
//			formData.PetType.length=formData.PetAge.length=formData.PetHome.length=formData.PetSex.length=formData.PetType.indexOf('0');
			var v = formData.v;
			delete formData.v;
			formDataS0 = $('#adoptS02Form').serializeObject();									// Create JSON object and get rid of unneeded selects 
			for (var i = formDataS0.PetType.length-1;i--;) {									// remove empty rows 
				console.debug(formDataS0.PetType);
				if (formDataS0.PetType[i]=='0') {
					formDataS0.PetType.splice(i,1);
					formDataS0.PetAge.splice(i,1);
					formDataS0.PetHome.splice(i,1);
					formDataS0.PetBreed.splice(i,1);
					formDataS0.PetSex.splice(i,1);
				}
			}

			$.mobile.loading('show');
			$.ajax({
				url: '/assets/code/saveAdopt.php',											// Save the application  
				type: 'POST',
				data: {step:2,v:v,formData:JSON.stringify(formData),formDataS0:JSON.stringify(formDataS0)},
				dataType: 'json',
				async: true,
				success: function (result) {				
					$.mobile.loading('hide');
					$(".myNav").removeClass("ui-btn-active");
					if (result.error) {
						$('#adoptS2ErrorText').text(result.errorDetail.error_msg);
						$('#adoptS2Error').popup('open', {x: 0, y:0});					
					} else {
						listDash();						
						formChange = false;
						$('#adoptS2Success').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$.mobile.loading('hide');
					$(".myNav").removeClass("ui-btn-active");
					$('#adoptS2ErrorText').text('Network error');
					$('#adoptS2Error').popup('open');
				}
			});		
		});
	}
});
$(document).on('pagecreate', '#adoptS3', function(){
	if (objUser.Role.Appl=='Y') {$('.ApplE').show();}					
	formChange = false; 	
	$('#adoptS3Form').validate({
		rules: {
//			Ans23: {selectcheck: true},
//			Ans24: {selectcheck: true},
			Ans23a: {required: true},
//			Ans25: {required: true},
//			Ans26: {required: true},
//			Ans27: {required: true},			
//			Ans28: {required: true},
//			Ans29: {required: true},
//			Ans30: {required: true},					
//			Ans31: {required: true}								
		},
		messages: {
//			Ans23: {selectcheck: "Please select."},		
//			Ans24: {selectcheck: "Please select."},
			Ans23a: {required: "Please select all that apply."}
//			Ans25: {required: "Please answer this question."},
//			Ans26: {required: "Please answer this question."},
//			Ans27: {required: "Please answer this question."},
//			Ans28: {required: "Please answer this question."},
//			Ans29: {required: "Please answer this question."},
//			Ans30: {required: "Please answer this question."},
//			Ans31: {required: "Please answer this question."}			
		},
		errorPlacement: function(error, element) {
			valEle = element;
			valErr = error;
			placement = $(element);
			var ansName = $(element).attr('name');
			if (ansName.substr(0,3)=='Ans') {
				var errNo = ansName.substr(3);
				placement = $('.AnsErr'+errNo);
			}
			console.debug(placement);
			error.insertAfter(placement);
		}
	});
	$('.forms').change(function() {
		formChange = true;
	});		
	if (!objPageCreate.adoptS3) {
		objPageCreate.adoptS3 = true;	
		$(document).on('click', '#adoptS3SaveBtn', function () {
			if (!$('#adoptS3Form').valid()) {
				return;
			}	
			formData = $('#adoptS3Form').serializeObject();									// Create JSON object and get rid of unneeded selects 
			if (typeof formData.Ans25 == 'string') {formData.Ans25 = [formData.Ans25];}
			if (typeof formData.Ans26 == 'string') {formData.Ans26 = [formData.Ans26];}			
			if (typeof formData.Ans23a == 'string') {formData.Ans23a = [formData.Ans23a];}	
//			formData.PetType.length=formData.PetAge.length=formData.PetHome.length=formData.PetSex.length=formData.PetType.indexOf('0');
			var v = formData.v;
			delete formData.v;
			$.mobile.loading('show');
			$.ajax({
				url: '/assets/code/saveAdopt.php',											// Save the application  
				type: 'POST',
				data: {step:3,v:v,formData:JSON.stringify(formData),fa:JSON.stringify(formData.Ans23a)},
				dataType: 'json',
				async: true,
				success: function (result) {				
					$.mobile.loading('hide');
					$(".myNav").removeClass("ui-btn-active");
					if (result.error) {
						$('#adoptS3ErrorText').text(result.errorDetail.error_msg);
						$('#adoptS3Error').popup('open', {x: 0, y:0});					
					} else {
						listDash();						
						formChange = false;
						$('#adoptS3Success').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$.mobile.loading('hide');
					$(".myNav").removeClass("ui-btn-active");
					$('#adoptS3ErrorText').text('Network error');
					$('#adoptS3Error').popup('open');
				}
			});		
		});
	}
});
$(document).on('pagecreate', '#adoptS4', function(){
	if (objUser.Role.Appl=='Y') {$('.ApplE').show();}					
	formChange = false; 	
	$('#adoptS4Form').validate({
		rules: {
//			Ans32: {required: true},
//			Ans33: {required: true},
//			Ans34: {required: true},
//			Ans35: {required: true},
//			Ans36: {required: true},			
//			Ans37: {required: true},
//			Ans38: {required: true},
//			Ans39: {required: true},					
//			Ans40: {required: true},					
//			Ans41: {required: true}								
		},
		messages: {
			Ans32: {required: "Please answer this question."},
			Ans33: {required: "Please answer this question."},
			Ans34: {required: "Please answer this question."},
			Ans35: {required: "Please answer this question."},
			Ans36: {required: "Please answer this question."},
			Ans37: {required: "Please answer this question."},
			Ans38: {required: "Please answer this question."},
			Ans39: {required: "Please answer this question."},
			Ans40: {required: "Please answer this question."},
			Ans41: {required: "Please answer this question."}			
		},
		errorPlacement: function(error, element) {
			valEle = element;
			valErr = error;
			placement = $(element);
			var ansName = $(element).attr('name');
			if (ansName.substr(0,3)=='Ans') {
				var errNo = ansName.substr(3);
				placement = $('.AnsErr'+errNo);
			}
			console.debug(placement);
			error.insertAfter(placement);
		}
	});
	$('.forms').change(function() {
		formChange = true;
	});		
	if (!objPageCreate.adoptS4) {
		objPageCreate.adoptS4= true;	
		$(document).on('click', '#adoptS4SaveBtn', function () {
			if (!$('#adoptS4Form').valid()) {
				return;
			}	
			formData = $('#adoptS4Form').serializeObject();									// Create JSON object and get rid of unneeded selects 
			var v = formData.v;
			delete formData.v;
			$.mobile.loading('show');
			$.ajax({
				url: '/assets/code/saveAdopt.php',											// Save the application  
				type: 'POST',
				data: {step:4,v:v,formData:JSON.stringify(formData)},
				dataType: 'json',
				async: true,
				success: function (result) {				
					$.mobile.loading('hide');
					$(".myNav").removeClass("ui-btn-active");
					if (result.error) {
						$('#adoptS4ErrorText').text(result.errorDetail.error_msg);
						$('#adoptS4Error').popup('open', {x: 0, y:0});					
					} else {
						listDash();						
						formChange = false;
						$('#adoptS4Success').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$.mobile.loading('hide');
					$(".myNav").removeClass("ui-btn-active");
					$('#adoptS4ErrorText').text('Network error');
					$('#adoptS4Error').popup('open');
				}
			});		
		});
	}
});
$(document).on('pagecreate', '#adoptS5', function(){
	if (objUser.Role.Appl=='Y') {$('.ApplE').show();}					
	formChange = false; 	
	$('#adoptS5Form').validate({
		rules: {
//			Ans42: {required: true},
//			Ans43: {required: true},
//			Ans44: {required: true},
//			Ans45: {required: true}							
		},
		messages: {
			Ans42: {required: "Please answer this question."},
			Ans43: {required: "Please answer this question."},
			Ans44: {required: "Please answer this question."},
			Ans45: {required: "Please answer this question."}			
		},
		errorPlacement: function(error, element) {
			valEle = element;
			valErr = error;
			placement = $(element);
			var ansName = $(element).attr('name');
			if (ansName.substr(0,3)=='Ans') {
				var errNo = ansName.substr(3);
				placement = $('.AnsErr'+errNo);
			}
			console.debug(placement);
			error.insertAfter(placement);
		}
	});
	$('.forms').change(function() {
		formChange = true;
	});		
	if (!objPageCreate.adoptS5) {
		objPageCreate.adoptS5= true;	
		$(document).on('click', '#adoptS5SaveBtn', function () {
			if (!$('#adoptS5Form').valid()) {
				return;
			}	
			formData = $('#adoptS5Form').serializeObject();									// Create JSON object and get rid of unneeded selects 
			var v = formData.v;
			delete formData.v;
			$.mobile.loading('show');
			$.ajax({
				url: '/assets/code/saveAdopt.php',											// Save the application  
				type: 'POST',
				data: {step:5,v:v,formData:JSON.stringify(formData)},
				dataType: 'json',
				async: true,
				success: function (result) {				
					$.mobile.loading('hide');
					$(".myNav").removeClass("ui-btn-active");
					if (result.error) {
						$('#adoptS5ErrorText').text(result.errorDetail.error_msg);
						$('#adoptS5Error').popup('open', {x: 0, y:0});					
					} else {
						listDash();						
						formChange = false;
						$('#adoptS5Success').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$.mobile.loading('hide');
					$(".myNav").removeClass("ui-btn-active");
					$('#adoptS5ErrorText').text('Network error');
					$('#adoptS5Error').popup('open');
				}
			});		
		});
	}
});
$(document).on('pagecreate', '#adoptS6', function(){
	if (objUser.Role.Appl=='Y') {$('.ApplE').show();}					
	formChange = false; 	
	$('#adoptS6Form').validate({
		rules: {
			Ans46: {required: true}							
		},
		messages: {
			Ans46: {required: "Please acknowledge."}			
//		},
//		errorPlacement: function(error, element) {
//			valEle = element;
//			valErr = error;
//			placement = $(element);
//			var ansName = $(element).attr('name');
//			if (ansName.substr(0,3)=='Ans') {
//				var errNo = ansName.substr(3);
//				placement = $('.AnsErr'+errNo);
//			}
//			console.debug(placement);
//			error.insertAfter(placement);
		}
	});
	$('.forms').change(function() {
		formChange = true;
	});		
	if (!objPageCreate.adoptS6) {
		objPageCreate.adoptS6= true;	
		$(document).on('click', '#adoptS6SaveBtn', function () {
			if (!$('#adoptS6Form').valid()) {
				return;
			}	
			formData = $('#adoptS6Form').serializeObject();									// Create JSON object and get rid of unneeded selects 
			var v = formData.v;
			delete formData.v;
			$.mobile.loading('show');
			$.ajax({
				url: '/assets/code/saveAdopt.php',											// Save the application  
				type: 'POST',
				data: {step:6,v:v,formData:JSON.stringify(formData)},
				dataType: 'json',
				async: true,
				success: function (result) {				
					$.mobile.loading('hide');
					$(".myNav").removeClass("ui-btn-active");
					if (result.error) {
						$('#adoptS6ErrorText').text(result.errorDetail.error_msg);
						$('#adoptS6Error').popup('open', {x: 0, y:0});					
					} else {
						listDash();						
						formChange = false;
						$('#adoptS6Success').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$.mobile.loading('hide');
					$(".myNav").removeClass("ui-btn-active");
					$('#adoptS6ErrorText').text('Network error');
					$('#adoptS6Error').popup('open');
				}
			});		
		});
	}
});
$(document).on('pagecreate', '#adoptS7', function(){
	if (objUser.Role.Appl=='Y') {$('.ApplE').show();}					
	formChange = false; 	
	$('#adoptS7Form').validate({
		rules: {
//			Ans47: {required: true},
//			Ans48: {required: true},
//			Ans49: {required: true},
//			Ans50: {required: true},
//			Ans51: {required: true},
//			Ans52: {required: true}			
		},
		messages: {
			Ans47: {required: "Please answer this question."},
			Ans48: {required: "Please answer this question."},			
			Ans49: {required: "Please answer this question."},			
			Ans50: {required: "Please answer this question."},			
			Ans51: {required: "Please answer this question."},			
			Ans52: {required: "Please answer this question."}			
		},
		errorPlacement: function(error, element) {
			valEle = element;
			valErr = error;
			placement = $(element);
			var ansName = $(element).attr('name');
			if (ansName.substr(0,3)=='Ans') {
				var errNo = ansName.substr(3);
				placement = $('.AnsErr'+errNo);
			}
			console.debug(placement);
			error.insertAfter(placement);
		}
	});
	$('.forms').change(function() {
		formChange = true;
	});		
	if (!objPageCreate.adoptS7) {
		objPageCreate.adoptS7= true;	
		$(document).on('click', '#adoptS7SaveBtn', function () {
			if (!$('#adoptS7Form').valid()) {
				return;
			}	
			formData = $('#adoptS7Form').serializeObject();									// Create JSON object and get rid of unneeded selects 
			var v = formData.v;
			delete formData.v;
			$.mobile.loading('show');
			$.ajax({
				url: '/assets/code/saveAdopt.php',											// Save the application  
				type: 'POST',
				data: {step:7,v:v,formData:JSON.stringify(formData)},
				dataType: 'json',
				async: true,
				success: function (result) {				
					$.mobile.loading('hide');
					$(".myNav").removeClass("ui-btn-active");
					if (result.error) {
						$('#adoptS7ErrorText').text(result.errorDetail.error_msg);
						$('#adoptS7Error').popup('open', {x: 0, y:0});					
					} else {
						listDash();						
						formChange = false;
						$('#adoptS7Success').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$.mobile.loading('hide');
					$(".myNav").removeClass("ui-btn-active");
					$('#adoptS7ErrorText').text('Network error');
					$('#adoptS7Error').popup('open');
				}
			});		
		});
	}
});
$(document).on('pagecreate', '#adoptS8', function(){
	if (objUser.Role.Appl=='Y') {$('.ApplE').show();}					
	formChange = false; 	
	$('#adoptS8Form').validate({
		groups: { Acknowl: "Ans53 Ans54 Ans55 Ans124 Ans121 Ans122 Ans123"},
		rules: {
			Ans124: {required: true},
			Ans121: {required: true},
			Ans122: {required: true},
			Ans123: {required: true},
			Ans53: {required: true},
			Ans54: {required: true},			
			Ans55: {required: true}						
		},
		messages: {
			Ans124: {required: "Please acknowledge all statements."},
			Ans121: {required: "Please acknowledge all statements."},
			Ans122: {required: "Please acknowledge all statements."},
			Ans123: {required: "Please acknowledge all statements."},
			Ans53: {required: "Please acknowledge all statements."},
			Ans54: {required: "Please acknowledge all statements."},			
			Ans55: {required: "Please acknowledge all statements."}			
		},
		errorPlacement: function(error, element) {
			valEle = element;
			valErr = error;
//			placement = $(element);
			placement = $('.S8Error');
			error.insertAfter(placement);
		}
	});
	$('.forms').change(function() {
		formChange = true;
	});		
	if (!objPageCreate.adoptS8) {
		objPageCreate.adoptS8= true;	
		$(document).on('click', '#adoptS8SaveBtn', function () {
			if (!$('#adoptS8Form').valid()) {
				return;
			}	
			formData = $('#adoptS8Form').serializeObject();									// Create JSON object and get rid of unneeded selects 
			var v = formData.v;
			delete formData.v;
			$.mobile.loading('show');
			$.ajax({
				url: '/assets/code/saveAdopt.php',											// Save the application  
				type: 'POST',
				data: {step:8,v:v,formData:JSON.stringify(formData)},
				dataType: 'json',
				async: true,
				success: function (result) {				
					$.mobile.loading('hide');
					$(".myNav").removeClass("ui-btn-active");
					if (result.error) {
						$('#adoptS8ErrorText').text(result.errorDetail.error_msg);
						$('#adoptS8Error').popup('open', {x: 0, y:0});					
					} else {
						listDash();						
						formChange = false;
						$('#adoptS8Success').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$.mobile.loading('hide');
					$(".myNav").removeClass("ui-btn-active");
					$('#adoptS8ErrorText').text('Network error');
					$('#adoptS8Error').popup('open');
				}
			});		
		});
	}
});

$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

adoptListHTML =	'<li style="padding-bottom:0.1em;padding-top:0.2em"><a href="/assets/pages/adopt.php?x={x}&l=&q=" style="padding:0.5em 1em;" data-transition="slide">'+
				'<h3 style="margin-bottom:0;">LastName, FirstName</h3>'+
				'<p style="margin-top:0;">Submitted: {date}</p>'+
				'<p>,,<br>Email:<br>Phone: (___)___-____</p>'+
				'</a></li>';
				
				
				