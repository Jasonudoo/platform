<?php

class FSF_Glossary
{
	function GetGlossary()
	{
		global $fsf_glossary;
	
		if (empty($fsf_glossary))
		{
			$db =& JFactory::getDBO();
			$query = 'SELECT * FROM #__fsf_glossary WHERE published = 1 ORDER BY LENGTH(word) DESC';
			$db->setQuery($query);
			$row = $db->loadAssocList();
			
			$fsf_glossary = array();
			
			foreach ($row as $data)
			{
				$fsf_glossary[$data['word']] = $data['description'];
			}
		}
	}

	function ReplaceGlossary($text)
	{
		global $fsf_glossary;
		global $fsf_glossary_offset;
		if (empty($fsf_glossary_offset)) $fsf_glossary_offset = 0;
		FSF_Glossary::GetGlossary();
		
		if (count($fsf_glossary) == 0)
			return $text;
				
		$replacewords = array();
		
		foreach($fsf_glossary as $word => $tip)
		{
			$link = "#";
			if (FSF_Settings::get('glossary_link'))
				$link = FSFRoute::_( 'index.php?option=com_fsf&view=glossary&letter='.strtolower(substr($word,0,1)).'#' . $word );
			$title = '';
			if (FSF_Settings::get('glossary_title'))
				$title = '$1';
			
			$key = "XXX_".$fsf_glossary_offset."_XXX";
			$fsf_glossary_offset++;
			$replacewords[$key] = array();
			// need to dissalow anything before or after this is a-z, 0-9, - _
			
			$rword = str_replace("/","\/",$word);
			preg_match_all("/(?!(?:[^<]+>))\b($rword)\b/uis",$text,$matches);
			if (count($matches) > 0)
			{
				foreach($matches[0] as $origword)
					$replacewords[$key][] = $origword;
				$replace = "<a href='$link' class='fsj_tip fsf_glossary_word'>$key</a>";
				$text = preg_replace("/(?!(?:[^<]+>))\b($rword)\b/uis","$replace",$text);
			}
		}
		
		foreach ($replacewords as $key => $words)
		{
			foreach ($words as $word)
			{
				$text = FSF_Glossary::str_replace_first($key, $word, $text);
			}	
		}
		return $text;
	}
	
	function str_replace_first($search, $replace, $subject) {
		$pos = strpos($subject, $search);
		if ($pos !== false) {
			$subject = substr_replace($subject, $replace, $pos, strlen($search));
		}
		return $subject;
	}
	
	function Footer()
	{
		global $fsf_glossary;
		FSF_Glossary::GetGlossary();
		
		if (count($fsf_glossary) == 0)
			return "";
	
		$tail = "<div id='glossary_words' style='display:none;'>";
		
		foreach($fsf_glossary as $word => $tip)
		{
			$wordl = strtolower($word);
			$wordl = preg_replace("/[^a-z0-9]/", "", $wordl);
			if (FSF_Settings::get('glossary_title'))
			{
				$tail .= "<div id='glossary_$wordl'><h4>$word</h4><div class='fsj_gt_inner'>$tip</div></div>";
			} else {
				$tail .= "<div id='glossary_$wordl'><div class='fsj_gt_inner'>$tip</div></div>";
			}
		}
		
		$tail .= "</div>";
		
		return $tail;
	}
}
