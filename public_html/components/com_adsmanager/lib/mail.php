<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class TMail {
	
	/**
	 * Mail function (uses phpMailer)
	 *
	 * @param   string   $from         From email address
	 * @param   string   $fromname     From name
	 * @param   mixed    $recipient    Recipient email address(es)
	 * @param   string   $subject      Email subject
	 * @param   string   $body         Message body
	 * @param   boolean  $mode         False = plain text, true = HTML
	 * @param   mixed    $cc           CC email address(es)
	 * @param   mixed    $bcc          BCC email address(es)
	 * @param   mixed    $attachment   Attachment file name(s)
	 * @param   mixed    $replyto      Reply to email address(es)
	 * @param   mixed    $replytoname  Reply to name(s)
	 *
	 * @return  boolean  True on success
	 *
	 * @see     JMail::sendMail()
	 * @since   11.1
	 */
	public static function sendMail($from, $fromname, $recipient, $subject, $body, $mode = 0, $cc = null, $bcc = null, $attachment = null,
                $replyto = null, $replytoname = null)
	{
		if (version_compare(JVERSION,'2.5.0','>=')) {
			// Get a JMail instance
			$mail = JFactory::getMailer();
			//$mail->sendMail("support@juloa.com", "support@juloa.com", "support@juloa.com","je fais un test", "je fais un test", 1);
			return $mail->sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment,$replyto,$replytoname);
		} else {
			return JUtility::sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment,$replyto,$replytoname);
		}
	}
}