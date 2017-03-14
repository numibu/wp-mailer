<?php
namespace mailer\base;

/**
 * Класс для проверки свойств модели, 
 * согласно правилам валидации которые прописаны в самой модели.
 */
class Validator
{
    use \mailer\base\PasswordHashTrait;
    
    /**
     * @var Array $validatorsType - список названий правил валидации, 
     * для сопоставления с методами валидации, которые есть в классе Validator.
     */
    public static $validatorsType = [
        'email' => 'isEmail',
        'required' => 'isRequired',
        'fileSize' => 'fileSize',
        'txtExt' => 'isTextExt',
        'max' => 'maxLength',
        'min' => 'minLength',
        'url' => 'urlValid',
        'trim' => 'trim',
        'compare' => 'compare',
        'hash' => 'hash'
    ];
    
    /**
     * Список атрибутов для проверки этим типом валидатора.
     * @var Array 
     */
    public $attributes = null;
    
    /**
     * Имя метода для проверки атрибутов.
     * @var String 
     */
    public $validatorMethodName = null;
    
    /**
     * Свойство для метода, который будет проверять на соответствие правилам.
     * Например длина имени не больше 12, 12 - и есть то свойство.
     * @var Mix 
     */
    public $property;
    
    /**
     * Описан ли метод валидации в модели?
     * По умолчанию - нет.
     * @var boolean 
     */
    public $isModelValidator = false;
    
    /**
     * Модель, с которой работает валидатор.
     * @var Model
     */
    public $model = null;
    
    /**
     * Конструктор валидатора. 
     * @param mix $model
     * @param array $attributeNameы
     */
    public function __construct($model, $attributeNames) {
       $this->model = $model;
       $this->attributes = $attributeNames;
    }

    /**
     * Генерирует валидатор.
     * @param string $type - имя валидатора.
     * @param mix $model - экземпляр модели
     * @param array $attributes - имена атрибутов для проверки 
     * @param mix|null $property
     * @return Validator
     */
    public static function createValidator($type, $model, $attributes, $property = null)
    {
        $attrNames = self::valueAtributeInModel($model, $attributes);
        
        $validator = new static($model, $attrNames);
        $validator->property = $property;
        if ($model->hasMethod($type)) {
            
            $validator->validatorMethodName = $type;
            $validator->isModelValidator = true;
            
        }elseif (isset(static::$validatorsType[$type])) {
            
            $validator->validatorMethodName = static::$validatorsType[$type];
        }
      
        return $validator;
    }
    
    /**
     * Вызывает методы валидации, если валидатор бросил исключение, 
     * то метод добавит ошибку с именем проверяемого атрибута в модель.
     */
    public function validate()
    {
        $funcName = $this->validatorMethodName;
        $attrArray = $this->attributes;
        if($this->isModelValidator){
            foreach ($attrArray as $key => $value) {
                try {
                    $this->isValidatorMethod($funcName);
                    $this->model->setActivAttr($attrArray[$key]);
                    $this->model->$funcName($this->model[$value]);
                } catch (\Exception $e) {
                    $this->addError($value, $e);
                }
            }
        }else{
            foreach ($attrArray as $key => $value) {
                try {
                    $this->$funcName($this->model[$value]);
                } catch (\Exception $e) {
                    $this->addError($value, $e);
                }
            }
        }
    }

    /**
     * Проверяет наличие атрибутов в модели. 
     * @param mix $model
     * @param string $attributes
     * @return array - массив имен атрибутов для проверки.
     */
    private static function valueAtributeInModel($model, $attributes)
    {
        $result = [];
        foreach ($attributes as $name) {
            if (!isset($model[$name])) {
                $model->addError($name, "Атрибут $name не найдено в модели: " . $model->getName());
                break;
            }
            $result[] = $name;
        }
        return $result;
    }

    /**
     * Проверяет наличие метода для валидации в модели.
     * @param string $validatorName - имя метода.
     * @return boolean, true - усли есть у модели.
     * @throws \Exception
     */
    private function isValidatorMethod($validatorName)
    {
        if (method_exists($this->model, $validatorName)){
            return true;
        }
        throw new \Exception('Not implemented validator');
    }
    
    /**
     * Вызывает метод добавления ошибок в модели. 
     * @param string $key.
     * @param event $e.
     */
    private function addError($key, $e)
    {
        $this->model->addError($key, $e->getMessage());
    }
    
