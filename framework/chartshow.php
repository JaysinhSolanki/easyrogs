<?php
require_once("adminsecurity.php");
$id	=	$_GET['id'];
$charts			=	$AdminDAO->getrows('system_chart',"*","pkchartid	=	'$id'");
$chart	=	$charts[0];
/*echo "<pre>";
print_r($chart);
echo "</pre>";*/
$categorynames  	 = explode(',',$chart['xaxis_staticname']);
$categoryYaxisNames  = explode(',',$chart['yaxis_staticname']);
$categoryYaxisDatas   = explode(" ",$chart['yaxis_staticdata']);
//$categorynames  =   array("Success","Danger","Warning","Info");
echo "<pre>";
print_r($categoryYaxisData);
echo "</pre>";
/****************************************************************************/
?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<div id="screenfrmdiv" style="display: block;">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-heading">
				<?php
					echo "Show Chart >>&nbsp;".$chart['chartname'];
				?>
			</div>
			<div class="panel-body">
            	<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
            </div>
		</div>
	</div>
</div>
<script>
Highcharts.chart('container', {
    chart: {
        type: 'column'
    },
    title: {
        text: '<?php echo $chart['charttitle']; ?>'
    },
    subtitle: {
        text: '<?php echo $chart['chartsubtitle']; ?>' 
    },
    xAxis: {
        categories: [
				<?php
				foreach($categorynames as $categoryname)
				{
				?>
					'<?php echo $categoryname; ?>',
				<?php
				}
				?>
            /*'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec'*/
        ],
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: '<?php echo $chart['chartname'];?>'
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [
		<?php
		
		foreach($categoryYaxisNames as $categoryYaxisName)
		{
		?>
			{
				name: '<?php echo $categoryYaxisName?>',
				data: [<?php foreach($categoryYaxisDatas as $data)
							{
					  ?>
								<?php echo $data; ?>,
					  <?php
							}
					  ?>]
			},
		<?php
			}
		?>	
	]
});
</script>