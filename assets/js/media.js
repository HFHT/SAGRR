var dropEvt = {};
//var dropFiles = [];
//var formData = new FormData();
$(document).on('pagecreate', '#media', function(){
	console.debug(mediamode);
	formChange = false;
	formData = new FormData();
	document.getElementById('files').addEventListener('change', handleFileSelect, false);			// create a listener for files input element
	// Setup the dnd listeners.
	var dropZone = document.getElementById('drop_zone');											// create listeners for drag and drop files
	dropZone.addEventListener('dragover', handleDragOver, false);
	dropZone.addEventListener('drop', handleDropSelect, false);
	$('.forms').change(function() {
		formChange = true;
	});	
	if (!objPageCreate.media) {
		objPageCreate.media = true;	
		$(document).on('click', '#mediaSaveBtn', function () {
			// form validation
			$('#mediaForm .myvalerr').remove();
			submitform = true;
			$.each($('#mediaForm input[type=text]'), function (key, value) {
				if (!($(value).val())) {
					submitform = false;
					$('<div class="myvalerr"><label class="error">Please provide caption.</label></div>').insertAfter($(value));
				}
			});
			$.each($('#mediaForm textarea'), function (key, value) {
				if (!($(value).val())) {
					submitform = false;
					$('<div class="myvalerr"><label class="error">Please provide description.</label></div>').insertAfter($(value));					
				}				
			});
			$.each($('#mediaForm select'), function (key, value) {
				if ($(value).val()==0) {
					submitform = false;
					$('<div class="myvalerr"><label class="error">Please select type.</label></div>').insertAfter($(value));					
				}
			});
			$(".myNav").removeClass("ui-btn-active");
			if (!submitform) {return;}
			// add all form elements to form object
			$.each($('#mediaForm').serializeArray(), function (key, value) {
				formData.append(value.name, value.value);
			});
			formData.append('Uploaded_by',objUser.userid);
			$.mobile.loading('show');			
			$.ajax({
				url: '/assets/code/saveMedia.php',											// Save the media  
				type: 'POST',
				data: formData,
				cache: false,
				dataType: 'json',
				processData: false,
				contentType: false,
//				async: true,
				success: function (result) {	
					$.mobile.loading('hide');							
					$(".myNav").removeClass("ui-btn-active");
					console.debug(result);
					if (result.error) {
						$('#mediaErrorText').text(result.errorDetail.error_msg);
						$('#mediaError').popup('open', {x: 0, y:0});					
					} else {
						formChange = false;
						$('#mediaList').empty();
						$('#mediaList').listview("refresh");		
						formData = new FormData();												// Clear out form data to prepare for next upload.						
						$('#mediaSuccess').popup( 'open', {x: 0, y:0});
					}
				},
				error: function (request,error) {
					$.mobile.loading('hide');			
					$(".myNav").removeClass("ui-btn-active");
					$('#mediaErrorText').text('Network error');
					$('#mediaError').popup('open', {x: 0, y:0});
				}
			});		
		});
	}
});

function handleFileSelect(evt) {																	// handles FileList from input element
	var files = evt.target.files; // FileList object
	dropEvt = evt;	
//	dropFiles = evt.target.files;
	// At this point I have the selected files !!!!
	handleFiles(files);
}
function handleDragOver(evt) {																		// handles the drag event
	evt.stopPropagation();
	evt.preventDefault();
	evt.dataTransfer.dropEffect = 'copy'; // Explicitly show this is a copy.
}
function handleDropSelect(evt) {																	// handles FileList from drop event
	evt.stopPropagation();
	evt.preventDefault();
	dropEvt = evt;
//	dropFiles = evt.dataTransfer.files;	
	var files = evt.dataTransfer.files; // FileList object.
	// At this point I have the selected files !!!!
//	if (files.length>1 && (mediamode=='p')) {														//!!!! Need to fix bug with multiple file upload
	if (files.length>1) {	
		$('#filecount').popup('open');
		return;
	}
	handleFiles(files);
}
function handleFiles(files) {																		// processes the FileList
	// Loop through the FileList, check for accepted files, create a handler for when the upload completes, upload file using readAsDataURL
	$.each(files, function(key, value) {
		formData.append(key, value);
	});
	for (var i = 0, f; f = files[i]; i++) {
		if (!acceptedFiles.filter(function ( obj ) {return obj.type=== f.type;})[0]) {
			$('#mediaList').append(xmediaHtml.replace(/{file}/g,f.name).replace(/{msg}/g,'Files of this file type are not accepted for upload to this site'));
			$('#mediaList').listview("refresh");		
			continue;
		}
		if (f.size>6100000) {
			$('#mediaList').append(xmediaHtml.replace(/{file}/g,f.name).replace(/{msg}/g,'Files greater than 6MB are not accepted for upload to this site'));
			$('#mediaList').listview("refresh");
			continue;
		}
		formChange = true; 	
		var reader = new FileReader();
		reader.onload = createHandler(f,i);											// Setup an onload handler for the readAsDataURL
		reader.readAsDataURL(f);													// Read the image data, when complete the onload handler fires
	}	
}	

