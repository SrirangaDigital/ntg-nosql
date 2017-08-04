<?php
    $auxilairy = array_pop($data);
    $parentType = $data[0]['Type'];
?>
<script>
$(document).ready(function(){

    $('.post.no-border').prepend('<div class="albumTitle <?=$parentType?>"><span><?=$parentType?></span></div>');

    $(window).scroll(function(){

        if ($(window).scrollTop() >= ($(document).height() - $(window).height())* 0.75){

            if($('#grid').attr('data-go') == '1') {

                var pagenum = parseInt($('#grid').attr('data-page')) + 1;
                $('#grid').attr('data-page', pagenum);

                getresult(base_url + 'listing/artefacts/<?=$parentType?>/<?=$auxilairy['category']?>?page='+pagenum);
            }
        }
    });
});     
</script>

<div id="grid" class="container-fluid" data-page="1" data-go="1">
    <div id="posts">
        <div class="post no-border">
            <div class="image-desc-full">
            </div>
        </div>
<?php foreach ($data as $row) { ?>
        <div class="post">
            <a href="<?=BASE_URL?>describe/artefact/<?=$row['idURL']?>" title="View Details" target="_blank">
                <img src="<?=$row['thumbnailPath']?>">
                <p class="image-desc"><?=$row['cardName']?></p>
            </a>
        </div>
<?php } ?>
    </div>
</div>
<div id="loader-icon">
    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><br />
    Loading more items
</div>
