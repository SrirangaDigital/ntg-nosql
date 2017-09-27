<?php
    $refererId = str_replace('/', '_', $data['id']);
    $auxiliary = array_pop($data);
    $disableKeys = ['id', 'albumID', 'Toc', 'Box', 'File', 'ForeignKeyId', 'ForeignKeyType'];
    $count = 0;
    $formgroup = 0;
?>

<div class="container">
    <div class="row gap-above-med">
        <div class="col-md-4">
            <div class="image-reduced-size">
                <img class="img-responsive" src="<?=$auxiliary['thumbnailPath']?>">
            </div>
        </div>            
        <div class="col-md-8">
            <div class="image-desc-full">
                <form  method="POST" class="form-inline updateDataArchive" role="form" id="updateData" action="<?=BASE_URL?>edit/updateArtefact" onsubmit="return validate()">
<?php
    foreach ($data as $key => $value) {

        $disable = (in_array($key, $disableKeys)) ? 'readonly' : '';
?>
                    <div class="form-group" id="frmgroup<?=$formgroup?>">
                        <input type="text" class="form-control edit key" name="id<?=$count?>[]"  value="<?=$key?>" <?=$disable?> />
                        <input type="text" class="form-control edit value" name="id<?=$count?>[]"  value="<?=$value?>"  <?=$disable?> />
        <?php if(!($disable)) { ?>
                        <i class="fa fa-times" title="Remove field" onclick="removeUpdateDataElement('frmgroup<?=$formgroup?>')" value="Remove"></i>
        <?php } if(($auxiliary['foreignKeys']) && (in_array($key, $auxiliary['foreignKeys']))) { ?>
                        <a  class="editDetails" href="<?=BASE_URL?>edit/foreignKey/<?=urlencode($key) . '/'. urlencode($value)?>?refererArtefact=<?=$refererId?>">Edit</a>
        <?php } ?>
                    </div>
<?php 
        $count++;
        $formgroup++;
    }
?>
                    <div id="keyvalues"></div>
                    <i class="fa fa-plus" title="Add new field" id="keyvaluebtn" onclick="addnewfields(keyvaluebtn)"></i>
                    <input class="updateSubmit" type="submit" id="submit" value="Update Data" />
                </form>    
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?=PUBLIC_URL?>js/addnewfields.js"></script>
<script type="text/javascript" src="<?=PUBLIC_URL?>js/validate.js"></script>
