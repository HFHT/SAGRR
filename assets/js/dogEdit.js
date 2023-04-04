//var objDogStatus = JSON.parse('{"InProcess":{"Available":"Available","Hold-Behavior":"Hold-Behavior","Hold-Medical":"Hold-Medical","InTransit":"InTransit"},"Fostered":{"Available":"Available","Hold-Behavior":"Hold-Behavior","Hold-Medical":"Hold-Medical"},"Adopted":{"NotAvailable":"NotAvailable"},"Deceased":{"NotAvailable":"NotAvailable"}}');
var objDogStatus = JSON.parse('{}');
var objDogNormal = JSON.parse('{"InProcess":{"Available":"Available","Hold-Behavior":"Hold-Behavior","Hold-Medical":"Hold-Medical","InTransit":"InTransit"},"Fostered":{"Available":"Available","Hold-Behavior":"Hold-Behavior","Hold-Medical":"Hold-Medical"},"Match-Trial":{"NotAvailable":"NotAvailable","Hold-Behavior":"Hold-Behavior","Hold-Medical":"Hold-Medical"},"Adopted":{"NotAvailable":"NotAvailable"}}');
var objDogBridged = JSON.parse('{"InProcess":{"Bridge":"Bridge"},"Fostered":{"Bridge":"Bridge"},"Match-Trial":{"Bridge":"Bridge"},"Adopted":{"Bridge":"Bridge"}}');
var objDogNCI = JSON.parse('{"InProcess":{"NCI":"NCI"},"Fostered":{"NCI":"NCI"},"Match-Trial":{"NCI":"NCI"},"Adopted":{"NCI":"NCI"}}');
$(document).on('pagecreate', '#dogInfo', function(){
	formChange = false;
	if (objUser.Role.Dog=='Y') {$('.DogE').show();}		
	$('#DogEdit').validate({
		rules: {
			SAGRR_id: {required: true, minlength: 3},
			DogName: {required: true, minlength: 2},
			FormerName: {minlength: 2},
			Birthdate: {USDate: true},
			InTakeDate: {USDate: true},
			Weight: {range: [0, 120]},
			DMChipData: {minlength: 10},
			AlteredBy: {required: function(element) {return $("#AlteredDate").val().length > 0;},minlength: 2},
			AlteredDate: {required: function(element) {return $("#AlteredBy").val().length > 0;},USDate: true}
		},
		messages: {
			SAGRR_id: {required: "Please enter SAGRR Id", minlength: "Please enter a valid Id"},
			DogName: {required: "Please enter a dog's name", minlength: "Please enter a valid name"},
			FormerName: {minlength: "Please enter a valid name"},
			DMChipData: {minlength: "Chip# is 10 to 15 characters"}
		}
	});
	$('.forms').change(function() {
		console.debug('form class changed');
		formChange = true;
	});	
	if (!objPageCreate.dogInfo) {
		objPageCreate.dogInfo = true;
		$(document).on('vclick', '#dogSaveBtn', function () {
			var $dogForm = $('#DogEdit');
			if (!$dogForm.valid()) {
				return;
			}			
			$.ajax({
				url: '/assets/code/saveDog.php',										/* Save the dog information */
				type: 'POST',
				data: $('#DogEdit').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						$('#dogErrorText').text(result.errorDetail.error_msg);
						$('#dogError').popup('open', {x: 0, y:0});					
					} else {
						formChange = false;
						$('#dogSuccess').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$('#dogErrorText').text('Network error');
					$('#dogError').popup('open');
				}
			});		
		});
	}
});
$(document).on('pagecreate', '#dogBig', function(){
	$("#bigDogTbl").tablesorter(); 
});
$(document).on('pagecreate', '#dogStatus', function(){
	if (objUser.Role.Dog=='Y') {$('.DogE').show();}	
	$('.dogBridged a').addClass('rainbowBridgeSmall');
	$('.dogNCI a').addClass('NCIsmall');	
	$("#bigDogTbl").tablesorter(); 
});
$(document).on('pagecreate', '#dogVisit', function(){
	if (objUser.Role.Dog=='Y') {$('.DogE').show();}	
	$("#bigDogTbl").tablesorter(); 
});

