<?php
class people extends mysql_table {
	public $pid = "";
	public $table_name = "people";
	public $table_sort = "sort";	
	public $published = 1;
	public $hide_in_nav = false;
	public $hide_in_sitemap = false;
	public $table_search_fields = array('nav_title','firstname','lastname','position','url_title','summary','body','specialist_technical_fields','area_of_expertise','window_title','meta_description','meta_keywords');

	function updatePersonOrder($person_list) {
		
		
		$asq = $this->get();	

		foreach($asq as $story){
			$count = checkSortOrder($person_list,$story['id']);
			$update_arr = array('sort'=>$count);
			$conditions = array('id'=>$story['id']);
			$this->update($this->getTableName(),$conditions, $update_arr);

		}
	}

	function getPerson($pid=null,$published=null) {		
		if(is_null($published)) $published = $this->getIsPublished();

		$result = array();
		/* Get section page */
		if($pid) {
			$conditions = array('url_title'=>$pid);
			if($published !== false) {
				$conditions['is_published'] = $published;
				if($published == 1) {
					if($published == 1) {
						$conditions['sql'] = "((publish_start <= NOW()) AND (publish_end >= NOW() OR publish_end = '0000-00-00 00:00:00'))";
					}
				}
			}

			$result = $this->get(null,$conditions);
			$result = array_shift($result);
		}
		if(!$result && $published == 1) {
			$result = $this->getErrorPage($pid);
		}

		return $result;
	}

	function getErrorPage(){
		return "Error";
	}

	function getPeople($hide_in_nav=null,$hide_in_sitemap=null,$published=null) {		
		if(is_null($published)) $published = $this->getIsPublished();
		if(is_null($hide_in_nav)) $hide_in_nav = $this->getIsHideInNav();
		if(is_null($hide_in_sitemap)) $hide_in_sitemap = $this->getIsHideInSitemap();		

		if($published !== false) {
			$conditions['is_published'] = $published;
			if($published == 1) {
				$conditions['sql'] = "((publish_start <= NOW()) AND (publish_end >= NOW() OR publish_end = '0000-00-00 00:00:00'))";
			}
		}
		if($hide_in_nav !== false) {
			$conditions['hide_in_nav'] = $hide_in_nav;
		}
		if($hide_in_sitemap !== false) {
			$conditions['hide_in_sitemap'] = $hide_in_sitemap;
		}
		

		return $this->get(null,$conditions);
	}	

	function getNextPersonSortValue() {
		return $this->getNextSort();
	}

	function search($skey) {
		return $this->searchFields($skey,"((is_published = 1 AND ((publish_start <= NOW()) AND (publish_end >= NOW() OR publish_end = '0000-00-00 00:00:00'))))");
	}

	/* GET & SET DATA OBJECT PROPERTIES */

	function getIsErrorPage() {
		return $this->error_page;
	}

	function setIsErrorPage($error_page) {
		$this->error_page = $error_page;
	}

	function getIsPublished() {
		return $this->published;
	}

	function setIsPublished($published) {
		$this->published = $published;
	}

	function getIsHideInNav() {
		return $this->hide_in_nav;
	}

	function setIsHideInNav($hide_in_nav) {
		$this->hide_in_nav = $hide_in_nav;
	}

	function getIsHideInSitemap() {
		return $this->hide_in_sitemap;
	}

	function setIsHideInSitemap($hide_in_sitemap) {
		$this->hide_in_sitemap = $hide_in_sitemap;
	}

