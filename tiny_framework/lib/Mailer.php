<?php

/**
 * Mailer.
 * 
 * @package mailer
 * @author Kerem Kayhan
 * @copyright UzakYakin(c)
 * @version 2010-10-20
 **/

require_once 'vendor/class.phpmailer.php';

/* SMTP SETTINGS */
define('MAILER_HOST', 'smtp.saglikbahcesi.com.tr');
define('MAILER_SMTP_DEBUG', 1);
define('MAILER_PORT', 587);

/* SMTP AUTHENTICATION */
define('MAILER_USERNAME', 'info@uzakyakni.net');
define('MAILER_PASSWORD', '123123');

/* FROM PARTS */
define('MAILER_FROM', 'info@uzakyakin.net');
define('MAILER_FROM_NAME', PROJECT_NAME);

/* HTML TEMPLATES */
define('MAILER_IS_HTML', 1); // Set to 0 if you want to send message as text...
define('MAILER_TEMPLATE', 'mail_template.php'); // Set to 0 if you don't want to use the template...

class Mailer 
{
	
	private $mail;
	
	public function __construct()
	{
		$this->mail 						= new PHPMailer();
		$this->mail->Host 			= MAILER_HOST;
		$this->mail->SMTPDebug	= MAILER_SMTP_DEBUG;
		$this->mail->Port 			= MAILER_PORT;
		$this->mail->Mailer 		= "smtp";
		$this->mail->SMTPAuth 	= "true";
		$this->mail->Username 	= MAILER_USERNAME;
		$this->mail->Password 	= MAILER_PASSWORD;
		$this->mail->IsHTML(MAILER_IS_HTML);
		$this->mail->CharSet		= "UTF-8";	
	}	
	
	public function send($subject, $message, $to, $printBody = false, $debug = false, $from = NULL, $fromname = NULL)
	{
		
		if( empty($to) ){
			throw new Exception("To email must be specified", 500);
			return false;
		}
		
		if( MAILER_TEMPLATE ){
			$body = $this->getMailTemplate($message);
		}else{
			$body = $message;
		}
		
		$this->mail->From			= ($from ? $from : MAILER_FROM);
		$this->mail->FromName	= ($fromname ? $fromname : MAILER_FROM_NAME);
		$this->mail->Subject  = $subject;
		$this->mail->Body 		= $body;
		$this->mail->AddAddress($to);
		
		$retVal = '';
		
		if($this->mail->Send()) {
			$retVal = true;
			if( $printBody ){
			 $retVal = $body;
			}
		} else {
			$retVal = false;
		  if( $debug ){
			 $retVal = 'Mail error: '.$mail->ErrorInfo;
			}			
		}

		return $retVal;
	
	}	
	
  public function getMailBody($message)
  {
    if( MAILER_TEMPLATE ){
      $body = $this->getMailTemplate($message);
    }else{
      $body = $message;
    }
    return $body;
  } 	
	
	
	public function getMailTemplate($message = null)
	{
		//$message = nl2br($message);
		if(!is_null($message))
		{
			ob_start();
			require MAILER_TEMPLATE;
			$mailTemplate = str_replace("{%message%}", $message, ob_get_contents());
			ob_end_clean();		
		}
		
		return $mailTemplate;
	}	
	
}

