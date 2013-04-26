<?php
/*
Generic editor and list class

List:
	Fields to list
	Main Field for link
	always has published
	always has author
	sometimes has ordering
	sometimes has added date
	sometimes has modifed date
	
	optional lookup fields for category
	
	optional split fields based on page break (annoucne + faq)
	
	*/
require_once (JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'content.php');

class FSF_ContentEdit_FAQs extends FSF_ContentEdit
{
	function __construct()
	{
		$this->id = "faqs";
		$this->descs = JText::_("FAQS");
		$this->has_ordering = 1;
		
		$this->table = "#__fsf_faq_faq";
		
		$this->fields = array();

		$field = new FSF_Content_Field("question",JText::_("QUESTION"),"long");
		$field->link = 1;
		$field->search = 1;
		$this->AddField($field);

		$field = new FSF_Content_Field("faq_cat_id",JText::_("CATEGORY"),"lookup");
		$field->lookup_table = "#__fsf_faq_cat";
		$field->lookup_required = 1;
		$field->lookup_id = "id";
		$field->lookup_order = "title";
		$field->lookup_title = "title";
		$field->lookup_select_msg = JText::_("PLEASE_SELECT_A_CATEGORY");
		$this->AddField($field);
		
		$field = new FSF_Content_Field("answer",JText::_("ANSWER"),"text");
		$field->search = 1;
		$field->more = "fullanswer";
		$this->AddField($field);
		
		$this->list = array();
		$this->list[] = "question";
		$this->list[] = "faq_cat_id";
		
		$this->edit = array();
		$this->edit[] = "faq_cat_id";
		$this->edit[] = "question";
		$this->edit[] = "answer";
		
		$this->order = "ordering DESC";
		
		$this->link = "index.php?option=com_fsf&view=faq&faqid=%ID%";

		$filter = new FSF_Content_Filter("faq_cat_id","id","title","#__fsf_faq_cat","title","CATEGORY");
		$this->AddFilter($filter);
	}
}