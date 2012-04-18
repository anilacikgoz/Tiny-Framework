<?php if ( ! defined('ACCESSIBLE') ) exit('NOT ACCESSIBLE'); ?>

<style>
.pager li {float:left;margin-right:5px;}
</style>

<ul class="pager">
	<li>
		<a href="<?php print url_for('user/userList',array('page'=> $array['previous']),false) ?>"><?php print "Önceki" ?></a>
	</li>
	<?php for ($i=1; $i<=$array['last']; $i++):?>
	<li>
		<a style="<?php if (Context::getInstance()->getRequest()->page == $i) print "text-decoration:underline"?>" href="<?php print url_for('user/userList',array('page'=>$i),false) ?>"><?php print $i?></a>
	</li>
	<?php endfor;?>
	<li>
	<a href="<?php print  url_for('user/userList',array('page'=>$array['next']),false) ?>"><?php print 'Sonraki' ?></a>
	</li>
</ul>
