/* Copyright (C) YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

(function(a){var f=function(){};a.extend(f.prototype,{name:"dropdownMenu",options:{mode:"default",itemSelector:"li",firstLevelSelector:"li.level1",dropdownSelector:"ul",duration:600,remainTime:800,remainClass:"remain",transition:"easeOutExpo",withopacity:!0,centerDropdown:!1,reverseAnimation:!1,fixWidth:!1,fancy:null},initialize:function(c,d){this.options=a.extend({},this.options,d);var b=this,h=null,f=!1;this.menu=c;this.dropdowns=[];this.options.withopacity=a.browser.msie&&parseFloat(a.browser.version)<
9?!1:this.options.withopacity;if(this.options.fixWidth){var p=5;this.menu.children().each(function(){p+=a(this).width()});this.menu.css("width",p)}this.menu.find(this.options.firstLevelSelector).each(function(d){var k=a(this),g=k.find(b.options.dropdownSelector).css({overflow:"hidden"});if(g.length){g.css("overflow","hidden");var e=a("<div>").data("dpwidth",parseFloat(g.width())).data("dpheight",parseFloat(g.height())).css({overflow:"hidden"}).append("<div></div>"),c=e.find("div:first").css({"min-width":e.data("dpwidth"),
"min-height":e.data("dpheight")});g.children().appendTo(c);e.appendTo(g);b.options.centerDropdown&&g.css("margin-left",(parseFloat(g.css("width"))/2-k.width()/2)*-1);b.dropdowns.push({dropdown:g,div:e,innerdiv:c})}k.bind({mouseenter:function(){f=!0;b.menu.trigger("menu:enter",[k,d]);if(h){if(h.index==d)return;h.item.removeClass(b.options.remainClass);h.div.hide()}if(g.length){k.addClass(b.options.remainClass);e.stop().show();var a=e.data("dpwidth"),c=e.data("dpheight");switch(b.options.mode){case "diagonal":var i=
{width:0,height:0},a={width:a,height:c};if(b.options.withopacity)i.opacity=0,a.opacity=1;e.css(i).animate(a,b.options.duration,b.options.transition);break;case "height":i={width:a,height:0};a={height:c};if(b.options.withopacity)i.opacity=0,a.opacity=1;e.css(i).animate(a,b.options.duration,b.options.transition);break;case "width":i={width:0,height:c};a={width:a};if(b.options.withopacity)i.opacity=0,a.opacity=1;e.css(i).animate(a,b.options.duration,b.options.transition);break;case "slide":g.css({width:a,
height:c});e.css({width:a,height:c,"margin-top":c*-1}).animate({"margin-top":0},b.options.duration,b.options.transition);break;default:i={width:a,height:c};a={};if(b.options.withopacity)i.opacity=0,a.opacity=1;e.css(i).animate(a,b.options.duration,b.options.transition)}h={item:k,div:e,index:d}}else h=active=null},mouseleave:function(c){if(c.srcElement&&a(c.srcElement).hasClass("module"))return!1;f=!1;g.length?window.setTimeout(function(){if(!(f||e.css("display")=="none")){b.menu.trigger("menu:leave",
[k,d]);var a=function(){k.removeClass(b.options.remainClass);h=null;e.hide()};if(b.options.reverseAnimation)switch(b.options.mode){case "diagonal":var c={width:0,height:0};if(b.options.withopacity)c.opacity=0;e.stop().animate(c,b.options.duration,b.options.transition,function(){a()});break;case "height":c={height:0};if(b.options.withopacity)c.opacity=0;e.stop().animate(c,b.options.duration,b.options.transition,function(){a()});break;case "width":c={width:0};if(b.options.withopacity)c.opacity=0;e.stop().animate(c,
b.options.duration,b.options.transition,function(){a()});break;case "slide":e.stop().animate({"margin-top":parseFloat(e.data("dpheight"))*-1},b.options.duration,b.options.transition,function(){a()});break;default:c={};if(b.options.withopacity)c.opacity=0;e.stop().animate(c,b.options.duration,b.options.transition,function(){a()})}else a()}},b.options.remainTime):b.menu.trigger("menu:leave")}})});if(this.options.fancy){var j=a.extend({mode:"move",transition:"easeOutExpo",duration:500,onEnter:null,onLeave:null},
this.options.fancy),l=this.menu.append('<div class="fancy bg1"><div class="fancy-1"><div class="fancy-2"><div class="fancy-3"></div></div></div></div>').find(".fancy:first").hide(),m=this.menu.find(".active:first"),n=null,o=function(b,a){if(!a||!(n&&b.get(0)==n.get(0)))l.stop().show().css("visibility","visible"),j.mode=="move"?!m.length&&!a?l.hide():l.animate({left:b.position().left+"px",width:b.width()+"px"},j.duration,j.transition):a?l.css({opacity:m?0:1,left:b.position().left+"px",width:b.width()+
"px"}).animate({opacity:1},j.duration):l.animate({opacity:0},j.duration),n=a?b:null};this.menu.bind({"menu:enter":function(b,a,c){o(a,!0);if(j.onEnter)j.onEnter(a,c,l)},"menu:leave":function(b,a,c){o(m,!1);if(j.onLeave)j.onLeave(a,c,l)}});m.length&&j.mode=="move"&&o(m,!0)}},matchHeight:function(){this.menu&&(this.menu.find("li.level2 div.sub").each(function(){var c=a(this),d=c.parent().find("div.hover-box4:first"),b=Math.max(c.height(),d.height());a([c,d]).each(function(){this.css("height",b)})}),
this._updateDimensions())},matchUlHeight:function(){this.menu&&(this.menu.find("div.dropdown-3").each(function(){var c=a(this).children(),d=0;a(c).each(function(){d=Math.max(a(this).height(),d)});a(c).each(function(){a(this).css("height",d)})}),this._updateDimensions())},_updateDimensions:function(){a(this.dropdowns).each(function(a,d){d.div.stop().show().data({dpwidth:d.innerdiv.width(),dpheight:d.innerdiv.height()}).hide();d.dropdown.css({"min-width":d.div.data("dpwidth"),"min-height":d.div.data("dpheight")})})}});
a.fn[f.prototype.name]=function(){var c=arguments,d=c[0]?c[0]:null;return this.each(function(){var b=a(this);if(f.prototype[d]&&b.data(f.prototype.name)&&d!="initialize")b.data(f.prototype.name)[d].apply(b.data(f.prototype.name),Array.prototype.slice.call(c,1));else if(!d||a.isPlainObject(d)){var h=new f;f.prototype.initialize&&h.initialize.apply(h,a.merge([b],c));b.data(f.prototype.name,h)}else a.error("Method "+d+" does not exist on jQuery."+f.name)})}})(jQuery);