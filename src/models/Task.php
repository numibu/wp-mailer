<?php

namespace mailer\models;

class Task /* extends Model */ {

    public $id = NULL;
    public $addressee_id;
    public $mailer_id;
    public $post_id;
    public $type;
    public $time;
    public $is_send = false;
    
    static $tableName = 'wp_list_of_mailer';

    public function __construct($id = NULL, $obj = NULL) {
        if ( $obj !== NULL) { $this->init(NULL, $obj); return $this;}
        if ( $id !== NULL ) { $this->init($id, NULL); return $this;}
        if ( $id === NULL ) { 
            $this->mailer_id = get_current_user_id(); 
            return $this;
        
        }
    }

    /**
     *  Возвращает массив имен атрибутов => значения.
     *  Атрибуты - все публичные и нестатические свойства класса
     * @return array
     */
    public function attributes() {
        $class = new \ReflectionClass($this);
        $names = [];
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $names[$property->getName()] = $property->getValue($this);
            }
        }
        return $names;
    }

    /**
     * Устанавливает значения атрибутов
     * @param массив $values значения атрибутов(name => value)
     *  для присвоения модели.
     * @return boolean 
     */
    public function setAttributes($values) {
        if (is_array($values)) {
            $attributes = $this->attributes();
            foreach ($values as $name => $value) {
                if (array_key_exists($name, $attributes)) {
                    $this->$name = $value;
                }
            }
        } else {
            $this->addError('setAttributes', 'данные не array');
            return false;
        }
        return true;
    }

    /**
     * Добавляет ошибки в массив ошибок, с именем и описанием.
     * @param string $name - имя ошибки.
     * @param string $error - описание.
     */
    public function addError($name, $error = '') {
        $this->_errors[$name][] = $error;
    }

    /**
     * Метод возвращает массив с ошибками.
     * @param type $name - имя ошибки.
     * @return array errors.
     */
    public function getErrors($name = null) {
        if ($name === null) {
            return $this->_errors === null ? [] : $this->_errors;
        } else {
            return isset($this->_errors[$name]) ? $this->_errors[$name] : [];
        }
    }
    
    public static function count($userID = null){
        global $wpdb;
        $table = static::$tableName;
        if ($userID === null) {
            $wpdb->get_results("SELECT id FROM $table");
        }else{
            $wpdb->get_results("SELECT id FROM $table WHERE mailer_id = $userID AND is_send = false ");
        }
        return ($wpdb->num_rows)*1;
    }


    /**
     * 
     * @global \WPDB $wpdb
     * @param array $conf [mailer_id, addressee_id, is_send, ]
     * @return array - Array of object
     */
    public static function getAll( $conf=array() )
    {
        global $wpdb;
        $table = static::$tableName; 
        $result = array();
        $userID = get_current_user_id(); 
        
        if( count($conf) === 0 ){
            $fields = static::key_implode(', ', static::getAttrNames());
            $sql = "SELECT $fields FROM $table WHERE mailer_id = $userID AND is_send = false ORDER BY addressee_id DESC";
            $result = $wpdb->get_results($sql);
        }else{
            $fields = static::key_implode(', ', $conf);
            $sql = "SELECT $fields FROM $table WHERE mailer_id = $userID AND is_send = false ORDER BY addressee_id DESC";
            $result = $wpdb->get_results($sql);
        }
        
        
        
        array_walk($result, 'static::creatTask');
        
        return $result;
    }
    
    /**
     * Methos return array of tasks order email
     * @global \WPDB $wpdb
     * @param string $mail - mail of addressee
     * @return array Task 
     */
    public static function getTasksOfMail($mail, $conf=array())
    {
        global $wpdb;
        $table = static::$tableName; 
        $result = array();
        $userID = get_current_user_id(); 
        //$mailerUser = get_user_by('ID', $userID);
        
        $user = get_user_by('email', $mail);
        
        if( count($conf) === 0 ){
            $fields = static::key_implode(', ', static::getAttrNames());
            $sql = "SELECT $fields FROM $table WHERE addressee_id = $user->ID AND mailer_id = $userID  AND is_send = false ";
            $result = $wpdb->get_results($sql);
        }else{
            $fields = static::key_implode(', ', $conf);
            $sql = "SELECT $fields FROM $table WHERE addressee_id = $user->ID AND mailer_id = $userID  AND is_send = false ";
            $result = $wpdb->get_results($sql);
        }
        
    
        array_walk($result, 'static::creatTask');
        
        return $result;
    }

    /**
     * Сохраняет запись.
     * @see $this->insert | $this->update.
     */
    public function save() {
        $this->time = date( 'Y-m-d H:i:s');
        if ($this->id === NULL) {
            $this->insert();
        } else {
            $this->update();
        }
    }

    /**
     * 
     * @global WPDB $wpdb
     * @return integer|false. Число удаленных строк или false.
     */
    public function delete() {
        global $wpdb;
        if ($this->id !== null) {
            $query = $wpdb->delete(static::$tableName, array('id' => $this->id), array('%d'));
            return $query;
        }
        return false;
    }

    /**
     * 
     * Возвращает 0 или false.
     *  0 - запрос был выполнен корректно, но ни одна строка не была обработана.
     * 
     *  false - запрос провалился или ошибка запроса.
     * 
     *  Так как возвращается 0, если никакие поля не были обновлены (изменены),
     *  но запрос был выполнен корректно, проверку результата запроса на ошибку
     *  нужно делать с учетом типа возвращаемых данных $res === false.
     * @global  WPDB
     * @return boolean|integer. 
     */
    protected function update() {
        global $wpdb;
        $res = $wpdb->update(static::$tableName, 
                            array(  'addressee_id' => $this->addressee_id,
                                    'mailer_id' => $this->mailer_id,
                                    'post_id' => $this->post_id,
                                    'type' => $this->type,
                                    'time' => $this->time,
                                    'is_send' => $this->is_send
                                ), 
                            array('id' => $this->id), 
                            array('%d', '%d', '%d', '%s', '%s', '%d')
                            );
        return $res;
    }

    /**
     * Вставляет указанные данные в указанную таблицу.
     * Устанавливает автоинкремент новой записи в поле 'id' .
     * Возвращает:
     *  true — при успешной записи данных;
     *  false — если данные не были вставлены в таблицу.
     * 
     * @global  WPDB
     * @return boolean. 
     */
    protected function insert() {
        global $wpdb;
        $res = $wpdb->insert(static::$tableName, 
                array('addressee_id' => $this->addressee_id,
                    'mailer_id' => $this->mailer_id,
                    'post_id' => $this->post_id,
                    'type' => $this->type,
                    'time' => $this->time,
                    'is_send' => $this->is_send), 
                array('%d', '%d', '%d', '%s', '%s', '%d')
        );

        if (false !== $res) {
            $this->id = $wpdb->insert_id;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Выборка записи с определенным id/
     * @global WPDB $wpdb
     * @param integer $id_item
     * @return array|object|null.
     */
    protected function select($id_item) {
        global $wpdb;
        $id = (integer) $id_item;
        $table = static::$tableName;
        $query = $wpdb->prepare(
                "SELECT * FROM $table WHERE id = %d ", $id
        );

        return $wpdb->get_results($query);
    }

    /**
     * This method for filling a Task object
     * @param integer $id_item
     * @return \mailer\models\Task
     */
    protected function init($id_item, $objSTD = NULL) {
        $res = ( !is_object($objSTD) )? $this->select($id_item) : array($objSTD);
        if ($res !== NULL && count($res) > 0) {
            $obj = $res[0];
            $this->id = $obj->id;
            $this->addressee_id = $obj->addressee_id;
            $this->mailer_id = $obj->mailer_id;
            $this->post_id = $obj->post_id;
            $this->type = $obj->type;
            $this->time = $obj->time;
            $this->is_send = $obj->is_send;
            return $this;
        }
    }
    
    protected static function creatTask(&$val, $key)
    {
        $val = new Task(true, $val);
    }

    /**
     * Return string as "1, 2, 32, second, first", from  keys of same array.
     * @param string $separator
     * @param array $inputArray
     * @return string
     */
    protected static function key_implode($separator, $inputArray) 
    {
        return implode($separator, $inputArray);
    }
    
    /**
     *  Возвращает массив имен атрибутов. ['id', 'name', 'age']...
     *  Атрибуты - все публичные и нестатические свойства класса
     * @return array
     */
    protected static function getAttrNames() {
        $class = new \ReflectionClass('\mailer\models\Task');
        $names = [];
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $names[] = $property->getName();
            }
        }
        return $names;
    }

}
