<?php defined('BASE_PATH') or exit('Tests must be loaded from within index.php!'); ?>
<!doctype html>

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>JAF Unit Tests</title>

	<style type="text/css">
	.content { width: 42em; margin: 0 auto; font-family: sans-serif; background: #fff; font-size: 1em; }
	h1 { letter-spacing: -0.04em; }
	h1 + p { margin: 0 0 2em; color: #333; font-size: 90%; font-style: italic; }
	code { font-family: monaco, monospace; }
	.ttests{ border-collapse: collapse; width: 100%; }
		.ttests td { padding: 0.4em; text-align: left; vertical-align: top; }
		 .ttests th { width: 12em; font-weight: normal; }
		 .ttests tr:nth-child(odd) { background: #eee; }
		 .ttests td.pass { color: #191; width:15%;}
		 .ttests td.fail { color: #911; width:15%;}
	#results { padding: 0.8em; color: #fff; font-size: 1.5em; }
	#results.pass { background: #191; }
	#results.fail { background: #911; }
	</style>

</head>
<body>
<div class="content">
	<h1>JAF Unit Tests</h1>

	<p>
		The following tests have been run to determine if JAF core works properly.
		If any of the tests have failed, please check WTF you did xD.
	</p>

<?php $failed = FALSE ?>

<?php foreach ($tests_results as $class=>$results):?>
    <h1><?=str_replace('_test','',$class)?></h1>
    <table cellspacing="0" class="ttests">	
        <?php foreach ($results as $values):?>
        <tr>
        	<td width="30%"><?=str_replace('_test','',$values['function'])?></td>
        	<?php if ($values['status']):?>
        		<td class="pass" >✔ PASS</td>
        	<?php else: $failed = TRUE ?>
        		<td class="fail" >✘ FAIL</td>
        	<?php endif?>
        	<td width="15%"><?=round($values['time'],4)?>s</td>
        	<td width="40%"><?=$values['notes']?></td>
        </tr>
        <?php endforeach ?>
    </table>
<?php endforeach ?>

<?php if ($failed === TRUE): ?>
	<p id="results" class="fail">✘ Some test FAILED, JAF may not work correctly.</p>
<?php else: ?>
	<p id="results" class="pass">✔ The code passed all tests correctly.	Congratulations!	</p>
<?php endif ?>
</div>
</body>
</html>