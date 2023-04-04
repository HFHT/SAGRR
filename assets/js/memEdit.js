$(document).on('pagecreate', '#memberPage', function(){
	if (objUser.Role.Mem=='N') {$('.memChecks').attr('disabled', true);}					
	formChange = false; 
	$('#memChecks').change(function() {
		$.ajax({
			url: '/assets/code/saveMemMembership.php',										/* Save the member information */
			type: 'POST',
			data: $('#memChecks').serialize(),
			dataType: 'json',
			async: true,
			success: function (result) {
				if (result.error) {
					alert(result.errorDetail.error_msg);
				} else {
					listDash();
				}
			},
			error: function (request,error) {
				alert('Network error');
			}
		});				
	});
});
$(document).on('pagecreate', '#memContact', function(){
	formChange = false; 
	if (objUser.Role.Mem=='Y' || objUser.Role.Dog=='Y' || objUser.Role.Appl=='Y') {$('.MemE').show();}		
});

$(document).on('pagecreate', '#memInfo', function(){
	formChange = false; 
	if (objUser.Role.Mem=='Y') {$('.MemE').show();}		
	$('#MemEdit').validate({
		rules: {
			Member_id: {required: true, number: true},
			LastName: {required: true, minlength: 2},
			Phone: {phoneUS: true},
			FirstName: {required: true, minlength: 2},
			MemberSince: {USDate: true},
			MemberRenewed: {USDate: true},
			MailCode: {zipcode: true},
			DMChipData: {minlength: 12},
			AlteredBy: {required: function(element) {return $("#AlteredDate").val().length > 0;},minlength: 2},
			AlteredDate: {required: function(element) {return $("#AlteredBy").val().length > 0;},USDate: true}
		},
		messages: {
			Member_id: {required: "Please enter Member Id", number: "Please enter a valid Id"},
			LastName: {required: "Please enter member name", minlength: "Please enter a valid name"},
			FirstName: {required: "Please enter member name", minlength: "Please enter a valid name"},
			Email: {email: "Please enter a valid email"},
			Phone: {required: "Please provide phone no.", phoneUS: "Please provide valid phone"}
		}
	});
	$('.forms').change(function() {
		formChange = true;
	});	
	if (!objPageCreate.memInfo) {
		objPageCreate.memInfo = true;
		$(document).on('vclick', '#memSaveBtn', function () {
			if (!$('#MemEdit').valid()) {
				return;
			}			
			$.ajax({
				url: '/assets/code/saveMember.php',										/* Save the member information */
				type: 'POST',
				data: $('#MemEdit').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {
					if (result.error) {
						$('#memErrorText').text(result.errorDetail.error_msg);
						$('#memError').popup('open', {x: 0, y:0});					
					} else {
						listDash();						
						formChange = false;
						$('#memSuccess').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$('#memErrorText').text('Network error');
					$('#memError').popup('open');
				}
			});		
		});
	}
});
$(document).on('pagecreate', '#memberList', function(){
	formChange = false;
	if (objUser.Role.Mem=='Y') {$('.MemE').show();} 	
	if (!objPageCreate.memberList) {
		objPageCreate.memberList = true;
		$(document).on('vclick', '#addMem', function () {
//			console.debug('addMem');
			$.ajax({
				url: '/assets/code/addMem.php',										/* Save the dog information */
				type: 'POST',
				data: {AddNew : 'y'},
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						alert('Request to add member failed, try again later');				
					} else {
						listDash();															/* refresh dashboard counts */						
						$('#member_list').prepend(memListHTML.replace(/{x}/g,result.data)).listview("refresh");						
					}
				},
				error: function (request,error) {
					$(".myNav").removeClass("ui-btn-active");
					alert('Network error');
				}
			});
		});	
	}
});

