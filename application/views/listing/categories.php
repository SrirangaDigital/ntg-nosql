<?php 
    $auxilairy = array_pop($data);
    $parentType = $auxilairy['parentType'];
?>
<script>
$(document).ready(function(){

    $('#posts').prepend('<div class="post no-border"><div class="albumTitle <?=$parentType?>"><span><?=$parentType?></span></div></div>');

    $(window).scroll(function(){

        if ($(window).scrollTop() >= ($(document).height() - $(window).height())* 0.75){

            if($('#grid').attr('data-go') == '1') {

                var pagenum = parseInt($('#grid').attr('data-page')) + 1;
                $('#grid').attr('data-page', pagenum);

                getresult(base_url + 'listing/categories/' + '<?=$parentType?>' + '/?page='+pagenum);
            }
        }
    });
});     
</script>
<div id="grid" class="container-fluid" data-page="1" data-go="1">
    <div id="posts">
<?php foreach ($data as $row) { ?>
        <div class="post">
            <a href="<?=BASE_URL?>listing/artefacts/<?=$row['parentType'] . '/' . $row['nameURL']?>" title="<?=$row['name']?>" target="_blank">
                <div class="fixOverlayDiv">
                    <img class="img-responsive" src="<?=$row['thumbnailPath']?>">
                    <div class="OverlayText"><?=$row['leafCount']?> <?=$row['parentType']?><?php if($row['leafCount'] > 1) echo 's'; ?><br /><span class="link"><i class="fa fa-link"></i></span></div>
                </div>
                <p class="image-desc"><strong><?=$row['name']?></strong></p>
            </a>
        </div>
<?php } ?>
    </div>
</div>
<div id="loader-icon">
    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><br />
    Loading more items
</div>
