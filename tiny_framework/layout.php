<?php if ( ! defined('ACCESSIBLE') ) exit('NOT ACCESSIBLE'); ?>
<?php header('Content-type: text/html; charset=utf-8'); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?php echo PROJECT_NAME ?></title>
  <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>

	<link rel="stylesheet" type="text/css" href="css/reset.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
  
</head>

<body>
	
	<div class="container">
		<?php Helper::include_partial('default/header') ?>
		<?php Helper::include_partial('default/navigation')?>
    	
		<?php echo $content; ?>
    
    <?php Helper::include_partial('default/footer')?>
	</div>
	
</body>
</html>