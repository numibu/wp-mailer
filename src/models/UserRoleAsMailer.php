<?php namespace mailer\models;

class UserRoleAsMailer {
    
    
    public static function getAllRoleWP()
    {
        $result = array();
        
        $roles = get_editable_roles();
        foreach( $roles as $role ){
            $result[] = $role['name'];
        }
        
        return $result;
    }

    public static function saveAllMailerRoles($roles)
    {
        $roleArray = static::getAllRoleWP();
        $newRoleAsMailer = array();
        
        foreach ($roles as $role){
            if ( in_array($role, $roleArray) ){
                $newRoleAsMailer[] = $role;
            }
        }
        
        update_option('mailer_role_rc_mailer_plugin', $newRoleAsMailer);
    }
    
    public static function getAllMailerRole()
    {
        $mailerRole = get_option('mailer_role_rc_mailer_plugin');
        if ( $mailerRole ) {
            return $mailerRole;
        }
        return array();
    }
    
    public static function getFormWithRole()
    {
        $action = str_replace( '%7E', '~', $_SERVER['REQUEST_URI']);
        $html = '<div class="mailerRoles"><p>Роли пользователей которые могут отправлять письма: </p><span>';
        $html .= '<form method="post" name="sendRoleAsMailer" action="'.$action.'">';
        $roleArray = static::getAllRoleWP();
        $mailerArray = static::getAllMailerRole();
        
        foreach ($roleArray as $value) {
            $isset = ( in_array($value, $mailerArray) )? 'checked' : '' ;
            $html .= "<label> $value: </label>";
            $html .= "<input type='hidden' name='roles[$value]' value='' />";
            $html .= "<input type='checkbox' name='roles[$value]' value='$value' $isset />";
            //$html .= '<br>';
        }   
        $html .= '<input type="submit" name="Submit" value="save" />';
        $html .= '</form></span></div>';
        
        return $html;
    }
    
    
}


