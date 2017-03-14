<?php namespace mailer\models;

use mailer\models\Task as Task;
use mailer\models\CustomTask as CustomTask;

class TaskGrid {
    
    public static function createTaskGrid()
    {
        $taskArray = Task::getAll();
        $html = '<table class="taskList" style=" width: 100%;">';
            $html .= '<tr>';
                $html .= '<th>Адресат</th>';
                $html .= '<th>Адресант (роль)</th>';
                $html .= '<th>Заголовок публикации</th>';
                $html .= '<th></th>';
                $html .= '<th></th>';
            $html .= '</tr>';
            
        $indexRow = 0;
        
        $html .= static::taskGrid($indexRow);
        $html .= static::customTaskGrid($indexRow);
        
        return $html;
    }
    
    
    public static function countTask(){
        $id = get_current_user_id();
        return static::countRegisterUserTask($id) 
                                            + static::countCustomUserTask($id);
    }
    
    protected static function taskGrid(&$indexRow)
    {
        $twins = array();
        $taskArray = Task::getAll();
        $html = '';
        foreach ($taskArray as $task){   
            $userAddressee = get_user_by( 'ID', $task->addressee_id );
            $mailer = get_user_by( 'ID', $task->mailer_id );
            $post = get_post( $task->post_id );
            
            $className = ($indexRow++%2)? 'odd-tr' : 'even-tr';
            $html .= '<tr class="sendRow ' . $className . '">';
            $html .= "<td>" . $userAddressee->data->user_login . "</td>";
            $html .= "<td>" . $mailer->data->user_login 
                    . " (role: " . $mailer->roles[0] .")</td>";
            $html .= "<td>$post->post_title</td>";
            $html .= "<td class='deleteCell'  onclick=\"deleteTask('register', $task->id)\">удалить</td>";
            
            if (!in_array($task->addressee_id, $twins)) {
                $twins[] = $task->addressee_id; 
                $html .= "<td class='sendCell' onclick=\"sendCell('register', '$userAddressee->user_email')\">отправить</td>";
            }else{
                $html .= "<td class='sendCell'> </td>";
            }
            
            $html .= '</tr>';
  
        }
        return $html;
    }
    
    protected static function customTaskGrid(&$indexRow)
    {
        $twins = array();
        $taskArray = CustomTask::getAll();
        $html = '';
        foreach ($taskArray as $task){
            $userAddresseeName = $task->addressee_name;
            $mailer = get_user_by( 'ID', $task->mailer_id );
            $post = get_post( $task->post_id );
            
            $className = ($indexRow++%2)? 'odd-tr' : 'even-tr';
            $html .= '<tr class="sendRow ' . $className . '">';
            $html .= "<td>" . $userAddresseeName . "</td>";
            $html .= "<td>" . $mailer->data->user_login 
                    . " (role: " . $mailer->roles[0] .")</td>";
            $html .= "<td>$post->post_title</td>";
            $html .= "<td class='deleteCell'  onclick=\"deleteTask('custom', $task->id)\">удалить</td>";
            
            if (!in_array($task->addressee_name, $twins)) {
                $twins[] = $task->addressee_name; 
                $html .= "<td class='sendCell' onclick=\"sendCell('custom', '$task->addressee_mail')\">отправить</td>";
            }else{
                $html .= "<td class='sendCell'> </td>";
            }
            
            $html .= '</tr>';
  
        }
        return  $html;
    }
    
    protected static function countRegisterUserTask($userID)
    {
        return Task::count($userID);
    }
            
    protected static function countCustomUserTask($userID)
    {
        return CustomTask::count($userID);
    }
}

