<?=View::js_tag('sorttable',View::template_url().'js/libs/sorttable.js',TRUE)?>
<style>
	.section {display:none;}
	.pointer {cursor:pointer;}
	.log_content {margin-left:25px;}
	.log_content h3{font-size:16px;margin:5px 0 10px 0;}
    #one-column-emphasis{font-family:"Lucida Sans Unicode", "Lucida Grande", Sans-Serif;font-size:12px;width:100%;text-align:left;border-collapse:collapse;}
    #one-column-emphasis th{font-size:14px;font-weight:400;color:#039;padding:12px 15px;}
    #one-column-emphasis td{color:#669;border-top:1px solid #e8edff;padding:10px 15px;}
    .oce-first{background:#d0dafd;border-right:10px solid transparent;border-left:10px solid transparent;}
    #one-column-emphasis tr:hover td{color:#339;background:#eff2ff;}
</style>
<script>
function show(id){element=document.getElementById(id);if(element!=null){element.style.display='block';}}
function hide(id){element=document.getElementById(id);if(element!=null){element.style.display='none';}}
function openClose(id){element=document.getElementById(id);if(element!=null){if(element.style.display=='block'){hide(id);}else{show(id);}}}
</script>

<div class="log_content">

<h2>Profiler & Log</h2>

<?php 
$error=FALSE;
foreach($log_array as $l)
{
    if ($l['name'] == 'log::error_handler')
    {
        $error=TRUE;
        break;
    }
}

if($error==TRUE):
?>
<h3 style="color:red;" class="pointer" onclick="openClose('error_section');">Errors</h3>
<div class="section" id="error_section">
<table id="one-column-emphasis" class="sortable" summary="JAF Log">
    <colgroup>
    	<col class="oce-first" />
    </colgroup>
    <thead>
    	<tr>
        	<th scope="col">#</th>
            <th scope="col">Time Stamp</th>
            <th scope="col">Time Used Secs</th>
            <th scope="col">Memory Stamp MB</th>                       
            <th scope="col">Memory Used MB</th>
            <th scope="col">Message</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i=0;
        foreach($log_array as $l):
            if ($l['name'] == 'log::error_handler'): ?>		
            <tr>
                <td><?=$i?></td>
                <td><?=date('d-m-Y - H:i:s',$l['current_time'])?></td>
                <td><?=round($l['used_time'],4)?></td>
                <td><?=round($l['current_memory'],4)?></td>
                <td><?=round($l['used_memory'],4)?></td>
                <td><?=$l['message']?></td>
            </tr>
            <?php
            $i++;
            endif;
       endforeach;?>
    </tbody>
</table>
</div>
<?endif?>

<h3 class="pointer" onclick="openClose('functions_section');">Functions by order</h3>
<div class="section" id="functions_section">
<table id="one-column-emphasis" class="sortable" summary="JAF Log">
    <colgroup>
    	<col class="oce-first" />
    </colgroup>
    <thead>
    	<tr>
        	<th scope="col">#</th>
            <th scope="col">Function</th>
            <th scope="col">Time Stamp</th>
            <th scope="col">Time Used Secs</th>
            <th scope="col">Memory Stamp MB</th>                       
            <th scope="col">Memory Used MB</th>
            <th scope="col">Message</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $slowest = 0; //slowest log executed
        $expensive =0; //log that used more memory
        $grouped = array();//group stats
        $i=1;
        foreach($log_array as $l)
        {
            //updating the group
            $grouped[$l['name']]=array(  'repetitions' =>$grouped[$l['name']]['repetitions']+1,
                                         'time_usage'  =>$grouped[$l['name']]['time_usage']+$l['used_time'],
                                         'memory_usage'=>$grouped[$l['name']]['memory_usage']+$l['used_memory']
                                        );
            
            if ($slowest['used_time']<$l['used_time'] && $i>1)
            {
                $slowest = $l;
            }
            
            if ($expensive['used_memory']<$l['used_memory'] && $i>1)
            {
                $expensive = $l;
            }
            ?>		
            <tr>
                <td><?=$i?></td>
                <td><?=$l['name']?></td>
                <td><?=date('d-m-Y - H:i:s',$l['current_time'])?></td>
                <td><?=round($l['used_time'],4)?></td>
                <td><?=round($l['current_memory'],4)?></td>
                <td><?=round($l['used_memory'],4)?></td>
                <td><?=$l['message']?></td>
            </tr>
            <?php $i++;}?>
    </tbody>
</table>
</div>

<h3 class="pointer" onclick="openClose('grouped_section');">Grouped by Functions</h3>
<div class="section" id="grouped_section">
<table id="one-column-emphasis" class="sortable" summary="JAF Log">
    <colgroup>
    	<col class="oce-first" />
    </colgroup>
    <thead>
    	<tr>
    		<th scope="col">#</th>
            <th scope="col">Function Grouped</th>
            <th scope="col">Repetitions</th>
            <th scope="col">Time Used Secs</th>
            <th scope="col">Memory Used MB</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $i=1;
        foreach($grouped as $g=>$v):?>
        <tr>
            <td><?=$i?></td>
            <td><?=$g?></td>
            <td><?=round($v['repetitions'],4)?></td>
            <td><?=round($v['time_usage'],4)?></td>
            <td><?=round($v['memory_usage'],4)?></td>
        </tr>
        <?php $i++; endforeach?>
    </tbody>
</table>
</div>

<h3 class="pointer" onclick="openClose('db_section');">Database Usage</h3>
<div class="section" id="db_section">
<table id="one-column-emphasis" class="sortable" summary="JAF Log">
    <colgroup>
    	<col class="oce-first" />
    </colgroup>
    <thead>
    	<tr>
        	<th scope="col">#</th>
        	<th scope="col">Function</th>
            <th scope="col">Time Stamp</th>
            <th scope="col">Time Used Secs</th>
            <th scope="col">Memory Stamp MB</th>                       
            <th scope="col">Memory Used MB</th>
            <th scope="col">Message</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i=0;
        foreach($log_array as $l):
            if (strpos($l['name'],'DB::')!==FALSE): ?>		
            <tr>
                <td><?=$i?></td>
                <td><?=$l['name']?></td>
                <td><?=date('d-m-Y - H:i:s',$l['current_time'])?></td>
                <td><?=round($l['used_time'],4)?></td>
                <td><?=round($l['current_memory'],4)?></td>
                <td><?=round($l['used_memory'],4)?></td>
                <td><?=$l['message']?></td>
            </tr>
            <?php
            $i++;
            endif;
       endforeach;?>
    </tbody>
</table>
</div>

<h3 class="pointer" onclick="openClose('includes_section');">Included files</h3>
<div class="section" id="includes_section">
<table id="one-column-emphasis" class="sortable" summary="JAF Log">
    <colgroup>
    	<col class="oce-first" />
    </colgroup>
    <thead>
    	<tr>
    		<th scope="col">#</th>
        	<th scope="col">File</th>
        	<th scope="col">Path</th>
        	<th scope="col">Fullname</th>
    	</tr>
    </thead>
    <tbody>
		<?php 
		$i=1;
		$included_files = get_included_files();
        foreach ($included_files as $filename):?>
            <tr>
            	<td><?=$i?></td>
            	<td><?=basename($filename);?></td>
            	<td><?=dirname($filename);?></td>
            	<td><?=$filename;?></td>
            </tr>
		<?php $i++;  endforeach;?>
	</tbody>
</table>
</div>

<h3 class="pointer" onclick="openClose('stats_section');">Summary</h3>
<div class="section" id="stats_section">
<table id="one-column-emphasis" summary="JAF Log">
    <colgroup>
    	<col class="oce-first" />
    </colgroup>
    <tbody>
        <tr>
            <td>Slowest</td>
            <td><?=$expensive['name']?></td>
            <td><?=date('d-m-Y - H:i:s',$expensive['current_time'])?></td>
            <td><?=round($expensive['used_time'],4)?></td>
            <td><?=round($expensive['current_memory'],4)?></td>
            <td><?=round($expensive['used_memory'],4)?></td>
            <td><?=$expensive['message']?></td>
        </tr>
        <tr>
            <td>Top Mem</td>
            <td><?=$expensive['name']?></td>
            <td><?=date('d-m-Y - H:i:s',$expensive['current_time'])?></td>
            <td><?=round($expensive['used_time'],4)?></td>
            <td><?=round($expensive['current_memory'],4)?></td>
            <td><?=round($expensive['used_memory'],4)?></td>
            <td><?=$expensive['message']?></td>
        </tr>
        <tr><td>Includes</td><td colspan=6><?=count($included_files)?></td></tr>
        <tr><td>Session</td><td colspan=6><?=print_r($_SESSION,1)?></td></tr>
        <tr><td>Cookies</td><td colspan=6><?=print_r(H::$_COOKIE,1)?></td></tr>
        <tr><td>Post</td><td colspan=6><?=print_r(H::$_POST,1)?></td></tr>
        <tr><td>Get</td><td colspan=6><?=print_r(H::$_GET,1)?></td></tr>
        <tr><td>Summary</td><td colspan=6><?=log::summary()?></td></tr>
    </tbody>
</table>

</div>