$(document).on('pagecreate', '#memSkills', function(){
	formChange = false;
	if (objUser.Role.Mem=='Y') {$('.MemE').show();}	
	$('#MemSkillsForm').validate({
		rules: {
		},
		messages: {
		}
	});	
	$('.forms').change(function() {
		formChange = true;
	});	
	if (!objPageCreate.memSkills) {
		objPageCreate.memSkills = true;
		$(document).on('vclick', '#memSkillSaveBtn', function () {
//			console.debug(this);
			if (!$('#MemSkillsForm').valid()) {
				return;
			}			
			$.ajax({
				url: '/assets/code/saveMemSkills.php',										/* Save the member information */
				type: 'POST',
				data: $('#MemSkillsForm').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						$('#memSkillErrorText').text(result.errorDetail.error_msg);
						$('#memSkillError').popup('open', {x: 0, y:0});					
					} else {
						formChange = false;
						$('#memSkillSuccess').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$('#memSkillErrorText').text('Network error');
					$('#memSkillError').popup('open');
				}
			});		
		});
	}
});
$(document).on('pagecreate', '#memInterest', function(){
	formChange = false; 
	if (objUser.Role.Mem=='Y') {$('.MemE').show();}	
	$('#memInterestForm').validate({
		rules: {
		},
		messages: {
		}
	});
	$('.forms').change(function() {
		formChange = true;
	});	
	if (!objPageCreate.memInterest) {
		objPageCreate.memInterest = true;
		$(document).on('vclick', '#memInterestSaveBtn', function () {
//			console.debug(this);
			if (!$('#memInterestForm').valid()) {
				return;
			}			
			$.ajax({
				url: '/assets/code/saveMemInterests.php',										/* Save the member information */
				type: 'POST',
				data: $('#memInterestForm').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						$('#memInterestErrorText').text(result.errorDetail.error_msg);
						$('#memInterestError').popup('open', {x: 0, y:0});					
					} else {
						formChange = false;
						listDash();															/* refresh dashboard counts */						
						$('#memInterestSuccess').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$('#memInterestErrorText').text('Network error');
					$('#memInterestError').popup('open');
				}
			});		
		});
	}
});
$(document).on('pagecreate', '#memCompany', function(){
	formChange = false; 
	if (objUser.Role.Mem=='Y') {$('.MemE').show();}
	$('#MemCompanyForm').validate({
		rules: {
			Company :{required: true, minlength: 2},
			WorkPhone: {phoneUS: true}
		},
		messages: {
			Company: {required: "Please provide company", minlength: "Please enter valid company"},
			Email: {email: "Please enter a valid email"},
			Phone: {phoneUS: "Please provide valid phone"}			
		}
	});
	$('.forms').change(function() {
		formChange = true;
	});	
	if (!objPageCreate.memCompany) {
		objPageCreate.memCompany = true;
		$(document).on('vclick', '#memCompanySaveBtn', function () {
//			console.debug(this);
			if (!$('#MemCompanyForm').valid()) {
				return;
			}			
			$.ajax({
				url: '/assets/code/saveMemCompany.php',										/* Save the member information */
				type: 'POST',
				data: $('#MemCompanyForm').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						$('#memCompanyErrorText').text(result.errorDetail.error_msg);
						$('#memCompanyError').popup('open', {x: 0, y:0});					
					} else {
						formChange = false;
						$('#memCompanySuccess').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$('#memCompanyErrorText').text('Network error');
					$('#memCompanyError').popup('open');
				}
			});		
		});
	}
});
$(document).on('pagecreate', '#memMembership', function(){
	formChange = false; 
	if (objUser.Role.Mem=='Y') {$('.MemE').show();}	else {$('.hideMemBtn').addClass('ui-state-disabled');}
	$('#MemMembershipForm').validate({
		rules: {
			Membership :{required: true},
			MemberLevel: {required: true},
			MemberSince: {USDate: true, required: true}
		},
		messages: {
			Membership: {required: "Please select Membership"},
			MemberLevel: {required: "Please select Membership Level"},
			MemberSince: {required: "Please provide Member Since"}			
		}
	});
	$('.forms').change(function() {
		formChange = true;
	});	
	$('#memMembershipRenew').click(function() {
			$('#memRenewConfirm').popup('open', {x: 0, y:0});						
	});		
	$('#memMembershipInactive').click(function() {
			$('#memInactiveConfirm').popup('open', {x: 0, y:0});					
	});		
	
	if (!objPageCreate.memMembership) {
		objPageCreate.memMembership = true;
		$(document).on('vclick', '#memRenewBtn', function () {
			var diffdays = ((Date.parse(dateForm) - Date.parse($('#MemberRenewed').val()))/(oneDay));
			console.debug('diffdays:',diffdays);
			if (diffdays > 200 || isNaN(diffdays)) {																/* If the form has an old date, set to today */
				$('#MemberRenewed').val(dateForm);
			}		
			$('#memHistory').val('Y');															/* Instruct script to make History record */
			$('#mBy_PeopleT_id').val(objUser.fk_PeopleT_id);
			$('#Membership').val('Active');
			$('#mAction').val('Renewal');
			if ($('#MemCount').val()==0) { $('#MemCount').val(1); }
			$.ajax({
				url: '/assets/code/saveMemMembership.php',										/* Clone Applicant into Member */
				type: 'POST',
				data: $('#MemMembershipForm').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {
					if (result.error) {
						alert(result.errorDetail.error_msg);
				} else {
					$('#Membership, #MemCount').selectmenu('refresh');
					$('#memHistoryList').prepend('<tr><td>'+$('#MemberRenewed').val()+'</td><td style="text-align:center">'+$('#Membership').val()+'</td><td style="text-align:center">'+$('#MemberLevel').val()+'</td><td style="text-align:center">'+$('#MemCount').val()+'</td><td>'+$('#mAction').val()+'</td></tr>');
					$('#memHistory_list').show();
					$('#hideMemRenew').hide();
					$('#memRenewConfirm').popup('close');
					formChange = false;
//						$( ":mobile-pagecontainer" ).pagecontainer( "change", "/assets/pages/memMembership.php?x="+$('#memMembership').data('memid')+"&l=&q=", { reload: true } );
//						console.debug('page reload');
					}
				},
				error: function (request,error) {
					alert('Network error');
				}
			});								
		});
		$(document).on('vclick', '#memInactiveBtn', function () {
			var diffdays = ((Date.parse(dateForm) - Date.parse($('#MemberRenewed').val()))/(oneDay));
			console.debug('diffdays:',diffdays);
			if (diffdays > 14 || isNaN(diffdays)) {																/* If the form has an old date, set to today */
				$('#MemberRenewed').val(dateForm);
			}		
			$('#memHistory').val('Y');															/* Instruct script to make History record */
			$('#mBy_PeopleT_id').val(objUser.fk_PeopleT_id);
			$('#Membership').val('Inactive');
			$('#mAction').val('Deactivate');
			$.ajax({
				url: '/assets/code/saveMemMembership.php',										/* Clone Applicant into Member */
				type: 'POST',
				data: $('#MemMembershipForm').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {
					if (result.error) {
						alert(result.errorDetail.error_msg);
				} else {
					$('#Membership, #MemCount').selectmenu('refresh');
					$('#memHistoryList').prepend('<tr><td>'+$('#MemberRenewed').val()+'</td><td style="text-align:center">'+$('#Membership').val()+'</td><td style="text-align:center">'+$('#MemberLevel').val()+'</td><td style="text-align:center">'+$('#MemCount').val()+'</td><td>'+$('#mAction').val()+'</td></tr>');
					$('#memHistory_list').show();
					$('#hideMemInactive').hide();					
					$('#memInactiveConfirm').popup('close');
					formChange = false;
//						$( ":mobile-pagecontainer" ).pagecontainer( "change", "/assets/pages/memMembership.php?x="+$('#memMembership').data('memid')+"&l=&q=", { reload: true } );
//						console.debug('page reload');
					}
				},
				error: function (request,error) {
					alert('Network error');
				}
			});								
		});

		$(document).on('vclick', '#memMembershipSaveBtn', function () {
//			console.debug(this);
			if (!$('#MemMembershipForm').valid()) {
				return;
			}			
			$.ajax({
				url: '/assets/code/saveMemMembership.php',										/* Save the member information */
				type: 'POST',
				data: $('#MemMembershipForm').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						$('#memMembershipErrorText').text(result.errorDetail.error_msg);
						$('#memMembershipError').popup('open', {x: 0, y:0});					
					} else {
						formChange = false;
						listDash();															/* refresh dashboard counts */						
						$('#memMembershipSuccess').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$('#memMembershipErrorText').text('Network error');
					$('#memMembershipError').popup('open');
				}
			});		
		});
	}
});
$(document).on('pagecreate', '#memContactEdit', function(){
	formChange = false; 
	if (objUser.Role.Mem=='Y' || objUser.Role.Dog=='Y' || objUser.Role.Appl=='Y') {$('.MemE').show();}	
	$('#memContactForm').validate({
		rules: {
			logDate :{USDate: true, required: true},
			logText: {required: true}
		},
		messages: {
			logDate: {required: "Please enter log date"},
			logText: {required: "Please provide log entry"},
		}
	});
	$('.forms').change(function() {
		formChange = true;
	});	
	if (!objPageCreate.memContactEdit) {
		objPageCreate.memContactEdit = true;
		$(document).on('vclick', '#memContactSaveBtn', function () {
//			console.debug(this);
			if (!$('#memContactForm').valid()) {
				return;
			}
			$('#logBy').val(objUser.name);
			$.ajax({
				url: '/assets/code/saveMemContact.php',										/* Save the member information */
				type: 'POST',
				data: $('#memContactForm').serialize(),
				dataType: 'json',
				async: true,
				success: function (result) {				
					if (result.error) {
						$('#memContactErrorText').text(result.errorDetail.error_msg);
						$('#memContactError').popup('open', {x: 0, y:0});					
					} else {
						formChange = false;
						listDash();															/* refresh dashboard counts */						
						$('#logid').val(result.data);										// Update the form with inserted/updated DB id
						$('#memContactSuccess').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$('#memContactErrorText').text('Network error');
					$('#memContactError').popup('open');
				}
			});		
		});
	}
});
$(document).on('pagecreate', '#memberBig', function(){
	$("#bigMemTbl").tablesorter(); 
});

memListHTML =	'<li style="padding-bottom:0.1em;padding-top:0.2em">'+
				'<a href="/assets/pages/member.php?x={x}&l={l}&q={q}" style="padding-bottom:0;padding-top:0" data-transition="slide">'+
				'<img src="/assets/img/person.jpg"/>'+
				'<h3>LastName, FirstName&nbsp; #: 000-000</h3>'+
				'<p>Membership: Pending&nbsp;:&nbsp;General'+
				'<br>Company:'+ 
				'</p></a></li>';