$(document).on('pagecreate', '#dogList', function(){
	formChange = false; 
	if (objUser.Role.Dog=='Y') {$('.DogE').show();}	
	if (!objPageCreate.dogList) {
		objPageCreate.dogList = true;
		$(document).on('vclick', '#addDog', function () {
			console.debug('addDog');
			$.ajax({
				url: '/assets/code/addDog.php',										/* Save the dog information */
				type: 'POST',
				data: {AddNew : 'y'},
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						alert('Request to add dog failed, try again later');				
					} else {
						listDash();															/* refresh dashboard counts */						
						$('#doglist').prepend(dogListHTML.replace(/{x}/g,result.rcd).replace(/{id}/g,result.id)).listview("refresh");						
					}
				},
				error: function (request,error) {
					alert('Network error');
				}
			});
		});	
	}
});
$(document).on('pagecreate', '#dogSourceList', function(){
	if (!objPageCreate.dogSourceList) {
		objPageCreate.dogSourceList = true;
		$(document).on('vclick', '#addSource', function () {
			$.ajax({
				url: '/assets/code/addSource.php',										/* Save the dog information */
				type: 'POST',
				data: {AddNew : 'y'},
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						alert('Request to add source failed, try again later');				
					} else {
						listDash();															/* refresh dashboard counts */						
						$('#dogSource_list').prepend(sourceListHTML.replace(/{x}/g,result.data)).listview("refresh");						
					}
				},
				error: function (request,error) {
					alert('Network error');
				}
			});
		});	
	}
});
$(document).on('pagecreate', '#sourceInfo', function(){
	formChange = false;	
	$('#sourceEditForm').validate({
		rules: {
			DogS_Name: {required: true, minlength: 3},			
			DogS_Phone: {phoneUS: true},
			DogS_Contact: {minlength: 3},
			DogS_Addr: {minlength: 3},
			DogS_Email: {email: true}
		},
		messages: {
			DogS_Name: {required: "Provide a source name", minlength: "Provide a valid source name"},
			DogS_Phone: {phoneUS: "Please provide valid phone"},
			DogS_Contact: {minlength: "Provide a valid contact"},
			DogS_Addr: {minlength: "Provide a valid address"},
			DogS_Email: {email: "Please enter a valid email"}
		}
	});	
	$('.forms').change(function() {
		console.debug('form class changed');
		formChange = true;
	});
	if (!objPageCreate.sourceInfo) {
		objPageCreate.sourceInfo = true;
		$(document).on('vclick', '#sourceSaveBtn', function () {
			if (!$('#sourceEditForm').valid()) {
				return;
			}			
			console.debug($('#sourceEditForm').serialize());
			$.ajax({
				url: '/assets/code/saveSource.php',										/* Save the dog information */
				type: 'POST',
				data: $('#sourceEditForm').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						$('#sourceErrorText').text(result.errorDetail.error_msg);
						$('#sourceError').popup('open');					
					} else {
						formChange = false;
						listDash();															/* refresh dashboard counts */						
						$('#sourceSuccess').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$('#sourceErrorText').text('Network error');
					$('#sourceError').popup('open', {x: 0, y:0});
				}
			});		
		});	
	}
});
$(document).on('pagecreate', '#dogIntake', function(){
	formChange = false;
	if (objUser.Role.Dog=='Y') {$('.DogE').show();}	
	$('#dogIntake').on("pagebeforehide", function ( event, ui ) {						/* This will be deprecated in 1.6.0 */
		if (ui.nextPage[0].id == 'memSelect') {												/* Hard-coded page name 				*/
			arySavedForm = $('#DogIntakeForm').serializeArray();
		} else {
			arySavedForm = [];
		}
	});
	if (arySavedForm[0] != null) {															/* Has form data been saved previously? */
		$.each(arySavedForm, function (key, value) {										/* Restore the form with saved data		*/
			if (value.name!=='DogCurStatus') {
				var sel = '#'+value.name;
				console.debug(sel+'='+value.value);
				$(sel).val(value.value);
				$(sel).val([value.value]);
			}
		});
		$("#DogSex,#fk_ColorT_id,#fk_SourceT_id,#DMChipMfg_id").selectmenu('refresh');
		$("#Weight").slider('refresh');
		$('input[name="DogCurStatus"]').attr('checked', false).checkboxradio("refresh");	
//		$("input[name=DogCurStatus][value='"+arySavedForm[0].value+"']").attr('checked', true).checkboxradio("refresh");
		/* The following is manually setting JQM classes since a refresh doesn't seem to add the classes back correctly */
//		$("input[name=DogCurStatus][value='"+arySavedForm[0].value+"']").siblings('label').removeClass('ui-radio-off').addClass('ui-radio-on ui-btn-active');
		console.debug(arySavedForm);
		arySavedForm = [];																	/* Clear out saved form data			*/
	}
	if (memNew) {																			/* Check to see if memSelect set responsible member */
		memNew = false;
		$('#DogIntakeForm #DogCurMember').val(objMemSelect.memName);
		$('#DogIntakeForm #DogCurMemNo').val(objMemSelect.memId);
		$('#DogIntakeForm #DogCurMember').val(objMemSelect.memName);
		$('#DogIntakeForm #InTake_fk_PeopleT').val(objMemSelect.memKey);
		$('#DogIntakeForm #memSelected').text(objMemSelect.memName);
		formChange = true;
	}	
	$('#DogIntakeForm').validate({
		rules: {
			Weight: {range:[0,120]},
			DogSex: {selectcheck: true},
			DogCurStatus: {selectcheck: true},
//			fk_ColorT_id: {selectcheck: true},
			fk_SourceT_id: {selectcheck: true},			
			dogSource: {minlength: 4}
		},
		messages: {
			Weight: {range: "Please provide weight"},
			DogSex: {selectcheck: "Please select sex"},
			DogCurStatus: {selectcheck: "Please select status"},
//			fk_ColorT_id: {selectcheck: "Please select color"},
			fk_SourceT_id: {selectcheck: "Please select a source"},
			dogSource: {minlength: "Provide a valid comment"}
		}
	});	
	$('.forms').change(function() {
		console.debug('form change');
		formChange = true;
	});
	if (!objPageCreate.dogIntake) {
		objPageCreate.dogIntake = true;
		$(document).on('vclick', '#intakeSaveBtn', function () {
			$('#DogCurStatusHid').val($('input[name="DogCurStatus"]:checked').val());			
			$('#DogErrContainer').empty();													/* Clear error message							*/
			$('#DogErrContainer1').empty();													/* Clear error message							*/
			if ($('#InTake_fk_PeopleT').val().length == 0) {									/* Check to see if responsible person is set	*/
				$('#DogErrContainer1').append('<label class="error" for="InTake_fk_PeopleT">Please choose Intake person</label>').show();
				return;
			}
			if ($('#DogCurStatusHid').val().length == 0) {									/* Check to see if responsible person is set	*/
				$('#DogErrContainer1').append('<label class="error" for="DogCurStatus">Please select status</label>').show();
				return;
			}
			if (!$('#DogIntakeForm').valid()) {
				return;
			}
			$('#createBy').val(objUser.name);
			console.debug($('#DogIntakeForm').serialize());
			$.ajax({
				url: '/assets/code/saveIntake.php',										/* Save the dog information */
				type: 'POST',
				data: $('#DogIntakeForm').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						$('#dogIntakeErrorText').text(result.errorDetail.error_msg);
						$('#dogIntakeError').popup('open');					
					} else {
						formChange = false;
						$('#dogIntakeSuccess').popup( 'open', {x: 0, y:0});
						listDash();															/* refresh dashboard counts */							
						$('body').pagecontainer('change', 'dog.php?l=ic&s=i&x='+result.data, {});
					}
				},
				error: function (request,error) {
					$('#dogIntakeErrorText').text('Network error');
					$('#dogIntakeError').popup('open', {x: 0, y:0});
				}
			});		
		});	
	}
});

