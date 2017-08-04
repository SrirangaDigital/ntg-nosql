<div class="container">
    <div class="row gap-above-med">
        <div class="col-md-4">
            <div class="image-reduced-size">
                <img class="img-responsive" src="<?=$data->thumbnailPath?>">
                <?php $idURL = $data->idURL;
                      $foreignKeys = $data->foreignKeys;
                      unset($data->foreignKeys);
                      unset($data->thumbnailPath); 
                      unset($data->idURL);
                ?>
            </div>
        </div>            
        <div class="col-md-8">
            <div class="image-desc-full">
                <form  method="POST" class="form-inline updateDataArchive" role="form" id="updateData" action="<?=BASE_URL?>edit/updateArtefactJson" onsubmit="return validate()">
                    <?=$viewHelper->displayDataInForm(json_encode($data),$foreignKeys)?>
                </form>    
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?=PUBLIC_URL?>js/addnewfields.js"></script>
<script type="text/javascript" src="<?=PUBLIC_URL?>js/validate.js"></script>
