<div class="container gap-above-med">
    <div class="row">
        <div class="col-md-12">
            <div id="cd-sec-nav">
                <a class="cd-sec-nav-trigger">Menu<span></span></a>
                <nav id="cd-sec-main-nav">
                    <ul>
                        <li><a class="years" href="<?=BASE_URL?>listing/structure/Journal?select=year"><i class="fa fa-calendar"></i> <?=NAV_ARCHIVE_VOLUME?></a></li>
                        <li><a class="titles" href="<?=BASE_URL?>articles/all/<?=DEFAULT_LETTER?>"><i class="fa fa-files-o"></i> <?=NAV_ARCHIVE_ARTICLES?></a></li>
                        <li><a class="authors" href="<?=BASE_URL?>listing/authors/<?=DEFAULT_LETTER?>"><i class="fa fa-users"></i> <?=NAV_ARCHIVE_AUTHORS?></a></li>
                        <li><a class="features" href="javascript:void(0);"><i class="fa fa-tags"></i> <?=NAV_ARCHIVE_FEATURES?></a></li>
                        <li><a class="search" href="<?=BASE_URL?>search/journal"><i class="fa fa-search"></i> <?=NAV_ARCHIVE_SEARCH?></a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