$(document).on('pagecreate', '#dogPage', function(){
	formChange = false;	
});
$(document).on('pagecreate', '#dogStatus', function(){
	formChange = false;	
});
$(document).on('pagecreate', '#dogVisit', function(){
	formChange = false;	
});
$(document).on('pagecreate', '#dogBeh', function(){
	formChange = false;
	if (objUser.Role.Dog=='Y') {$('.DogE').show();}
	$('#DogBehForm').validate({
		rules: {
		},
		messages: {
		}
	});	
	$('.forms').change(function() {
		console.debug('form class changed');
		formChange = true;
	});	
	if (!objPageCreate.dogBeh) {
		objPageCreate.dogBeh = true;
		$(document).on('vclick', '#dogBehSaveBtn', function () {
			if (!$('#DogBehForm').valid()) {
				return;
			}			
			$.ajax({
				url: '/assets/code/saveDogBeh.php',										/* Save the dog information */
				type: 'POST',
				data: $('#DogBehForm').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						$('#dogBehErrorText').text(result.errorDetail.error_msg);
						$('#dogBehError').popup('open', {x: 0, y:0});					
					} else {
						formChange = false;
						$('#dogBehSuccess').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$('#dogBehErrorText').text('Network error');
					$('#dogBehError').popup('open', {x: 0, y:0});
				}
			});		
		});	
	}
});
$(document).on('pagecreate', '#dogMedical', function(){
	formChange = false;	
	if (objUser.Role.Dog=='Y') {$('.DogE').show();}
	$('#DogMedicalForm').validate({
		rules: {
			MedicalClass : {required: true},
			BordetellaExpiration: {USDate: true},
			DA2PP_DHLPPExpiration: {USDate: true},
			LeptospirosisExpiration: {USDate: true},
			RabiesCounty: {required: function(element) {return $("#RabiesTag").val().length > 0;},minlength: 2},
			RabiesTag: {minlength: 6},
			RabiesExpiration: {USDate: true}
		},
		messages: {
			MedicalClass : {required: "Please select medical condition"},
			RabiesTag: {minlength: "Please enter valid tag number"},
			RabiesCounty: {minlength: "Please enter valid County"},
		},
		errorPlacement: function(error, element) {										// Perform custom placement for radio buttons for JQM
			placement = $(element);														// default to jQuery validator placement
			if ($(element).attr('name')=='MedicalClass') {								// if its a known radio button then update placement
				placement = $('.MedConErr');												// jQuery validator disrupts JQM radio button formats!!
			}
			error.insertAfter(placement);
		}
	});
	$('.forms').change(function() {
		console.debug('form class changed');
		formChange = true;
	});	
	$('#DogCurStatus').change(function(e) {
//			e.preventDefault();
//		e.stopPropagation();
		console.debug($('#DogCurStatus').val());
		if ($('#DogCurStatus').val()=='Hold-Medical') {
			$('#MedicalChange').text('placed on hold');
		} else {
			$('#MedicalChange').text('being taken off hold');
			if ($('#MedicalReleaseDate').val()=="") {
				$('#MedicalReleaseDate').val(dateForm);
			}
		}
		$('#MedChng').val($('#MedicalChange').text()); 
		$('#fk_PeopleT_id').val(objUser.fk_PeopleT_id);
		$('#createBy').val(objUser.name);
		$('#MedicalReleaseConfirm').popup('open', {x: 0, y:0});
	});	
	if (!objPageCreate.dogMedical) {
		objPageCreate.dogMedical = true;
		$(document).on('vclick', '#dogMedSaveBtn', function () {
			if (!$('#DogMedicalForm').valid()) {
				return;
			}			
			$.ajax({
				url: '/assets/code/saveDogMedical.php',										/* Save the dog information */
				type: 'POST',
				data: $('#DogMedicalForm').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						$('#dogMedicalErrorText').text(result.errorDetail.error_msg);
						$('#dogMedicalError').popup('open', {x: 0, y:0});					
					} else {
						formChange = false;
						listDash();															/* refresh dashboard counts */
						$('#dogMedicalSuccess').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$('#dogMedicalErrorText').text('Network error');
					$('#dogMedicalError').popup('open', {x: 0, y:0});
				}
			});		
		});	
	}
});
$(document).on('pagecreate', '#dogVisitEdit', function(){
	formChange = false;	
	if (objUser.Role.Dog=='Y') {$('.DogE').show();}
	$('#DogVetVisit').validate({
		rules: {
			VetDate: {required: true, USDate: true},			
			VetCost: {currency: true},
			VetFollowup: {USDate: true},
			fk_VetClinicT: {selectcheck: true}
		},
		messages: {
			VetDate: {required: "Provide date of visit"},
			fk_VetClinicT: {selectcheck: "Select a Clinic"}
		}
	});	
	$('.forms').change(function() {
		console.debug('form class changed');
		formChange = true;
	});
	if (!objPageCreate.dogVisitEdit) {
		objPageCreate.dogVisitEdit = true;
		$(document).on('vclick', '#dogVisitSaveBtn', function () {
			if (!$('#DogVetVisit').valid()) {
				return;
			}			
			console.debug($('#DogVetVisit').serialize());
			$.ajax({
				url: '/assets/code/saveDogVisit.php',										/* Save the dog information */
				type: 'POST',
				data: $('#DogVetVisit').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						$('#dogVisitErrorText').text(result.errorDetail.error_msg);
						$('#dogVisitError').popup('open');					
					} else {
						formChange = false;
						listDash();															/* refresh dashboard counts */						
						$('#VetVisit_id').val(result.data);									// Update the form with inserted/updated DB id
						$('#dogVisitSuccess').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$('#dogVisitErrorText').text('Network error');
					$('#dogVisitError').popup('open', {x: 0, y:0});
				}
			});		
		});	
	}
});
$(document).on('pagecreate', '#dogStatusEdit', function(){
//	$( "#dogStatusEdit" ).on( "pagecontainerbeforehide", function( event, ui ) {			/* For some reason this isn't firing */
//		console.debug('hide');
//	} );
	formChange = false;	
	if (objUser.Role.Dog=='Y') {$('.DogE').show();}
	$('#dogStatusEdit').on("pagebeforehide", function ( event, ui ) {						/* This will be deprecated in 1.6.0 */
		if (ui.nextPage[0].id == 'memSelect') {												/* Hard-coded page name 				*/
			arySavedForm = $('#DogStatusEditForm').serializeArray();
		} else {
			arySavedForm = [];
		}
	});
	if (arySavedForm[0] != null) {															/* Has form data been saved previously? */
		$.each(arySavedForm, function (key, value) {										/* Restore the form with saved data		*/
			var sel = '#'+value.name;
			$(sel).val(value.value);
			$(sel).val([value.value]);
		});
		DogProcStatus = arySavedForm[0].value;												/* Hard-coded based on field position on form */
		DogCurStatus = arySavedForm[1].value;
		arySavedForm = [];																	/* Clear out saved form data			*/
	}
	objDogStatus = objDogNormal;
	if (DogCurStatus=='Bridge') {
		objDogStatus = objDogBridged;
	}
	if (DogCurStatus=='NCI') {
		objDogStatus = objDogNCI;
	}	
	bldCntlGrp('#DogProcDiv','DogProcStatus',objDogStatus,DogProcStatus,localStorage.getItem("xxxMode")=='off');
	bldCntlGrp('#DogCurDiv','DogCurStatus',objDogStatus[DogProcStatus],DogCurStatus,$('#DStatusTrackT_id').val()!=='0');
	$('input[name="DogProcStatus"]').bind('change',function(event, ui) {
		bldCntlGrp('#DogCurDiv','DogCurStatus',objDogStatus[this.value],false);
		$('input[name="DogCurStatus"]').bind('change',function(event, ui) {
		});		
	});		
	if (memNew) {																			/* Check to see if memSelect set responsible member */
		memNew = false;
		$('#DogStatusEditForm #DogCurMember').val(objMemSelect.memName);
		$('#DogStatusEditForm #DogCurMemNo').val(objMemSelect.memId);
		$('#DogStatusEditForm #DogCurMember').val(objMemSelect.memName);
		$('#DogStatusEditForm #fk_PeopleT_id').val(objMemSelect.memKey);
		$('#DogStatusEditForm #memSelected').text(objMemSelect.memName);
	}
	$('#DogStatusEditForm').validate({
		rules: {
			DogProcStatus: {required: true},
			DogCurStatus: {required: true},
			StatusComment: {required: true, minlength :4},
			StatusDate: {required: true}
		},
		messages: {
			DogProcStatus: {required: "Please select primary status"},			
			DogCurStatus: {required: "Please select secondary status"},
			StatusComment: {required: "Please provide Notes", minlength : "Please provide valid Notes"},
			StatusDate: {required: "Please provide status date"}
		},
		errorLabelContainer: "#DogErrContainer"
	});
	$('.forms').change(function() {
//		console.debug('form class changed');
		formChange = true;
	});
		$('.dogNCIBridge').click(function() {
			console.debug($(this)[0].id);
			if (!$('#DogStatusEditForm').valid()) {
				return;
			}
			$('#DogNCIBridgeSel').val($(this)[0].id);										/* set NCI or Bridge */
			$('#createBy').val(objUser.name);												// Capture name of person creating status
			$.ajax({
				url: '/assets/code/saveDogStatus.php',										/* Save the dog information 					*/
				type: 'POST',
				data: $('#DogStatusEditForm').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						$('#dogStatusErrorText').text(result.errorDetail.error_msg);
						$('#dogStatusError').popup('open', {x: 0, y:0});					
					} else {
						formChange = false;
						if (result.DogNCIBridge=='dogNCI') {
							$('#dogStatusNCI').popup('open', {x: 0, y:0});							
						} else {
							$('#dogStatusBridge').popup('open', {x: 0, y:0});
						}
						listDash();															/* refresh dashboard counts */	
						/* put other cool stuff in here */
					}
				},
				error: function (request,error) {
					$('#dogStatusErrorText').text('Network error');
					$('#dogStatusError').popup('open', {x: 0, y:0});
				}
			});		
			
		});
	
	if (!objPageCreate.dogStatusEdit) {
		objPageCreate.dogStatusEdit = true;
		$('input[name="DogCurStatus"]').bind('change',function(event, ui) {
		});		
		
//		$('.my-date').datepicker();
		$(document).on('vclick', '#DogStatusHelpBtn', function() {
			$('#dogStatusHelp').popup('open', {x: 0, y:0});
		});		
		$(document).on('vclick', '#dogStatusSaveBtn', function () {
			$('#DogErrContainer').empty();													/* Clear error message							*/
			$('#DogErrContainer1').empty();													/* Clear error message							*/
//			if ($('#fk_PeopleT_id').val().length == 0) {									/* Check to see if responsible person is set	*/
//				$('#DogErrContainer1').append('<label class="error" for="fk_PeopleT_id">Please choose responsible person</label>').show();
//				return;
//			}
			if (!$('#DogStatusEditForm').valid()) {
				return;
			}			
			$('#createBy').val(objUser.name);												// Capture userid of person creating status
			$.ajax({
				url: '/assets/code/saveDogStatus.php',										/* Save the dog information 					*/
				type: 'POST',
				data: $('#DogStatusEditForm').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						$('#dogStatusErrorText').text(result.errorDetail.error_msg);
						$('#dogStatusError').popup('open', {x: 0, y:0});					
					} else {
						formChange = false;
						listDash();															/* refresh dashboard counts */						
						$('#DStatusTrackT_id').val(result.data);							// Update the form with inserted/updated DB id
						$('#dogStatusSuccess').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$('#dogStatusErrorText').text('Network error');
					$('#dogStatusError').popup('open', {x: 0, y:0});
				}
			});		
		});	
	}
});
function bldCntlGrp (CntlDiv,CntlId, CntlGrp, CntlSel, CntlDisable) {
	Cntlhtml = ['<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" class="{class}">',
			'<input name="{class}" id="{class}{i}" value="{key}" {checked} {disabled} type="radio"><label for="{class}{i}" class="my-radio">{key}</label>',
			'</fieldset>'];
	strGrp = Cntlhtml[0].replace(/{class}/g,CntlId);
	i=0; 
	$.each(CntlGrp, function (key, value) {
		strDisable='';
		if (CntlSel == key) {
			strchecked = 'checked="checked"';
		} else {
			strchecked = '';
			if (CntlDisable) {strDisable="disabled";}
		}
		strGrp = strGrp + Cntlhtml[1].replace(/{class}/g,CntlId).replace(/{i}/g,i).replace(/{key}/g,key).replace(/{value}/g,value).replace(/{checked}/g,strchecked).replace(/{disabled}/g,strDisable);
		i++;
	});
	strGrp = strGrp + Cntlhtml[2];
	$(CntlDiv).empty().append(strGrp).trigger('create');
}

 
dogListHTML =	'<li style="padding-bottom:0.1em;padding-top:0.2em">'+
				'<a href="/assets/pages/dog.php?x={x}&l={l}&q={q}&s={s}" style="padding-bottom:0;padding-top:0" data-transition="slide">'+
				'<img src="/assets/img/grstock.png"/>'+
				'<h3>New Dog&nbsp;&nbsp; #: {id}</h3>'+
				'<p>Status: New : New'+
				'<br>Sex: Unknown&nbsp;&nbsp;Color: Unknown'+
				'</p></a></li>';

sourceListHTML ='<li>'+
				'<a href="/assets/pages/dogSource.php?x={x}&l=&q=" style="padding-bottom:0.7em;padding-top:0" data-transition="slide">'+
				'<h3>New Source</h3>'+
				'<p>Contact: '+
				'<br>Email: '+
				'<br>Phone: (___)___-____'+
				'<br>'+
				'</p></a></li>';
				