<?php
namespace backend\controllers;

use Yii;
// use yii\web\Controller;
use yii\rest\ActiveController;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//echo 'hello';



class UserController extends ActiveController
{
    public $modelClass = 'common\models\Users';
    //public $modelClass = 'backend\models\User';
    
    public function actionUsers(){
        switch($_GET['model'])
        {
            case 'posts':
                $models = Post::model()->findAll();                
                break;
            default:
                $this->_sendResponse(501, sprintf('Error: Mode <b>list</b> is not implemented for model <b>%s</b>', $_GET['model']));
                Yii::app()->end();
                //exit; 
        }
        if(is_null($models)){
            $this->_sendResponse(200, sprintf('No items where found for model <b>%s</b>', $_GET['model']));
        } else{
            $rows = array();
            foreach($models as $model)
                $rows[] = $model->attributes;
            
            $this->_sendResponse(200, CJSON::encode($rows));
        }
    }
    
    /**
     * Sends the API response 
     * 
     * @param int $status 
     * @param string $body 
     * @param string $content_type 
     * @access private
     * @return void
     */
    private function _sendResponse($status = 200, $body = '', $content_type = 'text/html')
    {
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        // set the status
        header($status_header);
        // set the content type
        header('Content-type: ' . $content_type);

        // pages with body are easy
        if($body != '')
        {
            // send the body
            echo $body;
            exit;
        }
        // we need to create the body if none is passed
        else
        {
            // create some body messages
            $message = '';

            // this is purely optional, but makes the pages a little nicer to read
            // for your users.  Since you won't likely send a lot of different status codes,
            // this also shouldn't be too ponderous to maintain
            switch($status)
            {
                case 401:
                    $message = 'You must be authorized to view this page.';
                    break;
                case 404:
                    $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                    break;
                case 500:
                    $message = 'The server encountered an error processing your request.';
                    break;
                case 501:
                    $message = 'The requested method is not implemented.';
                    break;
            }

            // servers don't always have a signature turned on (this is an apache directive "ServerSignature On")
            $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];

            // this should be templatized in a real-world solution
            $body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
                        <html>
                            <head>
                                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                                <title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
                            </head>
                            <body>
                                <h1>' . $this->_getStatusCodeMessage($status) . '</h1>
                                <p>' . $message . '</p>
                                <hr />
                                <address>' . $signature . '</address>
                            </body>
                        </html>';

            echo $body;
            exit;
        }
    } // }}}            
}