    /**
     * Валидатор для строки с e-mail 
     * @param string $value - e-mail 
     * @return number|false type
     */
    private function isEmail($value)
    {
        $result = preg_match('/^(?:(?:[\w`~!#$%^&*\-=+;:{}\'|,?\/]+'
                . '(?:(?:\.(?:"(?:\\?[\w`~!#$%^&*\-=+;:{}\'|,?\/\.('
                . ')<>\[\] @]|\\"|\\\\)*"|[\w`~!#$%^&*\-=+;:{}\'|,?\/]+))'
                . '*\.[\w`~!#$%^&*\-=+;:{}\'|,?\/]+)?)|(?:"(?:\\?[\w`~!#$%^'
                . '&*\-=+;:{}\'|,?\/\.()<>\[\] @]|\\"|\\\\)+"))@'
                . '(?:[a-zA-Z\d\-]+(?:\.[a-zA-Z\d\-]+)*|\[\d{1,3}\.\d{1,3}'
                . '\.\d{1,3}\.\d{1,3}\])$/', $value);
        
         if ($result) {
            return true; 
        }
        throw new \Exception("Значение $value не cоответствует  e-mail");
    }
    
    /**
     * Правило которое проверяет есть ли значения для атрибута.  
     * @param mix $value
     * @return boolean
     * @throws \Exception
     */
    private function isRequired($value)
    {
        if (!empty($value)) {
            return true;
        }
        throw new \Exception('Поле не заполнено!');
    }
    
    /**
     * Валидатор размера файла.
     * @param int $value
     * @return boolean
     * @throws Exception
     */
    private function fileSize($value)
    {
        if ($value <= 102400) {
            return true;
        }
        throw new \Exception('Файл больше 100 Кб!');
    }
    
    /**
     * Валидатор расширения принятого файла.   
     * @param string $fileName
     * @return boolean
     * @throws \Exception
     */
    private function isTextExt($fileName)
    {
        $str = strrchr($fileName, '.');
        if ('txt' === substr($str, 1)) {
            return true;
        }
        throw new \Exception("Расширение файла не .txt");
    }
    
    /**
     * Проверка на нарушение максимального лимита.
     * @param float $value
     * @return boolean
     * @throws \Exception
     */
    private function maxLength($value)
    {
        if (iconv_strlen($value) <= $this->property) {
           return true; 
        }
        throw new \Exception("Значение $value длиннее допустимых $this->property символов!"); 
    }
    
    /**
     * Проверка на нарушение минимального лимита.
     * @param float $value
     * @return boolean
     * @throws \Exception
     */
    private function minLength($value)
    {
        if (iconv_strlen($value) >= $this->property) {
           return true; 
        }
        throw new \Exception("Значение $value длинее допустимых $this->property символов!");
    }
    
    /**
     * Проверка на корректность url.
     * @param string $value
     * @return number|false type
     */
    private function urlValid($value)
    {
        if ($value === '' || $value === NULL) {
            return true;
        }
        
        $result = preg_match('/([--:\w?@%&+~#=]*\.[a-z]{2,4}\/{0,2})((?:[?&]'
                            . '(?:\w+)=(?:\w+))+|[--:\w?@%&+~#=]+)?/', $value);
        if ($result) {
            return true; 
        }
        throw new \Exception("Значение $value не cоответствует  URL");
    }
    
    /**
     * Валидатор – хелпер, обрезает пробелы.    
     * @param string $value.
     * @return string.
     */
    private function trim(&$value)
    {
        return trim($value);
    }
    
    /**
     * Валидатор – сравнения двух строк.     
     * @return boolean
     */
    private function compare()
    {
        if (count($this->attributes) === 2) {
            $v1 = $this->model[$this->attributes[0]];
            $v2 = $this->model[$this->attributes[1]];
            if (strcmp($v1, $v2)) {
                return true;
            }else{
                throw new \Exception("Атрибуты $this->attributes[0] и "
                        . " $this->attributes[1] не равны");
            }
        }else{
            throw new \Exception("Не удалось проверить на сравнения атрибуты!");
        }    
    }
    
    /**
     * Метод хеширует пароль       
     * @param string $password
     */
    private function hash($password)
    {
        $this->model->password = $this->getHash($password);
    }
}