	function getTitle($id,$table_name=null){
		$table_name = (! $table_name) ? $this->getTableName() : $table_name;
		$out = "";
		$rtn = $this->getSingleResultSet("SELECT * FROM " . $table_name . " WHERE id='" . $id . "' LIMIT 1");	
		if(count($rtn) > 0){
			$out = $rtn['firstname']." ".$rtn['lastname'];
		}
		return $out;
	}
	function getUnselectedItems($section,$items,$table_name=null){
		$table_name = (! $table_name) ? $this->getTableName() : $table_name;

		if($section != 'all')
		{
			if($items != ""){
				$ids = str_replace(";",",",trim($items,';'));
				return $this->getResultSet("SELECT * FROM " . $table_name . " WHERE is_published=1 AND  section_homepage = 0 AND website_section = '".$section."' AND id NOT IN (" . $ids . ") ORDER BY NOT FIND_IN_SET(id, '" . $ids . "')");
			} else {
				return $this->getResultSet("SELECT * FROM " . $table_name . " WHERE is_published=1 AND section_homepage = 0 AND website_section = '".$section."'  ORDER BY title ASC,sort ASC");
				//var_dump(("SELECT * FROM " . $table_name . " WHERE is_published=1 AND section_homepage = 0 AND website_section = '".$section."'  ORDER BY title ASC,sort ASC"));
			}
		}
		else
		{
			if($items != ""){
				$ids = str_replace(";",",",trim($items,';'));
				return $this->getResultSet("SELECT *, CONCAT(firstname,' ' ,lastname) as fullname FROM " . $table_name . " WHERE is_published=1 AND  id NOT IN (" . $ids . ") ORDER BY NOT FIND_IN_SET(id, '" . $ids . "')");
			} else {
				return $this->getResultSet("SELECT *,CONCAT(firstname,' ', lastname) as fullname FROM " . $table_name . " WHERE is_published=1 ORDER BY firstname, lastname ASC,sort ASC");
			}
		}
	}
	function getPeopleByIds($ids){
		$ids = splitToIds($ids);
		$ids[] = "0";
		$ids = implode(",",$ids);

		return $this->getResultSet("SELECT * FROM " . $this->getTableName() . " WHERE id IN (" . $ids . ") ORDER BY FIND_IN_SET(id, '" . $ids . "')");
	}

	function searchPeople($keywords, $sector,$services){
		if($keywords != '' && $sector != 0 && $services != 0)
		{
			$extracondition ="specialist_technical_fields LIKE '%".$sector."%'";
			$extracondition .="AND area_of_expertise LIKE '%".$services."%'";
			$extracondition .="AND ((firstname LIKE '%".$keywords."%') OR (lastname LIKE '%".$keywords."%'))";

			$people_list=$this->searchFields(NULL,$extracondition);
		}
		else if($keywords != '' && $sector != 0)
		{
			$extracondition ="specialist_technical_fields LIKE '%".$sector."%'";
			$extracondition .="AND ((firstname LIKE '%".$keywords."%') OR (lastname LIKE '%".$keywords."%'))";

			$people_list=$this->searchFields(NULL,$extracondition);
		}
		else if($keywords != '' && $services != 0)
		{
			$extracondition ="area_of_expertise LIKE '%".$services."%'";
			$extracondition .="AND ((firstname LIKE '%".$keywords."%') OR (lastname LIKE '%".$keywords."%'))";

			$people_list=$this->searchFields(NULL,$extracondition);
		}
		else if($sector != 0 && $services != 0)
		{
			$extracondition ="specialist_technical_fields LIKE '%".$sector."%'";
			$extracondition .="AND area_of_expertise LIKE '%".$services."%'";

			$people_list=$this->searchFields(NULL,$extracondition);
		}
		else if($keywords != '')
		{
			$extracondition ="((firstname LIKE '%".$keywords."%') OR (lastname LIKE '%".$keywords."%'))";
			$people_list=$this->searchFields(NULL, $extracondition);
		}
		else if($sector != 0)
		{

			$extracondition ="specialist_technical_fields LIKE '%".$sector."%'";
			$people_list=$this->searchFields(NULL,$extracondition);
		}
		else if($services != 0)
		{
			
			$extracondition ="area_of_expertise LIKE '%".$services."%'";
			$people_list=$this->searchFields(NULL,$extracondition);
		}
		else
		{
			$people_list=$this->getPeople();
		}

		return $people_list;
	}

}
