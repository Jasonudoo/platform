<?php
/**
 * @version		1.0.0
 * @package		Joomla
 * @subpackage	Event Booking
 * @author      Jason<jason@netwebx.com>
 * @copyright	Copyright (C) 2010 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die('Restrict Access');

class com_scheduleorderInstallerScript
{
    public static $languageFiles = array('en-GB.com_scheduleorder.ini');
    /**
     * Method to run before installing the component
     */
    function preflight($type, $parent)
    {
        //Backup the old language file
        foreach (self::$languageFiles as $languageFile) {
            if (JFile::exists(JPATH_ROOT . '/language/en-GB/' . $languageFile)) {
                JFile::copy(JPATH_ROOT . '/language/en-GB/' . $languageFile,
                        JPATH_ROOT . '/language/en-GB/bak.' . $languageFile);
            }
        }
    }
    /**
     * method to install the component
     *
     * @return void
     */
    function install($parent)
    {
        $this->_updateDatabaseSchema();
    }

    function update($parent)
    {
        $this->_updateDatabaseSchema();
    }

    function _updateDatabaseSchema()
    {
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');
        $db = JFactory::getDBO();
        #Remove htaccess file to support image feature
        if (JFile::exists(JPATH_ROOT . '/media/com_scheduleorder/.htaccess')) {
            JFile::delete(JPATH_ROOT . '/media/com_scheduleorder/.htaccess');
        }

        //Delete the css files which are now moved to themes folder
        $files = array('default.css', 'fire.css', 'leaf.css', 'ocean.css',
                'sky.css', 'tree.css');
        $path = JPATH_ROOT . '/components/com_scheduleorder/assets/css/';
        foreach ($files as $file) {
            $filePath = $path . $file;
            if (JFile::exists($filePath)) {
                JFile::delete($filePath);
            }
        }

    }
    /**
     * Method to run after installing the component
     */
    function postflight($type, $parent)
    {
        //Restore the modified language strings by merging to language files
        $registry = new JRegistry();
        foreach (self::$languageFiles as $languageFile) {
            $backupFile = JPATH_ROOT . '/language/en-GB/bak.' . $languageFile;
            $currentFile = JPATH_ROOT . '/language/en-GB/' . $languageFile;
            if (JFile::exists($currentFile) && JFile::exists($backupFile)) {
                $registry->loadFile($currentFile, 'INI');
                $currentItems = $registry->toArray();
                $registry->loadFile($backupFile, 'INI');
                $backupItems = $registry->toArray();
                $items = array_merge($currentItems, $backupItems);
                $content = "";
                foreach ($items as $key => $value) {
                    $content .= "$key=\"$value\"\n";
                }
                JFile::write($currentFile, $content);
            }
        }
    }
}
