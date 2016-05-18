<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/config.inc.php');

if(!$_GET['section']) {
	require_once(PATH_ROOT . "index.php");
	exit();
}

$website_section = $website_sections->getByURLTitle($_GET['section']);
if(!$website_section) {
	$website_section = $website_sections->getGeneralSection();
	$_GET['pid'] = $_GET['section'];
}

$pages->setWebsiteSection($website_section['id']);
$pages->setSectionPageId($_GET['pid']);
$pages->setSubPageId($_GET['sid']);

$page = $pages->getPage();
$toppage = $pages->getPage(null,false,false);


if($page['is_subpage'] && $page['parent_id'] > 0) {
	$parentpage = $pages->getPage(null,null,false);
	if(!$toppage) $toppage = $parentpage;
}

if($_GET['sid']){
	if($page['image'] == ""){ $page['image'] = $parentpage['image']; }
}

if(!$toppage) $toppage = $page;

$bodyid = $website_section['lookup_key'];

if ( $page['page_type'] == "redirect" && $page['redirect_to'] ) {
	header( 'HTTP/1.1 301 Moved Permanently' );
	header( 'Location: ' . setup_url( $page['redirect_to'] ) );
	exit();
}
require_once(PATH_INC . "html_head.inc.php");

if($page['image'] == '' && $page['is_subpage'])
{
	$parentpage = $pages->getRow($page['parent_id']);
	$pageimage=$parentpage['image'];
}
else if($page['image'] == '')
	$pageimage=$keyvalue['default-images']['page-header'];
else
	$pageimage=$page['image'];
?>
	<section class="page-header" style="background-image:url(/assets/<?= $pageimage ?>)">
		<?php
		$sections = $website_sections->getByLookup('patent-sectors');
		if($_GET['section'] == $sections['url_title'])
		{
		?>
		<div class="bg-overlay">
			<div class="page-headr-text">
				<div class="container">
					<p>Sector focus</p>
					<h2><?= $page['title'] ?></h2>
				</div>
			</div>
		</div>
		<?php
		}
		?>
	</section>
<?php

if($page['override']) {
	require_once(PATH_OVERRIDE . $page['override'].".php");
} else {
	?>
	<section class="container">
		<div class="content-container double-column">
			<div class="col-xs-12 col-sm-12 col-md-8 no-pm">
				<div class="breadcrumb col-xs-12 col-sm-12 col-md-12">
				<?php
					if($page['website_section'] != 'general-content')
					{
						if($_GET['section'] && $_GET['pid'] && $_GET['sid']) {
							$breadcrumb_arr[$toppage['nav_title']] = $_GET['section'];
							$breadcrumb_arr[$parentpage['nav_title']] = $_GET['section']."/".$_GET['pid'];
							$breadcrumb_arr[(($subpage['nav_title']) ? $subpage['nav_title'] : $page['nav_title'])] = "";
						} else if($_GET['section'] && $_GET['pid']) {

							$section = $website_sections->getByLookup('patent-sectors');

							if( $_GET['section'] == $section['url_title'] ){
								$toppage['nav_title']="patent-sectors";
								$breadcrumb_arr[$toppage['nav_title']] = "";
							} else {
								$breadcrumb_arr[$toppage['nav_title']] = $_GET['section'];
							}

							$breadcrumb_arr[(($subpage['nav_title']) ? $subpage['nav_title'] : $page['nav_title'])] = "";

						} else if($_GET['section']) {
							$breadcrumb_arr[$toppage['nav_title']] = "";
						}
					}
					else
						$breadcrumb_arr[$page['nav_title']] = "";

					echo getBreadCrumbs($breadcrumb_arr);

					?>

				</div>
				<article class="col-xs-12 col-sm-12 col-md-12">
<?php
if($_GET['sid']){
	echo "					<p class=\"back\"><a href=\"/" . $toppage['url_title'] . "/" . $parentpage['url_title'] . "\">Back to " . $parentpage['title'] . "</a></p>\n";
}

if($_GET['sid']){
	echo "					<h1 class=\"hassub\">" . decodetext($parentpage['title']) . "</h1>\n";
	echo "					<h2 class=\"title\">" . decodetext($page['title']) . "</h2>\n";
} else {
	echo "					<h1>" . decodetext($page['title']) . "</h1>\n";
}
echo "					<div class=\"page-body\">" . prepbody($page['body'],$page) . "</div>\n";
?>
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
		require_once(PATH_INC."testimonial_section.inc.php")
	?>
	<?php
    if($page['show_meet_the_team'] == 1){
		require_once(PATH_INC . "meet-the-team_section.inc.php");
	}

	$section_ps = $website_sections->getByLookup('patent-sectors');
	$section_s = $website_sections->getByLookup('services');

	if($_GET['section'] == $section_ps['url_title'] OR $_GET['section'] == $section_s['url_title']){
		require_once(PATH_INC . "patent-sector_section.inc.php");
	?>
		<section class="container footer-margin">
			<div class="content-container-nopadding quote">
				<?= decodetext($keyvalue['homepage']['footer-quote']) ?>
			</div>
		</section>
	<?php
	}
	?>
<?php
}

require_once(PATH_INC . "html_foot.inc.php");