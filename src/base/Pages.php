<?php namespace mailer\base;

use mailer\base\View as View;
use mailer\Main as Main;
use mailer\models\UserRoleAsMailer as RAM;
use mailer\models\UserRoleAsAddressee as RAA;
use mailer\models\PostTypeToMailer as PTM;

class Pages {
    
    /**
     *
     * @var Main 
     */
    public $app;
    
    public function __construct() {
        $this->app = Main::instance();
        add_action('admin_menu',  array($this, 'addPages'));
    }
    
    public function addPages()
    {
        add_menu_page('rMailer', 'rMailer', 2, 'main', array($this, 'adminSettings'), $this->getIcon('coverIcon'));
        add_submenu_page(null, 'Admin Setings', 'Settings (admin)', 8, 'setting', array($this, 'setting'), $this->getIcon('settingIcon'));
        add_submenu_page('main', 'Realtor mailer page', 'mailer', 2, 'realtor', array($this, 'realtor'), $this->getIcon('workIcon'));
         
    }
    
    public function adminSettings()
    {
        View::render('admin-settings', array(), View::_ADMIN);   
    }
    
    public function realtor()
    {
        $pathCSS = '..'.DS.'assets'.DS.'css'.DS.'tab.css';
        wp_register_style('tab_css_mailer_plugin', plugins_url($pathCSS ,__FILE__ ));
        wp_enqueue_style('tab_css_mailer_plugin');
        
        wp_enqueue_style('ie7', 'http://ie7-js.googlecode.com/svn/version/2.1(beta3)/IE9.js');
        wp_style_add_data( 'ie7', 'conditional', 'lt IE 9' );
        
        $pathJS = '..'.DS.'assets'.DS.'js'.DS.'mailer-ajax.js';
        wp_register_script('tab_js_mailer_plugin', plugins_url($pathJS ,__FILE__ ));
        wp_enqueue_script('tab_js_mailer_plugin');
        
        $pathJS = '..'.DS.'assets'.DS.'js'.DS.'liHarmonica.js';
        wp_register_script('harmonica_js_mailer_plugin', plugins_url($pathJS ,__FILE__ ));
        wp_enqueue_script('harmonica_js_mailer_plugin');
    
        View::render('realtor', array(), View::_ADMIN);   
    }
    
    public function setting()
    {
        $roles = filter_input(INPUT_POST, 'roles', FILTER_DEFAULT , FILTER_REQUIRE_ARRAY);
        if ( $roles ){ RAM::saveAllMailerRoles($roles); }
        
        $rolesAddressee = filter_input(INPUT_POST, 'rolesAdrressee', FILTER_DEFAULT , FILTER_REQUIRE_ARRAY);
        if ( $rolesAddressee ){ RAA::saveAllAddresseRoles($rolesAddressee); }
        
        $postType = filter_input(INPUT_POST, 'postType', FILTER_DEFAULT , FILTER_REQUIRE_ARRAY);
        if ( $postType ){ PTM::saveAllPostTypes($postType); }
        
        View::render('setting', array(), View::_ADMIN); 
    }
    
    public function getIcon($name)
    {
        return static::$name();
    }
    
