<div class="container">
    <div class="row gap-above-med">
        <div class="col-md-4">
        </div>
        <div class="col-md-8">
            <div class="image-desc-full">
                <form  method="POST" class="form-inline updateDataArchive" role="form" id="updateData" action="<?=BASE_URL?>edit/updateArtefactJson" onsubmit="return validate()">
                    <?=$viewHelper->displayDataInForm(json_encode($data))?>
                </form>    
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?=PUBLIC_URL?>js/addnewfields.js"></script>
<script type="text/javascript" src="<?=PUBLIC_URL?>js/validate.js"></script>
