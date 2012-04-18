<?php if ( ! defined('ACCESSIBLE') ) exit('NOT ACCESSIBLE'); ?>
<?php

class Controller extends BaseController
{


	
	public function login($request = null)
	{
	  
	  if( $request->isPost() ){
	    
  	  $c = new Condition();
      $c->add('username', $request->username);
      $c->add('password', $request->password);
      
  	  $user = Database::getTable('user')->findOneConditionally($c);
  	  
	    if( ! $user ){
	      
  	    Flash::setFlash('notice', 'Invalid login');
  	    Flash::setFlash('username', $request->username);
  	    $this->redirectReferer($request);
  	    return false;
  	  }
  	  
  	  User::getInstance()->authenticate($user);
  	  
  	  $c = new Condition();
  	  $c->add('id', $user['id']);
      $c->add('last_login', date('Y-m-d H:i:s'));
  	  Database::getTable('user')->save($c);
  	  
  	  if( $request->hasParameter('remember')  ){
  	    setcookie(PROJECT_NAME."_remember_me", $request->email, time()+3600*24*365);
  	  }
  	  
	  }
	  
		if( User::getInstance()->isAuthenticated() ){
	    $this->redirect('default/index');
	  }	  

	}	
	
	public function register($request = null)
	{
	  
	  if( $request->isPost() ){
	    
  	  $c = new Condition();
      $c->add('name', $request->name);
      $c->add('email', $request->email);
      $c->add('username', $request->username);
      $c->add('password', $request->password);
      
  	  Database::getTable('user')->save($c);
  	  
	    $this->redirect('login/register');
  	  
	  }

	}	

	public function setPasswordRequest($request = null)
	{
	  
	  if( $request->isPost() ){
	    $user = Database::getTable('user')->findOneBy('email', $request->email);
	    if( $user ){
	      
	      $validate = createGuid();
	      
	      $c = new Condition();
	      $c->add('validate', $validate);
	      $c->add('id', $user['id']);
	      
	      Database::getTable('user')->save($c);
	    
  	    $mailer = new Mailer();
  	    
  	    $subject = __("Subjet for password request");
  	    
  	    //$message = "<p>".__("Click on the link below to set your password.")."</p><p><a href='" . Helper::getCurrentURL(true) . url_for('login/setPassword', array("q" => $validate)."'>" . Helper::getCurrentURL(true) . url_for('login/setPassword', array("q" => $validate)."<a/></p>"; 
  	    
  	    $to = $request->email;
  	    
  	    $mailer->send($subject, $message, $to);
  	    
  	    Flash::setFlash('notice', __('Set password e-mail has been sent. Please follow the link provided in the e-mail.'));
  	    
	      $this->redirectReferer($request);
	    
	    
	    }else{
	      Flash::setFlash('notice', __('Invalid e-mail, please use your Efes mail account.'));
	      
	      $this->redirectReferer($request);
	    }
	  }
  }	
	
  public function setPassword($request = null)
	{

    if( ! User::getInstance()->hasCulture() ){
      User::getInstance()->setCulture($request->lang);
    }	  
	  
	  $user = Database::getTable('user')->findOneBy('validate', $request->q);
	  
	  if( $user ){
  	  if( $request->isPost() ){
  	    $c = new Condition();
  	    $c->add('password', $request->password);
        $c->add('validate', '');
        $c->add('id', $user['id']);
        Database::getTable('user')->save($c);
        Flash::setFlash('email', $user['email']);
        Flash::setFlash('notice', __('Your passsword has been set. Enter and start!'));
        
	      $this->redirect('login/login');
	    }
	  
	  }else{
	    die("Invalid link.");
	  }
	  
	}
  
  
	public function logout($request = null)
	{
	  User::getInstance()->signOut();
	  setcookie(PROJECT_NAME."_remember_me", null, time()-3600);
	  session_destroy();
	  $this->redirect('default/index');
	}

  
}
