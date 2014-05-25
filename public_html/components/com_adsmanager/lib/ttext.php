<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class TText {
    public static function _($text) {
        if (strpos($text,",") !== false) {
            return $text;
        } else {
            return JText::_($text);
        }
    }
}