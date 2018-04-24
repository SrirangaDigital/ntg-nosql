<?php
    require_once 'application/views/journalMenu.php';
?>
<div class="container flat-page">
	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8 mainpage">
			<h1><?=ARCHIVE?> > <?=SEARCH?></h1>

			<form class="searchForm" action="<?=BASE_URL?>articles/search" method="GET">
				<div class="row">
					<div class="col-md-8">
						<div class="form-group row">
							<label for="title" class="col-lg-3 col-form-label form-control-label"><?=SEARCH_ARTICLE?> : </label>
							<div class="col-lg-9">
								<input name="title" id="title" class="form-control" type="text">
							</div>
						</div>
						<div class="form-group row">
							<label for="authornames" class="col-lg-3 col-form-label form-control-label"><?=SEARCH_AUTHOR?> : </label>
							<div class="col-lg-9">
								<input name="author.name" id="author.name" class="form-control" type="text">
							</div>
						</div>
<!--
						<div class="form-group row">
							<label for="feature" class="col-lg-3 col-form-label form-control-label"><?=SEARCH_FEATURE?> : </label>
							<div class="col-lg-9">
								<input name="feature" id="feature" class="form-control" type="text">
							</div>
						</div>
-->
						<div class="form-group row">
							<label for="fulltext" class="col-lg-3 col-form-label form-control-label"><?=SEARCH_WORD?> : </label>
							<div class="col-lg-9">
								<input name="fulltext" id="fulltext" class="form-control" type="text" placeholder="">
							</div>
						</div>
					</div>
				</div>
				<div class="row justify-content-center">
					<div class="col-md-3">
						<input name="submit" id="submit" class="form-control" type="submit" value="<?=SEARCH_SEARCH?>">
					</div>
					<div class="col-md-3">
						<input name="reset" id="reset" class="form-control" type="reset" value="<?=SEARCH_RESET?>">
					</div>
				</div>
			</form>
		</div>
		<div class="col-md-2"></div>
	</div>
</div>
