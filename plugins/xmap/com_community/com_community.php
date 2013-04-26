<?php
/**
 * @version 4.0
 * @package    JS XMAP
 * @author     Latitud WEB - Joomla Extensions {@link http://joomlaextensions.latitudweb.com}
 * @author     Created on 28-Jun-2010
 * @copyright (C) 2009-10 by Business Excellence through Information Technologies SL - All rights reserved!
 * @license http://joomlaextensions.latitudweb.com Free License GPLv3
 */
 
defined('_JEXEC') or die ('Restricted Access');

class xmap_com_community {

	function &getTree( &$xmap, &$parent, &$params ) {
	
		xmap_com_community::getCommunity($xmap, $parent, $params);

	}
	
	function getCommunity(&$xmap, &$parent, &$params) {
	
		$js15 = xmap_com_community::isJomSocial15();
		$js18 = xmap_com_community::isJomSocial18();
	
		$link_query = parse_url( $parent->link );
        parse_str( html_entity_decode($link_query['query']), $link_vars );
		$view = JArrayHelper::getValue($link_vars,'view','');
		$linksToDisplay = xmap_com_community::getJSHomeMenuLinks($view, $js15, $js18);
		
		$lang = & JFactory::getLanguage();
		$lang->load('com_community', JPATH_SITE);
		
		$profile_priority = !empty($params['profile_priority'])? $params['profile_priority'] : $parent->priority;
		$profile_changefreq = !empty($params['profile_changefreq'])? $params['profile_changefreq'] : $parent->changefreq;
		if (!$linksToDisplay["avoid-groups"]) {
			$groupscat_priority = !empty($params['groupscat_priority'])? $params['groupscat_priority'] : $parent->priority;
			$groupscat_changefreq = !empty($params['groupscat_changefreq'])? $params['groupscat_changefreq'] : $parent->changefreq;
			$groups_priority = !empty($params['groups_priority'])? $params['groups_priority'] : $parent->priority;
			$groups_changefreq = !empty($params['groups_changefreq'])? $params['groups_changefreq'] : $parent->changefreq;
			$bulletins_priority = !empty($params['bulletins_priority'])? $params['bulletins_priority'] : $parent->priority;
			$bulletins_changefreq = !empty($params['bulletins_changefreq'])? $params['bulletins_changefreq'] : $parent->changefreq;
			$discussions_priority = !empty($params['discussions_priority'])? $params['discussions_priority'] : $parent->priority;
			$discussions_changefreq = !empty($params['discussions_changefreq'])? $params['discussions_changefreq'] : $parent->changefreq;
		}
		
		if (!$linksToDisplay["avoid-events"]) {
			$events_priority = !empty($params['events_priority'])? $params['events_priority'] : $parent->priority;
			$events_changefreq = !empty($params['events_changefreq'])? $params['events_changefreq'] : $parent->changefreq;
			$eventscat_priority = !empty($params['eventscat_priority'])? $params['eventscat_priority'] : $parent->priority;
			$eventscat_changefreq = !empty($params['eventscat_changefreq'])? $params['eventscat_changefreq'] : $parent->changefreq;
		}
		
		if ($profile_priority!="-2") {
			if ($profile_priority == "-1") { $profile_priority = $parent->priority; }
			if ($profile_changefreq  == "-1") { $profile_changefreq = $parent->changefreq; }
		
			if (!$linksToDisplay["avoid-photos"]) {
				$photos_priority = !empty($params['photos_priority'])? $params['photos_priority'] : $parent->priority;
				$photos_changefreq = !empty($params['photos_changefreq'])? $params['photos_changefreq'] : $parent->changefreq;
			}
			if (!$linksToDisplay["avoid-videos"]) {
				$videos_priority = !empty($params['videos_priority'])? $params['videos_priority'] : $parent->priority;
				$videos_changefreq = !empty($params['videos_changefreq'])? $params['videos_changefreq'] : $parent->changefreq;
			}
			
			// All Users
			if (!$linksToDisplay["avoid-members"]) {
				$xmap->changeLevel(1);
				$all_users = xmap_com_community::buildNode(JText::sprintf('CC MEMBERS'), 'index.php?option=com_community&amp;view=search&amp;task=browse', $parent->id, 'auel', 0, $profile_priority, $profile_changefreq);
				$xmap->printNode($all_users);
				
				// Member links
				$rows = xmap_com_community::getDBUsers();
				$xmap->changeLevel(1);
				
				foreach($rows as $row) {
					$node_links = xmap_com_community::getProfileLinks($row->id);
					$parent = $all_users;
					foreach ($node_links as $key => $node_link) {
						if ($key == "albums") {
							if (!$linksToDisplay["avoid-photos"]) {
								if ($photos_priority!="-2") {
									// User photos and Albums
									$xmap->changeLevel(1);
									if ($photos_priority == "-1") { $photos_priority = $parent->priority; }
									if ($photos_changefreq  == "-1") { $photos_changefreq = $parent->changefreq; }
									$node = xmap_com_community::buildNode(JText::sprintf('CC USER PHOTOS TITLE', $row->name), $node_link, $parent->id, $parent->uid.'pel'.$key.$row->id, $row->pid, $photos_priority, $photos_changefreq);
						
									$photos = xmap_com_community::getDBPhotos($row->id, $js15);
									if (count($photos)>0) {
										$xmap->printNode($node);
										$xmap->changeLevel(1);
										foreach  ($photos as $photo) {
											$photo_links = xmap_com_community::getPhotoLinks($photo->id, $row->id);
											foreach ($photo_links as $key => $photo_link) {
												if ($key=="album")
													$xmap->printNode(xmap_com_community::buildNode($photo->name, $photo_link, $node->id, $node->uid.'phel'.$photo->id, $photo->pid, $photos_priority, $photos_changefreq));
												else
													$xmap->printNode(xmap_com_community::buildNode(JText::sprintf('CC PHOTOS ALBUM HEADING', $photo->name), $photo_link, $node->id, $node->uid.'phels'.$photo->id, $photo->pid, $photos_priority, $photos_changefreq));
											}
										}
										$xmap->changeLevel(-1);
									} else {
										if ($params['show_empty_albums']==1) {
											$xmap->printNode($node);
										}
									}
									$xmap->changeLevel(-1);
								}
							}
						} else if ($key == "videos") {
							if (!$linksToDisplay["avoid-videos"]) {
								if ($videos_priority!="-2") {
									// User videos
									$xmap->changeLevel(1);
									$videos = xmap_com_community::getDBVideos($row->id, $js15);
									if ($videos_priority == "-1") { $videos_priority = $parent->priority; }
									if ($videos_changefreq  == "-1") { $videos_changefreq = $parent->changefreq; }
									if (count($videos)>0) {
										$xmap->printNode(xmap_com_community::buildNode(JText::sprintf('CC USERS VIDEO TITLE', $row->name), $node_link, $parent->id, $parent->uid.'pel'.$key.$row->id, $row->pid, $videos_priority, $videos_changefreq));
							
										$xmap->changeLevel(1);
					
										foreach ($videos as $key => $video) {
											$video_links = xmap_com_community::getVideoLinks($video->id, $row->id);
											foreach ($video_links as $key => $video_link) {
												$xmap->printNode(xmap_com_community::buildNode($video->name, $video_link, $node->id, $node->uid.'vel'.$video->id, $video->pid, $videos_priority, $videos_changefreq));
											}
										}
										$xmap->changeLevel(-1);
									} else {
										if ($params['show_empty_videos']==1) {
											$xmap->printNode(xmap_com_community::buildNode(JText::sprintf('CC USERS VIDEO TITLE', $row->name), $node_link, $parent->id, $parent->uid.'pel'.$key.$row->id, $row->pid, $videos_priority, $videos_changefreq));
										}
									}	
					
									$xmap->changeLevel(-1);
								}
							}
						} else {
							// User Profiles
							$node = xmap_com_community::buildNode($row->name, $node_link, $parent->id, $parent->uid.'pel'.$key.$row->id, $row->pid, $profile_priority, $profile_changefreq );
							$xmap->printNode($node);
							$parent = $node;
						}
					}
				}
				$xmap->changeLevel(-1);
				$xmap->changeLevel(-1);
			} else {
				//Albums/Photos direct Menu access
				if (!$linksToDisplay["avoid-photos"]) {
					if ($photos_priority!="-2") {
						// User photos and Albums
						$photos = xmap_com_community::getAllDBPhotos($js15, $linksToDisplay);
						if (count($photos)>0) {
							if ($photos_priority == "-1") { $photos_priority = $parent->priority; }
							if ($photos_changefreq  == "-1") { $photos_changefreq = $parent->changefreq; }
							$xmap->changeLevel(1);
							foreach  ($photos as $photo) {
								$photo_links = xmap_com_community::getPhotoLinks($photo->id, $photo->creator);
								foreach ($photo_links as $key => $photo_link) {
									if ($key=="album")
										$xmap->printNode(xmap_com_community::buildNode($photo->name, $photo_link, $node->id, $node->uid.'phel'.$photo->id, $photo->pid, $photos_priority, $photos_changefreq));
									else
										$xmap->printNode(xmap_com_community::buildNode(JText::sprintf('CC PHOTOS ALBUM HEADING', $photo->name), $photo_link, $node->id, $node->uid.'phels'.$photo->id, $photo->pid, $photos_priority, $photos_changefreq));
								}
							}
							$xmap->changeLevel(-1);
						}
					}
				}
				
				//Videos direct Menu access
				if (!$linksToDisplay["avoid-videos"]) {
					if ($videos_priority!="-2") {
						// User videos
						$videos = xmap_com_community::getAllDBVideos($js15, $linksToDisplay);
						if (count($videos)>0) {
							if ($videos_priority == "-1") { $videos_priority = $parent->priority; }
							if ($videos_changefreq  == "-1") { $videos_changefreq = $parent->changefreq; }
							$xmap->changeLevel(1);
							foreach ($videos as $key => $video) {
								$video_links = xmap_com_community::getVideoLinks($video->id, $row->id);
								foreach ($video_links as $key => $video_link) {
									$xmap->printNode(xmap_com_community::buildNode($video->name, $video_link, $node->id, $node->uid.'vel'.$video->id, $video->pid, $videos_priority, $videos_changefreq));
								}
							}
							$xmap->changeLevel(-1);
						}	
					}
				}
			}
		}
		
		// JS Groups
		if (!$linksToDisplay["avoid-groups"]) {
			// All Groups
			if ($groups_priority!="-2") {
				if ($groups_priority == "-1") { $groups_priority = $parent->priority; }
				if ($groups_changefreq  == "-1") { $groups_changefreq = $parent->changefreq; }
				
				if ((!isset($parent->link)) || ($parent->link!='index.php?option=com_community&view=groups')) {
					$xmap->changeLevel(1);
					$all_groups = xmap_com_community::buildNode(JText::sprintf('CC GROUP'), 'index.php?option=com_community&amp;view=groups', $parent->id, 'agel', 0, $groups_priority, $groups_changefreq);
					$xmap->printNode($all_groups);
				}
		
				if ($groupscat_priority!="-2") {
					// Group Categories
					if ($groupscat_priority == "-1") { $groupscat_priority = $parent->priority; }
					if ($groupscat_changefreq  == "-1") { $groupscat_changefreq = $parent->changefreq; }
					if ($bulletins_priority == "-1") { $bulletins_priority = $parent->priority; }
					if ($bulletins_changefreq  == "-1") { $bulletins_changefreq = $parent->changefreq; }
					if ($discussions_priority == "-1") { $discussions_priority = $parent->priority; }
					if ($discussions_changefreq  == "-1") { $discussions_changefreq = $parent->changefreq; }
					if ($photos_priority == "-1") { $photos_priority = $parent->priority; }
					if ($photos_changefreq  == "-1") { $photos_changefreq = $parent->changefreq; }
					$xmap->changeLevel(1);
					$groupcats = xmap_com_community::getDBGroupCats();
					foreach ($groupcats as $key => $groupcat) {
						$groupcat_links = xmap_com_community::getGroupCatLinks($groupcat->id);
						foreach ($groupcat_links as $key => $groupcat_link) {
							$xmap->printNode(xmap_com_community::buildNode($groupcat->name, $groupcat_link, $parent->id, 'gc'.$groupcat->id, $all_groups->id, $groupscat_priority, $groupscat_changefreq));
						}
						// Groups
						$xmap->changeLevel(1);
						$groups = xmap_com_community::getDBGroupsByCategory($groupcat->id);
						foreach ($groups as $key => $group) {
							$group_links = xmap_com_community::getGroupLinks($group->id);
							foreach ($group_links as $key => $group_link) {
								$xmap->printNode(xmap_com_community::buildNode($group->name, $group_link, $parent->id, 'gel'.$group->id, $all_groups->id, $groups_priority, $groups_changefreq));
							}
							
							if ($bulletins_priority!="-2") {
								// Group bulletins
								$xmap->changeLevel(1);
								$bulletins_links = xmap_com_community::getBulletinsLink($group->id);
								foreach ($bulletins_links as $bulletins_link) {
									$xmap->printNode(xmap_com_community::buildNode(JText::sprintf('CC VIEW ALL BULLETINS TITLE', $group->name), $bulletins_link, $group->id, 'gbl'.$group->id, $group->id, $bulletins_priority, $bulletins_changefreq));
								}
								$bulletins = xmap_com_community::getDBGroupNews($group->id);
								if (count($bulletins)>0) {
									$xmap->changeLevel(1);
									foreach ($bulletins as $bulletin) {
										$bulletin_links = xmap_com_community::getBulletinLinks($group->id, $bulletin->id);
										foreach ($bulletin_links as $bulletin_link) {
											$xmap->printNode(xmap_com_community::buildNode($bulletin->title, $bulletin_link, $group->id, 'gb'.$bulletin->id, $group->id, $bulletins_priority, $bulletins_changefreq));
										}
									}
									$xmap->changeLevel(-1);
								}
								$xmap->changeLevel(-1);
							}
							
							if ($discussions_priority!="-2") {
								// Group discussions
								$xmap->changeLevel(1);
								$discussions_links = xmap_com_community::getDiscussionsLink($group->id);
								foreach ($discussions_links as $discussions_link) {
									$xmap->printNode(xmap_com_community::buildNode(JText::sprintf('CC VIEW ALL DISCUSSIONS TITLE', $group->name), $discussions_link, $group->id, 'gdl'.$group->id, $group->id, $groups_priority, $groups_changefreq));
								}
								$discussions = xmap_com_community::getDBGroupDiscussions($group->id);
								if (count($discussions)>0) {
									$xmap->changeLevel(1);
									foreach ($discussions as $discussion) {
										$discussion_links = xmap_com_community::getDiscussionLinks($group->id, $discussion->id);
										foreach ($discussion_links as $discussion_link) {
											$xmap->printNode(xmap_com_community::buildNode($discussion->title, $discussion_link, $group->id, 'gd'.$discussion->id, $group->id, $groups_priority, $groups_changefreq));
										}
									}
									$xmap->changeLevel(-1);
								}
								$xmap->changeLevel(-1);
							}
							
							if ($js15) {
								if ((!$linksToDisplay["avoid-group-photos"]) && ($photos_priority!="-2")) {
									// Group photos
									$xmap->changeLevel(1);
									$group_albums_links = xmap_com_community::getGroupAlbumsLink($group->id);
									foreach ($group_albums_links as $group_albums_link) {
										$xmap->printNode(xmap_com_community::buildNode(JText::sprintf('CC ALL PHOTOS TITLE'), $group_albums_link, $group->id, 'gal'.$group->id, $group->id, $photos_priority, $photos_priority));
									}
									$group_albums = xmap_com_community::getDBGroupPhotos($group->id);
									if (count($group_albums)>0) {
										$xmap->changeLevel(1);
										foreach ($group_albums as $group_album) {
											$group_album_links = xmap_com_community::getGroupAlbumLinks($group_album->id, $group->id);
											foreach ($group_album_links as $key => $group_album_link) {
												if ($key=="group-album")
													$xmap->printNode(xmap_com_community::buildNode($group_album->name, $group_album_link, $group->id, 'ga'.$group_album->id, $group->id, $photos_priority, $photos_changefreq));
												else
													$xmap->printNode(xmap_com_community::buildNode(JText::sprintf('CC PHOTOS ALBUM HEADING', $group_album->name), $group_album_link, $group->id, 'gav'.$group_album->id, $group->id, $photos_priority, $photos_changefreq));
											
												
											}
										}
										$xmap->changeLevel(-1);
									}
									$xmap->changeLevel(-1);
								}
								
								if ((!$linksToDisplay["avoid-group-videos"]) && ($videos_priority!="-2")) {
									// Group videos
									$xmap->changeLevel(1);
									$group_videos_links = xmap_com_community::getGroupVideosLink($group->id);
									foreach ($group_videos_links as $group_videos_link) {
										$xmap->printNode(xmap_com_community::buildNode(JText::sprintf('CC ALL VIDEOS'), $group_videos_link, $group->id, 'gvl'.$group->id, $group->id, $videos_priority, $videos_priority));
									}
									$group_videos = xmap_com_community::getDBGroupVideos($group->id);
									if (count($group_videos)>0) {
										$xmap->changeLevel(1);
										foreach ($group_videos as $group_video) {
											$group_video_links = xmap_com_community::getGroupVideoLinks($group_video->id, $group->id);
											foreach ($group_video_links as $key => $group_video_link) {
												$xmap->printNode(xmap_com_community::buildNode($group_video->name, $group_video_link, $group->id, $group->uid.'gvel'.$group_video->id, $group_video->pid, $videos_priority, $videos_changefreq));
											}
										}
										$xmap->changeLevel(-1);
									}
									$xmap->changeLevel(-1);
								}
							}
						}
						$xmap->changeLevel(-1);
					}
					$xmap->changeLevel(-1);
				}
				if ((!isset($parent->link)) || ($parent->link!='index.php?option=com_community&view=groups')) {
					$xmap->changeLevel(-1);
				}
			}
		}
		
		// JS Events
		if ((!$linksToDisplay["avoid-events"]) && ($js18)) {
			// All Events
			if ($events_priority!="-2") {
				if ($events_priority == "-1") { $events_priority = $parent->priority; }
				if ($events_changefreq  == "-1") { $events_changefreq = $parent->changefreq; }
				if ($eventscat_priority == "-1") { $eventscat_priority = $parent->priority; }
				if ($eventscat_changefreq  == "-1") { $eventscat_changefreq = $parent->changefreq; }
				
				if ((!isset($parent->link)) || ($parent->link!='index.php?option=com_community&view=events')) {
					$xmap->changeLevel(1);
					$all_events = xmap_com_community::buildNode(JText::sprintf('CC EVENTS'), 'index.php?option=com_community&amp;view=events', $parent->id, 'aeel', 0, $events_priority, $events_changefreq);
					$xmap->printNode($all_events);
				}
				
				if ($eventscat_priority!="-2") {
					// Event Categories
					if ($eventscat_priority == "-1") { $eventscat_priority = $parent->priority; }
					if ($eventscat_changefreq  == "-1") { $eventscat_changefreq = $parent->changefreq; }
					
					$xmap->changeLevel(1);
					$eventcats = xmap_com_community::getDBEventCats();
					foreach ($eventcats as $key => $eventcat) {
						$eventcat_links = xmap_com_community::getEventCatLinks($eventcat->id);
						foreach ($eventcat_links as $key => $eventcat_link) {
							$xmap->printNode(xmap_com_community::buildNode($eventcat->name, $eventcat_link, $parent->id, 'ec'.$eventcat->id, $all_events->id, $eventscat_priority, $eventscat_changefreq));
						}
						// Events
						$xmap->changeLevel(1);
						$events = xmap_com_community::getDBEventsByCategory($eventcat->id);
						foreach ($events as $key => $event) {
							$guest_pad = false;
							$event_links = xmap_com_community::getEventLinks($event->id);
							foreach ($event_links as $key => $event_link) {
								switch($key) {
									case "event":
										$xmap->printNode(xmap_com_community::buildNode($event->title, $event_link, $parent->id, 'eel'.$event->id, $all_events->id, $eventscat_priority, $eventscat_changefreq));
										break;
									case "event_adm":
										if (!$guest_pad) {
											$xmap->changeLevel(1);
											$guest_pad = true;
										}
										$xmap->printNode(xmap_com_community::buildNode(JText::sprintf('CC ADMINS'), $event_link, $parent->id, 'eael'.$event->id, $all_events->id, $eventscat_priority, $eventscat_changefreq));
										break;
									case "event_guests":
										if (!$guest_pad) {
											$xmap->changeLevel(1);
											$guest_pad = true;
										}
										$xmap->printNode(xmap_com_community::buildNode(JText::sprintf('CC CONFIRMED GUESTS'), $event_link, $parent->id, 'egel'.$event->id, $all_events->id, $eventscat_priority, $eventscat_changefreq));
										break;
									case "event_await":
										if (!$guest_pad) {
											$xmap->changeLevel(1);
											$guest_pad = true;
										}
										$xmap->printNode(xmap_com_community::buildNode(JText::sprintf('CC PENDING GUESTS'), $event_link, $parent->id, 'ewel'.$event->id, $all_events->id, $eventscat_priority, $eventscat_changefreq));
										break;
									case "event_blocked":
										if (!$guest_pad) {
											$xmap->changeLevel(1);
											$guest_pad = true;
										}
										$xmap->printNode(xmap_com_community::buildNode(JText::sprintf('CC BLOCKED GUESTS'), $event_link, $parent->id, 'ebel'.$event->id, $all_events->id, $eventscat_priority, $eventscat_changefreq));
										break;
								}
							}
							if ($guest_pad) {
								$xmap->changeLevel(-1);
							}
						}
						$xmap->changeLevel(-1);
					}
					$xmap->changeLevel(-1);
				}
				if ((!isset($parent->link)) || ($parent->link!='index.php?option=com_community&view=events')) {
					$xmap->changeLevel(-1);
				}
				
			}
		}
	}
	
