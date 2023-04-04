// Example of having a skeleton HTML section and using replace to fill it in
function loadDogList() {
	var htmlDogList = 	'<li style="padding-bottom:0.1em;padding-top:0.2em">' +
		'<a href="/assets/pages/dog.php?x={DogT_id}" style="padding-bottom:0;padding-top:0" data-id="{row}" data-transition="slide">' +
		'<img src="{DogPhotoLink}"/>' +
		'<h3>{DogName}&nbsp;&nbsp; #: {SAGRR_id}</h3>' +
		'<p>Status: {DogProcStatus} : {DogCurStatus}'+
		'<br>Sex: {DogSex}&nbsp;&nbsp;Color: {fk_ColorT_id}'+
		'</p></a></li>';		
	var newRowContent = '';		
	$.each(objDogList, function(row, str) {
		newRowContent = newRowContent + htmlDogList.replace(/{DogT_id}/g,str.DogT_id).replace(/{row}/g,row).replace(/{DogPhotoLink}/g,str.DogPhotoLink).replace(/{DogName}/g,str.DogName).replace(/{SAGRR_id}/g,str.SAGRR_id).replace(/{DogSex}/g,nullString(str.DogSex)).replace(/{DogProcStatus}/g,nullString(str.DogProcStatus)).replace(/{DogCurStatus}/g,nullString(str.DogCurStatus)).replace(/{fk_ColorT_id}/g,nullString(str.fk_ColorT_id));
	});
	$('#dog_list').empty().append(newRowContent).listview('refresh');        		
}	

// Example of reading a data property from a DOM element such as data-dogkey=4
	$(document).on('vclick', '.yesDog', function() {
		console.debug($(this).data("dogkey"));
	});

// Example of cloning a piece of html and appending it
htmlPeople = $('#htmlPeople').clone();							// Locate the piece of code via it's ID
htmlPeople.attr('id','');										// Clear the id so we won't have two of the same in the DOM
htmlTemp = htmlPeople.clone();									// Create a copy ?
$('#tblPerson').append(htmlTemp);								// Append the copy to a table (in this case)

// Example of setting local storage
localStorage.setItem('userInfo',objUser);

// Example of pushing an object into an array 
var person =[];
person.push({age: 35, name: 'John Doe'});
person.push({age: 26, name: 'Jane Doe'});
person.push({age: 45, name: 'James Doe'});


// Example of an html Image factory, this will pump out an array of <img src='' alt='' title=''> elements
// https://en.wikipedia.org/wiki/Factory_method_pattern
var createImage = function (src,title) {
	var img = new Image();
	img.src = src;
	img.alt = title;
	img.title = title;
	return img;
};
var images = [];
images.push(createImage(reader.result,'Title'));

// The following jQuery finds a specific tr (n) and then the first td on that row and then appends an uploaded image to it.
$($($('#mediaTbl').find('tr')[n]).find('td')).append(images[0].src)


// Example of a jQuery extension
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

$.fn.serializeObject = function()																		// Form Data into a JSON object
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