<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
include_once '../inc/psl-config.php';
 
$error_msg = '';
$error_str = '';
$error_sql = '';
$data = array();
//print_r($_POST);
//print_r($_GET);
//print_r($_FILES);

try {
	if (!isset($_FILES[0]['error'])) {
		throw new RuntimeException('No files were sent for upload to server.');
		}
    $error = false;
    $files = array();

    $uploaddir = '../../uploads/files/';
	if ($_POST['mediamode']=='member' || $_POST['mediamode']=='dog') {
		$uploadthumb = '../../uploads/profile/';
		define ("WIDTH","80");
		define ("HEIGHT","80");			
	} else {
		$uploadthumb = '../../uploads/thumbs/';
		define ("WIDTH","128");
		define ("HEIGHT","128");		
	}	
	$i=0;
    foreach($_FILES as $file) {
		if ($file['error'] == 0) {
			if ($file['size'] > 6100000) {
				throw new RuntimeException('File exceeded max file size');
			}
			$finfo = new finfo(FILEINFO_MIME_TYPE);											// Double check MIME type, don't trust [type] 
			if (false === $ext = array_search(
				$finfo->file($file['tmp_name']),
					array(
							'image/png',
							'image/gif',
							'image/jpeg',
							'image/pjpeg',
							'image/x-ms-bmp',
							'text/plain',
							'text/html',
							'application/rtf',
							'application/x-zip-compressed',
							'application/pdf',
							'application/msword',
							'application/vnd.ms-excel',
							'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
							'application/vnd.openxmlformats-officedocument.presentationml.presentation',
							'application/vnd.ms-powerpoint',
							'application/vnd.openxmlformats-officedocument.presentationml.slide',
							'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
							'video/mp4',
							'video/mpeg',
							'audio/aac',
							'audio/mp3',
							'audio/mp4',
							'audio/x-m4a',
							'audio/wav',
							'audio/x-ms-wma',
							'audio/mpeg'
					),
				true)) {
				throw new RuntimeException('File Type not supported for upload to site!');
				}
//			if(move_uploaded_file($file['tmp_name'], $uploaddir .basename($file['name']))) {
			$newFile = uniqid('FN') .'.' .getExtension($file['name']);
			if(move_uploaded_file($file['tmp_name'], $uploaddir .$newFile)) {
				$files[] = $uploaddir .$file['name'];
				$ext=getExtension($newFile);
//				var_dump($ext);
				if ((!strcasecmp("jpg",$ext) || !strcasecmp("jpeg",$ext) || !strcasecmp("gif",$ext))) {
					image_fix_orientation($uploaddir .$newFile);
				}				
				if ($_POST['thumbfile'][$i]=='') {
					$ThumbName = make_thumb($uploaddir .$newFile,$uploadthumb .$newFile,WIDTH,HEIGHT);					
				} else {
					$ThumbName = '/assets/img/'.$_POST['thumbfile'][$i].'.png';					
				}
				array_push($data,array('origname'=>$file['name'],'newname'=>$newFile,'type'=>$file['type'],'size'=>$file['size']));	
				$Fk_PeopleT_id = $_POST['Fk_PeopleT_id']=='' ? '0' : $_POST['Fk_PeopleT_id'];
				$Fk_Dog_id = $_POST['Fk_Dog_id']=='' ? '0' : $_POST['Fk_Dog_id'];;
				$Fk_Vet_id = $_POST['Fk_Vet_id']=='' ? '0' : $_POST['Fk_Vet_id'];;
				$Fk_VetVisit_id = $_POST['Fk_VetVisit_id']=='' ? '0' : $_POST['Fk_VetVisit_id'];;
				$fk_applid = $_POST['fk_applid']=='' ? '0' : $_POST['fk_applid'];;
				$Caption = filter_var($_POST['Caption'][$i], FILTER_SANITIZE_STRING);
				$FileDesc = filter_var($_POST['FileDesc'][$i], FILTER_SANITIZE_STRING);
				$attachType = $_POST['attachType'][$i];
				$Uploaded_by = $_POST['Uploaded_by'];
				$filedate = date('Y-m-d', strtotime(str_replace('-', '/', $_POST['filedate'][$i])));
				$newFile = $uploaddir .$newFile;
				$filename = $file['name'];
				$filetype = $file['type'];
				$prep_stmt = "INSERT INTO FileAsset (Fk_PeopleT_id,Fk_Dog_id,Fk_Vet_id,Fk_VetVisit_id,fk_applid,Caption,FileDesc,Uploaded_by,FileName,ThumbName,OriginalFileName,MIME_Type,FileOrigDate,attachType,Deleted) VALUES ($Fk_PeopleT_id,$Fk_Dog_id,$Fk_Vet_id,$Fk_VetVisit_id,$fk_applid,'$Caption','$FileDesc','$Uploaded_by','$newFile','$ThumbName','$filename','$filetype','$filedate','$attachType','N')";		
				$error_sql = $prep_stmt;
				if ($result = $mysqli->query($prep_stmt)) {
					if ($_POST['mediamode']=='dog') {
						$prep_stmt = "UPDATE DogT SET DogPhotoLink='$ThumbName' WHERE DogT_id=$Fk_Dog_id";
						if ($result = $mysqli->query($prep_stmt)) {
						} else {
							$error_sql = $prep_stmt;
							$error_str = $mysqli->error;
							throw new RuntimeException('Update of Dog record failed.');	
						}
					}
					if ($_POST['mediamode']=='member') {
						$prep_stmt = "UPDATE PeopleT SET PhotoLink='$ThumbName' WHERE PeopleT_id=$Fk_PeopleT_id";
						if ($result = $mysqli->query($prep_stmt)) {
						} else {
							$error_sql = $prep_stmt;
							$error_str = $mysqli->error;
							throw new RuntimeException('Update of Member record failed.');
						}
					}
				} else {
					$error_sql = $prep_stmt;
					$error_str = $mysqli->error;
					throw new RuntimeException('Database Error saving file information.');
				}								
			} else {
				throw new RuntimeException('File upload error on Move');
			}
		} else {
			throw new RuntimeException('File upload error = '.$file['error']);
		}
		$i++;
    }

} catch (RuntimeException $e) {
	$error_msg = $e->getMessage();
}

