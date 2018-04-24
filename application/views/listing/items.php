<?php
    $data = json_decode($data, true);
    require_once 'application/views/journalMenu.php';
?>

<div class="container dynamic-page">
    <div class="row">
        <div class="col-md-12 mainpage">
            <h1><?=$data['pageTitle']?></h1>
<?php if(isset($data['alphabet'])) {?>
            
            <p class="alphabet">    
    <?php foreach ($data['alphabet'] as $letter) { ?>

                <a class="letter" href="<?=BASE_URL?>listing/authors/<?=$letter?>"><?=$letter?></a>
    <?php } ?>
            </p>

<?php } ?>
<?php if(isset($data['subTitle'])) {?>
            <h5 class="text-right"><?= $viewHelper->roman2Kannada(sizeof($data['values']))?> <?=$data['subTitle']?></h5>
<?php } ?>
<?php foreach ($data['values'] as $row) { ?>
    
    <?php if(isset($row['item'])) { ?>
            <div class="full-width-card red-edge">
                <h3 class="author"><a href="<?=$data['nextUrl']?><?=$row['item']?>"><?=$row['item']?></a></h3>
            </div>
    <?php } ?>
<?php } ?>
        </div>
    </div>
</div>
