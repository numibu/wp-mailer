<?php namespace mailer\models;

class UserRoleAsAddressee {
    
    
    public static function getAllRoleWP()
    {
        $result = array();
        
        $roles = get_editable_roles();
        foreach( $roles as $role ){
            $result[] = $role['name'];
        }
        
        return $result;
    }

    public static function saveAllAddresseRoles($roles)
    {
        $roleArray = static::getAllRoleWP();
        $newRoleAsAddresse = array();
        
        foreach ($roles as $role){
            if ( in_array($role, $roleArray) ){
                $newRoleAsAddresse[] = $role;
            }
        }
        
        update_option('addressee_role_rc_mailer_plugin', $newRoleAsAddresse);
    }
    
    public static function getAllAddresseeRole()
    {
        $addresseRole = get_option('addressee_role_rc_mailer_plugin');
        if ( $addresseRole ) {
            return $addresseRole;
        }
        return array();
    }
    
    public static function getFormWithRole()
    {
        $action = str_replace( '%7E', '~', $_SERVER['REQUEST_URI']);
        $html = '<div class="addresseeRoles"><p>Роли пользователей кто может получать письма: </p><span>';
        $html .= '<form method="post" name="sendRoleAsAddressee" action="'.$action.'">';
        $roleArray = static::getAllRoleWP();
        $addresseeArray = static::getAllAddresseeRole();
        
        foreach ($roleArray as $value) {
            $isset = ( in_array($value, $addresseeArray) )? 'checked' : '' ;
            $html .= "<label> $value: </label>";
            $html .= "<input type='hidden' name='rolesAdrressee[$value]' value='' />";
            $html .= "<input type='checkbox' name='rolesAdrressee[$value]' value='$value' $isset />";
            //$html .= '<br>';
        }   
        $html .= '<input type="submit" name="Submit" value="save" />';
        $html .= '</form></span></div>';
        
        return $html;
    }
    
    
}