	function isJomSocial15() {
		// Check JS v1.5
		return (xmap_com_community::getJomSocialVersion()==='4');
	}
	
	function isJomSocial18() {
		// Check JS v1.8
		return (xmap_com_community::getJomSocialVersion()==='7');
	}
	
	function getJomSocialVersion() {
		// Check the JS Database version.
		$db = &JFactory::getDBO();
		$db->setQuery("SELECT params FROM #__community_config WHERE name=\"dbversion\"");
		return $db->loadResult();
	}
	
	function getDBUsers() {
		// Fetch members
		$db = &JFactory::getDBO();
		$query = "SELECT id, id as pid, username as name FROM #__users WHERE block=0 ORDER BY id";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		return $list;
	}
	
	function getDBPhotos($userid, $js15) {
		// Fetch photos & albums
		$db = &JFactory::getDBO();
		if ($js15)
			$query = "SELECT id, name FROM #__community_photos_albums WHERE permissions=0 AND creator=" . $userid . " AND groupid=0 ORDER BY id";
		else
			$query = "SELECT id, name FROM #__community_photos_albums WHERE permissions=0 AND creator=" . $userid . " ORDER BY id";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		return $list;
	}
	
	function getAllDBPhotos($js15, $linksToDisplay) {
		// Fetch all photos & albums
		$db = &JFactory::getDBO();
		if (($js15) && ($linksToDisplay["avoid-groups-photos"])) 
			$query = "SELECT id, name, creator FROM #__community_photos_albums WHERE permissions=0 AND groupid=0 ORDER BY id";
		else
			$query = "SELECT id, name, creator FROM #__community_photos_albums WHERE permissions=0 ORDER BY id";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		return $list;
	}
	