    protected static function coverIcon()
    {
        // Base 64 encoded SVG image.
        return "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjxzdmcgaGVpZ2h0PSIzMnB4IiB2ZXJzaW9uPSIxLjEiIHZpZXdCb3g9IjAgMCAzMiAzMiIgd2lkdGg9IjMycHgiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6c2tldGNoPSJodHRwOi8vd3d3LmJvaGVtaWFuY29kaW5nLmNvbS9za2V0Y2gvbnMiIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj48dGl0bGUvPjxkZXNjLz48ZGVmcy8+PGcgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIiBpZD0iUGFnZS0xIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSI+PGcgZmlsbD0iIzkyOTI5MiIgaWQ9Imljb24tMi1tYWlsLWVudmVsb3BlLW9wZW4iPjxwYXRoIGQ9Ik0yMC42MzgxNTE2LDIwIEwxOC40MjA1Njk0LDIxLjk4Mjc4ODkgTDE4LjUxOTk0NjksMjEuOTgyMjcwNCBMMTguNTE5OTQ2OSwyMS45ODIyNzA0IEwxOC41LDIyLjAwMDAwMSBMMTUuMTIxNDkzMSwyMi4wMDAwMDEgTDE2LjgxODE5MDIsMjEuOTkxMTQ4OSBMMTYuODE4MTkwMiwyMS45OTExNDg5IEwxNC41NTEwMDQsMjEuOTgwMzA5OSBMMTIuMzA2NjUyOCwyMCBMMjAuNjM4MTUxNiwyMCBMMjAuNjM4MTUxNiwyMCBaIE0yMS43NTY1NjczLDE5IEwyNSwxNi4wOTk5NzU2IEwyNSw4LjAwODYyNTc3IEMyNSw3LjQ1MTU3NzE0IDI0LjU0NTI5MTEsNyAyNC4wMDAwMzk4LDcgTDguOTk5OTYwMiw3IEM4LjQ0NzY5NzQzLDcgOCw3LjQ0MzczNTcxIDgsOC4wMDIwNzU5NiBMOCwxNi4yMDAwMTIyIEwxMS4xNzMzMTk1LDE5IEwyMS43NTY1NjczLDE5IEwyMS43NTY1NjczLDE5IFogTTEzLjA5MDkwOTEsNiBMMTYuNSwzIEwxOS45MDkwOTA5LDYgTDI0LjAwMjU3ODEsNiBDMjUuMTA5MDc0Niw2IDI2LDYuODk1MjU4MTIgMjYsNy45OTk2MTQ5OCBMMjYsMTEuMzYgTDI5LDE0IEwyOSwyOC4wMDU5Mzk3IEMyOSwyOS4xMDU0ODYyIDI4LjEwMjk2LDMwIDI2Ljk5NjQwNTEsMzAgTDYuMDAzNTk0ODYsMzAgQzQuODg5NzYzMjQsMzAgNCwyOS4xMDcyMjg4IDQsMjguMDA1OTM5NyBMNCwxNCBMNywxMS4zNiBMNyw3Ljk5OTYxNDk4IEM3LDYuODg3NDMzMjkgNy44OTQyNzYyNSw2IDguOTk3NDIxOTEsNiBMMTMuMDkwOTA5MSw2IEwxMy4wOTA5MDkxLDYgTDEzLjA5MDkwOTEsNiBaIE0xOC40Mjc4MzQ5LDYgTDE2LjUsNC4zMDAwMDAxOSBMMTQuNTcyMTY1MSw2IEwxOC40Mjc4MzQ5LDYgTDE4LjQyNzgzNDksNiBMMTguNDI3ODM0OSw2IFogTTI2LDEyLjY3NzI3MjcgTDI3LjUsMTQgTDI2LDE1LjMzMzMzMzUgTDI2LDEyLjY3NzI3MjcgTDI2LDEyLjY3NzI3MjcgTDI2LDEyLjY3NzI3MjcgWiBNNywxNS4zMzMzMzM1IEw1LjUsMTQgTDcsMTIuNjc3MjcyOCBMNywxNS4zMzMzMzM1IEw3LDE1LjMzMzMzMzUgTDcsMTUuMzMzMzMzNSBaIE0xMy41LDIzIEw2LjUsMjkgTDI2LjUsMjkgTDE5LjUsMjMgTDEzLjUsMjMgTDEzLjUsMjMgWiBNMjcuNjg1MTQsMjguNzI1MTcwMSBMMjAsMjIuMDE3MDg5OCBMMjgsMTUgTDI4LDIxLjUwMDAxOTkgTDI4LDI4LjAwMDAzOTggQzI4LDI4LjI4MzE1MzcgMjcuODc4OTk0OSwyOC41NDE4NTY5IDI3LjY4NTE0LDI4LjcyNTE3MDEgTDI3LjY4NTE0LDI4LjcyNTE3MDEgTDI3LjY4NTE0LDI4LjcyNTE3MDEgWiBNNS4zMTQ4NiwyOC43MjUxNzAxIEwxMywyMi4wMTcwODk4IEw1LDE1IEw1LDIxLjUwMDAxOTkgTDUsMjguMDAwMDM5OCBDNSwyOC4yODMxNTM3IDUuMTIxMDA1MTQsMjguNTQxODU2OSA1LjMxNDg2LDI4LjcyNTE3MDEgTDUuMzE0ODYsMjguNzI1MTcwMSBMNS4zMTQ4NiwyOC43MjUxNzAxIFogTTEwLDEwIEwxMCwxMSBMMjMsMTEgTDIzLDEwIEwxMCwxMCBMMTAsMTAgWiBNMTAsMTMgTDEwLDE0IEwyMywxNCBMMjMsMTMgTDEwLDEzIEwxMCwxMyBaIE0xMCwxNiBMMTAsMTcgTDIzLDE3IEwyMywxNiBMMTAsMTYgTDEwLDE2IFoiIGlkPSJtYWlsLWVudmVsb3BlLW9wZW4iLz48L2c+PC9nPjwvc3ZnPg==";
    }
    
    protected static function settingIcon()
    {
        return "";
    }
    
