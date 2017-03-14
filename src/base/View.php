<?php
namespace mailer\base;

/* 
 * Класс для работы с отображением страниц.  
 */

class View
{
    /**
     * Слой для видов административной части
     * @var string 
     */
    const _ADMIN = 'admin';
    /**
     * Слой для видов публичной части
     * @var type 
     */
    const _FRONT = 'front';
    
    /**
     * Метод для формирования ответа.  
     * @param string $filename - имя файла отображения. 
     * @param array $params - массив параметров (для вывода в отображении).
     * @param string (View::_ADMIN|View::_FRONT) $layer
     * @return string|boolean
     */
    public static function render($filename, $params = [], $layer = View::_FRONT)
    {
        $path = MAILER_DIR .'src' . DS . 'views' . DS . $layer;
        $path .= DS . $filename . '.php';
        
        if(file_exists($path)){
            ob_start();
            ob_implicit_flush(false);
            extract($params, EXTR_OVERWRITE);
            require($path);
            echo ob_get_clean();
            //return ob_get_clean();
        } else { 
            return false;
        }
    }

}
