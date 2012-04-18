<?php if ( ! defined('ACCESSIBLE') ) exit('NOT ACCESSIBLE'); ?>
<?php 
if( false == User::getInstance()->isAuthenticated() ){
  if( Context::getInstance()->getController()->hasAction('login') ){
    Context::getInstance()->getController()->redirect('login/login');
  }else{
    throw new Exception('--- This action is SECURE ---' );
  }
}