	function getDBVideos($userid, $js15) {
		// Fetch videos
		$db = &JFactory::getDBO();
		if ($js15)
			$query = "SELECT id, title as name FROM #__community_videos WHERE permissions=0 and creator=" . $userid . " AND groupid=0 ORDER BY id";
		else
			$query = "SELECT id, title as name FROM #__community_videos WHERE permissions=0 and creator=" . $userid . " ORDER BY id";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		return $list;
	}
	
	function getAllDBVideos($js15, $linksToDisplay) {
		// Fetch all videos
		$db = &JFactory::getDBO();
		if (($js15) && ($linksToDisplay["avoid-groups-photos"])) 
			$query = "SELECT id, title as name, creator FROM #__community_videos WHERE permissions=0 AND groupid=0 ORDER BY id";
		else
			$query = "SELECT id, title as name, creator FROM #__community_videos WHERE permissions=0 ORDER BY id";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		return $list;
	}
	
	function getDBGroups(){
		// Fetch Groups
		$db = &JFactory::getDBO();
		$query = "SELECT id, name FROM #__community_groups WHERE published=1 ORDER BY id";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		return $list;
	}
	
	function getDBGroupCats(){
		// Fetch Group Categories
		$db = &JFactory::getDBO();
		$query = "SELECT id, name FROM #__community_groups_category ORDER BY id";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		return $list;
	}
	
