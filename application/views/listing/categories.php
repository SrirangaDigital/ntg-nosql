<?php 
    $auxiliary = array_pop($data);
    $parentType = $auxiliary['parentType'];
    $filter = $auxiliary['filter'];
?>
<script>
$(document).ready(function(){

    if($('.post').length < <?=PER_PAGE?>) {

        $('#grid').attr('data-go', '0');
        $('#grid').append('<div id="no-more-icon">No more<br />items<br />to show</div>');
    }

    $(window).scroll(function(){

        if ($(window).scrollTop() >= ($(document).height() - $(window).height())* 0.75){

            if($('#grid').attr('data-go') == '1') {

                var pagenum = parseInt($('#grid').attr('data-page')) + 1;
                $('#grid').attr('data-page', pagenum);

                var nextURL = window.location.href + '&page=' + pagenum;
                getresult(nextURL);
            }
        }
    });
});     
</script>
<div id="grid" class="container-fluid" data-page="1" data-go="1">
    <div id="posts">
        <div class="post no-border">
            <div class="albumTitle <?=$parentType?>">
                <span class="head"><?=$parentType?>S</span><br />
<?php foreach ($filter as $key => $value) { ?>
                <span class="select"><em><?=preg_replace('/([A-Z])/', " $1", $key)?>:</em> <?=$value?></span><br />
<?php } ?>
                <span class="select"><?=preg_replace('/([A-Z])/', " $1", $auxiliary['selectKey'])?><em> - wise</em></span>
            </div>
        </div>
<?php foreach ($data as $row) { ?>
        <div class="post">
            <a href="<?=$row['nextURL']?>" title="<?=$row['name']?>" target="_blank">
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
