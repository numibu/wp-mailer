<?php
namespace mailer\base;

use ArrayAccess;
use ArrayIterator;
use ReflectionClass;

/* 
 * Модель является базовым классом для моделей данных.
 */

class Model extends ActivRecord implements ArrayAccess
{
    private $_activAttr;
    private $_errors;
    
    /**
     *  Возвращает массив имен атрибутов => значения.
     *  Атрибуты - все публичные и нестатические свойства класса
     * @return array
     */
    public function attributes()
    {
        $class = new ReflectionClass($this);
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
     */
    public function setAttributes($values)
    {
        if (is_array($values)) {
            $attributes = $this->attributes();
            foreach ($values as $name => $value) {
                if (array_key_exists($name, $attributes)) {
                    $this->$name = $value;
                }
            }
        }
    }
    
    /**
     * Возвращает имя класса модели.
     * @return string
     */
    public function getName()
    {
        $reflector = new ReflectionClass($this);
        return $reflector->getShortName();
    }
    
    /**
     * Проверяет наличие метода.
     * @param string $name - имя метода. 
     * @return boolean
     */
    public function hasMethod($name){
        $reflector = new ReflectionClass($this);
        return $reflector->hasMethod($name);
    }

    /**
     * Возвращает массив правил валидации данных.
     * @return array
     */
    public function rules()
    {
        return [];
    }
    
    /**
     * Метод проверяет данные по правилам валидации, которые определены в классе. 
     * @return boolean, вернет true – если ошибки валидации не обнаружены. 
     */
    public function validate()
    {
        foreach ($this->createValidators() as $validator) {
            if ($validator instanceof Validator) {
                $validator->validate();
            }
        }
        return !$this->hasErrors();
    }
    
    /**
     * Метод генерирует валидаторы, по правилам которые возвращает метод rule.
     * @return ArrayObject validators
     */
    public function createValidators()
    {
        $validators = new \ArrayObject;
        foreach ($this->rules() as $rule) {
            if ($rule instanceof Validator) {
                $validators->append($rule);
            } elseif (is_array($rule) && isset($rule[0], $rule[1])) { // attributes, validator type
                $param = isset($rule[2])? $rule[2] : null;
                $validator = Validator::createValidator($rule[1], $this, (array) $rule[0], $param); // $rule[1][] - параметр валидатору //array_slice($rule, 2)
                $validators->append($validator);
            } else {
                throw new Exception('Не правильно составлено правило валидации!');
            }
        }
        return $validators;
    }
    
    /**
     * Добавляет ошибки в массив ошибок, с именем и описанием.
     * @param string $name - имя ошибки.
     * @param string $error - описание.
     */
    public function addError($name, $error = '')
    {
        $this->_errors[$name][] = $error;
    }
    
    /**
     * Метод возвращает массив с ошибками.
     * @param type $name - имя ошибки.
     * @return array errors.
     */
    public function getErrors($name = null)
    {
        if ($name === null) {
            return $this->_errors === null ? [] : $this->_errors;
        } else {
            return isset($this->_errors[$name]) ? $this->_errors[$name] : [];
        }
    }
    
    /**
     * Возвращает итератор для обхода атрибутов в модели.
     * Обязателен для интерфейса [[\IteratorAggregate]].
     * @return ArrayIterator.
     */
    public function getIterator()
    {
        $attributes = $this->attributes();
        return new ArrayIterator($attributes);
    }
    
    /**
     * Возвращает true если есть ошибка 
     * @param string|null $name - имя ошибки.
     * @return boolean.
     */
    public function hasErrors($name = null)
    {
        return $name === null ? !empty($this->_errors) : isset($this->_errors[$name]);
    }
    
    /**
     * Устанавливает имя атрибута модели, 
     * с которым работает валидатор в данные момент.
     * @param string $attrName - имя атрибута 
     */
    public function setActivAttr($attrName)
    {
        $this->_activAttr = $attrName;
    }
    
    /**
     * Возвращает имя атрибута модели, 
     * с которым работает валидатор в данные момент.
     */
    public function getActivAttr()
    {
        return $this->_activAttr;
    }
    
    /**
     * Обязательный метод для интерфейса [[\ArrayAccess]]
     * @param mixed $offset 
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->$offset !== null;
    }
    
    /**
     * Обязательный метод для интерфейса [[\ArrayAccess]].
     * Позволяет делать так: ' $value = $model[$offset]; '.
     * @param mixed $offset 
     * @return mixed,  null - если нет элементов  
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }
    /**
     * Обязательный метод для интерфейса  [[\ArrayAccess]].
     * Позволяет делать так: ' $model[$offset] = $item; '.
     * @param integer $offset .
     * @param mixed $item - value.
     */
    public function offsetSet($offset, $item)
    {
        $this->$offset = $item;
    }
    /**
     * Обязательный метод для интерфейса [[\ArrayAccess]].
     * Для удаления элемента 'unset($model[$offset])'.
     * @param mixed $offset.
     */
    public function offsetUnset($offset)
    {
        $this->$offset = null;
    }
    
}
