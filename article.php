<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/config.inc.php');

if(!$_GET['section']) {
	require_once(PATH_ROOT . "index.php");
	exit();
}

$articles=new articles();

$website_section = $website_sections->getByURLTitle($_GET['section']);
$bodyid = $website_section['lookup_key'];
$page=$articles->getPage($website_section['id'],$_GET['pid']);

require_once(PATH_INC . "html_head.inc.php");

if($page['image'] == '')
	{
		$parentpage = $pages->getPage($website_section['id']);
		$pageimage=$parentpage['image'];
	}
	else
		$pageimage=$page['image'];
?>
	<section class="page-header" style="background-image:url(/assets/<?= $pageimage ?>)">
		<div class="container">
		</div>
	</section>

	<section class="container">
		<div class="content-container double-column">
			<div class="col-xs-12 col-sm-12 col-md-8 no-pm">
				<div class="breadcrumb col-xs-12 col-sm-12 col-md-12">
					<a href="/">home</a><span>></span><a href="/<?= $_GET['section'] ?>"><?= urlToName($_GET['section']) ?></a><span>></span><?= urlToName($_GET['pid']) ?>
				</div>
				<div class="backtopage col-xs-12 col-sm-12 col-md-12">
					<a href="/<?= $_GET['section'] ?>">Back to <?= urlToName($_GET['section']) ?></a>
				</div>
				<article class="col-xs-12 col-sm-12 col-md-12">
					<h1><?= decodetext($page['title']) ?></h1>
					<div class="page-body"><?= prepbody($page['body'],$page) ?></div>
				</article>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-4 no-pm">
				<?php
					require_once(PATH_INC."aside_sidebar.inc.php")
				?>
			</div>
		</div>
	</section>
	<?php
	if($page['show_meet_the_team'] == 1)
	{
		require_once(PATH_INC . "meet-the-team_section.inc.php");
	}
require_once(PATH_INC . "html_foot.inc.php");