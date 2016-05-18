<?php
if($ws['id'] > 0) {
	$website_section=$ws['id'];
	$_POST['data_website_section'] = $ws['id'];

	//** Stops unauthorized users from accessing admin pages
	if($_SESSION[$_SESSION['cmssetup']['access'].'cmsaccess']['is_section_' . $ws['id']] != 1){ header('Location: /admin/'); }
	if(!$_SESSION[$_SESSION['cmssetup']['access'].'cmsaccess']){ header("Location: " . $keyvalue['system-variables']['website-path']); }

	$_SESSION[$_SESSION['cmssetup']['access']]['websection'] = $ws['lookup_key'];
	$_SESSION[$_SESSION['cmssetup']['access']]['navselected'] = str_replace("-","_",$ws['lookup_key']) . "_articles";
	$_SESSION[$_SESSION['cmssetup']['access']]['selectedtitle'] = ucfirst(str_replace("-"," ",$ws['lookup_key'])) . " / Content: Articles";
}

$articles = new articles();

$articles->setWebsiteSection($website_section);
$articles->setRowId($_POST['id']);
$articles->setIsPublished( false );
$articles->setIsHideInNav( false );
$articles->setIsHideInSitemap( false );

//$article_page = "article page url";

$article = $articles->getPages();

//** Get Articles content
if ( $_POST['id'] != "" ) {
	$item = $articles->getRow();
}

//** Save page
if ( $_POST['action'] == "savehomepage" ) {	

	
	if ( $_POST['data_id'] == "" ) { unset( $_POST['data_id'] ); }
	

	$_POST['data_publish_start'] = $_POST['ps_year'] . "-" . $_POST['ps_month'] . "-" . $_POST['ps_day'];
	$_POST['data_publish_end'] = $_POST['pe_year'] . "-" . $_POST['pe_month'] . "-" . $_POST['pe_day'];
	$_POST['data_publication_date'] = $_POST['p_year'] . "-" . $_POST['p_month'] . "-" . $_POST['p_day'];
	

	if ( !$_POST['data_url_title'] ) {
		if ( $_POST['data_nav_title'] ) {
			$_POST['data_url_title'] = strip_url_title( $_POST['data_nav_title'] );
		} else {
			$_POST['data_url_title'] = strip_url_title( $_POST['data_title'] );
			$_POST['data_nav_title'] = $_POST['data_title'];
		}
	} else {
		$_POST['data_url_title'] = strip_url_title( $_POST['data_url_title'] );
	}

	$dataArray = getDataFromPost();

	if ( $item['is_published'] != 1 && $_POST['data_is_published'] == 1 ) {
		$dataArray['published'] = date( 'Y-m-d H:i:s' );
		$dataArray['published_by'] = $_SESSION[$_SESSION['cmssetup']['access'].'cmsaccess']['user_id'];
	}
	if ( $_POST['id'] == "" ) {
		$dataArray['created']=date( 'Y-m-d H:i:s' );
		$dataArray['created_by']=$_SESSION[$_SESSION['cmssetup']['access'].'cmsaccess']['user_id'];
		$dataArray['updated_by']=$_SESSION[$_SESSION['cmssetup']['access'].'cmsaccess']['user_id'];
		$ins = $articles->insertRow( $dataArray );
		$article = $ins;
	} else {
		$dataArray['updated_by']=$_SESSION[$_SESSION['cmssetup']['access'].'cmsaccess']['user_id'];
		$ins = $articles->updateRow( $dataArray );
		$article = $item['id'];
	}

	$_POST['action'] = "";

	$_POST['article'] = $article;		

	$item = $articles->getPage( $article );

	header( "Location: " . $actionpath );
}

//** Delete page
if ( ( $_POST['action'] == "delete" ) && !$hide_delete && !$item['lookup_key'] ) {
	$articles->deleteRow();
	header( "Location: " . $actionpath );
}

//** Toolbar Buttons
if ( $_POST['action'] == "" ) {
	if (!$hide_add && !READONLY ) {
		$toolbarbuttons = "			<li><input type=\"button\" value=\"New Article\" onclick=\"setAction('editpage','" . $formname . "');\" /></li>\n";
	}
}

require_once PATH_ADMIN_INC . "html_head.inc.php";