	function getDBGroupsByCategory($catid){
		// Fetch Groups
		$db = &JFactory::getDBO();
		$query = "SELECT id, name FROM #__community_groups WHERE published=1 and categoryid=" . $catid . " ORDER BY id";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		return $list;
	}
	
	function getDBGroupNews($groupid){
		// Fetch Group Bulletins
		$db = &JFactory::getDBO();
		$query = "SELECT id, title FROM #__community_groups_bulletins WHERE published=1 and groupid=" . $groupid . " ORDER BY date DESC";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		return $list;
	}
	
	function getDBGroupDiscussions($groupid){
		// Fetch Group Discussions
		$db = &JFactory::getDBO();
		$query = "SELECT id, title FROM #__community_groups_discuss WHERE groupid=" . $groupid . " ORDER BY created DESC";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		return $list;
	}
	
	function getDBGroupPhotos($groupid){
		// Fetch Group Albums
		$db = &JFactory::getDBO();
		$query = "SELECT id, name FROM #__community_photos_albums WHERE permissions=0 AND groupid=" . $groupid . " ORDER BY id";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		return $list;
	}
	
	function getDBGroupVideos($groupid){
		// Fetch Group Videos
		$db = &JFactory::getDBO();
		$query = "SELECT id, title as name FROM #__community_videos WHERE permissions=0 and groupid=" . $groupid . " ORDER BY id";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		return $list;
	}
	
