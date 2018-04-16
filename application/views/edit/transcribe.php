
<?php echo '<script>var myarray = '.json_encode($data['images']) .';</script>' ?>;

<div class="container">
    <div class="row gap-above-med">
        <div class="col-md-6 trans-col1" id="col1">
        	<div id="transcribeimages" class="letter_thumbnails"> 
               <?php

                    $numberOfImages = sizeof($data['images']);

                    $class = ($numberOfImages > 1) ? 'trans-img-small ' : 'img-center ';

                    foreach ($data['images'] as $imageThumbPath ) {
                            
                        $imagePath = str_replace('thumbs/', '', $imageThumbPath);

                        if ($class == 'img-center ') $imageThumbPath = $imagePath;

                        $imageID = str_replace(DATA_URL . $data['details']['id'] . '/', '', $imagePath);
                        $imageID = 'image_' . intval(str_replace(PHOTO_FILE_EXT, '', $imageID));

                        echo '<img id="' . $imageID . '" class="' . $class . 'img-responsive" data-original="' . $imagePath . '" src="' . $imageThumbPath . '">';
                    }
                ?>
        	</div>
        </div>        
        <div class="col-md-6 trans-col2">
        	<form  method="POST" role="form" id="updateData" action="<?=BASE_URL?>edit/updatetranscribe">
 				<label>ID: 001/002.003/051/001.JPG</label><br />
 				<textarea rows="25" cols="50"></textarea>
        	</form>
        </div>	
    </div>
    <div class="row gap-above-med">
    	<div class="col-md-6">
    		<button id="nextImage" type="button" class="btn btn-primary" >Transcribe Next Image</button>
    	</div>    	
    	<div class="col-md-6">
    		<button type="button" class="btn btn-primary">Save Transcribe</button>
    	</div>
    </div>
</div>
<script>
	function changeImage(myarray){
		var transDiv = document.getElementById("transcribeimages");
		var image = transDiv.getElementsByTagName("img")[0];
		var imageID = image.id;
		imageID = imageID.replace("image_","");
		var Index = imageID - 1;
		imageID++;

		var thumbnail = myarray[Index + 1];
		var nextImage = thumbnail;
		nextImage = nextImage.replace('thumbs/','');

		image.setAttribute("id", 'image_' + imageID);
		image.setAttribute("src", thumbnail);
		image.setAttribute("data-original", "");
		image.setAttribute("data-original", nextImage);

		alert(image.getAttribute("data-original"));

		// transDiv.innerHTML="<p>Hello</p>";
		// var imgString = '<img id="image_' + imageID + '" class="trans-img-small img-responsive" data-original="' + nextImage + '" ' + 'src="' + thumbnail + '" >';
		// transDiv.removeChild(transDiv.childNodes[0]); 
		// alert(transDiv);
        // var viewer = new Viewer(document.getElementById('transcribeimages'), {url: 'data-original', inline: true, minHeight: 500});
        // viewer.destroy();
		// var column1 = document.getElementById("col1");
		// column1.removeChild(column1.childNodes[0]);
		// transDiv.innerHTML = imgString;
        // var viewer = new Viewer(document.getElementById('transcribeimages'), {url: 'data-original', inline: true, minHeight: 500});
        // var viewer = new Viewer(document.getElementById('transcribeimages'));
        // viewer.play()
	}
$(document).ready(function(){

    $('#nextImage').on('click', function(e){

        $('.viewer-next').trigger('click');
    });
});	
</script>
<script type="text/javascript" src="<?=PUBLIC_URL?>js/viewer.js"></script>