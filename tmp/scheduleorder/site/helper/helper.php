<?php
/**
 * @version		1.0.0
 * @package		Joomla
 * @subpackage	Schedule Order
 * @author      Jason<jason@netwebx.com
 * @copyright	Copyright (C) 2013 NetWebX.COM
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die('Restricted Access');

class ScheduleOrderHelper
{
    /**
     * Get configuration data and store in config object
     *
     * @return object
     */
    public static function getConfig($nl2br = false, $language = null)
    {
        static $config;
        if (!$config) {
            $config = new stdClass;
            $db = JFactory::getDBO();
            if (!$language) {
                if (JLanguageMultilang::isEnabled()
                        || JFactory::getApplication()->isSite())
                    $language = JFactory::getLanguage()->getTag();
                else
                    $language = JComponentHelper::getParams('com_languages')
                            ->get('site', 'en-GB');
            }
            //Check to see whether there is any settings with this language
            $sql = 'SELECT COUNT(*) FROM #__eb_configs WHERE `language`="'
                    . $language . '"';
            $db->setQuery($sql);
            $total = $db->loadResult();
            if (!$total) {
                $defaultLanguage = JComponentHelper::getParams('com_languages')
                        ->get('site', 'en-GB');
                $sql = 'SELECT * FROM #__eb_configs WHERE `language`="'
                        . $defaultLanguage . '"';
            } else {
                $sql = 'SELECT * FROM #__eb_configs WHERE `language`="'
                        . $language . '"';
            }
            $db->setQuery($sql);
            $rows = $db->loadObjectList();
            for ($i = 0, $n = count($rows); $i < $n; $i++) {
                $row = $rows[$i];
                $key = $row->config_key;
                $value = stripslashes($row->config_value);
                if ($nl2br)
                    $value = nl2br($value);
                $config->$key = $value;
            }
        }

        return $config;
    }

    public static function getURL()
    {
        static $url;
        if (!$url) {
            $ssl = ScheduleOrderHelper::getConfigValue('use_https');
            $url = JURI::base();
            if ($ssl)
                $url = str_replace('http://', 'https://', $url);
        }

        return $url;
    }
    /**
     * Get specify config value
     *
     * @param string $key
     */
    public static function getConfigValue($key)
    {
        $db = JFactory::getDBO();
        $defaultLanguage = JComponentHelper::getParams('com_languages')
                ->get('site', 'en-GB');
        if (JLanguageMultilang::isEnabled()
                || JFactory::getApplication()->isSite())
            $language = JFactory::getLanguage()->getTag();
        else
            $language = $defaultLanguage;
        $sql = 'SELECT COUNT(*) FROM #__eb_configs WHERE `language`="'
                . $language . '" AND config_key="' . $key . '"';
        $db->setQuery($sql);
        $total = $db->loadResult();
        if (!$total) {
            $sql = 'SELECT config_value FROM #__eb_configs WHERE `language`="'
                    . $defaultLanguage . '" AND config_key="' . $key . '"';
        } else {
            $sql = 'SELECT config_value FROM #__eb_configs WHERE `language`="'
                    . $language . '" AND config_key="' . $key . '" ';
        }
        $db->setQuery($sql);

        return $db->loadResult();
    }
    /**
     * Get Itemid of Joom Donation
     *
     * @return int
     */
    public static function getItemid()
    {
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $sql = "SELECT id FROM #__menu WHERE link LIKE '%index.php?option=" . COMPONENT_NAME . "%' AND published=1 AND `access` IN ("
                . implode(',', $user->getAuthorisedViewLevels())
                . ") ORDER BY `access`";
        $db->setQuery($sql);
        $itemId = $db->loadResult();
        if (!$itemId) {
            $Itemid = JRequest::getInt('Itemid');
            if ($Itemid == 1)
                $itemId = 999999;
            else
                $itemId = $Itemid;
        }
        return $itemId;
    }

    public static function formatCurrency($amount, $config,
            $currencySymbol = null)
    {
        $decimals = isset($config->decimals) ? $config->decimals : 2;
        $dec_point = isset($config->dec_point) ? $config->dec_point : '.';
        $thousands_sep = isset($config->thousands_sep) ? $config->thousands_sep
                : ',';
        $symbol = $currencySymbol ? $currencySymbol : $config->currency_symbol;

        return $config->currency_position ? (number_format($amount, $decimals,
                        $dec_point, $thousands_sep) . $symbol)
                : ($symbol
                        . number_format($amount, $decimals, $dec_point,
                                $thousands_sep));
    }
    /**
     * Load language from main component
     *
     */
    public static function loadLanguage()
    {
        static $loaded;
        if (!$loaded) {
            $lang = JFactory::getLanguage();
            $tag = $lang->getTag();
            if (!$tag)
                $tag = 'en-GB';
            $lang->load(COMPONENT_NAME, JPATH_ROOT, $tag);
            $loaded = true;
        }
    }
    /**
     * Get email content. For [PAYMENT_DETAIL] tag
     *
     * @param object $config
     * @param object $row
     * @return string
     */ 
    public static function getEmailContent($config, $row)
    {
        $Itemid = JRequest::getInt('Itemid');
        $db = JFactory::getDBO();
        $viewConfig['name'] = 'form';
        $viewConfig['base_path'] = JPATH_ROOT . '/components/' . COMPONENT_NAME . '/emailtemplates';
        $viewConfig['template_path'] = JPATH_ROOT . '/components/' . COMPONENT_NAME . '/emailtemplates';
        //We will need to check
        if ($config->multiple_booking) {
            $viewConfig['layout'] = 'cart';
        } else {
            //Check to see whether this registration record
            $sql = 'SELECT COUNT(*) FROM #__eb_registrants WHERE group_id='
                    . $row->id;
            $db->setQuery($sql);
            $total = $db->loadResult();
            if ($total)
                $viewConfig['layout'] = 'group_detail';
            else
                $viewConfig['layout'] = 'individual_detail';
        }
        $view = new JViewLegacy($viewConfig);
        if ($config->multiple_booking) {
            $view->assignRef('config', $config);
            $view->assignRef('row', $row);
            $view->assignRef('Itemid', $Itemid);
            $sql = 'SELECT a.*, b.event_date, b.title FROM #__eb_registrants AS a INNER JOIN #__eb_events AS b ON a.event_id=b.id WHERE a.id='
                    . $row->id . ' OR a.cart_id=' . $row->id;
            $db->setQuery($sql);
            $rows = $db->loadObjectList();
            $sql = 'SELECT SUM(total_amount) FROM #__eb_registrants WHERE id='
                    . $row->id . ' OR cart_id=' . $row->id;
            $db->setQuery($sql);
            $totalAmount = $db->loadResult();

            $sql = 'SELECT SUM(tax_amount) FROM #__eb_registrants WHERE id='
                    . $row->id . ' OR cart_id=' . $row->id;
            $db->setQuery($sql);
            $taxAmount = $db->loadResult();

            $sql = 'SELECT SUM(discount_amount) FROM #__eb_registrants WHERE id='
                    . $row->id . ' OR cart_id=' . $row->id;
            $db->setQuery($sql);
            $discountAmount = $db->loadResult();
            $amount = $totalAmount - $discountAmount;

            $sql = 'SELECT SUM(deposit_amount) FROM #__eb_registrants WHERE id='
                    . $row->id . ' OR cart_id=' . $row->id;
            $db->setQuery($sql);
            $depositAmount = $db->loadResult();

            //Added support for custom field feature
            $jcFields = new JCFields($row->id, false, 4);
            $view->assignRef('jcFields', $jcFields);

            $view->assignRef('discountAmount', $discountAmount);
            $view->assignRef('totalAmount', $totalAmount);
            $view->assignRef('items', $rows);
            $view->assignRef('amount', $amount);
            $view->assignRef('taxAmount', $taxAmount);
            $view->assignRef('depositAmount', $depositAmount);
        } else {
            $sql = 'SELECT event_date, title, currency_symbol, params FROM #__eb_events WHERE id='
                    . $row->event_id;
            $db->setQuery($sql);
            $rowEvent = $db->loadObject();
            $sql = 'SELECT a.* FROM #__eb_locations AS a '
                    . ' INNER JOIN #__eb_events AS b '
                    . ' ON a.id = b.location_id ' . ' WHERE b.id ='
                    . $row->event_id;
            ;
            $db->setQuery($sql);
            $rowLocation = $db->loadObject();
            //Override config			
            $params = new JRegistry($rowEvent->params);
            $keys = array('s_lastname', 'r_lastname', 's_organization',
                    'r_organization', 's_address', 'r_address', 's_address2',
                    'r_address2', 's_city', 'r_city', 's_state', 'r_state',
                    's_zip', 'r_zip', 's_country', 'r_country', 's_phone',
                    'r_phone', 's_fax', 'r_fax', 's_comment', 'r_comment');
            foreach ($keys as $key) {
                $config->$key = $params->get($key, 0);
            }
            $keys = array('gr_lastname', 'gr_lastname', 'gs_organization',
                    'gr_organization', 'gs_address', 'gr_address',
                    'gs_address2', 'gr_address2', 'gs_city', 'gr_city',
                    'gs_state', 'gr_state', 'gs_zip', 'gr_zip', 'gs_country',
                    'gr_country', 'gs_phone', 'gr_phone', 'gs_fax', 'gr_fax',
                    'gs_email', 'gr_email', 'gs_comment', 'gr_comment');
            foreach ($keys as $key) {
                $config->$key = $params->get($key, 0);
            }

            $view->assignRef('rowEvent', $rowEvent);
            $view->assign('config', $config);
            $view->assignRef('row', $row);
            $view->assignRef('rowLocation', $rowLocation);
            if (ScheduleOrderHelper::isGroupRegistration($row->id)) {
                $sql = 'SELECT * FROM #__eb_registrants WHERE group_id='
                        . $row->id;
                $db->setQuery($sql);
                $rowMembers = $db->loadObjectList();
                $view->assignRef('rowMembers', $rowMembers);
            } else {
                $jcFields = new JCFields($row->event_id, false, 0);
                $view->assignRef('jcFields', $jcFields);
            }
        }
        ob_start();
        $view->display();
        $text = ob_get_contents();
        ob_end_clean();
        return $text;
    }
    /**
     * Build category dropdown
     *
     * @param int $selected
     * @param string $name
     * @param Boolean $onChange
     * @return string
     */
    public static function buildCategoryDropdown($selected, $name = "parent",
            $onChange = true)
    {
        $db = JFactory::getDBO();
        $sql = "SELECT id, parent, parent AS parent_id, name, name AS title FROM #__eb_categories";
        $db->setQuery($sql);
        $rows = $db->loadObjectList();
        $children = array();
        if ($rows) {
            // first pass - collect children
            foreach ($rows as $v) {
                $pt = $v->parent;
                $list = @$children[$pt] ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }
        $list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999,
                0, 0);
        $options = array();
        $options[] = JHTML::_('select.option', '0', JText::_('Top'));
        foreach ($list as $item) {
            $options[] = JHTML::_('select.option', $item->id,
                    '&nbsp;&nbsp;&nbsp;' . $item->treename);
        }

        if ($onChange)
            return JHtml::_('select.genericlist', $options, $name,
                    array('option.text.toHtml' => false,
                            'option.text' => 'text', 'option.value' => 'value',
                            'list.attr' => 'class="inputbox" onchange="submit();"',
                            'list.select' => $selected));
        else
            return JHtml::_('select.genericlist', $options, $name,
                    array('option.text.toHtml' => false,
                            'option.text' => 'text', 'option.value' => 'value',
                            'list.attr' => 'class="inputbox" ',
                            'list.select' => $selected));

    }
    /**
     * Parent category select list
     *
     * @param object $row
     * @return void
     */
    public static function parentCategories($row)
    {
        $db = JFactory::getDBO();
        $sql = "SELECT id, parent, parent AS parent_id, name, name AS title FROM #__eb_categories";
        if ($row->id)
            $sql .= ' WHERE id != ' . $row->id;
        if (!$row->parent) {
            $row->parent = 0;
        }
        $db->setQuery($sql);
        $rows = $db->loadObjectList();
        $children = array();
        if ($rows) {
            // first pass - collect children
            foreach ($rows as $v) {
                $pt = $v->parent;
                $list = @$children[$pt] ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }
        $list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999,
                0, 0);

        $options = array();
        $options[] = JHTML::_('select.option', '0', JText::_('Top'));
        foreach ($list as $item) {
            $options[] = JHTML::_('select.option', $item->id,
                    '&nbsp;&nbsp;&nbsp;' . $item->treename);
        }

        return JHtml::_('select.genericlist', $options, 'parent',
                array('option.text.toHtml' => false, 'option.text' => 'text',
                        'option.value' => 'value',
                        'list.attr' => ' class="inputbox" ',
                        'list.select' => $row->parent));
    }

    public static function attachmentList($attachment, $config)
    {
        jimport('joomla.filesystem.folder');
        $path = JPATH_ROOT . '/media/' . COMPONENT_NAME;
        $files = JFolder::files($path, strlen(trim($config->attachment_file_types)) ? $config->attachment_file_types : 'bmp|gif|jpg|png|swf|zip|doc|pdf|xls');
        $options = array();
        $options[] = JHTML::_('select.option', '', JText::_('EB_SELECT_ATTACHMENT'));
        for ($i = 0, $n = count($files); $i < $n; $i++) 
        {
            $file = $files[$i];
            $options[] = JHTML::_('select.option', $file, $file);
        }
        return JHTML::_('select.genericlist', $options, 'attachment',
                'class="inputbox"', 'value', 'text', $attachment);
    }

    /**
     * Get total document of a category
     *
     * @param int $categoryId
     */
    public static function getTotalEvent($categoryId, $includeChildren = true)
    {
        $user = JFactory::getUser();
        $hidePastEvents = ScheduleOrderHelper::getConfigValue('hide_past_events');
        $db = JFactory::getDBO();
        $arrCats = array();
        $cats = array();
        $arrCats[] = $categoryId;
        $cats[] = $categoryId;
        if ($includeChildren) {
            while (count($arrCats)) {
                $catId = array_pop($arrCats);
                //Get list of children category
                $sql = 'SELECT id FROM #__eb_categories WHERE parent=' . $catId
                        . ' AND published=1';
                $db->setQuery($sql);
                $rows = $db->loadObjectList();
                for ($i = 0, $n = count($rows); $i < $n; $i++) {
                    $row = $rows[$i];
                    $arrCats[] = $row->id;
                    $cats[] = $row->id;
                }
            }
        }

        if ($hidePastEvents)
            $sql = 'SELECT COUNT(a.id) FROM #__eb_events AS a INNER JOIN #__eb_event_categories AS b ON a.id = b.event_id WHERE b.category_id IN('
                    . implode(',', $cats)
                    . ') AND published = 1 AND `access` IN ('
                    . implode(',', $user->getAuthorisedViewLevels())
                    . ') AND event_date >= NOW() ';
        else
            $sql = 'SELECT COUNT(a.id) FROM #__eb_events AS a INNER JOIN #__eb_event_categories AS b ON a.id = b.event_id WHERE b.category_id IN('
                    . implode(',', $cats) . ') AND `access` IN ('
                    . implode(',', $user->getAuthorisedViewLevels())
                    . ') AND published = 1 ';

        $db->setQuery($sql);

        return (int) $db->loadResult();
    }
    /**
     * Check to see whether this event still accept registration
     *
     * @param int $eventId
     * @return Boolean
     */
    public static function acceptRegistration($eventId)
    {
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $gid = $user->get('aid');
        if (!$eventId)
            return false;
        $sql = 'SELECT * FROM #__eb_events WHERE id=' . $eventId
                . ' AND published=1 ';
        $db->setQuery($sql);
        $row = $db->loadObject();
        if (!$row)
            return false;
        if ($row->registration_type == 3)
            return false;

        if (!in_array($row->registration_access,
                $user->getAuthorisedViewLevels())) {
            return false;
        }

        if ($row->cut_off_date == $db->getNullDate()) {
            $sql = 'SELECT DATEDIFF(NOW(), event_date) AS number_days FROM #__eb_events WHERE id='
                    . $eventId;
        } else {
            $sql = 'SELECT DATEDIFF(NOW(), cut_off_date) AS number_days FROM #__eb_events WHERE id='
                    . $eventId;
        }
        $db->setQuery($sql);
        $numberDays = $db->loadResult();
        if ($numberDays > 0) {
            return false;
        }
        if ($row->event_capacity) {
            //Get total registrants for this event
            $sql = 'SELECT SUM(number_registrants) AS total_registrants FROM #__eb_registrants WHERE event_id='
                    . $eventId
                    . ' AND group_id=0 AND (published=1 OR (payment_method LIKE "os_offline%" AND published != 2))';
            $db->setQuery($sql);
            $numberRegistrants = (int) $db->loadResult();
            if ($numberRegistrants >= $row->event_capacity)
                return false;
        }
        //Check to see whether the current user has registered for the event
        $preventDuplicateRegistration = ScheduleOrderHelper::getConfigValue(
                'prevent_duplicate_registration');
        if ($preventDuplicateRegistration && $user->get('id')) {
            $sql = 'SELECT COUNT(id) FROM #__eb_registrants WHERE event_id='
                    . $eventId . ' AND user_id=' . $user->get('id')
                    . ' AND (published=1 OR (payment_method LIKE "os_offline%" AND published != 2))';
            $db->setQuery($sql);
            $total = $db->loadResult();
            if ($total) {
                return false;
            }
        }
        return true;
    }
    /**
     * Get total registrants
     *
     */
    public static function getTotalRegistrants($eventId)
    {
        $db = JFactory::getDBO();
        $sql = 'SELECT SUM(number_registrants) AS total_registrants FROM #__eb_registrants WHERE event_id='
                . $eventId
                . ' AND group_id=0 AND (published=1 OR (payment_method LIKE "os_offline%" AND published != 2))';
        $db->setQuery($sql);
        $numberRegistrants = (int) $db->loadResult();
        return $numberRegistrants;
    }
    /**
     * Get registration rate for group registration
     *
     * @param int $eventId
     * @param int $numberRegistrants
     * @return 
     */
    public static function getRegistrationRate($eventId, $numberRegistrants)
    {
        $db = JFactory::getDBO();
        $sql = 'SELECT price FROM #__eb_event_group_prices WHERE event_id='
                . $eventId . ' AND registrant_number <= ' . $numberRegistrants
                . ' ORDER BY registrant_number DESC LIMIT 1';
        $db->setQuery($sql);
        $rate = $db->loadResult();
        if (!$rate) {
            $sql = 'SELECT individual_price FROM #__eb_events WHERE id='
                    . $eventId;
            $db->setQuery($sql);
            $rate = $db->loadResult();
        }
        return $rate;
    }
    /**
     * Check to see whether the ideal payment plugin installed and activated
     * @return boolean	 
     */
    public static function idealEnabled()
    {
        $db = JFactory::getDBO();
        $sql = 'SELECT COUNT(id) FROM #__eb_payment_plugins WHERE name="os_ideal" AND published=1';
        $db->setQuery($sql);
        $total = $db->loadResult();
        if ($total) {
            require_once JPATH_COMPONENT . '/payments/ideal/ideal.class.php';
            return true;
        } else {
            return false;
        }
    }
    /**	 
     * Get list of banks for ideal payment plugin
     * @return array
     */
    public static function getBankLists()
    {
        $idealPlugin = os_payments::loadPaymentMethod('os_ideal');
        $params = new JRegistry($idealPlugin->params);
        $partnerId = $params->get('partner_id');
        $ideal = new iDEAL_Payment($partnerId);
        $bankLists = $ideal->getBanks();
        return $bankLists;
    }
    /**
     * Helper function for sending emails to registrants and administrator
     *
     * @param RegistrantScheduleOrder $row
     * @param object $config
     */ 
    public static function sendEmails($row, $config)
    {
        $db = JFactory::getDBO();
        $jconfig = new JConfig();
        $mailer = JFactory::getMailer();
        if ($row->language != '*') {
            $config = ScheduleOrderHelper::getConfig(false, $row->language);
        }
        if ($config->from_name)
            $fromName = $config->from_name;
        else
            $fromName = $jconfig->fromname;
        if ($config->from_email)
            $fromEmail = $config->from_email;
        else
            $fromEmail = $jconfig->mailfrom;
        $sql = "SELECT * FROM #__eb_events WHERE id=" . $row->event_id;
        $db->setQuery($sql);
        $event = $db->loadObject();
        $params = new JRegistry($event->params);
        $keys = array('s_lastname', 's_organization', 's_address',
                's_address2', 's_city', 's_state', 's_zip', 's_country',
                's_phone', 's_fax', 's_comment');
        foreach ($keys as $key) {
            $config->$key = $params->get($key, 0);
        }
        //Need to over-ridde some config options				
        $emailContent = ScheduleOrderHelper::getEmailContent($config, $row);
        if ($config->multiple_booking) {
            $sql = 'SELECT event_id FROM #__eb_registrants WHERE id='
                    . $row->id . ' OR cart_id=' . $row->id . ' ORDER BY id';
            $db->setQuery($sql);
            $eventIds = $db->loadColumn();
            $sql = 'SELECT title FROM #__eb_events WHERE id IN ('
                    . implode(',', $eventIds) . ') ORDER BY FIND_IN_SET(id, "'
                    . implode(',', $eventIds) . '")';
            $db->setQuery($sql);
            $eventTitles = $db->loadColumn();
            $eventTitle = implode(', ', $eventTitles);
        } else {
            $sql = 'SELECT title FROM #__eb_events WHERE id=' . $row->event_id;
            $db->setQuery($sql);
            $eventTitle = $db->loadResult();
        }
        $replaces = array();
        $replaces['event_title'] = $eventTitle;
        $replaces['event_date'] = JHTML::_('date', $event->event_date,
                $config->event_date_format, null);
        $replaces['first_name'] = $row->first_name;
        $replaces['last_name'] = $row->last_name;
        $replaces['organization'] = $row->organization;
        $replaces['address'] = $row->address;
        $replaces['address2'] = $row->address;
        $replaces['city'] = $row->city;
        $replaces['state'] = $row->state;
        $replaces['zip'] = $row->zip;
        $replaces['country'] = $row->country;
        $replaces['phone'] = $row->phone;
        $replaces['fax'] = $row->phone;
        $replaces['email'] = $row->email;
        $replaces['transaction_id'] = $row->transaction_id;
        $replaces['comment'] = $row->comment;
        $replaces['amount'] = ScheduleOrderHelper::formatCurrency($row->amount, $config, $event->currency_symbol);
        //Add support for location tag
        $sql = 'SELECT a.* FROM #__eb_locations AS a '
                . ' INNER JOIN #__eb_events AS b '
                . ' ON a.id = b.location_id ' . ' WHERE b.id ='
                . $row->event_id;
        ;
        $db->setQuery($sql);
        $rowLocation = $db->loadObject();
        if ($rowLocation) {
            $replaces['location'] = $rowLocation->name . ' ('
                    . $rowLocation->address . ', ' . $rowLocation->city . ','
                    . $rowLocation->state . ', ' . $rowLocation->zip . ', '
                    . $rowLocation->country . ')';
        } else {
            $replaces['location'] = '';
        }
        //Override config messages
        $sql = 'SELECT * FROM #__eb_events WHERE id=' . $row->event_id;
        $db->setQuery($sql);
        $rowEvent = $db->loadObject();
        if ($rowEvent) {
            if (strlen(trim(strip_tags($rowEvent->user_email_body)))) {
                $config->user_email_body = $rowEvent->user_email_body;
            }
            if (strlen(trim(strip_tags($rowEvent->user_email_body_offline)))) {
                $config->user_email_body_offline = $rowEvent
                        ->user_email_body_offline;
            }
        }
        //Notification email send to user
        $subject = $config->user_email_subject;
        if (strpos($row->payment_method, 'os_offline') !== false) {
            $body = $config->user_email_body_offline;
        } else {
            $body = $config->user_email_body;
        }
        $subject = str_replace('[EVENT_TITLE]', $eventTitle, $subject);
        $body = str_replace('[REGISTRATION_DETAIL]', $emailContent, $body);
        foreach ($replaces as $key => $value) {
            $key = strtoupper($key);
            $body = str_replace("[$key]", $value, $body);
        }
        $ccEmails = null;
        if ($config->send_email_to_group_members
                && ScheduleOrderHelper::isGroupRegistration($row->id)) {
            $ccEmails = array();
            $sql = 'SELECT email FROM #__eb_registrants WHERE group_id='
                    . $row->id;
            $db->setQuery($sql);
            $memberEmails = $db->loadColumn();
            if (count($memberEmails)) {
                foreach ($memberEmails as $memberEmail) {
                    if ($memberEmail && ($memberEmail != $row->email)
                            && !in_array($memberEmail, $ccEmails)) {
                        $ccEmails[] = $memberEmail;
                    }
                }
            }
        }
        if ($event->attachment) {
            $mailer->sendMail($fromEmail, $fromName, $row->email, $subject,
                            $body, 1, $ccEmails, null,
                            JPATH_ROOT . '/media/' .COMPONENT_NAME . '/'
                                    . $event->attachment);
            $mailer->ClearAttachments();
        } else {
            $mailer
                    ->sendMail($fromEmail, $fromName, $row->email, $subject,
                            $body, 1, $ccEmails);
        }
        //Send emails to notification emails
        if (strlen(trim($event->notification_emails)) > 0)
            $config->notification_emails = $event->notification_emails;
        if ($config->notification_emails == '')
            $notificationEmails = $fromEmail;
        else
            $notificationEmails = $config->notification_emails;
        $notificationEmails = str_replace(' ', '', $notificationEmails);
        $emails = explode(',', $notificationEmails);
        $subject = $config->admin_email_subject;
        $subject = str_replace('[EVENT_TITLE]', $eventTitle, $subject);
        $body = $config->admin_email_body;
        $body = str_replace('[REGISTRATION_DETAIL]', $emailContent, $body);
        foreach ($replaces as $key => $value) {
            $key = strtoupper($key);
            $body = str_replace("[$key]", $value, $body);
        }
        for ($i = 0, $n = count($emails); $i < $n; $i++) {
            $email = $emails[$i];
            $mailer->ClearAllRecipients();
            $mailer
                    ->sendMail($fromEmail, $fromName, $email, $subject, $body,
                            1);
        }
    }
    /**
     * Helper function for sending emails to registrants and administrator
     *
     * @param RegistrantScheduleOrder $row
     * @param object $config
     */
    public static function sendRegistrationApprovedEmail($row, $config)
    {
        require_once JPATH_ROOT . '/components/' . COMPONENT_NAME . '/payments/os_payment.php';
        require_once JPATH_ROOT . '/components/' . COMPONENT_NAME . '/payments/os_payments.php';
        $mailer = JFactory::getMailer();
        ScheduleOrderHelper::loadLanguage();
        $jconfig = new JConfig();
        $db = JFactory::getDBO();
        if ($config->from_name)
            $fromName = $config->from_name;
        else
            $fromName = $jconfig->fromname;
        if ($config->from_email)
            $fromEmail = $config->from_email;
        else
            $fromEmail = $jconfig->mailfrom;
        $sql = "SELECT * FROM #__eb_events WHERE id=" . $row->event_id;
        $db->setQuery($sql);
        $event = $db->loadObject();
        $params = new JRegistry($event->params);
        $keys = array('s_lastname', 's_organization', 's_address',
                's_address2', 's_city', 's_state', 's_zip', 's_country',
                's_phone', 's_fax', 's_comment');
        foreach ($keys as $key) {
            $config->$key = $params->get($key, 0);
        }
        //Need to over-ridde some config options
        $emailContent = ScheduleOrderHelper::getEmailContent($config, $row);
        if ($config->multiple_booking) {
            $sql = 'SELECT event_id FROM #__eb_registrants WHERE id='
                    . $row->id . ' OR cart_id=' . $row->id . ' ORDER BY id';
            $db->setQuery($sql);
            $eventIds = $db->loadColumn();
            $sql = 'SELECT title FROM #__eb_events WHERE id IN ('
                    . implode(',', $eventIds) . ') ORDER BY FIND_IN_SET(id, "'
                    . implode(',', $eventIds) . '")';
            $db->setQuery($sql);
            $eventTitles = $db->loadColumn();
            $eventTitle = implode(', ', $eventTitles);
        } else {
            $sql = 'SELECT title FROM #__eb_events WHERE id=' . $row->event_id;
            $db->setQuery($sql);
            $eventTitle = $db->loadResult();
        }
        $replaces = array();
        $replaces['event_title'] = $eventTitle;
        $replaces['event_date'] = JHTML::_('date', $event->event_date,
                $config->event_date_format, null);
        $replaces['first_name'] = $row->first_name;
        $replaces['last_name'] = $row->last_name;
        $replaces['organization'] = $row->organization;
        $replaces['address'] = $row->address;
        $replaces['address2'] = $row->address;
        $replaces['city'] = $row->city;
        $replaces['state'] = $row->state;
        $replaces['zip'] = $row->zip;
        $replaces['country'] = $row->country;
        $replaces['phone'] = $row->phone;
        $replaces['fax'] = $row->phone;
        $replaces['email'] = $row->email;
        $replaces['transaction_id'] = $row->transaction_id;
        $replaces['comment'] = $row->comment;
        //$replaces['amount'] = number_format($row->amount, 2) ;
        $replaces['amount'] = ScheduleOrderHelper::formatCurrency($row->amount,
                $config, $event->currency_symbol);
        //Add support for location tag
        $sql = 'SELECT a.* FROM #__eb_locations AS a '
                . ' INNER JOIN #__eb_events AS b '
                . ' ON a.id = b.location_id ' . ' WHERE b.id ='
                . $row->event_id;
        ;
        $db->setQuery($sql);
        $rowLocation = $db->loadObject();
        if ($rowLocation) {
            $replaces['location'] = $rowLocation->name . ' ('
                    . $rowLocation->address . ', ' . $rowLocation->city . ','
                    . $rowLocation->state . ', ' . $rowLocation->zip . ', '
                    . $rowLocation->country . ')';
        } else {
            $replaces['location'] = '';
        }
        //Override config messages
        $sql = 'SELECT * FROM #__eb_events WHERE id=' . $row->event_id;
        $db->setQuery($sql);
        $rowEvent = $db->loadObject();
        if ($rowEvent) {
            if (strlen(trim(strip_tags($rowEvent->user_email_body)))) {
                $config->user_email_body = $rowEvent->user_email_body;
            }
            if (strlen(trim(strip_tags($rowEvent->user_email_body_offline)))) {
                $config->user_email_body_offline = $rowEvent
                        ->user_email_body_offline;
            }
        }
        //Notification email send to user
        if (strlen(trim($event->registration_approved_email_subject)))
            $subject = $event->registration_approved_email_subject;
        else
            $subject = $config->registration_approved_email_subject;
        if (strlen(trim(strip_tags($event->registration_approved_email_body))))
            $body = $event->registration_approved_email_body;
        else
            $body = $config->registration_approved_email_body;
        $subject = str_replace('[EVENT_TITLE]', $eventTitle, $subject);
        $body = str_replace('[REGISTRATION_DETAIL]', $emailContent, $body);
        foreach ($replaces as $key => $value) {
            $key = strtoupper($key);
            $body = str_replace("[$key]", $value, $body);
        }
        $mailer
                ->sendMail($fromEmail, $fromName, $row->email, $subject, $body,
                        1);
    }
    /**
     * Send email when users fill-in waitinglist
     * 
     * @param  object $row
     * @param object $config
     */
    public static function sendWaitinglistEmail($row, $config)
    {
        $jconfig = new JConfig();
        $db = JFactory::getDBO();
        $mailer = JFactory::getMailer();
        if ($config->from_name)
            $fromName = $config->from_name;
        else
            $fromName = $jconfig->fromname;
        if ($config->from_email)
            $fromEmail = $config->from_email;
        else
            $fromEmail = $jconfig->mailfrom;
        $sql = "SELECT * FROM #__eb_events WHERE id=" . $row->event_id;
        $db->setQuery($sql);
        $event = $db->loadObject();
        //Supported tags
        $replaces = array();
        $replaces['event_title'] = $event->title;
        $replaces['first_name'] = $row->first_name;
        $replaces['last_name'] = $row->last_name;
        $replaces['organization'] = $row->organization;
        $replaces['address'] = $row->address;
        $replaces['address2'] = $row->address;
        $replaces['city'] = $row->city;
        $replaces['state'] = $row->state;
        $replaces['zip'] = $row->zip;
        $replaces['country'] = $row->country;
        $replaces['phone'] = $row->phone;
        $replaces['fax'] = $row->phone;
        $replaces['email'] = $row->email;
        $replaces['comment'] = $row->comment;
        $replaces['number_registrants'] = $row->number_registrants;
        //Notification email send to user
        $subject = $config->watinglist_confirmation_subject;
        $body = $config->watinglist_confirmation_body;
        $subject = str_replace('[EVENT_TITLE]', $event->title, $subject);
        foreach ($replaces as $key => $value) {
            $key = strtoupper($key);
            $body = str_replace("[$key]", $value, $body);
        }
        $mailer
                ->sendMail($fromEmail, $fromName, $row->email, $subject, $body,
                        1);
        //Send emails to notification emails
        if (strlen(trim($event->notification_emails)) > 0)
            $config->notification_emails = $event->notification_emails;
        if ($config->notification_emails == '')
            $notificationEmails = $fromEmail;
        else
            $notificationEmails = $config->notification_emails;
        $notificationEmails = str_replace(' ', '', $notificationEmails);
        $emails = explode(',', $notificationEmails);
        $subject = $config->watinglist_notification_subject;
        $subject = str_replace('[EVENT_TITLE]', $event->title, $subject);
        $body = $config->watinglist_notification_body;
        foreach ($replaces as $key => $value) {
            $key = strtoupper($key);
            $body = str_replace("[$key]", $value, $body);
        }
        for ($i = 0, $n = count($emails); $i < $n; $i++) {
            $email = $emails[$i];
            $mailer->ClearAllRecipients();
            $mailer
                    ->sendMail($fromEmail, $fromName, $email, $subject, $body,
                            1);
        }
    }
    /**
     * Get country code
     *
     * @param string $countryName
     * @return string
     */
    public static function getCountryCode($countryName)
    {
        $db = JFactory::getDBO();
        $sql = 'SELECT country_2_code FROM #__eb_countries WHERE LOWER(name)="'
                . JString::strtolower($countryName) . '"';
        $db->setQuery($sql);
        $countryCode = $db->loadResult();
        if (!$countryCode)
            $countryCode = 'US';
        return $countryCode;
    }
    /**
     * Get color code of an event based on in category
     * @param int $eventId
     * @return Array
     */
    public static function getColorCodeOfEvent($eventId)
    {
        static $colors;
        if (!isset($colors[$eventId])) {
            $db = JFactory::getDbo();
            $sql = 'SELECT color_code FROM #__eb_categories AS a INNER JOIN #__eb_event_categories AS b ON a.id = b.category_id WHERE b.event_id='
                    . $eventId;
            $db->setQuery($sql);
            $colors[$eventId] = $db->loadResult();
        }

        return $colors[$eventId];
    }
    /**
     * Get title of the given payment method
     * @param string $methodName
     */ 
    public static function getPaymentMethodTitle($methodName)
    {
        static $titles;
        if (!isset($titles[$methodName])) {
            $sql = 'SELECT title FROM #__eb_payment_plugins WHERE name="'
                    . $methodName . '"';
            $db->setQuery($sql);
            $methodTitle = $db->loadResult();
            if ($methodTitle) {
                $titles[$methodName] = $methodTitle;
            } else {
                $titles[$methodName] = $methodName;
            }
        }

        return $titles[$methodName];
    }
    /**
     * Display copy right information
     *
     */
    public static function displayCopyRight()
    {
        echo '<div class="copyright" style="text-align:center;margin-top: 5px;"><a href="http://www.netwebx.com/components/schedule_order.html" target="_blank"><strong>Schedule Order</strong></a> version 1.0.0, Copyright (C) 2010-2013 <a href="http://www.netwebx.com" target="_blank"><strong>NetWebX.COM</strong></a></div>';
    }
    /**
     * Load bootstrap css and javascript file
     */
    public static function loadBootstrap($loadJs = true)
    {
        $document = JFactory::getDocument();
        if ($loadJs) {
            $document->addScript(JUri::root() . 'components/com_scheduleorder/assets/js/jquery.min.js');
            $document->addScript(JUri::root() . 'components/com_scheduleorder/assets/js/jquery-noconflict.js');
            $document->addScript(JUri::root() . 'components/com_scheduleorder/assets/js/bootstrap.min.js');
        }

        $document->addStyleSheet(JUri::root() . 'components/com_scheduleorder/assets/css/bootstrap.min.css');
    }
    /**
     * Get version number of GD version installed
     * Enter description here ...
     * @param unknown_type $user_ver
     */
    public static function getGDVersion($user_ver = 0)
    {
        if (!extension_loaded('gd')) {
            return 0;
        }

        static $gd_ver = 0;

        // just accept the specified setting if it's 1.
        if ($user_ver == 1) {
            $gd_ver = 1;
            return 1;
        }

        // use static variable if function was cancelled previously.
        if ($user_ver != 2 && $gd_ver > 0) {
            return $gd_ver;
        }

        // use the gd_info() function if posible.
        if (function_exists('gd_info')) {
            $ver_info = gd_info();
            $match = null;
            preg_match('/\d/', $ver_info['GD Version'], $match);
            $gd_ver = $match[0];

            return $match[0];
        }

        // if phpinfo() is disabled use a specified / fail-safe choice...
        if (preg_match('/phpinfo/', ini_get('disable_functions'))) {
            if ($user_ver == 2) {
                $gd_ver = 2;
                return 2;
            } else {
                $gd_ver = 1;
                return 1;
            }
        }
        // ...otherwise use phpinfo().
        ob_start();
        phpinfo(8);
        $info = ob_get_contents();
        ob_end_clean();
        $info = stristr($info, 'gd version');
        $match = null;
        preg_match('/\d/', $info, $match);
        $gd_ver = $match[0];

        return $match[0];
    }
    /**
     * 
     * Resize image to a pre-defined size
     * @param string $srcFile
     * @param string $desFile
     * @param int $thumbWidth
     * @param int $thumbHeight
     * @param string $method gd1 or gd2
     * @param int $quality
     */ 
    public static function resizeImage($srcFile, $desFile, $thumbWidth,
            $thumbHeight, $quality)
    {
        $app = JFactory::getApplication();
        $imgTypes = array(1 => 'GIF', 2 => 'JPG', 3 => 'PNG', 4 => 'SWF',
                5 => 'PSD', 6 => 'BMP', 7 => 'TIFF', 8 => 'TIFF', 9 => 'JPC',
                10 => 'JP2', 11 => 'JPX', 12 => 'JB2', 13 => 'SWC',
                14 => 'IFF');
        $imgInfo = getimagesize($srcFile);
        if ($imgInfo == null) {
            $app->enqueueMessage(JText::_('EB_IMAGE_NOT_FOUND', 'error'));
            return false;
        }
        $type = strtoupper($imgTypes[$imgInfo[2]]);
        $gdSupportedTypes = array('JPG', 'PNG', 'GIF');
        if (!in_array($type, $gdSupportedTypes)) {
            $app->enqueueMessage(JText::_('EB_ONLY_SUPPORT_TYPES'), 'error');
            return false;
        }
        $srcWidth = $imgInfo[0];
        $srcHeight = $imgInfo[1];
        //Should canculate the ration	        	        	        
        $ratio = max($srcWidth / $thumbWidth, $srcHeight / $thumbHeight, 1.0);
        $desWidth = (int) $srcWidth / $ratio;
        $desHeight = (int) $srcHeight / $ratio;
        $gdVersion = ScheduleOrderHelper::getGDVersion();
        if ($gdVersion <= 0) {
            //Simply copy the source to target folder
            jimport('joomla.filesystem.file');
            JFile::copy($srcFile, $desFile);
            return false;
        } else {
            if ($gdVersion == 1) {
                $method = 'gd1';
            } else {
                $method = 'gd2';
            }
        }
        switch ($method) {
        case 'gd1':
            if ($type == 'JPG')
                $srcImage = imagecreatefromjpeg($srcFile);
            elseif ($type == 'PNG')
                $srcImage = imagecreatefrompng($srcFile);
            else
                $srcImage = imagecreatefromgif($srcFile);
            $desImage = imagecreate($desWidth, $desHeight);
            imagecopyresized($desImage, $srcImage, 0, 0, 0, 0, $desWidth,
                    $desHeight, $srcWidth, $srcHeight);
            imagejpeg($desImage, $desFile, $quality);
            imagedestroy($srcImage);
            imagedestroy($desImage);
            break;
        case 'gd2':
            if (!function_exists('imagecreatefromjpeg')) {
                echo JText::_('GD_LIB_NOT_INSTALLED');
                return false;
            }
            if (!function_exists('imagecreatetruecolor')) {
                echo JText::_('GD2_LIB_NOT_INSTALLED');
                return false;
            }
            if ($type == 'JPG')
                $srcImage = imagecreatefromjpeg($srcFile);
            elseif ($type == 'PNG')
                $srcImage = imagecreatefrompng($srcFile);
            else
                $srcImage = imagecreatefromgif($srcFile);
            if (!$srcImage) {
                echo JText::_('JA_INVALID_IMAGE');
                return false;
            }
            $desImage = imagecreatetruecolor($desWidth, $desHeight);
            imagecopyresampled($desImage, $srcImage, 0, 0, 0, 0, $desWidth,
                    $desHeight, $srcWidth, $srcHeight);
            imagejpeg($desImage, $desFile, $quality);
            imagedestroy($srcImage);
            imagedestroy($desImage);
            break;
        }

        return true;
    }
    /**
     * Calcuate total discount for the registration
     * @return decimal
     */ 
    function calcuateDiscount()
    {
        return 10;
    }

    /**
     * Generate User Input Select
     * @param int $userId
     */
    public static function getUserInput($userId, $fieldName = 'user_id')
    {
        // Initialize variables.
        $html = array();
        $link = 'index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=user_id';
        // Initialize some field attributes.
        $attr = ' class="inputbox"';
        // Load the modal behavior script.
        JHtml::_('behavior.modal', 'a.modal_user_id');
        // Build the script.
        $script = array();
        $script[] = '	function jSelectUser_user_id(id, title) {';
        $script[] = '		var old_id = document.getElementById("user_id").value;';
        $script[] = '		if (old_id != id) {';
        $script[] = '			document.getElementById("' . $fieldName
                . '").value = id;';
        $script[] = '			document.getElementById("user_id_name").value = title;';
        $script[] = '		}';
        $script[] = '		SqueezeBox.close();';
        $script[] = '	}';
        // Add the script to the document head.
        JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
        // Load the current username if available.
        $table = JTable::getInstance('user');
        if ($userId) {
            $table->load($userId);
        } else {
            $table->name = '';
        }
        // Create a dummy text field with the user name.
        $html[] = '<div class="fltlft">';
        $html[] = '	<input type="text" id="user_id_name"' . ' value="'
                . htmlspecialchars($table->name, ENT_COMPAT, 'UTF-8') . '"'
                . ' disabled="disabled"' . $attr . ' />';
        $html[] = '</div>';
        // Create the user select button.
        $html[] = '<div class="button2-left">';
        $html[] = '<div class="blank">';
        $html[] = '<a class="modal_user_id" title="'
                . JText::_('JLIB_FORM_CHANGE_USER') . '"' . ' href="' . $link
                . '"' . ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
        $html[] = '	' . JText::_('JLIB_FORM_CHANGE_USER') . '</a>';
        $html[] = '</div>';
        $html[] = '</div>';
        // Create the real field, hidden, that stored the user id.
        $html[] = '<input type="hidden" id="' . $fieldName . '" name="'
                . $fieldName . '" value="' . $userId . '" />';

        return implode("\n", $html);
    }
    /**
     * Check category access
     *
     * @param int $categoryId
     */
    public static function checkCategoryAccess($categoryId)
    {
        $mainframe = JFactory::getApplication();
        $Itemid = JRequest::getInt('Itemid');
        $user = JFactory::getUser();
        $db = JFactory::getDBO();
        $sql = 'SELECT `access` FROM #__eb_categories WHERE id=' . $categoryId;
        $db->setQuery($sql);
        $access = (int) $db->loadResult();
        if (!in_array($access, $user->getAuthorisedViewLevels())) {
            $mainframe->redirect('index.php', JText::_('NOT_AUTHORIZED'));
        }
    }
    /**
     * Check to see whether the current user can 
     *
     * @param int $eventId
     */ 
    public static function checkEventAccess($eventId)
    {
        $mainframe = JFactory::getApplication();
        $Itemid = JRequest::getInt('Itemid');
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $sql = 'SELECT `access` FROM #__eb_events WHERE id=' . $eventId;
        $db->setQuery($sql);
        $access = (int) $db->loadResult();

        if (!in_array($access, $user->getAuthorisedViewLevels())) {
            $mainframe->redirect('index.php', JText::_('NOT_AUTHORIZED'));
        }

    }
    /**
     * Check to see whether a users to access to registration history
     * Enter description here
     */
    public static function checkAccessHistory()
    {
        $user = JFactory::getUser();
        if (!$user->get('id')) {
            JFactory::getApplication()
                    ->redirect('index.php?option=' . COMPONENT_NAME,
                            JText::_('NOT_AUTHORIZED'));
        }
    }
    /**
     * 
     * Check the access to registrants history from frontend
     */
    public static function checkRegistrantsAccess()
    {
        $user = JFactory::getUser();
        if (!$user
                ->authorise('scheduleorder .registrants_management',
                        COMPONENT_NAME)) {
            JFactory::getApplication()
                    ->redirect('index.php', JText::_('NOT_AUTHORIZED'));
        }
    }
    /**
     * Check to see whether the current users can access View List function
     */
    public static function canViewRegistrantList()
    {
        $user = JFactory::getUser();

        return $user->authorise('scheduleorder .view_registrants_list', COMPONENT_NAME);
    }
    /**
     * 
     * Check to see whether this users has permission to edit registrant
     */
    public static function checkEditRegistrant()
    {
        $user = JFactory::getUser();
        $db = JFactory::getDBO();
        $cid = Jrequest::getVar('cid', array());
        $registrantId = (int) $cid[0];
        $canAccess = true;
        if (!$registrantId)
            $canAccess = false;
        $sql = 'SELECT user_id, email FROM #__eb_registrants WHERE id='
                . $registrantId;
        $db->setQuery($sql);
        $rowRegistrant = $db->loadObject();
        if ($user->authorise('scheduleorder .registrants_management', COMPONENT_NAME)
                || ($user->get('id') == $rowRegistrant->user_id)
                || ($user->get('email') == $rowRegistrant->email)) {
            $canAccess = true;
        } else {
            $canAccess = false;
        }
        if (!$canAccess) {
            JFactory::getApplication()
                    ->redirect('index.php', JText::_('NOT_AUTHORIZED'));
        }
    }
    /**
     * Check to see whether this event can be cancelled	 
     * @param int $eventId
     */
    public static function canCancel($eventId)
    {
        $db = JFactory::getDBO();
        $sql = 'SELECT COUNT(*) FROM #__eb_events WHERE id=' . $eventId
                . ' AND enable_cancel_registration = 1 AND (DATEDIFF(cancel_before_date, NOW()) >=0) ';
        $db->setQuery($sql);
        $total = $db->loadResult();
        if ($total)
            return true;
        else
            return false;
    }
    public static function canExportRegistrants($eventId = 0)
    {
        $user = JFactory::getUser();
        if ($eventId) {
            $db = JFactory::getDbo();
            $sql = 'SELECT created_by FROM #__eb_events WHERE id=' . $eventId;
            $db->setQuery($sql);
            $createdBy = (int) $db->loadResult();
            return (($createdBy > 0 && $createdBy == $user->id)
                    || $user->authorise('scheduleorder .registrants_management', COMPONENT_NAME));
        } else {
            return $user->authorise('scheduleorder .registrants_management', COMPONENT_NAME);
        }
    }
    /**
     * Check to see whether the users can cancel registration
     * 
     * @param int $eventId
     */
    public static function canCancelRegistration($eventId)
    {
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $userId = $user->get('id');
        $email = $user->get('email');
        if (!$userId)
            return false;
        $sql = 'SELECT id FROM #__eb_registrants WHERE event_id=' . $eventId
                . ' AND (user_id=' . $userId . ' OR email="' . $email
                . '") AND (published=1 OR (payment_method LIKE "os_offline%" AND published!=2))';
        $db->setQuery($sql);
        $registrantId = $db->loadResult();
        if (!$registrantId)
            return false;

        $sql = 'SELECT COUNT(*) FROM #__eb_events WHERE id=' . $eventId
                . ' AND enable_cancel_registration = 1 AND (DATEDIFF(cancel_before_date, NOW()) >=0) ';
        $db->setQuery($sql);
        $total = $db->loadResult();

        if (!$total)
            return false;

        return $registrantId;
    }

    /**
     * Check to see whether the current user can edit registrant
     *
     * @param int $eventId
     * @return boolean
     */
    public static function checkEditEvent($eventId)
    {
        $user = JFactory::getUser();
        $db = JFactory::getDBO();
        if ($user->get('guest'))
            return false;
        if (!$eventId)
            return false;
        $sql = 'SELECT * FROM #__eb_events WHERE id=' . $eventId;
        $db->setQuery($sql);
        $rowEvent = $db->loadObject();
        if (!$rowEvent)
            return false;
        //User can only edit event created by himself	
        if ($rowEvent->created_by != $user->get('id'))
            return false;

        return true;
    }

    public static function isGroupRegistration($id)
    {
        if (!$id)
            return false;
        $db = JFactory::getDbo();
        $sql = 'SELECT COUNT(*) FROM #__eb_registrants WHERE group_id=' . $id;
        $db->setQuery($sql);
        $total = (int) $db->loadResult();
        return $total > 0 ? true : false;
    }

    public static function updateGroupRegistrationRecord($groupId)
    {
        $db = JFactory::getDBO();
        $config = ScheduleOrderHelper::getConfig();
        if ($config->collect_member_information) {
            $row = JTable::getInstance('ScheduleOrder', 'Registrant');
            $row->load($groupId);
            if ($row->id) {
                $sql = "UPDATE #__eb_registrants SET published=$row->published, transaction_id='$row->transaction_id', payment_method='$row->payment_method' WHERE group_id="
                        . $row->id;
                $db->setQuery($sql);
                $db->query();
            }
        }
    }
    /**
     * Check to see whether the current users can add events from front-end
     * 
     */
    public static function checkAddEvent()
    {
        $user = JFactory::getUser();
        return ($user->id > 0 && $user->authorise('scheduleorder .addevent', COMPONENT_NAME));
    }
    /**
     * Create a user account	 
     * @param array $data
     * @return int Id of created user
     */
    public static function saveRegistration($data)
    {
        //Need to load com_users language file			
        $lang = JFactory::getLanguage();
        $tag = $lang->getTag();
        if (!$tag)
            $tag = 'en-GB';
        $lang->load('com_users', JPATH_ROOT, $tag);
        $data['name'] = $data['first_name'] . ' ' . $data['last_name'];
        $data['password1'] = $data['password2'] = $data['password'];
        $data['email1'] = $data['email2'] = $data['email'];
        require_once JPATH_ROOT
                . '/components/com_users/models/registration.php';
        $model = new UsersModelRegistration();
        $ret = $model->register($data);
        $db = JFactory::getDbo();
        //Need to get the user ID based on username
        $sql = 'SELECT id FROM #__users WHERE username="' . $data['username']
                . '"';
        $db->setQuery($sql);
        return (int) $db->loadResult();
    }
    /**
     * Get list of recurring event dates
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int $dailyFrequency
     * @param int $numberOccurencies
     * @return array
     */ 
    public static function getDailyRecurringEventDates($startDate, $endDate,
            $dailyFrequency, $numberOccurencies)
    {
        $eventDates = array();
        $eventDates[] = $startDate;
        //Convert to unix timestamp for easili maintenance
        $startTime = strtotime($startDate);
        $endTime = strtotime($endDate . ' 23:59:59');
        if ($numberOccurencies) {
            $count = 1;
            $i = 1;
            while ($count < $numberOccurencies) {
                $i++;
                $count++;
                $nextEventDate = $startTime + ($i - 1) * $dailyFrequency * 24
                                * 3600;
                $eventDates[] = strftime('%Y-%m-%d %H:%M:%S', $nextEventDate);
            }
        } else {
            $i = 1;
            while (true) {
                $i++;
                $nextEventDate = $startTime + ($i - 1) * 24 * $dailyFrequency
                                * 3600;
                if ($nextEventDate <= $endTime) {
                    $eventDates[] = strftime('%Y-%m-%d %H:%M:%S',
                            $nextEventDate);
                } else {
                    break;
                }
            }
        }
        return $eventDates;
    }
    /**
     * Get weekly recurring event dates
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param Int $weeklyFrequency
     * @param int $numberOccurrencies
     * @param array $weekDays
     * @return array
     */
    public static function getWeeklyRecurringEventDates($startDate, $endDate,
            $weeklyFrequency, $numberOccurrencies, $weekDays)
    {
        $eventDates = array();
        $startTime = strtotime($startDate);
        $originalStartTime = $startTime;
        $endTime = strtotime($endDate . ' 23:59:59');
        if ($numberOccurrencies) {
            $count = 0;
            $i = 0;
            $weekDay = date('w', $startTime);
            $startTime = $startTime - $weekDay * 24 * 3600;
            while ($count < $numberOccurrencies) {
                $i++;
                $startWeekTime = $startTime
                        + ($i - 1) * $weeklyFrequency * 7 * 24 * 3600;
                foreach ($weekDays as $weekDay) {
                    $nextEventDate = $startWeekTime + $weekDay * 24 * 3600;
                    if (($nextEventDate >= $originalStartTime)
                            && ($count < $numberOccurrencies)) {
                        $eventDates[] = strftime('%Y-%m-%d %H:%M:%S',
                                $nextEventDate);
                        $count++;
                    }
                }
            }
        } else {
            $weekDay = date('w', $startTime);
            $startTime = $startTime - $weekDay * 24 * 3600;
            while ($startTime < $endTime) {
                foreach ($weekDays as $weekDay) {
                    $nextEventDate = $startTime + $weekDay * 24 * 3600;
                    ;
                    if ($nextEventDate < $originalStartTime)
                        continue;
                    if ($nextEventDate <= $endTime) {
                        $eventDates[] = strftime('%Y-%m-%d %H:%M:%S',
                                $nextEventDate);
                    } else {
                        break;
                    }
                }
                $startTime += $weeklyFrequency * 7 * 24 * 3600;
            }
        }
        return $eventDates;
    }
    /**
     * Get list of monthly recurring
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int $monthlyFrequency
     * @param int $numberOccurrencies
     * @param string $monthDays
     * @return array
     */
    public static function getMonthlyRecurringEventDates($startDate, $endDate,
            $monthlyFrequency, $numberOccurrencies, $monthDays)
    {
        $eventDates = array();
        $startTime = strtotime($startDate);
        $hour = date('H', $startTime);
        $minute = date('i', $startTime);
        $originalStartTime = $startTime;
        $endTime = strtotime($endDate . ' 23:59:59');
        $monthDays = explode(',', $monthDays);
        if ($numberOccurrencies) {
            $count = 0;
            $currentMonth = date('m', $startTime);
            $currentYear = date('Y', $startTime);
            while ($count < $numberOccurrencies) {
                foreach ($monthDays as $day) {
                    $nextEventDate = mktime($hour, $minute, 0, $currentMonth,
                            $day, $currentYear);
                    if (($nextEventDate >= $originalStartTime)
                            && ($count < $numberOccurrencies)) {
                        $eventDates[] = strftime('%Y-%m-%d %H:%M:%S',
                                $nextEventDate);
                        $count++;
                    }
                }
                $currentMonth += $monthlyFrequency;
                if ($currentMonth > 12) {
                    $currentMonth -= 12;
                    $currentYear++;
                }
            }
        } else {
            $currentMonth = date('m', $startTime);
            $currentYear = date('Y', $startTime);
            while ($startTime < $endTime) {
                foreach ($monthDays as $day) {
                    $nextEventDate = mktime($hour, $minute, 0, $currentMonth,
                            $day, $currentYear);
                    if (($nextEventDate >= $originalStartTime)
                            && ($nextEventDate <= $endTime)) {
                        $eventDates[] = strftime('%Y-%m-%d %H:%M:%S',
                                $nextEventDate);
                    }
                }
                $currentMonth += $monthlyFrequency;
                if ($currentMonth > 12) {
                    $currentMonth -= 12;
                    $currentYear++;
                }
                $startTime = mktime(0, 0, 0, $currentMonth, 1, $currentYear);
            }
        }
        return $eventDates;
    }
    public static function getDeliciousButton($title, $link)
    {
        $img_url = "components/" . COMPONENT_NAME . "/assets/images/socials/delicious.png";
        return '<a href="http://del.icio.us/post?url=' . rawurlencode($link)
                . '&amp;title=' . rawurlencode($title) . '" title="Submit '
                . $title . ' in Delicious" target="blank" >
		<img src="' . $img_url . '" alt="Submit ' . $title
                . ' in Delicious" />
		</a>';
    }
    public static function getDiggButton($title, $link)
    {
        $img_url = "components/" . COMPONENT_NAME . "/assets/images/socials/digg.png";
        return '<a href="http://digg.com/submit?url=' . rawurlencode($link)
                . '&amp;title=' . rawurlencode($title) . '" title="Submit '
                . $title . ' in Digg" target="blank" >
        <img src="' . $img_url . '" alt="Submit ' . $title
                . ' in Digg" />
        </a>';
    }
    public static function getFacebookButton($title, $link)
    {
        $img_url = "components/" . COMPONENT_NAME . "/assets/images/socials/facebook.png";
        return '<a href="http://www.facebook.com/sharer.php?u='
                . rawurlencode($link) . '&amp;t=' . rawurlencode($title)
                . '" title="Submit ' . $title
                . ' in FaceBook" target="blank" >
        <img src="' . $img_url . '" alt="Submit ' . $title
                . ' in FaceBook" />
        </a>';
    }
    public static function getGoogleButton($title, $link)
    {
        $img_url = "components/" . COMPONENT_NAME . "/assets/images/socials/google.png";
        return '<a href="http://www.google.com/bookmarks/mark?op=edit&bkmk='
                . rawurlencode($link) . '" title="Submit ' . $title
                . ' in Google Bookmarks" target="blank" >
        <img src="' . $img_url . '" alt="Submit ' . $title
                . ' in Google Bookmarks" />
        </a>';
    }
    public static function getStumbleuponButton($title, $link)
    {
        $img_url = "components/" . COMPONENT_NAME . "/assets/images/socials/stumbleupon.png";
        return '<a href="http://www.stumbleupon.com/submit?url='
                . rawurlencode($link) . '&amp;title=' . rawurlencode($title)
                . '" title="Submit ' . $title
                . ' in Stumbleupon" target="blank" >
        <img src="' . $img_url . '" alt="Submit ' . $title
                . ' in Stumbleupon" />
        </a>';
    }
    public static function getTechnoratiButton($title, $link)
    {
        $img_url = "components/" . COMPONENT_NAME . "/assets/images/socials/technorati.png";
        return '<a href="http://technorati.com/faves?add='
                . rawurlencode($link) . '" title="Submit ' . $title
                . ' in Technorati" target="blank" >
        <img src="' . $img_url . '" alt="Submit ' . $title
                . ' in Technorati" />
        </a>';
    }
    public static function getTwitterButton($title, $link)
    {
        $img_url = "components/" . COMPONENT_NAME . "/assets/images/socials/twitter.png";
        return '<a href="http://twitter.com/?status='
                . rawurlencode($title . " " . $link) . '" title="Submit '
                . $title . ' in Twitter" target="blank" >
        <img src="' . $img_url . '" alt="Submit ' . $title
                . ' in Twitter" />
        </a>';
    }

    /**
     * Add submenus, only used for Joomla 1.6
     * 
     * @param string $vName
     */
    public static function addSubMenus($vName = 'events')
    {
        JSubMenuHelper::addEntry(JText::_('Configuration'),
                'index.php?option=' . COMPONENT_NAME . '&view=configuration',
                $vName == 'configuration');
        JSubMenuHelper::addEntry(JText::_('Categoriy'),
                'index.php?option=' . COMPONENT_NAME . '&view=category',
                $vName == 'categories');
        JSubMenuHelper::addEntry(JText::_('Events'),
                'index.php?option=' . COMPONENT_NAME . '&view=events',
                $vName == 'events');
        JSubMenuHelper::addEntry(JText::_('Registrants'),
                'index.php?option=' . COMPONENT_NAME . '&view=registrants',
                $vName == 'registrants');
        JSubMenuHelper::addEntry(JText::_('Custom Fields'),
                'index.php?option=' . COMPONENT_NAME . '&view=fields',
                $vName == 'fields');
        JSubMenuHelper::addEntry(JText::_('Locations'),
                'index.php?option=' . COMPONENT_NAME . '&view=locations',
                $vName == 'locations');
        JSubMenuHelper::addEntry(JText::_('Coupons'),
                'index.php?option=' . COMPONENT_NAME . '&view=coupons',
                $vName == 'coupons');
        JSubMenuHelper::addEntry(JText::_('Payment Plugins'),
                'index.php?option=' . COMPONENT_NAME . '&view=plugins',
                $vName == 'plugins');
        JSubMenuHelper::addEntry(JText::_('Translation'),
                'index.php?option=' . COMPONENT_NAME . '&view=language',
                $vName == 'language');
        JSubMenuHelper::addEntry(JText::_('Export Registrants'),
                'index.php?option=' . COMPONENT_NAME . '&task=csv_export', false);
        JSubMenuHelper::addEntry(JText::_('Waiting List'),
                'index.php?option=' . COMPONENT_NAME . '&view=waitings',
                $vName == 'waitings');
        JSubMenuHelper::addEntry(JText::_('Mass Mail'),
                'index.php?option=' . COMPONENT_NAME . '&view=massmail',
                $vName == 'massmail');
    }
}
?>