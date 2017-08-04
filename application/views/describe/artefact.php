<script>
$(document).ready(function(){

    var bgColor = $('.albumTitle.' + '<?=$data['details']['Type']?>').css('background-color');
    var fgColor = $('.albumTitle span').css('color');

    $('.albumTitle span').css('color', bgColor);
    $('.albumTitle.' + '<?=$data['details']['Type']?>').css('background-color', fgColor);
});
</script>
<div class="container">
    <div class="row gap-above-med">
        <div class="col-md-9">
            <ul class="pager">
                <?php if($data['neighbours']['prevID']) {?> 
                <li class="previous"><a href="<?=BASE_URL?>describe/artefact/<?=$data['neighbours']['prevID']?>">&lt; Previous</a></li>
                <?php } ?>
                <?php if($data['neighbours']['nextID']) {?> 
                <li class="next"><a href="<?=BASE_URL?>describe/artefact/<?=$data['neighbours']['nextID']?>">Next &gt;</a></li>
                <?php } ?>
            </ul>
            <div id="viewletterimages" class="letter_thumbnails">
                <?php
                    $numberOfImages = sizeof($data['images']);
                    $class = ($numberOfImages > 1) ? 'img-small ' : 'img-center ';

                    foreach ($data['images'] as $imageThumbPath ) {
                            
                        $imagePath = str_replace('thumbs/', '', $imageThumbPath);

                        if ($class == 'img-center ') $imageThumbPath = $imagePath;
                        echo '<img class="' . $class . 'img-responsive" data-original="' . $imagePath . '" src="' . $imageThumbPath . '">';
                    }
                ?>
            </div>
        </div>            
        <div class="col-md-3">
            <div class="image-desc-full">
                <div class="albumTitle <?=$data['details']['Type']?>"><span><?=$data['details']['Type']?></span></div>
                <ul class="list-unstyled">
                <?php

                    $idURL = str_replace('/', '_', $data['details']['id']);
                    foreach ($data['details'] as $key => $value) {

                        echo '<li><strong>' . $key . ':</strong><span class="image-desc-meta">' . $viewHelper->formatDisplayString($value) . '</span></li>';
                    }
                ?>
                <?php if(isset($_SESSION['login'])) {?>
                    <?=$viewHelper->linkPDFIfExists($data['details']['id'])?>
                    <li><a class="editDetails" href="<?=BASE_URL?>edit/artefact/<?=$idURL?>">Edit Details</a></li>
                <?php } ?>    
                </ul>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?=PUBLIC_URL?>js/viewer.js"></script>
