<?php

/*
 * LMS version 1.8-cvs
 *
 *  (C) Copyright 2001-2005 LMS Developers
 *
 *  Please, see the doc/AUTHORS for more information about authors!
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License Version 2 as
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 *  USA.
 *
 *  $Id$
 */

function GetDomainList($order='name,asc')
{
	global $DB;

	list($order,$direction) = sscanf($order, '%[^,],%s');

	($direction != 'desc') ? $direction = 'asc' : $direction = 'desc';

	switch($order)
	{
		case 'id':
			$sqlord = " ORDER BY id $direction";
		break;
		case 'description':
			$sqlord = " ORDER BY description $direction";
		break;
		default:
			$sqlord = " ORDER BY name $direction";
		break;
	}

	$list = $DB->GetAll('SELECT id, name, description FROM domains'.($sqlord != '' ? $sqlord : ''));
	
	$list['total'] = sizeof($list);
	$list['order'] = $order;
	$list['direction'] = $direction;

	return $list;
}

if(!isset($_GET['o']))
	$SESSION->restore('dlo', $o);
else
	$o = $_GET['o'];
$SESSION->save('dlo', $o);

if ($SESSION->is_set('dlp') && !isset($_GET['page']))
	$SESSION->restore('dlp', $_GET['page']);
	    
$page = (!isset($_GET['page']) ? 1 : $_GET['page']); 
$pagelimit = (!isset($LMS->CONFIG['phpui']['domainlist_pagelimit']) ? $listdata['total'] : $LMS->CONFIG['phpui']['domainlist_pagelimit']);
$start = ($page - 1) * $pagelimit;

$SESSION->save('dlp', $page);

$layout['pagetitle'] = trans('Domains List');

$domainlist = GetDomainList($o);
$listdata['total'] = $domainlist['total'];
$listdata['order'] = $domainlist['order'];
$listdata['direction'] = $domainlist['direction'];
unset($domainlist['total']);
unset($domainlist['order']);
unset($domainlist['direction']);

$SESSION->save('backto', $_SERVER['QUERY_STRING']);

$SMARTY->assign('pagelimit', $pagelimit);
$SMARTY->assign('page', $page);
$SMARTY->assign('start', $start);
$SMARTY->assign('domainlist', $domainlist);
$SMARTY->assign('listdata', $listdata);
$SMARTY->display('domainlist.html');

?>