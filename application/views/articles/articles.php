<?php
    $data = json_decode($data, true);
    require_once 'application/views/journalMenu.php';
?>

<div class="container dynamic-page">
    <div class="row">
        <div class="col-md-12 mainpage">
            <div class="row clear-margins">
                <div class="col-md-6">
                    <h1><?=$data['pageTitle']?></h1>
                </div>
                <div class="col-md-6">
                    <h5 class="text-right"><?= $viewHelper->roman2Kannada(sizeof($data['articles']))?> <?=ARTICLES?></h5>
                </div>
            </div>
<?php if(isset($data['alphabet'])) {?>
            
            <p class="alphabet">    
    <?php foreach ($data['alphabet'] as $letter) { ?>
                <a class="letter" href="<?=BASE_URL?>articles/all/<?=$letter?>"><?=$letter?></a>
    <?php } ?>
            </p>
<?php } ?>

<?php foreach ($data['articles'] as $article) { ?>
    
            <div class="full-width-card blue-edge">
                <h4 class="publication-details">
                    <?php if(isset($article['feature'])) { ?><span class="red"><a href="<?=BASE_URL?>articles/category/feature/<?=$article['feature']?>"><?=$article['feature']?></a></span><?php } ?>
                    <?php if(isset($article['series'])) { ?><span class="brown"><a href="<?=BASE_URL?>articles/category/series/<?=$article['series']?>"><?=$article['series']?></a></span><?php } ?>
                    <span class="gray"><a href="<?=BASE_URL?>articles/toc?year=<?=$article['year']?>&month=<?=$article['month']?>"><?=$viewHelper->kannadaMonth($article['month'])?>, <?=$viewHelper->roman2Kannada($article['year'])?>, <?php echo (isset($article['mname']))? $article['mname'] . ',': ''?> (<?=ARCHIVE_YEAR?> <?=$viewHelper->roman2Kannada($viewHelper->rlZero($article['year']))?>, <?=ARCHIVE_MONTH?> <?=$viewHelper->roman2Kannada($viewHelper->rlZero($article['month']))?>)</a></span>
                </h4>
                <h2 class="clear-margins title">
                    <a target="_blank" href="<?=BASE_URL?>article/text/<?=$article['id']?>/#page=<?=$article['relativePageNumber']?>" class="pdf"><?=$article['title']?></a>
                </h2>
    <?php if(isset($article['author'])) { ?>
                <h3 class="author by">
        <?php foreach($article['author'] as $author) { ?>
                    <span><a href="<?=BASE_URL?>articles/author/<?=$author['name']?>"><?=$author['name']?></a></span>
        <?php } ?>
                </h3>
    <?php } ?>
                <h4><a class="downloadpdf" target="_blank" href="<?=BASE_URL?>article/download/<?=$article['id']?>/<?=$article['relativePageRange']?>"><?=DOWNLOAD_PDF?></a></h4>
            </div>
<?php } ?>

        </div>
    </div>
</div>
