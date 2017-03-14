<?php namespace mailer\models;

use mailer\models\UserRoleAsAddressee as Addresse;

class AddresseeList {
    
     public static function getListAddressee()
    {
        $addresseeArray = Addresse::getAllAddresseeRole();
        
        if ( count($addresseeArray)>0 ) {
            return get_users( array( 'role__in'=> $addresseeArray ) );
        }else{
            return array();
        }
    }
    
    
    public static function  getHTMLContent()
    {
       $list = static::getListAddressee();
       $html = "<ul class='addresseeList'>";
       
       foreach ($list as $item) {
           
           $login = $item->data->user_login;
           $niceName = $item->data->user_nicename;
           $mail = $item->data->user_email;
           $registered = $item->data->user_registered;
           $ID = $item->data->ID;
           
           $input = "<input type='checkbox' name='client' user-type='register' user-id='$ID' value='1'>";
           $html .= "<li> $input <strong>login: </strong> $login,"
                   . " <strong>name : </strong> <span style='color: green;'>$niceName</span>,"
                   . "<strong>email: </strong> <span style='color: blue;'>$mail</span> </li>";
           
       }
       
       $html .="</ul>";
       return $html;
    }
    
    public static function getHTMLFormAddCustomAddressee()
    {
        $inputName = "<p> Имя: <input type='text' name='customAddresseName'  placeholder=''> ";
        $inputMail = " mail: <input type='text' name='customAddresseMail'  placeholder=''> ";
        $submit = ' <input type="submit" value="в список" onclick="addToListAddresse()"></p>';
        
        return $inputName . $inputMail . $submit;
    }
    
}

