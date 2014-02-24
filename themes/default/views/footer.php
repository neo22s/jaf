<?php defined('BASE_PATH') or die('No direct script access.');?>
	<div id="footer-container">
		<footer class="wrapper">
			<h3>A nice footer</h3>
			<?php  $r=Router::get_url();?>
			<a href="<?=$r?>"><?=$r?></a>
			</br>
			<?php echo log::summary();?>
		</footer>
	</div>
	<?php echo View::js_tag('jquery-151','http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js',FALSE)?>
	<script>!window.jQuery && document.write(unescape('%3Cscript src="<?php echo View::template_url()?>js/libs/jquery-1.5.1.min.js"%3E%3C/script%3E'))</script>
	<?php View::js_return('footer');?>
	<!--[if lt IE 7 ]>
	<?php echo View::js_tag('ie-png-fix',View::template_url().'js/libs/dd_belatedpng.js',FALSE)?>
	<script> DD_belatedPNG.fix('img, .png_bg');</script>
	<![endif]-->
	<?php if(ANALYTICS!=FALSE):?>
	<script>
		var _gaq=[['_setAccount','<?php echo ANALYTICS;?>'],['_trackPageview']];
		(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.async=1;
		g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
		s.parentNode.insertBefore(g,s)}(document,'script'));
	</script>
	<?php endif;?>
</body>
</html>