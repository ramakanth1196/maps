<?php
	include('db.php');
	if(isset($_GET['page']))
		$page=$_GET['page'];
	else
		$page=1;
	$date = new DateTime();
	$main_arr=array();
	$ref_arr=array();
	$snip_arr=array();
	date_default_timezone_set('Asia/Calcutta');
	if(isset($_GET['db']))
		$db1=$_GET['db'];
	else
		$db1='moneycontrol';
	if($db1=="economictimes")
		$perpage=10;
	else
		$perpage=15;
	$start=$page*$perpage -$perpage;
	//print $db1;
	$sql="SELECT * FROM ".$db1."  LIMIT ".$start." , ".$perpage ;
	echo "<h4 align='center'>PageNo:".$page."</h4>";
	$result=mysql_query($sql,$conn);
	$count=0;
	while($row=mysql_fetch_array($result))
	{		
		$row_arr=array();
		$str=$row['title'];
		date_default_timezone_set('Asia/Calcutta');
		$curtime=date('Y-m-d H:i:s');
		
		$clicks=$row['clicks'];
		if($row['order']<30)
			$weight=((30-$row['order']))+(int)((10*log(($clicks+1),2)));
		else
			if($clicks==0)
				$weight = rand(10,30);
			else
				$weight=(int)((10*log(($clicks+1),2)));
		
		str_replace(',','\\,',$str);
		str_replace('[','\\[',$str);
		str_replace(']','\\]',$str);
		str_replace('<','\\<',$str); 
		str_replace('>','\\>',$str);
		$str=addslashes($str);
		$gbl="Global";
		$gbl=addslashes($gbl);
		$str1="'".$str."'";
		$gbl="'".$gbl."'";
		array_push($row_arr,$str1);
		array_push($row_arr,$gbl);
		array_push($row_arr,$weight);
		array_push($main_arr,$row_arr);
		$snippet=$row['snippet'];
		$snippet=addslashes($snippet);
		$snippet="'".$snippet."'";
		$href=$row['url'];
		$href=addslashes($href);
		$href="'".$href."'";
		array_push($ref_arr,$href);
		array_push($snip_arr,$snippet);
		$count +=1;
		if($count>$perpage)
			break;
	}
?>

<html>
<head>
<style>
#tooltip {
    display: none;
    position: absolute;
	width:300px;
    background-color: #FFFFFF;
    padding-left: 5px;
	position:absolute;
	z-index:2000;
}
#tooltipTopLine {
    font-weight: bold;
}
#tooltipBody{

}
#tooltipBottomLine {
	font-weight: bold;
    
}
</style>
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/style.css" rel="stylesheet" media="screen">
<script src="js/jquery-1.7.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap.js"></script>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
	google.load('visualization', '1', {packages: ['treemap']});
</script>
	
<script type="text/javascript">
	var snipList=[],refList=[];
	<?php
		foreach ($ref_arr as $j) {
			echo "refList.push([".$j."]);";
		}
	?>
		

	function loadXMLDoc(url)
	{
		var xmlhttp;
		if (window.XMLHttpRequest)
		{// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else
		{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function()
		{
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
				//alert(xmlhttp.responseText);
				location.reload();
				return xmlhttp.responseText;
			}
		}

		xmlhttp.open("post","clickupdate.php?url="+url,false);
		xmlhttp.send();
		location.reload();
		return xmlhttp.responseText;
	}
		
	function drawVisualization() {
	var arr=[
			['Location', 'Parent', 'Market trade volume (size)'],
			['Global',    null,                 0]
		 
			];
	<?php
		foreach ($main_arr as $i) {
			echo "arr.push([" . implode(", ", $i) . "]);";
		}
	?>
	 
		
	<?php
		foreach ($snip_arr as $i) {
			echo "snipList.push([".$i."]);";
		}
	?> 
	// Create and populate the data table.
	var data = google.visualization.arrayToDataTable(arr);
	// Create and draw the visualization.
	var treemap = new google.visualization.TreeMap(document.getElementById('visualization'));
	treemap.draw(data, {
		minColor: '#0b610b',
		midColor: '#d00',
		maxColor: '#1e6597',
		headerHeight: 25,
		fontFamily:'Verdana',
		fontColor: 'white',
		fontSize:20,
		opacity:1,
		showScale: true});
		google.visualization.events.addListener(treemap, 'select', function () {
		var selection=treemap.getSelection();
		var item=selection[0];
		var rowNo=item.row;				
		var ref= refList[item.row-1];
		loadXMLDoc(ref);
		//document.write(ref);
		ref="http://"+ref;
		window.open(ref);
		  
		});
	google.visualization.events.addListener(treemap, 'onmouseover', function(e)
	{
		var idx=e.row;
		var content1=snipList[idx-1];
	});
	google.visualization.events.addListener(treemap, 'onmouseout', function (e) {
        // hide the tooltip
		$('#tooltip').hide();
	});
  // Save the selected row index in case getSelection is called.
  // Trigger a select event.
}
      
	function selectHandler(e) {
		var provider = data.getValue(e.row, 1);
		var totalService = data.getValue(e.row, 2);
	}


	google.setOnLoadCallback(drawVisualization);
</script>	


<head>
<style>
a.ex1:hover,a.ex1:active,a.ex1:visited,a.ex1:link {color:red;}
</style>
</head>
	
</head>
<body>
  <select onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
    <option value="">Select...</option>
    <option value="http://localhost/maps/new.php?db=economictimes">economictimes</option>
    <option value="http://localhost/maps/new.php?db=moneycontrol">moneycontrol</option>
</select>
<br />
<br />
<!-- Modal -->
<?php
if($page>1)
{
	print "<div style = 'text-align:left; float:left;'>";
	print "<a class='ex1'  href='new.php?db=".$db1."&page=".($page-1)."'>&#171;Previous</a>";
	print "</div>";
}
if($page<14)
{
	print "<div style = 'text-align:right; float:right;'>";
	print "<a class='ex1'  href='new.php?db=".$db1."&page=".($page+1)."'>Next&#187;</a>"; 
	print "</div>";
}
?>
<br />
<div id="tooltip">
    <span id="tooltipTopLine"></span><br />
	<span id="tooltipBody"></span><br />
    <span id="tooltipBottomLine"></span>
</div>

	 <div id="visualization" style="width: 100%; height: 100%;"></div>


<body>
</html>