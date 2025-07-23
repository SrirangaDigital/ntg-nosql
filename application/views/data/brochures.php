<div id="grid" class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <ul class="list-unstyled" style="column-count: 10; column-gap: 30px;">

                <?php foreach (array_reverse($data) as  $id) { ?>
                        <li><a href="<?=$viewHelper->genLink($id)?>"><?=$id?></a></li>
                <?php } ?>

            </ul>
        </div>
    </div>

</div>
