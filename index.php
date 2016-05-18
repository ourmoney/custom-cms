<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/config.inc.php');

$homepage = new homepage();
$page = $homepage->getRow();
$bodyid="homepage";

require_once(PATH_INC . "html_head.inc.php");

$hero_panels = new hero_panels();
$panels = $hero_panels->getPublished();

if(count($panels) > 0){

?>
<!--style="background:url(/assets/header_slide.png)" -->
<section class="hero-panel" >
	<div class="container" style="width:100%;padding:0;">
		<div class="bxslider homepagecarousel">
<?php
foreach($panels as $panel){
    if($panel['image_landscape'] && (!$mobiledetect->isMobile() || $mobiledetect->isTablet())) {
        $panel['image'] = $panel['image_landscape'];
    }

    if($panel['video'] != '' && !$mobiledetect->isMobile()){
        echo "          <div class=\"slide videoslide\">\n";

        if($panel['url']) {
            echo "              <a href=\"" . setup_url($panel['url']) . "\">\n";
        }

        if($panel['image'] != "") {
            echo "                      <img src=\"/assets/" . $panel['image'] . "\" />\n";
        }

        echo "                  <video height=\"680\" width=\"1920\" autoplay=\"autoplay\" muted=\"true\">\n";
        echo "                      <source src=\"assets/" . decodetext($panel['video']) . "\" type=\"video/mp4\">\n";
        echo "                  </video>\n";
        echo "                  <div class=\"text-pane\">\n";
        echo "                      <h2 class=\"heading-text\">" . decodetext($panel['title']) . "</h2>\n";
        echo "                      " . decodetext($panel['body']) . "\n";
        echo "                  </div>\n";

        if($panel['url']) {
		    echo "				</a>\n";
        }

        echo "          </div>\n";
    } else {
        if(trim($panel['image']) != ""){
            echo "          <div class=\"slide\">\n";

            if($panel['url']) {
                echo "              <a href=\"" . setup_url($panel['url']) . "\">\n";
            }

            echo "                  <img src=\"/assets/" . $panel['image'] . "\" />\n";
            echo "                  <div class=\"text-pane\">\n";
            echo "                      <h2 class=\"heading-text\">" . decodetext($panel['title']) . "</h2>\n";
            echo "                      " . decodetext($panel['body']) . "\n";
            echo "                  </div>\n";

            if($panel['url']) {
                echo "              </a>\n";
            }

            echo "			</div>\n";
		}
	}
}
?>
		</div>
	</div>
</section>
<?php
}
?>
<section class="container">
	<div class="content-container primary">
		<?php
		if($page['body'] != ""){
			echo "<div class=\"page-copy\">" . prepBody($page['body']) . "</div>";
		}
		?>
		<div class="col-xs-12 col-sm-6 col-md-8">
			<div class="case-study">
			<?php

				$articles = new articles();
				$case_studies=$articles->getPageArticles($page['case_study']);

				$section=$website_sections->getByLookup('case-studies');

				$cs=$articles->getPages($section['id']);

				$cs_urls = "/" . $section['url_title'] . "/" . date('Y',strtotime($cs[0]['publication_date'])) . "/" . date('m',strtotime($cs[0]['publication_date'])) . "/" . date('d',strtotime($cs[0]['publication_date'])) . "/" ;

				?>
				<div class="bxslider">
					<?php
					foreach ($case_studies as $case_study) {
						$cs_url = $cs_urls.$case_study['url_title'];
						$thumbnail_image=($case_study['thumbnail_image']) ? $case_study['thumbnail_image'] : $keyvalue['default_images']['case-study'];
					?>
					  <div class="slide">
						  <div class="image"><img src="/assets/<?= $thumbnail_image ?>" /></div>
						  <div class="text-pane">
						  		<div class="lede">Feature case study</div>
						  		<a href="<?= $cs_url ?>"><h1 class="heading-text"><?= $case_study['title'] ?></h1></a>
						  </div>
						  <div><a class="btn-look-inside" href="<?= $cs_url ?>">Look inside</a></div>
					  </div>
					<?php
					}
					?>
				</div>
			</div>
		</div>
		<aside class="col-xs-12 col-sm-6 col-md-4">
			<div class="service ">
				<div class="heading-text">Service quicklinks</div>
				<?php
				$section=$website_sections->getByLookup('services');
				$dropdownmenu='';
		    	$directmenu='';
				foreach ($pages->getPages($section['id'],null,"0") as $navpage) {

		    		$subpage=	$pages->getPages($section['id'],$navpage['id'],"0");


		    		if($subpage){

		    			$dropdownmenu .='<div class="dropdown dropdown-stack">
					  	<button class="btn btn-dropdown btn-service-dropdown dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
					    '.$navpage['nav_title'].'
					  	</button>
					  	<ul class="dropdown-menu service-dropdown-menu" aria-labelledby="dropdownMenu1">';
					  		$dropdownmenu .="<li><a href=\"/" . $section['url_title'] . "/" . $navpage['url_title'] . "\">".$navpage['nav_title']."</a></li>";

					  		foreach ($subpage as $navpage) {

					  			$pagerecord=$pages->getPageById($navpage['parent_id']);
								$url="/".$section['url_title']."/".$pagerecord['url_title']."/".$navpage['url_title'];

					    		$dropdownmenu .='<li><a href="'.$url.'">'.$navpage['nav_title'].'</a></li>';

					    	}
					  	$dropdownmenu .='</ul>
					</div>';

		    		}
		    		else{

		    			$url="/".$section['url_title']."/".$navpage['url_title'];

		    			$directmenu .='<div class="direct-links">
							<ul class="normal-menu" aria-labelledby="dropdownMenu1">
								    <li><a href="'.$url.'">'.$navpage['nav_title'].'</a></li>
							</ul>
						</div>';
		    		}
		    	}
		    	echo $dropdownmenu.$directmenu;
				?>
			</div>
		</aside>
	</div>