	function getDBEventCats(){
		// Fetch Event Categories
		$db = &JFactory::getDBO();
		$query = "SELECT id, name FROM #__community_events_category ORDER BY id";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		return $list;
	}
	
	function getDBEventsByCategory($catid){
		// Fetch Events
		$db = &JFactory::getDBO();
		$query = "SELECT id, title FROM #__community_events WHERE published=1 and catid=" . $catid . " ORDER BY id";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		return $list;
	}
	
	function getJSHomeMenuLinks($view, $js15, $js18) {
		// JS direct Menu accesses
		$db = &JFactory::getDBO();
		$query = "SELECT m.link FROM #__menu AS m LEFT JOIN #__components AS c ON m.componentid=c.id WHERE m.published=1 AND c.link=\"option=com_community\" AND m.link<>\"index.php?option=com_community&view=frontpage\" ORDER BY m.id";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		
		if ((empty($view)) || ($view=='frontpage')){
			$result = array("avoid-groups" => false,
							"avoid-photos" => false,
							"avoid-videos" => false,
							"avoid-members" => false,
							"avoid-group-photos" => false,
							"avoid-group-videos" => false,
							"avoid-events" => false
						);
			if ((!$js15) && (!$js18)) {
				$result["avoid-group-photos"] = true;
				$result["avoid-group-videos"] = true;
				$result["avoid-events"] = true;
			}
			foreach ($list as $item) {
				if ($item->link=="index.php?option=com_community&view=groups") {
					$result["avoid-groups"] = true;
					$result["avoid-group-photos"] = true;
					$result["avoid-group-videos"] = true;
				} else if ($item->link=="index.php?option=com_community&view=photos") {
					$result["avoid-photos"] = true;
					$result["avoid-group-photos"] = true;
				} else if ($item->link=="index.php?option=com_community&view=videos") {
					$result["avoid-videos"] = true;
					$result["avoid-group-videos"] = true;
				} else if ($item->link=="index.php?option=com_community&view=events") {
					$result["avoid-events"] = true;
				}
			}
			return $result;
		} else {
			$result = array("avoid-groups" => true,
							"avoid-photos" => true,
							"avoid-videos" => true,
							"avoid-members" => true,
							"avoid-group-photos" => true,
							"avoid-group-videos" => true,
							"avoid-events" => true
						);
			switch($view) {
				case "groups":
					$result["avoid-groups"] = false;
					if (($js15) && ($js18)) {
						$result["avoid-group-photos"] = false;
						$result["avoid-group-videos"] = false;
						foreach ($list as $item) {
							if ($item->link=="index.php?option=com_community&view=photos") {
								$result["avoid-group-photos"] = true;
							} else if ($item->link=="index.php?option=com_community&view=videos") {
								$result["avoid-group-videos"] = true;
							}
						}
					}
					break;
				case "photos":
					$result["avoid-photos"] = false;
					$result["avoid-group-photos"] = false;
					break;
				case "videos":
					$result["avoid-videos"] = false;
					$result["avoid-group-videos"] = false;
					break;
				case "events":
					$result["avoid-events"] = false;
					break;
				default:
					break;
			}
			return $result;
		}
	}
	
