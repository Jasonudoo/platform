<?php
		if (empty($tmpl))
		{
			JRequest::setVar('tmpl', 'component');
		}
		$footer= $OSESoftHelper -> renderOSETM();
		$this->assignRef('footer', $footer);
		$preview_menus= $OSESoftHelper -> getPreviewMenus();
		$this->assignRef('preview_menus', $preview_menus);
		$this->assignRef('OSESoftHelper', $OSESoftHelper);
		
		$title = JText :: _('OSE Membership™ Addon Management');
		$this->assignRef('title', $title);
		