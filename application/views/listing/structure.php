<?php
    require_once 'application/views/journalMenu.php';
    $data = json_decode($data, true);
    $auxiliary = $data['values']['auxiliary'];
    unset($data['values']['auxiliary']);
?>

<div class="container dynamic-page">
    <div class="row">
        <div class="col-md-12 mainpage">
            <h1><?=$viewHelper->getStructurePageTitle($auxiliary['filter'])?></h1>
            <div class="row">
<?php foreach ($data['values'] as $row) { $filter = array_merge([$auxiliary['selectKey']=>$row['name']],$auxiliary['filter']); ?>
                <a href="<?=$row['nextURL']?>" class="col-md-2">
    <?php if(ARCHIVE_STRUCTURE_TYPE == 'pictorial') { ?>
                    <div class="archive-structure-pictorial">
                        <img class="img-fluid" src="<?=$viewHelper->getCoverPage($filter)?>" alt="Cover page" />
                        <p><?=$viewHelper->getDisplayName([$auxiliary['selectKey']=>$row['name']])?></p>
    <?php } else { ?>
                    <div class="full-width-card red-edge">
                        <h3 class="author"><?=$row["name"]?></h3>
    <?php } ?>
                    </div>
                </a>    
<?php } ?>
            </div>
        </div>
    </div>
</div>