// Create an HTML img along with its title and alt attributes
var createImage = function (src,title) {
	var img = new Image();
	img.src = src;
	img.alt = title;
	img.title = title;
	img.width = 80;
	return img;
};
// Create an onLoad handler for when the readAsDataURL completion event fires we can store the image data and its file information.
var createHandler = function (currentFile,idx) {
	return function onLoad(e) {																				// onLoad handler
		imgPlaceHolder = acceptedFiles.filter(function ( obj ) {return obj.type=== currentFile.type;})[0];
		console.debug(imgPlaceHolder);
		$('#mediaList').append(mediaHtml.replace(/{file}/g,currentFile.name).replace(/{date}/g,currentFile.lastModifiedDate.toLocaleDateString()).replace(/{thumb}/g,imgPlaceHolder.show));
		if (imgPlaceHolder.show=='') {
			document.getElementById('mediaImg').appendChild(createImage(e.srcElement.result,currentFile.name));
		} else {
			document.getElementById('mediaImg').appendChild(createImage('/assets/img/'+imgPlaceHolder.show+'.png','alt'));
		}
		if (mediamode=='p') {
			$('#attachType').find('option').remove().end().append('<option value="Profile">Profile</option>').val('Profile');
		}
		$('#mediaImg').removeAttr('id');
		$('#mediaList').listview("refresh");
	};
};

mediaHtml =	'<li style="padding-bottom:0.1em;padding-top:0.2em">'+
			'<div class="ui-grid-a">'+
			'<div class="ui-block-a" style="width:30%"><div class="ui-bar ui-bar-a" id="mediaImg" style="padding-bottom:0;"></div></div>'+
			'<div class="ui-block-b" style="width:70%"><div class="ui-bar ui-bar-a">'+
			'<p style="font-size:1em;margin:2px;">{file}&nbsp;&nbsp;{date}</p>'+
			'<select name="attachType[]" id="attachType" data-mini="true" dir="ltr" style="margin:2px;">'+
			'<option value="0">-Select Type-</option><option value="Photo">Photo</option><option value="Document">Document</option><option value="XRay">XRay</option><option value="Invoice">Invoice</option>'+
			'</select><input name="Caption[]" id="Caption" type="text" placeholder="Caption.." style="display:flex;margin:2px;"/></div>'+
			'</div></div>'+
			'</div>'+
			'<div class="ui-grid-solo">'+
			'<div class="ui-block-a"><div class="ui-bar ui-bar-a"><textarea name="FileDesc[]" id="FileDesc" data-mini="true" placeholder="Description.." style="width:95%;"></textarea></div></div>'+
			'</div>'+
			'</li><input type="hidden" name="filedate[]" value="{date}"/><input type="hidden" name="thumbfile[]" value="{thumb}"/>';
xmediaHtml ='<li style="padding-bottom:0.1em;padding-top:0.2em">'+
			'<div class="ui-grid-a">'+
			'<div class="ui-block-a" style="width:30%"><div class="ui-bar ui-bar-a" style="padding-bottom:0;"><img src="/assets/img/bad80.png"></div></div>'+
			'<div class="ui-block-b" style="width:70%"><div class="ui-bar ui-bar-a">'+
			'<p style="font-size:1em;margin:2px;white-space:pre-line;">{file}</p><p style="font-size:0.9em;white-space:pre-line;">{msg}!</p>'+
			'</div>'+
			'</div></div>'+
			'</div>'+
			'</li>';

acceptedFiles = [	{type:'image/png',show:''},
					{type:'image/gif',show:''},
					{type:'image/jpeg',show:''},
					{type:'image/pjpeg',show:''},
					{type:'image/x-ms-bmp',show:''},
					{type:'text/plain',show:'doc80'},
					{type:'text/html',show:'doc80'},
					{type:'application/rtf',show:'doc80'},
					{type:'application/x-zip-compressed',show:'zip80'},
					{type:'application/pdf',show:'pdf80'},
					{type:'application/msword',show:'doc80'},
					{type:'application/vnd.ms-excel',show:'xls80'},
					{type:'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',show:'xls80'},
					{type:'application/vnd.ms-powerpoint',show:'ppt80'},
					{type:'application/vnd.openxmlformats-officedocument.presentationml.presentation',show:'ppt80'},
					{type:'application/vnd.openxmlformats-officedocument.presentationml.slide',show:'ppt80'},
					{type:'application/vnd.openxmlformats-officedocument.wordprocessingml.document',show:'doc80'},
					{type:'video/mp4',show:'vid80'},
					{type:'video/mpeg',show:'vid80'},					
					{type:'audio/aac',show:'aud80'},
					{type:'audio/mp3',show:'aud80'},
					{type:'audio/mp4',show:'aud80'},
					{type:'audio/x-m4a',show:'aud80'},
					{type:'audio/wav',show:'aud80'},			
					{type:'audio/x-ms-wma',show:'aud80'},				
					{type:'audio/mpeg',show:'aud80'}];