	function getProfileLinks($id){
		return array(
			"profile" => 'index.php?option=com_community&amp;view=profile&amp;userid='.$id,
			"albums" => 'index.php?option=com_community&amp;view=photos&amp;task=myphotos&amp;userid='.$id,
			"videos" => 'index.php?option=com_community&amp;view=videos&amp;task=myvideos&amp;userid='.$id
			);
	}
	
	function getPhotoLinks($id, $userid){
		return array(
			"album" => 'index.php?option=com_community&amp;view=photos&amp;task=album&amp;albumid='.$id.'&amp;userid='.$userid,
			"display" => 'index.php?option=com_community&amp;view=photos&amp;task=photo&amp;albumid='.$id
			);
	}
	
	function getVideoLinks($id, $userid){
		return array(
			"video" => 'index.php?option=com_community&amp;view=videos&amp;task=video&amp;videoid='.$id.'&userid='.$userid
			);
	}
	
	function getGroupLinks($id){
		return array(
			"group" => 'index.php?option=com_community&amp;view=groups&amp;task=viewgroup&amp;groupid='.$id
			);
	}
	
	function getGroupCatLinks($id){
		return array(
			"groupcat" => 'index.php?option=com_community&amp;view=groups&amp;categoryid='.$id
			);
	}
	
