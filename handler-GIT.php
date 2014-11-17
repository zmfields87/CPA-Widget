<?php
	
$state = $_REQUEST['state'];
	
try 
{
	$db = new PDO("mysql:host=censored;dbname=censored","censored","censored");
}	catch (Exception $e) 
	{
		echo "Could not connect to database.";
		exit;
	}

$stmt = $db->prepare("SELECT MIN_ED, 150_HRS, MIN_AGE, RES, CIT, PAGE_URL 
		FROM CPA_db WHERE STATE = '$state'");

?>

<? 					
echo "<p>CPA Exam Requirements for " . $state . "</p>";
	
				if($stmt->execute()) { 
						while($rows = $stmt->fetch(PDO::FETCH_ASSOC)) { 
							
							echo "<ul>";
							echo "<li>Minimum Education:</li>";
							echo "<li class='min_ed'>" . $rows['MIN_ED'] . "<a href='" . $rows['PAGE_URL'] . "' target='_blank'>**</a></li>";
							echo "<li>150 Hours of Education?</li>";
							echo "<li class='150_hrs'>" . $rows['150_HRS'] . "<a href='" . $rows['PAGE_URL'] . "' target='_blank'>**</a></li>";
							echo "<li>Minimum Age?</li>";
							echo "<li class='min_age'>" . $rows['MIN_AGE'] . "</li>";
							echo "<li>State Residency?</li>";
							echo "<li class='res'>" . $rows['RES'] . "</li>";
							echo "<li>U.S. Citizenship?</li>";
							echo "<li class='cit'>" . $rows['CIT'] . "</li>";
							echo "</ul>";
				
					 } } ?>
					 
	 				
					 