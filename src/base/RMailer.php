<?php namespace mailer\base;

include_once(ABSPATH . WPINC . '/class-phpmailer.php'); 

class RMailer{
    
    protected $mailer;
    protected $userMailer;
    protected $AddresseeUser;
    protected $content;

    /**
     * Конструктор письма.
     * @param \mailer\base\WP_User $mailerUser. User of WordPress
     * @param array $AddresseeUser. array('name@asd.net', 'name').
     * @param type $content. Content of htmlBody
     */
    public function __construct(\WP_User $userMailer, \stdClass $AddresseeUser, $content) 
    {
        $this->mailer = new \PHPMailer();
        $this->userMailer = $userMailer;
        $this->AddresseeUser = $AddresseeUser;
        $this->content = $content;
        
        $this->init($this->prepare());
        $this->rmSend();
    }

    public function rmSend()
    {
        $result = $this->mailer->Send();
        if(!$result){
            return $this->mailer->ErrorInfo;
        }
        return $result;    
    }
    
    /**
     * 
     * @param array $conf - [   IsSMTP => true , // if true call method ->IsSMTP();
     Host => '',
     SMTPDebug => 0, // 2 - view message
     SMTPAuth => true|false,
     SMTPSecure => 'tls',
     Port => 587,
     Username => '',// asd @ gmail.com
     Password => 'password', 
     addReplyTo => array('email', 'title to replay'),
     addresseeMail => array('email', 'name of addressee'),
     SetFrom => array('email', 'title from'),
     IsHTML => true|false,
     CharSet => 'utf-8',
     Subject => 'theme email',
     htmlBody = $htmlOut;
     $rm->AltBody = 'alternative text';
     *  ] 
     */
    private function init($conf = array())
    {
        if ($conf['IsSMTP']) {
            $this->mailer->IsSMTP();
            $this->mailer->Host = ($conf['IsSMTP'] && in_array('Host', $conf))? $conf['Host'] : '';
            $this->mailer->SMTPDebug = ($conf['SMTPDebug'])? $conf['SMTPDebug'] : 0;
            $this->mailer->SMTPAuth = ($conf['SMTPAuth'])? $conf['SMTPAuth'] : true;
            $this->mailer->SMTPSecure = ($conf['SMTPSecure'])? $conf['SMTPSecure'] : 'tls';
            $this->mailer->Port = ($conf['Port'])? $conf['Port'] : 587;
            $this->mailer->Username = ($conf['Username'])? $conf['Username'] : 'alexandrr.naumenko';
            $this->mailer->Password = ($conf['Password'])? $conf['Password'] : '0937202604';
        }else{
            //$this->mailer->Mailer = 'mail';
        }
        
        (is_array($conf['addReplyTo']))? $this->mailer->AddReplyTo($conf['addReplyTo'][0] , $conf['addReplyTo'][1]) : null;
        (is_array($conf['addresseeMail']))? $this->mailer->AddAddress($conf['addresseeMail'][0] , $conf['addresseeMail'][1]) : null;
        (is_array($conf['SetFrom']))? $this->mailer->SetFrom($conf['SetFrom'][0] , $conf['SetFrom'][1]) : null;
        ($conf['IsHTML'])? $this->mailer->IsHTML(true) : null ;
        $this->mailer->CharSet = ($conf['CharSet'])? $conf['CharSet'] : 'utf-8';
        $this->mailer->Subject = ($conf['Subject'])? $conf['Subject'] : 'Theme of email';
        $this->mailer->Body = ($conf['IsHTML'])? $conf['htmlBody'] : '';
        $this->mailer->AltBody = ($conf['AltBody'])? $conf['AltBody'] : 'alternative text';

    }
    
    private function prepare()
    {
        return array(
                    'IsSMTP' => false , // if true call method ->IsSMTP();
                    'Host' => 'smtp.gmail.com',
                    'SMTPDebug' => 2, // 2 - view message
                    'SMTPAuth' => true,
                    'SMTPSecure' => 'tls',
                    'Port' => 587,
                    'Username' => 'alexandrr.naumenko',// asd @ gmail.com
                    'Password' => '123456789', 
                    'addReplyTo' => array($this->userMailer->data->user_email, $this->userMailer->data->user_nicename ),
                    'addresseeMail' => array($this->AddresseeUser->email, $this->AddresseeUser->name),
                    'SetFrom' => array('localhost', 'title from'),
                    'IsHTML' => true,
                    'CharSet' => 'utf-8',
                    'Subject' => 'theme email',
                    'htmlBody' =>  $this->getHTML(),
                    'AltBody' => 'alternative text'
        );
        
    }
   
    private function getHTML()
    {
        $youRealtor = '<br><span style="color: red;">You realtor ' .
                        $this->userMailer->data->user_nicename . ', email:' .
                        $this->userMailer->data->user_email . '</span>';
        
        $htmlBody = "<html><head>
            <title>My HTML Email</title>
            </head>
            <body>
            <h1>Тестовое письмо</h1>
            <br>
            ";
        
        return $htmlBody . $this->content . $youRealtor . '</body></html>';
    }
}