    protected static function workIcon()
    {
        return "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjxzdmcgaGVpZ2h0PSIzMnB4IiB2ZXJzaW9uPSIxLjEiIHZpZXdCb3g9IjAgMCAzMiAzMiIgd2lkdGg9IjMycHgiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6c2tldGNoPSJodHRwOi8vd3d3LmJvaGVtaWFuY29kaW5nLmNvbS9za2V0Y2gvbnMiIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj48dGl0bGUvPjxkZXNjLz48ZGVmcy8+PGcgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIiBpZD0iUGFnZS0xIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSI+PGcgZmlsbD0iIzE1N0VGQiIgaWQ9Imljb24tMTM2LWRvY3VtZW50LWVkaXQiPjxwYXRoIGQ9Ik0yNi40NDMyMjc4LDEyLjE1MDMzNDUgTDE1LjE1NzAxMzEsMjMuNDQ5OTA2NCBMMTUuMTU3MDEzMSwyMy40NDk5MDY0IEwxMi41NTE0NDY1LDIwLjg0NDMzOTcgTDIzLjg0MzUzODMsOS41NTA2NDUxMyBMMjYuNDQzMjI3OCwxMi4xNTAzMzQ1IEwyNi40NDMyMjc4LDEyLjE1MDMzNDUgWiBNMjcuMTQ5OTE2NCwxMS40NDI4MDk2IEwyOC44NzkwOTU0LDkuNzExNTg0MDUgQzI5LjI2OTA2OSw5LjMyMTE0ODkyIDI5LjI2NjE5NSw4LjY4NjUwNDIzIDI4Ljg3NDMsOC4yOTU2ODQ5NyBMMjcuNjk0NDg2Niw3LjExOTEwOTk4IEMyNy4zMDE4NjQ2LDYuNzI3NTY1NjQgMjYuNjY5MjU3Nyw2LjcyNDUyNDY2IDI2LjI3NzkxMjYsNy4xMTU5MjUzMSBMMjQuNTUwNTk0OSw4Ljg0MzQ4ODE3IEwyNy4xNDk5MTY0LDExLjQ0MjgwOTYgTDI3LjE0OTkxNjQsMTEuNDQyODA5NiBaIE0xMS45MDM3MDYxLDIxLjYxMDgxMjkgTDExLjI2NDE2MDIsMjQuNzIzNTEwMyBMMTQuMzk5MDY0NSwyNC4xMDYxNzEzIEwxMS45MDM3MDYxLDIxLjYxMDgxMjkgTDExLjkwMzcwNjEsMjEuNjEwODEyOSBMMTEuOTAzNzA2MSwyMS42MTA4MTI5IFogTTIyLDEwIEwyMiwxMCBMMTYsMyBMNS4wMDI3NjAxMywzIEMzLjg5NjY2NjI1LDMgMywzLjg5ODMzODMyIDMsNS4wMDczMjk5NCBMMywyNy45OTI2NzAxIEMzLDI5LjEwMTI4NzggMy44OTA5MjUzOSwzMCA0Ljk5NzQyMTkxLDMwIEwyMC4wMDI1NzgxLDMwIEMyMS4xMDU3MjM4LDMwIDIyLDI5LjEwMTc4NzYgMjIsMjguMDA5MjA0OSBMMjIsMTggTDI5LjU4MDEwNjcsMTAuNDE5ODkzMiBDMzAuMzY0MjkyMSw5LjYzNTcwNzg1IDMwLjM2NjE4ODEsOC4zNjYxODgwOSAyOS41ODk3NDk2LDcuNTg5NzQ5NjIgTDI4LjQxMDI1MDQsNi40MTAyNTAzNiBDMjcuNjMxMzkwNiw1LjYzMTM5MDYgMjYuMzcyNzgxLDUuNjI3MjE4OTcgMjUuNTgwMTA2Nyw2LjQxOTg5MzI3IEwyMiwxMCBMMjIsMTAgTDIyLDEwIFogTTIxLDE5IEwyMSwyOC4wMDY2MDIzIEMyMSwyOC41NTUwNTM3IDIwLjU1MjMwMjYsMjkgMjAuMDAwMDM5OCwyOSBMNC45OTk5NjAyLDI5IEM0LjQ1NDcwODkzLDI5IDQsMjguNTU0MzE4NyA0LDI4LjAwNDU0MyBMNCw0Ljk5NTQ1NzAzIEM0LDQuNDU1MjYyODggNC40NDU3MzUyMyw0IDQuOTk1NTc3NSw0IEwxNSw0IEwxNSw4Ljk5NDA4MDk1IEMxNSwxMC4xMTM0NDUyIDE1Ljg5NDQ5NjIsMTEgMTYuOTk3OTEzMSwxMSBMMjEsMTEgTDExLDIxIEwxMCwyNiBMMTUsMjUgTDIxLDE5IEwyMSwxOSBMMjEsMTkgWiBNMTYsNC41IEwxNiw4Ljk5MTIxNTIzIEMxNiw5LjU0ODM1MTY3IDE2LjQ1MDY1MTEsMTAgMTYuOTk2NzM4OCwxMCBMMjAuNjk5OTUxMiwxMCBMMTYsNC41IEwxNiw0LjUgWiIgaWQ9ImRvY3VtZW50LWVkaXQiLz48L2c+PC9nPjwvc3ZnPg==";
    }
    
}
