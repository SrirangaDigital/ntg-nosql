<style>
    /*override default styles -  display headerless view*/
    .navbar, .sub-nav, .pager, #k-subfooter{
        display: none;
    }
    .push-down, .first-row{
        margin: 0;
        padding: 0;
    }
    .viewer-container{
        width: 50%;
    }
    iframe{
        height: 100vh;
        width: 100%;
        padding: 0;
        margin: 0;
        border: none;
    }
    body{
        overflow-y: hidden;
    }
    .gap-above-large {
		margin: 0px;
	}
</style>

    <div class="row clear-margins">
        <div class="col-md-6 clear-paddings">
            <div id="transcribeimages" class="letter_thumbnails">
                <?php
                    $numberOfImages = sizeof($data['images']);

                    $class = ($numberOfImages > 1) ? 'img-small ' : 'img-center ';

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
        <div class="col-md-6 clear-paddings">
            <iframe src="<?=DATA_URL . $data['details']['id']?>/transcription.pdf"></iframe>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?=PUBLIC_URL?>js/viewer.js"></script>