	function getBulletinsLink($groupid){
		return array(
			"bulletins" => 'index.php?option=com_community&amp;view=groups&amp;task=viewbulletins&amp;groupid='.$groupid
			);
	}
	
	function getBulletinLinks($groupid, $bulletinid){
		return array(
			"bulletin" => 'index.php?option=com_community&amp;view=groups&amp;task=viewbulletin&amp;groupid='.$groupid.'&amp;bulletinid='.$bulletinid
			);
	}
	
	function getDiscussionsLink($groupid){
		return array(
			"discussions" => 'index.php?option=com_community&amp;view=groups&amp;task=viewdiscussions&amp;groupid='.$groupid
			);
	}
	
	function getDiscussionLinks($groupid, $topicid){
		return array(
			"discussion" => 'index.php?option=com_community&amp;view=groups&amp;task=viewdiscussion&amp;groupid='.$groupid.'&amp;topicid='.$topicid
			);
	}
	
	function getGroupAlbumsLink($groupid){
		return array(
			"group-albums" => 'index.php?option=com_community&amp;view=photos&amp;groupid='.$groupid
			);
	}
	
	function getGroupAlbumLinks($id, $groupid){
		return array(
			"group-album" => 'index.php?option=com_community&amp;view=photos&amp;task=album&amp;albumid='.$id.'&amp;groupid='.$groupid,
			"group-album-display" => 'index.php?option=com_community&amp;view=photos&amp;task=photo&amp;albumid='.$id.'&amp;groupid='.$groupid
			);
	}
	