if ( $_POST['action'] == "editpage" ) {
	//** Edit page
	echo "<input type=\"hidden\" name=\"data_website_section\" value=\"" . $website_section . "\" />\n";
	echo "<input type=\"hidden\" name=\"article\" value=\"" . $_POST['article'] . "\" />\n";
	echo "<table class=\"edit\">\n";
	echo "	<thead>\n";
	echo "		<tr>\n";
	echo "			<td colspan=\"2\">\n";
	if ( $_POST['seltab'] != "" ) { $seltab = $_POST['seltab']; } else { $seltab = "pagedetails"; }
	if ( $seltab == "pagedetails" ) { $st0 = " class=\"tabsel\""; $show_pagedetails = ""; } else { $st0 = ""; $show_pagedetails = " style=\"display: none;\""; }
	if ( $seltab == "seooptions" ) { $st1 = " class=\"tabsel\""; $show_seooptions = ""; } else { $st1 = ""; $show_seooptions = " style=\"display: none;\""; }
	if ( $seltab == "adminoptions" ) { $st2 = " class=\"tabsel\""; $show_adminoptions = ""; } else { $st2 = ""; $show_adminoptions = " style=\"display: none;\""; }
	echo "				<input type=\"hidden\" name=\"seltab\" value=\"" . $seltab . "\"/>\n";
	echo "				<ul class=\"tabs\">\n";
	echo "					<li><a href=\"#\" onclick=\"updateCMSTab('pagedetails','editpage','".$item['id']."','".$formaction."');\"" . $st0 . ">Basic information</a></li>\n";
	echo "					<li><a href=\"#\" onclick=\"updateCMSTab('seooptions','editpage','".$item['id']."','".$formaction."');\"" . $st1 . ">SEO options</a></li>\n";
	echo "					<li><a href=\"#\" onclick=\"updateCMSTab('adminoptions','editpage','".$item['id']."','".$formaction."');\"" . $st2 . ">Admin options</a></li>\n";
	echo "				</ul>\n";
	echo "			</td>\n";
	echo "		</tr>\n";
	echo "	</thead>\n";
	echo "	<tfoot>\n";
	echo "		<tr>\n";
	echo "			<td class=\"buttons\" colspan=\"2\">\n";
	echo "				<input type=\"button\" value=\"Cancel\" onclick=\"goPage('$actionpath');\" />\n";
	if ( !READONLY ) {
		if ( !$hide_delete && !$item['lookup_key'] ) {
			echo "				".displayDelete( $item['id'], $_SESSION[$_SESSION['cmssetup']['access'].'cmsrole'], $formname ) . "\n";
		}
		echo "				<input type=\"button\" value=\"Save\" onclick=\"setActionId('savehomepage','" . $item['id'] . "','$formname');\" />\n";
	}
	echo "			</td>\n";
	echo "		</tr>\n";
	echo "	</tfoot>\n";
	echo "	<tbody>\n";
	echo display_line_header($show_pagedetails,"Basic Information",'1');

	
		

	echo display_check_field("data_is_published","Status:",$item['is_published'],$show_pagedetails,'1,0','0','Published,Draft');

		
	echo display_line_break($show_pagedetails,'rule');
	
	echo display_date_select("data_publication_date","p","Publication date:",$item['publication_date'],$show_pagedetails);
	echo display_text_field( "data_nav_title", "Navigation title:", $item['nav_title'], $show_pagedetails, '1' );
	echo display_text_field( "data_title", "Title:", $item['title'], $show_pagedetails, '1' );
	echo display_text_field( "data_url_title", "URL title:<span class=\"info\">If blank, the name field will be used</span>", $item['url_title'], $show_pagedetails, '1' );	
	echo display_asset_input("data_image","Header Image<span class=\"info\">2645px x 645px</span>",$item['image'],$show_pagedetails);
	echo display_asset_input("data_thumbnail_image","Thumbnail Image<span class=\"info\">630px x 400px</span>",$item['thumbnail_image'],$show_pagedetails);
	echo display_text_area( "data_summary", "Summary:", $item['summary'], $show_pagedetails);
	echo display_richtext_editor( "data_body", "Article text", $item['body'], $show_pagedetails );	
	echo "		<tr ".$show_pagedetails."><td colspan=\"2\">&nbsp;</td></tr>\n";
		
	$tr_style_tag = $show_pagedetails;
	$object_label = "Further information";
	$object_type = "further-information";
	$label_field = "title";
	require PATH_ADMIN_INC . "page_objects.inc.php";

	if($ws['lookup_key'] == 'news-and-views')
	{
		/********* SHOW CASE STUDY **************/

		echo "		<tr ".$show_pagedetails."><td colspan=\"2\">&nbsp;</td></tr>\n";
		echo display_line_header($show_pagedetails,"Case study display option",'1');

		if($_POST['data_show_case_study'] != ""){ $show_case_study = $_POST['data_show_case_study']; } else { $show_case_study = $item['show_case_study']; }

		echo display_check_field("data_show_case_study","Show Case Study:",$item['show_case_study'],$show_pagedetails,'1,0',$show_case_study,'Yes,No',"updateToPage('editpage','" . $item['id'] . "','" . $formname . "','meettheteam')");
	   
	    if($show_case_study == '1')
	    {
	    	echo "		<tr ".$show_pagedetails."><td colspan=\"2\">&nbsp;</td></tr>\n";
			
			$section = $website_sections->getByLookup('case-studies');	
			$casestudy = $pages->getUnselectedItems($section['id'],$item['case_study'],'articles');
			
			$ex = array_filter(explode(";",trim($item['case_study'],';')));

			$ary = array(); $n=0;
			foreach($ex AS $row){
				if($row != ""){
					$ary[$n]['id'] = $row;
					$ary[$n]['title'] = $pages->getTitle($row,'articles');
					$n++;
				}
			}

			$tr_style_tag = $show_pagedetails;
			echo display_multi_select("data_case_study","",$item['case_study'],$casestudy,$ary,$tr_style_tag,array('picker-label'=>' - Articles'));
	    }
	}
	if($ws['lookup_key'] == 'case-studies')
	{
		echo "		<tr ".$show_pagedetails."><td colspan=\"2\">&nbsp;</td></tr>\n";	
		echo display_line_header($show_pagedetails,"News & Views display option",'1');

		if($_POST['data_show_news_and_views'] != ""){ $show_news_and_views = $_POST['data_show_news_and_views']; } else { $show_news_and_views = $item['show_news_and_views']; }

		echo display_check_field("data_show_news_and_views","Show Case Study:",$item['show_news_and_views'],$show_pagedetails,'1,0',$show_news_and_views,'Yes,No',"updateToPage('editpage','" . $item['id'] . "','" . $formname . "','meettheteam')");
	   
	    if($show_news_and_views == '1')
	    {
	    	echo "		<tr ".$show_pagedetails."><td colspan=\"2\">&nbsp;</td></tr>\n";
			
			$section = $website_sections->getByLookup('news-and-views');	
			$newsandviews = $pages->getUnselectedItems($section['id'],$item['news_and_views'],'articles');
			
			$ex = array_filter(explode(";",trim($item['news_and_views'],';')));

			$ary = array(); $n=0;
			foreach($ex AS $row){
				if($row != ""){
					$ary[$n]['id'] = $row;
					$ary[$n]['title'] = $pages->getTitle($row,'articles');
					$n++;
				}
			}

			$tr_style_tag = $show_pagedetails;
			echo display_multi_select("data_news_and_views","",$item['news_and_views'],$newsandviews,$ary,$tr_style_tag,array('picker-label'=>' - Articles'));
	    }
	}

	 /********* SHOW VACANCIES **************/
	
	

		echo display_line_header($show_pagedetails,"Vacancies display option",'1');
		
		if($_POST['data_show_vacancies'] != ""){ $show_vacancies = $_POST['data_show_vacancies']; } else { $show_vacancies = $item['show_vacancies']; }

		echo display_check_field("data_show_vacancies","Show Vacancies::",$item['show_vacancies'],$show_pagedetails,'1,0',$show_vacancies,'Yes,No',"updateToPage('editpage','" . $item['id'] . "','" . $formname ."','meettheteam')");

	    if($show_vacancies == '1')
	    {
	    	echo "		<tr ".$show_pagedetails."><td colspan=\"2\">&nbsp;</td></tr>\n";

			$section = $website_sections->getByLookup('careers');

			$vacancies=new vacancies();

			$vacancies_list = $vacancies->getUnselectedItems('all',$item['vacancies'],'vacancies');

			$ex = array_filter(explode(";",trim($item['vacancies'],';')));

			$ary = array(); $n=0;
			foreach($ex AS $row){
				if($row != ""){
					$ary[$n]['id'] = $row;
					$ary[$n]['title'] = $vacancies->getTitle($row,'vacancies');
					$n++;
				}
			}

			$tr_style_tag = $show_pagedetails;
			echo display_multi_select("data_vacancies","",$item['vacancies'],$vacancies_list,$ary,$tr_style_tag,array('picker-label'=>' - Vacancy','data_array_title'=>'title'));

	    }
	/********* SERVICE **************/

	$services_section = $website_sections->getByLookup('services');
	$services = $pages->getUnselectedItems($services_section['id'], $item['related_services']);
	
	foreach ($services as $service) {
		if($service['parent_id'])
		{
			$parentpage=$pages->getTitle($service['parent_id']);
			$servicearray[]=array('id'=>$service['id'],'title' => "(".$parentpage.") - ".$service['title']);
		}
		else
		{
			$servicearray[]=array('id'=>$service['id'],'title' => $service['title']);
		}
	}

	$ex = array_filter(explode(";",trim($item['related_services'],';')));

	$ary = array(); $n=0;
	
	foreach($ex AS $row){

		if($row != ""){
			$ary[$n]['id'] = $row;

			$pagerecord=$pages->getRow($row);

			if($pagerecord['parent_id'])
			{
				$parentpage=$pages->getTitle($pagerecord['parent_id']);
				$ary[$n]['title'] = "(".$parentpage.") - ".$pages->getTitle($row);
			}
			else
			{
				$ary[$n]['title'] = $pages->getTitle($row);
			}	

			$n++;
		}
	}

	$tr_style_tag = $show_pagedetails;
	echo display_multi_select("data_related_services","Related services",$item['related_services'],$servicearray,$ary,$tr_style_tag,array('picker-label'=>' - Related services'));

	echo "		<tr ".$show_pagedetails."><td colspan=\"2\">&nbsp;</td></tr>\n";

	$patentsector_section = $website_sections->getByLookup('patent-sectors');
	$related_sectors = $pages->getUnselectedItems($patentsector_section['id'], $item['related_sectors']);
	
	$ex = array_filter(explode(";",trim($item['related_sectors'],';')));

	$ary = array(); $n=0;
	foreach($ex AS $row){
		if($row != ""){
			$ary[$n]['id'] = $row;
			$ary[$n]['title'] = $pages->getTitle($row);
			$n++;
		}
	}

	$tr_style_tag = $show_pagedetails;
	echo display_multi_select("data_related_sectors","Related sectors",$item['related_sectors'],$related_sectors,$ary,$tr_style_tag,array('picker-label'=>' - Related sectors'));

	/********* SHOW TEAM **************/
	//if($ws['lookup_key'] == 'case-studies')
	//{
		echo "		<tr ".$show_pagedetails."><td colspan=\"2\">&nbsp;</td></tr>\n";
		echo display_line_header($show_pagedetails,"Meet the team display option",'1');

		if($_POST['data_show_meet_the_team'] != ""){ $show_meet_the_team = $_POST['data_show_meet_the_team']; } else { $show_meet_the_team = $item['show_meet_the_team']; }

		echo display_check_field("data_show_meet_the_team","Show Meet the team:",$item['show_meet_the_team'],$show_pagedetails,'1,0',$show_meet_the_team,'Yes,No',"updateToPage('editpage','" . $item['id'] . "','" . $formname . "','meettheteam')");
	   
	    if($show_meet_the_team == '1')
	    {
	    	echo "		<tr ".$show_pagedetails."><td colspan=\"2\">&nbsp;</td></tr>\n";
			
			$section = $website_sections->getByLookup('people');	
			
			$people=new people();

			$people_list = $people->getUnselectedItems('all',$item['meet_the_team'],'people');
			
			$ex = array_filter(explode(";",trim($item['meet_the_team'],';')));

			$ary = array(); $n=0;
			foreach($ex AS $row){
				if($row != ""){
					$ary[$n]['id'] = $row;
					$ary[$n]['fullname'] = $people->getTitle($row,'people');
					$n++;
				}
			}

			$tr_style_tag = $show_pagedetails;
			echo display_multi_select("data_meet_the_team","",$item['meet_the_team'],$people_list,$ary,$tr_style_tag,array('picker-label'=>' - People','data_array_title'=>'fullname'));
	    }
   // }
    echo "		<tr ".$show_pagedetails."><td colspan=\"2\"><a name=\"meettheteam\"></a></td></tr>\n";

	$tr_style_tag = $show_seooptions;
	require_once PATH_ADMIN_INC . "options_seo.inc.php";

	$tr_style_tag = $show_adminoptions;
	require_once PATH_ADMIN_INC . "options_admin.inc.php";

	echo "		<tr><td colspan=\"2\">&nbsp;</td></tr>\n";
	echo "	</tbody>\n";
	echo "</table>\n";

} else {
	//** View default
	
	$cspan = "6";
	$condition=array('website_section' => $website_section);

	$geq = $articles->getYears(null,$condition);

	if(count($geq) > 0){
		echo "<table class=\"view long\">\n";
		echo "	<tbody>\n";

		foreach($geq as $lvl1){
			if($lvl1['year'] == $_GET['lvl1']){
				$geq2 = $articles->getMonths($lvl1['year'],null,$condition);
				echo "		<tr>\n";
				echo "			<td class=\"lvl1 long\" colspan=\"" . $cspan . "\"><a href=\"" . $actionpath . "\" class=\"sel\">" . $lvl1['year'] . "</a></td>\n";
				echo "		</tr>\n";

				foreach($geq2 as $lvl2){
					if($lvl2['month'] == $_GET['lvl2']){
						echo "		<tr>\n";
						echo "			<td class=\"lvl2 long\" colspan=\"" . $cspan . "\"><a href=\"" . $actionpath . "?lvl1=" . urlencode($lvl1['year']) . "\" class=\"sel\">" . $lvl2['monthname'] . "</a></td>\n";
						echo "		</tr>\n";

						echo "		<tr>\n";
						echo "			<th>&nbsp;</th>\n";
						echo "			<th>Title</th>\n";
						echo "			<th>URL</th>\n";
						echo "			<th>Publication date</th>\n";
						echo "			<th>Status</th>\n";;
						echo "			<th>&nbsp;</th>\n";
						echo "		</tr>\n";


						$pq = $articles->getRowsByDate($lvl2['month'],$lvl1['year'],null,$condition);
						
						$i = 1;
						foreach($pq as $article){
							if(($i%2) === 0){ $str_class = "odd"; } else { $str_class = "even"; }

							echo "		<tr class=\"" . $str_class . "\" style=\"vertical-align: top;\">\n";
							echo "			<td class=\"value\">" . $i . "</td>\n";
							echo "			<td class=\"value long\">" . ((strlen($article['title']) > 100) ? substr($article['title'],0,97) . "..." : $article['title']) . "</td>\n";
							echo "			<td class=\"value nowrap\"><a target=\"_blank\" href=\"/" . $ws['url_title'] . "/" . date('Y/m/d',strtotime($article['publication_date'])) . "/" . $article['url_title'] . "\">Link</a></td>\n";
							echo "			<td class=\"value nowrap\">" . date("d.m.Y", strtotime($article['publication_date'])) . "</td>\n";
							echo "			<td class=\"value nowrap\">" . (($article['is_published'] == 0) ? "Draft" : "Published") . "</td>\n";
							if(!READONLY) {
								echo "			<td><input type=\"button\" class=\"button\" value=\"Edit\" onclick=\"setActionId('editpage','" . $article['id'] . "','$formname');\" /></td>\n";
							} else {
								echo "			<td><input type=\"button\" class=\"button\" value=\"View\" onclick=\"setActionId('editpage','" . $article['id'] . "','$formname');\" /></td>\n";
							}
							echo "		</tr>\n";
							$i++;
						}
						echo "		<tr class=\"noover\"><td colspan=\"" . $cspan . "\">&nbsp;</td></tr>\n";
					} else {
						echo "		<tr>\n";
						echo "			<td class=\"lvl2 long\" colspan=\"" . $cspan . "\"><a href=\"" . $actionpath . "?lvl1=" . urlencode($lvl1['year']) . "&lvl2=" . urlencode($lvl2['month']) . "\">" . $lvl2['monthname'] . "</a></td>\n";
						echo "		</tr>\n";
					}
				}
			} else {
				echo "		<tr>\n";
				echo "			<td class=\"lvl1 long\" colspan=\"" . $cspan . "\"><a href=\"" . $actionpath . "?lvl1=" . urlencode($lvl1['year']) . "\">" . $lvl1['year'] . "</a></td>\n";
				echo "		</tr>\n";
			}
		}
		echo "	</tbody>\n";
		echo "</table>\n";
	} else {
		echo "<p>There are currently no news items.</p>\n";
	}
}

require_once PATH_ADMIN_INC . "html_foot.inc.php";
