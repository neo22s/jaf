<?php defined('BASE_PATH') or die('No direct script access.');?>
<?php require VIEWS_PATH.'header.php';?>
	<div id="main" class="wrapper">
		<?php require VIEWS_PATH.'aside.php';?>
		<article>
			<header>
				<h2><?php echo $meta_title;?></h2>
				<p><?php echo $content;?>.</p>
				URL				
				<?=Router::get_url('test_route',array('alphaonly',45565,'alpha_and_num'))?>
			</header>
			<h3>A smaller heading</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sodales urna non odio egestas tempor. Nunc vel vehicula ante. Etiam bibendum iaculis libero, eget molestie nisl pharetra in. In semper consequat est, eu porta velit mollis nec. Curabitur posuere enim eget turpis feugiat tempor. Etiam ullamcorper lorem dapibus velit suscipit ultrices. Proin in est sed erat facilisis pharetra. Pellentesque auctor neque quis nisl lacinia id rutrum lacus venenatis.</p>	
			<h3>A smaller heading</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sodales urna non odio egestas tempor. Nunc vel vehicula ante. Etiam bibendum iaculis libero, eget molestie nisl pharetra in. In semper consequat est, eu porta velit mollis nec. Curabitur posuere enim eget turpis feugiat tempor. Etiam ullamcorper lorem dapibus velit suscipit ultrices. Proin in est sed erat facilisis pharetra. Pellentesque auctor neque quis nisl lacinia id rutrum lacus venenatis.</p>
			<footer>
			<h3>Article footfer</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sodales urna non odio egestas tempor. Nunc vel vehicula ante. Etiam bibendum iaculis libero, eget molestie nisl pharetra in. In semper consequat est, eu porta velit mollis nec. Curabitur posuere enim eget turpis feugiat tempor. Etiam ullamcorper lorem dapibus velit suscipit ultrices. Proin in est sed erat facilisis pharetra. Pellentesque auctor neque quis nisl lacinia id rutrum lacus venenatis.</p>
			</footer>	
		</article>
	</div>
<?php require VIEWS_PATH.'footer.php';?>