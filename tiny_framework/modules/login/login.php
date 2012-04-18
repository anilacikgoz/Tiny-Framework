<?php if ( ! defined('ACCESSIBLE') ) exit('NOT ACCESSIBLE'); ?>

<?php if( Flash::hasFlash('username') ): ?>
<script type="text/javascript">
<!--
$(document).ready(function(){
	$('#username').val('<?php echo Flash::getFlash('username') ?>');	
})
//-->
</script>
<?php endif; ?>

<style>
<!--
#loginform{
  border: 1px solid; 
  width: 320px; 
  margin: 0 auto; 
  background-color: #F7F7F7; 
  border: 1px solid #CCCCCC; 
  padding: 30px;
  margin-top: 60px;
  
}

#loginform ul li {
  overflow: hidden;
  clear: both;
  padding: 5px 0;
}
-->
</style>

<form action="<?php echo Helper::url_for('login/login') ?>" method="post" id="loginform">
  <ul>
    <li>
      <label for="email"><?php echo 'Username'; ?></label>
      <input type="text" id="username" name="username" class="text" style="width: 300px;" value="" />
    </li>
    <li>
      <label for="email"><?php echo 'Password' ?></label>
      <input type="password" name="password" class="text" style="width: 300px;" value="" />
    </li>
    <li>
    	<input type="checkbox" id="remember" name="remember" class="text" style="width: 13px; height: 13px; margin-top: 2px; float: left; border: 0 none;" value="1" />
      <label for="remember" style="float: left; margin: 0; padding-left: 4px; "><?php echo 'Remember me' ?></label>
    </li>
  		<li>
        <label></label>
        <?php Helper::include_partial('default/notice'); ?>
        
      </li>                
    <li class="buttons">
      <input type="submit" name="submit" id="submit" value="<?php echo 'Login' ?>" />
    </li>
    
  </ul>
</form>

<script type="text/javascript">
$(document).ready(function() {
	$("#email").focus();
});
</script>