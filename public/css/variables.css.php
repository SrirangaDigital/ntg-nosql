<style type="text/css">

body {
	font-size: <?=ARCHIVE_BASE_FONT_SIZE?>;
}

.mainpage .full-width-card h3.author.by:before{
 content: '<?=AUTHOR_PREFIX?> ';
 margin-right: 2px;
}
.mainpage .full-width-card h3.author span:not(:first-of-type):last-of-type:before{
 content: ' <?=AUTHOR_JOINER?> ';
}

</style>