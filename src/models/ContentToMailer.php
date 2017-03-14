<?php namespace mailer\models;

use mailer\models\PostTypeToMailer as PostType;

class ContentToMailer {
    
    public static function getListContent()
    {
        $typePostArray = PostType::getAllPostTypeToMailer();
        if ( count($typePostArray)>0 ) {
            
            $args = array(
            'numberposts'     => 45, // тоже самое что posts_per_page
            'orderby'         => 'post_date',
            'order'           => 'DESC',
            'post_type'       => $typePostArray,
            'post_status'     => 'publish'
            );
            
            return get_posts( $args );
        
        }else{
            return array();
        }
    }
    
    public static function  getHTMLContent()
    {
       $posts = static::getListContent();
       $html = "<ul class='mailerList'>";
       
       foreach ($posts as $post) {
           $title = $post->post_title;
           $type = $post->post_type;
           $ID = $post->ID;
           
           $html .= "<li class='itemPostToMailer' onclick = \"clickToContentItem('$type', $ID)\" data-post-type='$type' data-post-id='$ID'>$title</li>";
           
       }
       
       $html .="</ul>";
       return $html;
    }
    
    
    public static function  getHTMLContent2()
    {
       $posts = static::getListContent();
       $html = "<ul class='mailerList anyClass skinClear'>"; //skinPlank
       $allCat = get_categories();
       $cats = array();
       $postsOrderByCats = array();
               
        foreach($allCat as $c){
            $cat = get_category( $c );
            $cats[$cat->cat_ID] = array( 'name' => $cat->name, 'slug' => $cat->slug );
        }
       
       foreach ($posts as $post) {
           //$title = $post->post_title;
           //$type = $post->post_type;
           $ID = $post->ID;
           $postCats = wp_get_post_categories($ID);
           
           foreach ($postCats as $key){
               $postsOrderByCats[$cats[$key]['name']][] = $post;
           }
           
           //$html .= "<li class='itemPostToMailer' onclick = \"clickToContentItem('$type', $ID)\" data-post-type='$type' data-post-id='$ID'>$title</li>";
           
       }
       //var_dump($postsOrderByCats); wp_die();
       
       foreach ($postsOrderByCats as $cat => $val){
           $html .= "<li><a href=\"#\"> $cat </a><ul>";
           foreach ($val as $post) {
                $title = $post->post_title;
                $type = $post->post_type;
                $ID = $post->ID;
                $html .= "<li class='itemPostToMailer' onclick = \"clickToContentItem('$type', $ID)\" data-post-type='$type' data-post-id='$ID'>$title</li>";
           };
           $html .= "</ul></li>";
       }
       
       
       
       //var_dump($postsOrderByCats); wp_die();
       
       $html .="</ul>";
       return $html;
    }
    
}
