<h2>Select Tours</h2> 
<hr> 
<?php 
$mysqli = connect();
echo '<form action="index.php?page=1" method="post">'; 
echo '<select name="countryid" class="col-sm-3 col-md-3 col-lg-3">'; 
$res=$mysqli->query("SELECT * FROM countries ORDER BY country");
echo '<option value="0">Select country...</option>'; 
while ($row=mysqli_fetch_array($res, MYSQLI_NUM)) 
{ 
	echo '<option value="'.$row[0].'">'.$row[1].' </option>'; 
} 
mysqli_free_result($res); 
echo '<input type="submit" name="selcountry" value="Select Country" class="btn btn-xs btn-primary">'; 
echo '</select>';
if(isset($_POST['selcountry'])) 
{ 
	echo '<br/>'; 
	$countryid=$_POST['countryid']; 
	if($countryid == 0) 
		exit(); 
	$res=$mysqli->query("SELECT * FROM cities where countryid=".$countryid." ORDER BY city"); 
	echo '<select name="cityid" class="col-sm-3 col-md-3 col-lg-3">'; 
	echo '<option value="0">Select city...</option>'; 
	while ($row=mysqli_fetch_array($res, MYSQLI_NUM)) 
	{  
		echo '<option value="'.$row[0].'">'.$row[1].'</option>'; 
	} 
	mysqli_free_result($res); 
	echo '</select>'; 
	echo '<input type="submit" name="selcity" value="Select City"  class="btn btn-xs btn-primary">'; 
} 
echo '</form>';
if(isset($_POST['selcity'])) 
{ 
	$cityid=$_POST['cityid']; 
	$sel='SELECT co.country, ci.city, ho.hotel, ho.cost, ho.stars, ho.id  FROM hotels ho, cities ci, countries co  WHERE ho.cityid=ci.id  AND ho.countryid=co.id AND ho.cityid='.$cityid; 
	$res=$mysqli->query($sel); 
	echo '<table width="100%" class="table table striped tbtours text-center">'; 
	echo '<thead style="font-weight: bold"> <td>Hotel</td><td>Country</td><td>City</td> <td>Price</td><td>Stars</td><td>link</td></thead>'; 
	while ($row=mysqli_fetch_array($res, MYSQLI_NUM)) 
	{  
		echo '<tr id="'.$row[1].'">';  
		echo '<td>'.$row[2].'</td> <td>'.$row[0].'</td> <td>'.$row[1].'</td><td>$'.$row[3].'</td><td>'.$row[4].'</td><td> <a href="pages/hotelinfo.php?hotel='.$row[5]. '" target="_blank"> more info</a></td>';  
		echo '</tr>'; 
	} 
	echo '</table><br>'; 
} 
?>