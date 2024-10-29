<?php

class Email
{
    private $sendgrid;
    private $mail;
    private $from_email;
    private $template_id;
    private $substitutions;
    private $to_email;
    private $subject;

    private static $from_name = 'SnackCrate';

    public function __construct()
    {
        $this->sendgrid = new SendGrid( $_ENV['sendgrid_api_key'] );
		$this->mail = new SendGrid\Mail\Mail();

        $this->from_email = 'noreply@snackcrate.com'; // default
        $this->subject = ' '; //default
    }

    public function setFromEmail($from_email)
    {
        $this->from_email = $from_email;
    }

    public function setToEmail($to_email)
    {
        $this->to_email = $to_email;
    }

    public function setSubstitutions($substitutions)
    {
        foreach($substitutions as $tag => $replacement)
        {
            $this->mail->addSubstitution($tag, $replacement);
        }
    }

    public function setTemplateId($template_id)
    {
        $this->template_id = $template_id;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function sendEmail()
    {
        if( 
            empty($this->to_email) || 
            empty($this->template_id) 
        )
        {
            return false;
        }

        return $this->_send();
    }

    private function _send()
    {
        try 
        {
            $this->mail->setFrom( $this->from_email, self::$from_name );
            $this->mail->addTo( $this->to_email );
		    $this->mail->setSubject( $this->subject );
            $this->mail->setTemplateId( $this->template_id );

            $response = $this->sendgrid->send( $this->mail );
		
		    if (!$response) 
            {
		        throw new Exception("Did not receive response.");
		    }
            return true;
		} 
        catch ( Exception $e ) {
		    return $e->getMessage();
		}
    }
}