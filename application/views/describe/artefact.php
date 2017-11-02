<script>
$(document).ready(function(){

    var bgColor = $('.albumTitle').css('background-color');
    var fgColor = $('.albumTitle span').css('color');

    $('.albumTitle span').css('color', bgColor);
    $('.albumTitle').css('background-color', fgColor);

    // Triggering a click event on page which has to be opened
    $('.toc a').on('click', function(e){

        var imageID = $(this).attr('data-href');
        $('#' + imageID).trigger('click');
    });
});
</script>
<div class="container">
    <div class="row gap-above-med">
        <div class="col-md-9">
            <ul class="pager">
                <?php if($data['neighbours']['prevID']) {?> 
                <li class="previous"><a href="<?=BASE_URL?>describe/artefact/<?=$data['neighbours']['prevID']?>?<?=$data['filter']?>">&lt; Previous</a></li>
                <?php } ?>
                <?php if($data['neighbours']['nextID']) {?> 
                <li class="next"><a href="<?=BASE_URL?>describe/artefact/<?=$data['neighbours']['nextID']?>?<?=$data['filter']?>">Next &gt;</a></li>
                <?php } ?>
            </ul>
            <div id="viewletterimages" class="letter_thumbnails">
                <?php

                    if(isset($data['external'])){

                        echo '<div class="iframeHolder">';
                        include $data['external']['fileName'];
                        echo '</div>';
                    }
                    else{

                        $numberOfImages = sizeof($data['images']);

                        $class = ($numberOfImages > 1) ? 'img-small ' : 'img-center ';

                        foreach ($data['images'] as $imageThumbPath ) {
                                
                            $imagePath = str_replace('thumbs/', '', $imageThumbPath);

                            if ($class == 'img-center ') $imageThumbPath = $imagePath;

                            $imageID = str_replace(DATA_URL . $data['details']['id'] . '/', '', $imagePath);
                            $imageID = 'image_' . intval(str_replace(PHOTO_FILE_EXT, '', $imageID));

                            echo '<img id="' . $imageID . '" class="' . $class . 'img-responsive" data-original="' . $imagePath . '" src="' . $imageThumbPath . '">';
                        }
                    }
                ?>
            </div>
        </div>            
        <div class="col-md-3">
            <div class="image-desc-full">
                <div class="albumTitle <?=$data['details']['Type']?>"><span class="head"><?=$data['details']['Type']?></span></div>
                <ul class="list-unstyled">
                <?php

                    // Bring AccessionCards to the last row
                    $accessionCards = [];
                    if (isset($data['details']['AccessionCards'])) {

                        $accessionCards = $data['details']['AccessionCards'];
                        unset($data['details']['AccessionCards']);
                        $data['details']['AccessionCards'] = $accessionCards;
                    }

                    $idURL = str_replace('/', '_', $data['details']['id']);

                    $toc = $data['details']['Toc'] = (isset($data['details']['Toc'])) ? $data['details']['Toc'] : '';
                    unset($data['details']['Toc']);

                    foreach ($data['details'] as $key => $value) {

                        echo '<li><strong>' . $key . ':</strong><span class="image-desc-meta">' . $viewHelper->formatDisplayString($value) . '</span></li>';
                    }
                ?>
                <?php if(isset($_SESSION['login']) || SHOW_PDF) {?>
                    <?=$viewHelper->linkPDFIfExists($data['details']['id'])?>
                <?php } ?>
                <?php if(isset($_SESSION['login'])) {?>
                    <li><a class="editDetails" href="<?=BASE_URL?>edit/artefact/<?=$idURL?>">Edit Details</a></li>
                <?php } ?>
                </ul>
                <?php if($accessionCards) echo $viewHelper->includeAccessionCards($accessionCards); ?>
                <?php if($toc) echo $viewHelper->displayToc($toc); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?=PUBLIC_URL?>js/viewer.js"></script>