$results = array(
'post' => $_POST,
'error' => (! empty($error_msg)),
'errorDetail' => array(
	'error_sql' => $error_sql,
	'error_str' => $error_str,
	'error_msg' => $error_msg),
'data' =>  $data);
echo json_encode($results);
//$mysqli->close();

// this is the function that will create the thumbnail image from the uploaded image
// the resize will be done considering the width and height defined, but without deforming the image
function make_thumb($img_name,$filename,$new_w,$new_h) {
	//get image extension.
	$ext=getExtension($img_name);
	if (!(!strcasecmp("jpg",$ext) || !strcasecmp("jpeg",$ext) || !strcasecmp("png",$ext) || !strcasecmp("gif",$ext))) {
		return $ext;
	}
	//creates the new image using the appropriate function from gd library
	if(!strcasecmp("jpg",$ext) || !strcasecmp("jpeg",$ext))
		$src_img=imagecreatefromjpeg($img_name);

	if(!strcasecmp("png",$ext))
		$src_img=imagecreatefrompng($img_name);
	
	if(!strcasecmp("gif",$ext))
		$src_img=imagecreatefromgif($img_name);

	//gets the dimmensions of the image
	$old_x=imageSX($src_img);
	$old_y=imageSY($src_img);

	// next we will calculate the new dimmensions for the thumbnail image
	// the next steps will be taken:
	// 1. calculate the ratio by dividing the old dimmensions with the new ones
	// 2. if the ratio for the width is higher, the width will remain the one define in WIDTH variable
	// and the height will be calculated so the image ratio will not change
	// 3. otherwise we will use the height ratio for the image
	// as a result, only one of the dimmensions will be from the fixed ones
	$ratio1=$old_x/$new_w;
	$ratio2=$old_y/$new_h;
	if($ratio1>$ratio2) {
		$thumb_w=$new_w;
		$thumb_h=$old_y/$ratio1;
	} else {
		$thumb_h=$new_h;
		$thumb_w=$old_x/$ratio2;
	}

	// we create a new image with the new dimmensions
	$dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);

	// resize the big image to the new created one
	imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);

	// output the created image to the file. Now we will have the thumbnail into the file named by $filename
	if(!strcmp("png",$ext))
		imagepng($dst_img,$filename);
	else
		imagejpeg($dst_img,$filename);

	//destroys source and destination images.
	imagedestroy($dst_img);
	imagedestroy($src_img);
	return $filename;
}
// This function fixes orientation issues
function image_fix_orientation($filename) {
//	var_dump($filename);
    $exif = exif_read_data($filename);
//	var_dump($exif);
    if (!empty($exif['Orientation'])) {
        $image = imagecreatefromjpeg($filename);
        switch ($exif['Orientation']) {
            case 3:
                $image = imagerotate($image, 180, 0);
                break;

            case 6:
                $image = imagerotate($image, -90, 0);
                break;

            case 8:
                $image = imagerotate($image, 90, 0);
                break;
        }

        imagejpeg($image, $filename, 90);
    }
}
// This function returns the file extension.
function getExtension($str) {
	$i = strrpos($str,".");
	if (!$i) { return ""; }
	$l = strlen($str) - $i;
	$ext = substr($str,$i+1,$l);
	return $ext;
}
?>