	function getGroupVideosLink($groupid){
		return array(
			"group-videos" => 'index.php?option=com_community&amp;view=videos&amp;groupid='.$groupid
			);
	}
	
	function getGroupVideoLinks($id, $groupid){
		return array(
			"group-video" => 'index.php?option=com_community&amp;view=videos&amp;task=video&amp;videoid='.$id.'&amp;groupid='.$groupid
		);
	}
	
	function getEventCatLinks($id){
		return array(
			"eventcat" => 'index.php?option=com_community&amp;view=events&amp;categoryid='.$id
			);
	}
	
	function getEventLinks($id){
		return array(
			"event" => 'index.php?option=com_community&amp;view=events&amp;task=viewevent&amp;eventid='.$id,
			"event_adm" => 'index.php?option=com_community&amp;view=events&amp;task=viewguest&amp;type=-1&amp;eventid='.$id,
			"event_guests" => 'index.php?option=com_community&amp;view=events&amp;task=viewguest&amp;type=1&amp;eventid='.$id,
			"event_await" => 'index.php?option=com_community&amp;view=events&amp;task=viewguest&amp;type=0&amp;eventid='.$id,
			"event_blocked" => 'index.php?option=com_community&amp;view=events&amp;task=viewguest&amp;type=4&amp;eventid='.$id
			);
	}
	
	function buildNode($name, $link, $id, $uid, $pid, $priority, $changefreq) {
		$node = new stdclass;
		$node->name = $name;
		$node->link = $link;
		$node->id   = $id;
		$node->uid  = $uid;   // unique id of the element in all the component
		$node->pid  = $pid;		// parent id
		$node->priority   = $priority;		
		$node->changefreq = $changefreq;
		$node->tree = array();
		return $node;
	}
}