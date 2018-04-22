<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>Good weather</title>
	<meta neme="description" content="Check the weather for your city.">
	<meta name="keywords" content="weather, city, 5 day weather, check weather">
	<meta http-equiv="X-Ua-Compatible" content="IE=edge">
	<link rel="stylesheet" href="style.css">
</head>

<body>
	<header>
		<div id="h1_background">
			<h1>Check weather</h1>
		<div>
	</header>
	<main>
		<form action="index.php" method="POST">
			<input type="text" name="city" placeholder="Enter name of city" required/>
			<input type="submit" name="send" value="Submit" />
		</form>
		
			<?php
			function curl($url)
			{
				$ci=curl_init();
				curl_setopt($ci, CURLOPT_URL, $url);
				curl_setopt($ci, CURLOPT_RETURNTRANSFER,1);
				$score_curl=curl_exec($ci);
				curl_close($ci);
				return $score_curl;
			}
				
			if(isset($_POST['city']))
			{
				$urlScore=curl("http://api.openweathermap.org/data/2.5/forecast?q=".$_POST['city']."&appid=9c398cd4cf22ab63cebf65a655f9d64d");	
				$scoreArray = json_decode($urlScore, TRUE);
			}

			if(isset($_GET['city']))
			{
				$urlScore=curl("http://api.openweathermap.org/data/2.5/forecast?q=".$_GET['city']."&appid=9c398cd4cf22ab63cebf65a655f9d64d");	
				$scoreArray = json_decode($urlScore, TRUE);
				$_POST['city']=$_GET['city'];
				$_POST['send']=true;
			}
			?>
		
		
		<?php
			if(isset($scoreArray))
			{
				if(isset($scoreArray['cnt']) and isset($_POST['send']))
				{
					$howMuch=$scoreArray['cnt'];
					
					$city =$_POST['city'];
					echo'<h3>Weather for '.$city.'</h3>';
				
					for($i=0;$i<$howMuch;$i++)
					{
						
						$day=substr($scoreArray['list'][$i]['dt_txt'],0,10);
						$i-1>=0 ? $prevDay=substr($scoreArray['list'][$i-1]['dt_txt'],0,10) : $prevDay="0000-00-00";
						if($i==0)echo'<h2>'.$day.'</h2><div class="day">';
						if($i+1<$howMuch)$nextDay=substr($scoreArray['list'][$i+1]['dt_txt'],0,10);
						$time=substr($scoreArray['list'][$i]['dt_txt'],11,5);
						$temp_min=$scoreArray['list'][$i]['main']['temp_min']-273;
						$temp_max=$scoreArray['list'][$i]['main']['temp_max']-273;
						$wind_speed=$scoreArray['list'][$i]['wind']['speed'];
						echo '<div class="time">
								<div class="time_header">
									<h4>'.$time.'&nbsp; | &nbsp;'.$scoreArray['list'][$i]['weather'][0]['main'].'</h4>
								</div>
								<div class="weather_icon">
									<img class="icon" src="http://openweathermap.org/img/w/'.$scoreArray['list'][$i]['weather'][0]['icon'].'.png"></img>
								</div>
								<div class="temperature">
									<div class="temp max">'.round($temp_max,1).'<sup>o</sup>C</div>
									<div class="temp min">'.round($temp_min,1).'<sup>o</sup>C</div>
								</div>
								<div class="details">
									<h4>Details:</h4>
									Wind speed:&nbsp;'.round($wind_speed,2).'&nbsp; m/s</br>
									Pressure:&nbsp;'.$scoreArray['list'][$i]['main']['pressure'].'&nbsp hPa</br>
									Humidity:&nbsp;'.$scoreArray['list'][$i]['main']['humidity'].'%
								</div>
							</div>';
						if($day!=$nextDay)
						{
							echo'</div><h2>'.$nextDay.'</h2><div class="day">';
						}
							 
						if($i==$howMuch-1) echo'</div>'; 
					}
					$ip=$_SERVER['REMOTE_ADDR'];
					$date_from=$scoreArray['list'][0]['dt_txt'];
					$date_to=$scoreArray['list'][$howMuch-1]['dt_txt'];
			
					
						
					
				}else
					$Error="Sorry we don't know this city";
					
				
			}
			
			if(isset($Error))
				{
					echo"<div class='error'>".$Error."</div>";
					unset($Error);
				}
			
			echo'<h5>Last searched cities:</h5>';
					
					require_once('conect.php');
					
						if(!isset($Error))
						{
							$add=$conect->prepare('INSERT INTO history (ip,date_from,date_to,city) VALUES (:ip,:date_from,:date_to,:city)');
							$add->bindParam(':ip',$ip,PDO::PARAM_STR);
							$add->bindParam(':date_from',$date_from,PDO::PARAM_STR);
							$add->bindParam(':date_to',$date_to,PDO::PARAM_STR);
							$add->bindParam(':city',$city,PDO::PARAM_STR);
							$add->execute();
							
							$lastSearch=$conect->prepare('SELECT DISTINCT city FROM history  ORDER BY id DESC LIMIT 5');
							$lastSearch->execute();
							$count=$lastSearch->rowCount();
							
							echo'<table id="lastSearch">';
							if($count!=0)
							{
								foreach($lastSearch as $lastCity)
								{
									echo'<tr>
											<td>
												<button onclick="listSearch(\''.$lastCity['city'].'\')">
													'.$lastCity['city'].'
												</button>
											</td>
										</tr>';
								}
							}else
								echo"<tr><td>There aren't recent searches</td>";
							echo'</table>';
						}
				
		?>
		
		
		
		
	</main>
</body>

</html>

<script>
	function listSearch(cityName)
	{
			window.location.href='index.php?city='+cityName;
	}
</script>