<?php namespace mailer\base;

class AjaxController {
    
    
    public static function __callStatic( $name, $arg)
    {
        if ( method_exists( __CLASS__, $name ) ) {
            //static::$name();
            add_action('wp_ajax_' . $name, AjaxController::$name() );
        }else{
            return false;
        }
    }
    
    private static function addTaskToMailer()
    {
        $error = array();
        $arguments = array(
                    'addressee_id' => array( 'filter' => FILTER_VALIDATE_INT ),
                    'type' => array( 'filter' => FILTER_SANITIZE_STRING ),
                    'post_id' => array( 'filter' => FILTER_VALIDATE_INT )
                );
        
        $request = filter_input(INPUT_POST, 'requestItemsArray', FILTER_DEFAULT);
        $requestItemsArray = json_decode($request, true);
        $inputArray = array();
        
        foreach ( $requestItemsArray as $item ){
            $inputArray[] = array_merge( $inputArray, filter_var_array( $item, $arguments ) );
        }
        
        foreach ($inputArray as $rawTask){
            $task = new \mailer\models\Task();
            if ($rawTask === false || $rawTask === NULL) {
                $task->addError('декодирование фходных данных', 'данные не распознаны');
                $error[] = $task->getErrors();
            } else {
                $task->setAttributes($rawTask);
                $task->save();
            }
        }
        
        $return = array('error'=>$error);
        wp_send_json($return);
    }
    
    private static function addCustomTaskToMailer()
    {
        $arguments = array(
                    'addressee_name' => array( 'filter' => FILTER_SANITIZE_STRING ),
                    'addressee_mail' => array( 'filter' => FILTER_VALIDATE_EMAIL ),
                    'type' => array( 'filter' => FILTER_SANITIZE_STRING ),
                    'post_id' => array( 'filter' => FILTER_VALIDATE_INT )
                );
        
        $request = filter_input(INPUT_POST, 'requestCustomItemsArray', FILTER_DEFAULT);
        $requestItemsArray = json_decode($request, true);
        $inputArray = array();
        
        foreach ( $requestItemsArray as $item ){
            $inputArray[] = array_merge( $inputArray, filter_var_array( $item, $arguments ) );
        }
        
        foreach ($inputArray as $rawTask){
            $task = new \mailer\models\CustomTask();
            if ($rawTask === false || $rawTask === NULL) {
                $task->addError('декодирование фходных данных', 'данные не распознаны');
                $error[] = $task->getErrors();
            } else {
                $task->setAttributes($rawTask);
                $task->save();
            }
        }
        $return = array('error'=>$error);
        wp_send_json($return);
    }
    
    private static function getTaskGrid(){
        $taskGrid = \mailer\models\TaskGrid::createTaskGrid();
        $return = array (
            'error' => array(),
            'body'  => array(
                'taskGrid' => $taskGrid
            )
        );
        wp_send_json($return);
    }
    
    private static function deleteTask(){
        $error = array();
        
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        
        if ($type === 'custom') {$task = new \mailer\models\CustomTask($id);}
        if ($type === 'register') {$task = new \mailer\models\Task($id);}
        
        $res = $task->delete();
        
        unset($task);
        if (!$res) {$error[] = "Ошибка во время удаления $id. код: $res";}
        
        $return = array('error'=>$error,
                            'task'=>$task,
                                'res'=>$res);;
        wp_send_json($return);
    }
    
    private static function send(){
        ini_set("display_errors",1);
        error_reporting(E_ALL);
        $error = array();
        
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);// user type (register | custom)
        $mail = filter_input(INPUT_POST, 'mail', FILTER_VALIDATE_EMAIL);//user MAIL
        
        if ($type === 'custom') {
            $tasksArray = \mailer\models\CustomTask::getTasksOfMail($mail);
        }
        
        if ($type === 'register') {
            $tasksArray = \mailer\models\Task::getTasksOfMail($mail);    
        }
        
        $userMailer = NULL;
        $content = '';
        
        if ( count($tasksArray)>0 ) { 
            
            $userMailer = get_user_by('ID', $tasksArray[0]->mailer_id);
            
            $addresseeName = ($type === 'custom')? $tasksArray[0]->addressee_name : get_user_by('ID', $tasksArray[0]->addressee_id)->data->user_nicename;
            $addresseeMail = ($type === 'custom')? $tasksArray[0]->addressee_mail : get_user_by('ID', $tasksArray[0]->addressee_id)->data->user_email;
            
            foreach ($tasksArray as $task){
                $post = get_post($task->post_id);
                $content .= '<br>' . $post->post_title;
            }
            
        }
        
        $AddresseeUser = new \stdClass();
        $AddresseeUser->email = $addresseeMail;
        $AddresseeUser->name = $addresseeName;
                
        $result = new RMailer($userMailer, $AddresseeUser, $content);
        
        if(!$result){
            $error[] = $result;
        }else{
            foreach ($tasksArray as $task){
                $task->is_send = true;
                $task->save();
            }
        }
 
        
        $return = array('error'=>$error,
                            'tasksArray'=>$tasksArray,
                            'mailer'=>$userMailer,
                            'result'=>$result
                        );
        
        wp_send_json($return);
    }
    
}

