<?php
namespace mailer\base;

/**
 * Трейт который возвращает хеш переданного значения + солька
 */

trait PasswordHashTrait 
{
    public function getHash($pass) 
    {
        return md5($pass + '0937202604');
    }
}