<?php namespace mailer\models;

class PostTypeToMailer {
    
    
    public static function getAllPostType()
    {
        return get_post_types();
    }

    public static function saveAllPostTypes($postTypes)
    {
        $postTypeArray = static::getAllPostType();
        $newPostTypeToMailer = array();
        
        foreach ($postTypes as $type){
            if ( in_array($type, $postTypeArray) ){
                $newPostTypeToMailer[] = $type;
            }
        }
        
        update_option('post_types_rc_mailer_plugin', $newPostTypeToMailer);
    }
    
    public static function getAllPostTypeToMailer()
    {
        $mailerRole = get_option('post_types_rc_mailer_plugin');
        if ( $mailerRole ) {
            return $mailerRole;
        }
        return array();
    }
    
    public static function getFormWithPostType()
    {
        $action = str_replace( '%7E', '~', $_SERVER['REQUEST_URI']);
        $html = '<div class="mailerTaxo"><p>Selected post types to mailer!</p><span>';
        $html .= '<form method="post" name="sendPostTYpe" action="'.$action.'">';
        $postTypeArray = static::getAllPostType();
        $mailerPostTypeArray = static::getAllPostTypeToMailer();
        
        foreach ($postTypeArray as $value) {
            $isset = ( in_array($value, $mailerPostTypeArray) )? 'checked' : '' ;
            $html .= "<label> $value: </label>";
            $html .= "<input type='hidden' name='postType[$value]' value='' />";
            $html .= "<input type='checkbox' name='postType[$value]' value='$value' $isset />";
            //$html .= '<br>';
        }   
        $html .= '<input type="submit" name="Submit" value="save" />';
        $html .= '</form></span></div>';
        
        return $html;
    }
    
    
}