</section>
<section class="service-sector">
	<div class="container">
		<div class="content-container secondary">
			<aside class="col-xs-12 col-sm-6 col-md-4">
				<div class="latest-news">
				<?php
					$articles=new articles();
					$section=$website_sections->getByLookup('news-and-views');
					$news=$articles->getPages($section['id']);
					$news_url = "/" . $section['url_title'] . "/" . date('Y',strtotime($news[0]['publication_date'])) . "/" . date('m',strtotime($news[0]['publication_date'])) . "/" . date('d',strtotime($news[0]['publication_date'])) . "/" . $news[0]['url_title'];
				?>
					<div class="heading-text">Latest news + views</div>
					<div class="news aside-fix-height">
						<div class="news-title"><a href="<?= $news_url ?>"><?= $news[0]['title'] ?></a></div>
						<div class="published-date"><?= convertDateToString($news[0]['publication_date']) ?></div>
						<div class="summary-text"><?= $news[0]['summary'] ?></div>
					</div>
					<div>
						<a class="btn-see-all" href="/<?= $section['url_title'] ?>"> See all posts</a>
					</div>
				</div>
			</aside>
			<?php
			//if($page['show_vacancies'] == 1 )
			//{
				$section = $website_sections->getByLookup('careers');
				$vacancies = new vacancies();
				$vacancies=$vacancies->getVacancies(null,null,null,4);

				$alljobs=$pages->getPageByLookup('vacancy');


				$alljobsurl="/" . $section['url_title'] . "/".$alljobs['url_title'];
				?>

				<aside class="col-xs-12 col-sm-6 col-md-4">
					<div class="current-vacancies">
						<div class="heading-text">Current vacancies</div>
						<div class="vacancies aside-fix-height">
							<div class="vacancies">
								<?php
								foreach ($vacancies as $vacancy) {
									$vacancy_url = $alljobsurl . "/" . date('Y',strtotime($vacancy['publication_date'])) . "/" . date('m',strtotime($vacancy['publication_date'])) . "/" . date('d',strtotime($vacancy['publication_date'])) . "/" . $vacancy['url_title'];

								?>
								<div class="vacancy">
									<div class="vacancy-title"><a href="<?= $vacancy_url  ?>"><?= $vacancy['title']  ?> <span class="location"><?= $vacancy['joblocation'] ?></span></a></div>
								</div>
								<?php
								}
								?>
							</div>
						</div>
						<div>
							<a class="btn-see-all" href="<?= $alljobsurl ?>"> See all jobs</a>
						</div>
					</div>
				</aside>
			<?php
			//}
			$page_advert=$pages->getRow($page['pageadvert_square']);
			$ws=$website_sections->getRow(decodetext($page_advert['website_section']));

			if($page_advert['is_subpage'])
			{
				$pagerecord=$pages->getRow($page_advert['parent_id']);

				$url="/".$ws['url_title']."/".$page_advert['url_title']."/".$pagerecord['url_title'];
			}
			else{
				$url="/".$ws['url_title']."/".$page_advert['url_title'];
			}


			?>
			<aside class="col-xs-12 col-sm-6 col-md-4">
				<div class="page-advert"  style="background-image:url(assets/<?= $page_advert['thumbnail_image'] ?>)">
					<div class="bg-overlay">
						<div class="heading-text"><?= $page_advert['title'] ?></div>
						<div>
							<a class="btn-see-all" href="<?= $url ?>">Find out more</a>
						</div>
					</div>
				</div>
			</aside>
		</div>
	</div>
</section>

<section class="container">
	<div class="content-container-nopadding quote">
		<?= decodetext($keyvalue['homepage']['quote1']) ?>
	</div>
</section>
<?php
			//}
if($page['show_pageadvert_wide'])
{
	$page_advert=$pages->getRow($page['pageadvert_wide']);
	$ws=$website_sections->getRow(decodetext($page_advert['website_section']));

	if($page_advert['is_subpage'])
	{
		$pagerecord=$pages->getRow($page_advert['parent_id']);

		$url="/".$ws['url_title']."/".$page_advert['url_title']."/".$pagerecord['url_title'];
	}
	else if($page_advert['section_homepage'])
	{
		$url="/".$ws['url_title'];
	}
	else{
		$url="/".$ws['url_title']."/".$page_advert['url_title'];
	}
	
	$background_image=($keyvalue['homepage']['wide-banner']) ? 	$keyvalue['homepage']['wide-banner'] : $page_advert['image']
	?>
	<section class="wide-advert-section" style="background-image:url(assets/<?= $background_image  ?>)">
		<div class="container">
			<div class="content-container-nopadding">
				<div class="overlay-box">
					<div class="heading-text"><?= $page_advert['title'] ?></div>
					<div>
						<a class="btn-see-all" href="<?= $url ?>">Find out more</a>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php
}
	require_once(PATH_INC . "patent-sector_section.inc.php");
?>


<section class="container footer-margin">
	<div class="content-container-nopadding quote">
		<?= decodetext($keyvalue['homepage']['footer-quote']) ?>
	</div>
</section>

<?php
require_once(PATH_INC . "html_foot.inc.php");