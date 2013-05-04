<?php
/**
 * @version		1.0.0
 * @package		Joomla
 * @subpackage	Schedule Order
 * @author      Jason<jason@netwebx.com>
 * @copyright	Copyright (C) 2013 NetWebX.COM
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die('Restricted Access');

/**
 * HTML View class for Schedule Order component
 *
 * @static
 *
 * @package Joomla
 * @subpackage Schedule Order
 * @since 1.0.1
 */
class ScheduleOrderViewConfiguration extends JView
{

    function display ($tpl = null)
    {
        $db = JFactory::getDBO();
        $config = $this->get('Data');
        //language settings
        $language = JRequest::getVar('language', '', 'post');
        if (! $language)
        {
            $language = JRequest::getVar('language', '', 'get');
        }
        if (! $language)
        {
            $language = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
        }
        $options = array();
        $sql = 'SELECT a.lang_code AS value, a.title AS text FROM #__languages AS a WHERE a.published=1 ORDER BY a.ordering ';
        $db->setQuery($sql);
        $options = array_merge($options, $db->loadObjectList());
        $lists['language'] = JHTML::_('select.genericlist', $options, 'language', 'class="inputbox" onchange="submit();"', 'value', 'text', $language);
        $this->assignRef('language', $language);

        $lists['use_https'] = JHTML::_('select.booleanlist', 'use_https', '', $config->use_https);
        
        $lists['load_bootstrap_css_in_frontend'] = JHTML::_(
                'select.booleanlist', 'load_bootstrap_css_in_frontend', '',
                isset($config->load_bootstrap_css_in_frontend) ? $config->load_bootstrap_css_in_frontend : 1);
        
        $lists['enable_captcha'] = JHTML::_('select.genericlist', $options,
                'enable_captcha', ' class="inputbox" ', 'value', 'text',
                $config->enable_captcha);
        
        $lists['bypass_captcha_for_registered_user'] = JHTML::_(
                'select.booleanlist', 'bypass_captcha_for_registered_user', '',
                $config->bypass_captcha_for_registered_user);

        // Get list of country
        $sql = 'SELECT name AS value, name AS text FROM #__eb_countries ORDER BY name';
        $db->setQuery($sql);
        $rowCountries = $db->loadObjectList();
        $options = array();
        $options[] = JHTML::_('select.option', '',
                JText::_('SCH_SELECT_DEFAULT_COUNTRY'));
        $options = array_merge($options, $rowCountries);
        $lists['country_list'] = JHTML::_('select.genericlist', $options,
                'default_country', '', 'value', 'text', $config->default_country);
        
        $lists['show_cat_decription_in_table_layout'] = JHTML::_(
                'select.booleanlist', 'show_cat_decription_in_table_layout', '',
                $config->show_cat_decription_in_table_layout);
        $lists['show_price_in_table_layout'] = JHTML::_('select.booleanlist',
                'show_price_in_table_layout', '',
                $config->show_price_in_table_layout);
        $lists['show_image_in_table_layout'] = JHTML::_('select.booleanlist',
                'show_image_in_table_layout', '',
                $config->show_image_in_table_layout);
        $lists['show_cat_decription_in_calendar_layout'] = JHTML::_(
                'select.booleanlist', 'show_cat_decription_in_calendar_layout',
                '', $config->show_cat_decription_in_calendar_layout);
        // Fields configuration
        $lists['s_lastname'] = JHTML::_('select.booleanlist', 's_lastname', '',
                $config->s_lastname);
        $lists['r_lastname'] = JHTML::_('select.booleanlist', 'r_lastname', '',
                $config->r_lastname);
        $lists['s_organization'] = JHTML::_('select.booleanlist',
                's_organization', '', $config->s_organization);
        $lists['r_organization'] = JHTML::_('select.booleanlist',
                'r_organization', '', $config->r_organization);
        $lists['s_address'] = JHTML::_('select.booleanlist', 's_address', '',
                $config->s_address);
        $lists['r_address'] = JHTML::_('select.booleanlist', 'r_address', '',
                $config->r_address);
        $lists['s_address2'] = JHTML::_('select.booleanlist', 's_address2', '',
                $config->s_address2);
        $lists['r_address2'] = JHTML::_('select.booleanlist', 'r_address2', '',
                $config->r_address2);
        $lists['s_city'] = JHTML::_('select.booleanlist', 's_city', '',
                $config->s_city);
        $lists['r_city'] = JHTML::_('select.booleanlist', 'r_city', '',
                $config->r_city);
        $lists['s_state'] = JHTML::_('select.booleanlist', 's_state', '',
                $config->s_state);
        $lists['r_state'] = JHTML::_('select.booleanlist', 'r_state', '',
                $config->r_state);
        $lists['s_zip'] = JHTML::_('select.booleanlist', 's_zip', '',
                $config->s_zip);
        $lists['r_zip'] = JHTML::_('select.booleanlist', 'r_zip', '',
                $config->r_zip);
        $lists['s_country'] = JHTML::_('select.booleanlist', 's_country', '',
                $config->s_country);
        $lists['r_country'] = JHTML::_('select.booleanlist', 'r_country', '',
                $config->r_country);
        $lists['s_phone'] = JHTML::_('select.booleanlist', 's_phone', '',
                $config->s_phone);
        $lists['r_phone'] = JHTML::_('select.booleanlist', 'r_phone', '',
                $config->r_phone);
        $lists['s_fax'] = JHTML::_('select.booleanlist', 's_fax', '',
                $config->s_fax);
        $lists['r_fax'] = JHTML::_('select.booleanlist', 'r_fax', '',
                $config->r_fax);
        $lists['s_comment'] = JHTML::_('select.booleanlist', 's_comment', '',
                $config->s_comment);
        $lists['r_comment'] = JHTML::_('select.booleanlist', 'r_comment', '',
                $config->r_comment);
        // Group fields configuration
        $lists['gs_lastname'] = JHTML::_('select.booleanlist', 'gs_lastname',
                '', $config->gs_lastname);
        $lists['gr_lastname'] = JHTML::_('select.booleanlist', 'gr_lastname',
                '', $config->gr_lastname);
        $lists['gs_organization'] = JHTML::_('select.booleanlist',
                'gs_organization', '', $config->gs_organization);
        $lists['gr_organization'] = JHTML::_('select.booleanlist',
                'gr_organization', '', $config->gr_organization);
        $lists['gs_address'] = JHTML::_('select.booleanlist', 'gs_address', '',
                $config->gs_address);
        $lists['gr_address'] = JHTML::_('select.booleanlist', 'gr_address', '',
                $config->gr_address);
        $lists['gs_address2'] = JHTML::_('select.booleanlist', 'gs_address2',
                '', $config->gs_address2);
        $lists['gr_address2'] = JHTML::_('select.booleanlist', 'gr_address2',
                '', $config->gr_address2);
        $lists['gs_city'] = JHTML::_('select.booleanlist', 'gs_city', '',
                $config->gs_city);
        $lists['gr_city'] = JHTML::_('select.booleanlist', 'gr_city', '',
                $config->gr_city);
        $lists['gs_state'] = JHTML::_('select.booleanlist', 'gs_state', '',
                $config->gs_state);
        $lists['gr_state'] = JHTML::_('select.booleanlist', 'gr_state', '',
                $config->gr_state);
        $lists['gs_zip'] = JHTML::_('select.booleanlist', 'gs_zip', '',
                $config->gs_zip);
        $lists['gr_zip'] = JHTML::_('select.booleanlist', 'gr_zip', '',
                $config->gr_zip);
        $lists['gs_country'] = JHTML::_('select.booleanlist', 'gs_country', '',
                $config->gs_country);
        $lists['gr_country'] = JHTML::_('select.booleanlist', 'gr_country', '',
                $config->gr_country);
        $lists['gs_phone'] = JHTML::_('select.booleanlist', 'gs_phone', '',
                $config->gs_phone);
        $lists['gr_phone'] = JHTML::_('select.booleanlist', 'gr_phone', '',
                $config->gr_phone);
        $lists['gs_fax'] = JHTML::_('select.booleanlist', 'gs_fax', '',
                $config->gs_fax);
        $lists['gr_fax'] = JHTML::_('select.booleanlist', 'gr_fax', '',
                $config->gr_fax);
        $lists['gs_email'] = JHTML::_('select.booleanlist', 'gs_email', '',
                $config->gs_email);
        $lists['gr_email'] = JHTML::_('select.booleanlist', 'gr_email', '',
                $config->gr_email);
        
        $lists['gs_comment'] = JHTML::_('select.booleanlist', 'gs_comment', '',
                $config->gs_comment);
        $lists['gr_comment'] = JHTML::_('select.booleanlist', 'gr_comment', '',
                $config->gr_comment);
        // aitinglist fields configuration
        $lists['swt_lastname'] = JHTML::_('select.booleanlist', 'swt_lastname',
                '', $config->swt_lastname);
        $lists['rwt_lastname'] = JHTML::_('select.booleanlist', 'rwt_lastname',
                '', $config->rwt_lastname);
        $lists['swt_organization'] = JHTML::_('select.booleanlist',
                'swt_organization', '', $config->swt_organization);
        $lists['rwt_organization'] = JHTML::_('select.booleanlist',
                'rwt_organization', '', $config->rwt_organization);
        $lists['swt_address'] = JHTML::_('select.booleanlist', 'swt_address',
                '', $config->swt_address);
        $lists['rwt_address'] = JHTML::_('select.booleanlist', 'rwt_address',
                '', $config->rwt_address);
        $lists['swt_address2'] = JHTML::_('select.booleanlist', 'swt_address2',
                '', $config->swt_address2);
        $lists['rwt_address2'] = JHTML::_('select.booleanlist', 'rwt_address2',
                '', $config->rwt_address2);
        $lists['swt_city'] = JHTML::_('select.booleanlist', 'swt_city', '',
                $config->swt_city);
        $lists['rwt_city'] = JHTML::_('select.booleanlist', 'rwt_city', '',
                $config->rwt_city);
        $lists['swt_state'] = JHTML::_('select.booleanlist', 'swt_state', '',
                $config->swt_state);
        $lists['rwt_state'] = JHTML::_('select.booleanlist', 'rwt_state', '',
                $config->rwt_state);
        $lists['swt_zip'] = JHTML::_('select.booleanlist', 'swt_zip', '',
                $config->swt_zip);
        $lists['rwt_zip'] = JHTML::_('select.booleanlist', 'rwt_zip', '',
                $config->rwt_zip);
        $lists['swt_country'] = JHTML::_('select.booleanlist', 'swt_country',
                '', $config->swt_country);
        $lists['rwt_country'] = JHTML::_('select.booleanlist', 'rwt_country',
                '', $config->rwt_country);
        $lists['swt_phone'] = JHTML::_('select.booleanlist', 'swt_phone', '',
                $config->swt_phone);
        $lists['rwt_phone'] = JHTML::_('select.booleanlist', 'rwt_phone', '',
                $config->rwt_phone);
        $lists['swt_fax'] = JHTML::_('select.booleanlist', 'swt_fax', '',
                $config->swt_fax);
        $lists['rwt_fax'] = JHTML::_('select.booleanlist', 'rwt_fax', '',
                $config->rwt_fax);
        $lists['swt_comment'] = JHTML::_('select.booleanlist', 'swt_comment',
                '', $config->swt_comment);
        $lists['rwt_comment'] = JHTML::_('select.booleanlist', 'rwt_comment',
                '', $config->rwt_comment);
        $options = array();
        $options[] = JHTML::_('select.option', '', JText::_('SCH_NO_MAPPING'));
        if ($config->cb_integration > 0) {
            if ($config->cb_integration == 1) {
                // Get list of CB fields
                $sql = 'SELECT name AS `value`, name AS `text` FROM #__comprofiler_fields WHERE `table` LIKE "%_comprofiler%" AND published=1';
                $db->setQuery($sql);
                $options = array_merge($options, $db->loadObjectList());
            } else {
                // Get list of Jomsocial field code
                $sql = 'SELECT fieldcode AS `value`, fieldcode AS `text` FROM #__community_fields WHERE published=1 AND fieldcode != ""';
                $db->setQuery($sql);
                $options = array_merge($options, $db->loadObjectList());
            }
            $fields = array(
                    'm_firstname',
                    'm_lastname',
                    'm_organization',
                    'm_address',
                    'm_address2',
                    'm_city',
                    'm_state',
                    'm_zip',
                    'm_country',
                    'm_phone',
                    'm_fax'
            );
            foreach ($fields as $field) {
                $lists[$field] = JHTML::_('select.genericlist', $options,
                        $field, 'class="inputbox"', 'value', 'text',
                        $config->{$field});
            }
        }
       // Get tab object
        $this->assignRef('lists', $lists);
        $this->assignRef('config', $config);
        parent::display($tpl);
    }
}