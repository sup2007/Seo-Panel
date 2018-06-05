<?php

/***************************************************************************
 *   Copyright (C) 2009-2011 by Geo Varghese(www.seopanel.in)  	   *
 *   sendtogeo@gmail.com   												   *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 *                                                                         *
 *   This program is distributed in the hope that it will be useful,       *
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of        *
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
 *   GNU General Public License for more details.                          *
 *                                                                         *
 *   You should have received a copy of the GNU General Public License     *
 *   along with this program; if not, write to the                         *
 *   Free Software Foundation, Inc.,                                       *
 *   59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.             *
 ***************************************************************************/

# class defines all connection functions
class ConnectionController extends Controller {
	
		
	/**
	 * Function to display connections
	 */
	function listConnections($info = ''){
	
		$userId = isLoggedIn();
		$sql = "select t.*, k.name keyword from $this->tablName t left join keywords k on t.ref_id=k.id where 1=1";
		$conditions = "";
	
		if (isset($info['status'])) {
			if (($info['status'] == 'success') || ($info['status'] == 'fail')) {
				$statVal = ($info['status']=='success') ? 1 : 0;
				$conditions .= " and crawl_status=$statVal";
				$urlParams .= "&status=".$info['status'];
			}
		} else {
			$info['status'] = '';
		}
		
		$this->set('statVal', $info['status']);
	
		if (empty($info['keyword'])) {
			$info['keyword'] =  '';
		} else {
			$info['keyword'] = urldecode($info['keyword']);
			$searchKeyword = addslashes($info['keyword']);
			$conditions .= " and (ref_id like '%$searchKeyword%' or subject like '%$searchKeyword%' or crawl_referer like '%$searchKeyword%'
			or log_message like '%$searchKeyword%' or k.name like '%$searchKeyword%' or crawl_link like '%$searchKeyword%'
			or crawl_cookie like '%$searchKeyword%' or crawl_post_fields like '%$searchKeyword%' or crawl_useragent like '%$searchKeyword%')";
			$urlParams .= "&keyword=".urlencode($info['keyword']);
		}
		
		$this->set('keyword', $info['keyword']);
		
		$crawlType = "";
		if (!empty($info['crawl_type'])) {
			$crawlType = $info['crawl_type'];
			$conditions .= " and crawl_type='".addslashes($crawlType)."'";
			$urlParams .= "&crawl_type=".$crawlType;
		}
		
		// find different crawl types
		$crawlTypeSql = "select distinct crawl_type from $this->tablName";
		$crawlTypeList = $this->db->select($crawlTypeSql);
		$this->set('crawlTypeList', $crawlTypeList);
		$this->set('crawlType', $crawlType);		
		
		$proxyId = "";
		if (!empty($info['proxy_id'])) {
			$proxyId = $info['proxy_id'];
			$conditions .= " and proxy_id='".intval($proxyId)."'";
			$urlParams .= "&proxy_id=".$proxyId;
		}
		
		// find different proxy used
		$proxySql = "select distinct proxy_id, proxy, port from $this->tablName t, proxylist pl 
		where pl.id=t.proxy_id and t.proxy_id!=0";
		$proxyList = $this->db->select($proxySql);
		$this->set('proxyList', $proxyList);
		$this->set('proxyId', $proxyId);
		
		$seId = "";
		$seController = New SearchEngineController();
		$seList = $seController->__getAllSearchEngines();
		$seNameList = array();
		foreach ($seList as $seInfo) {
			$seNameList[] = $seInfo['domain'];
		}
		
		if (!empty($info['se_id'])) {
			$seId = intval($info['se_id']);
			$conditions .= " and (subject='$seId' or subject in ('".implode(",", $seNameList)."'))";
			$urlParams .= "&se_id=".$seId;
		}
		
		$this->set('seList', $seList);
		$this->set('seId', $seId);		
		
		if (!empty ($info['from_time'])) {
			$fromTime = strtotime($info['from_time'] . ' 00:00:00');
		} else {
			$fromTime = mktime(0, 0, 0, date('m'), date('d') - 30, date('Y'));
		}
		
		if (!empty ($info['to_time'])) {
			$toTime = strtotime($info['to_time'] . ' 00:00:00');
		} else {
			$toTime = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		}
		
		$fromTimeLabel = date('Y-m-d', $fromTime);
		$toTimeLabel = date('Y-m-d', $toTime);
		$this->set('fromTime', $fromTimeLabel);
		$this->set('toTime', $toTimeLabel);
		$urlParams .= "&from_time=$fromTimeLabel&to_time=$toTimeLabel";
		
		// sql created using param
		$sql .= " $conditions and crawl_time >='$fromTimeLabel 00:00:00' and crawl_time<='$toTimeLabel 23:59:59' order by id DESC";
		
		// pagination setup
		$this->db->query($sql, true);
		$this->paging->setDivClass('pagingdiv');
		$this->paging->loadPaging($this->db->noRows, SP_PAGINGNO);
		$pagingDiv = $this->paging->printPages('log.php', '', 'scriptDoLoad', 'content', $urlParams);
		$this->set('pagingDiv', $pagingDiv);
		$sql .= " limit ".$this->paging->start .",". $this->paging->per_page;
	
		$logList = $this->db->select($sql);
		$this->set('pageNo', $info['pageno']);
		$this->set('list', $logList);
		$this->set('urlParams', $urlParams);
		$this->set('fromPopUp', $info['fromPopUp']);
		$this->render('log/crawlloglist');
	}
	
}
?>