/* Copyright (C) YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

(function($){

	$(document).bind('ready', function() {
		
		/* Accordion menu */
		$('.menu-accordion').accordionMenu({ mode:'slide' });


		/* Dropdown menu */
		$('div#menu').dropdownMenu({ mode: 'height', transition: 'easeOutExpo', fancy: true});
		

		/* Morph: main menu */
		var enterColor = '#c39600';
		var leaveColor = '#323232';
		
		var menuEnter = { 'color': enterColor };
		var menuLeave = { 'color': leaveColor };
		
		$('div#menu li.level1').morph( menuEnter, menuLeave,
		{ transition: 'linear', duration: 300 },
		{ transition: 'easeInSine', duration: 700 }, '.level1');
		
		/* Morph: level2 and deeper items of main menu (drop down) */
		$('div#menu ul.level2 a').morph( menuEnter, menuLeave,
			{ transition: 'easeOutExpo', duration: 300},
			{ transition: 'easeInSine', duration: 500 });
		
		/* Morph: level1 subline of main menu */
		var enterColor = '#aa7800';
		var leaveColor = '#323232';
		
		var menuEnter = { 'color': enterColor };
		var menuLeave = { 'color': leaveColor };
		
		$('div#menu li.level1').morph(menuEnter, menuLeave,
		{ transition: 'easeOutExpo', duration: 300 },
		{ transition: 'easeInSine', duration: 700 }, 'span.sub');
		
		 /* Morph: sub menu */
		var enterColor = '#d25028';
		var leaveColor = '#323232';
		
		var submenuEnter = { 'color': enterColor};
		var submenuLeave = { 'color': leaveColor};
		
		$('div#middle ul.menu a, div#middle ul.menu span.separator').morph(submenuEnter, submenuLeave,
		{ transition: 'easeOutExpo', duration: 100},
		{ transition: 'easeInSine', duration: 700 });

		/* Smoothscroll */
		$('a[href="#page"]').smoothScroller({ duration: 500 });
		
		/* Match height of div tags */
		$('div.topbox div.deepest').matchHeight(40);
		$('div.bottombox div.deepest').matchHeight(40);
		$('div.maintopbox div.deepest').matchHeight(40);
		$('div.mainbottombox div.deepest').matchHeight(40);
		$('div.contenttopbox div.deepest').matchHeight(40);
		$('div.contentbottombox div.deepest').matchHeight(40);

	});

})(jQuery);