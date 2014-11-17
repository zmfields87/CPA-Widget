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

$stmt = $db->prepare("SELECT WORK_EXP, ETHICS, RECIP_N, PAGE_URL 
		FROM CPA_db WHERE STATE = '$state'");

?>

<? 					
echo "<p>CPA Licensing Requirements for " . $state . "</p>";
	
				if($stmt->execute()) { 
						while($rows = $stmt->fetch(PDO::FETCH_ASSOC)) { 
							
							echo "<ul>";
							echo "<li>Work Experience?</li>";
							echo "<li class='work_exp'>" . $rows['WORK_EXP'] . "<a href='" . $rows['PAGE_URL'] . "' target='_blank'>**</a></li>";
							echo "<li>Ethics Exam?</li>";
							echo "<li class='ethics'>" . $rows['ETHICS'] . "</li>";
							echo "<li>Accepts Other State CPA Licenses?</li>";
							echo "<li class='recip_n'>" . $rows['RECIP_N'] . "<a href='" . $rows['PAGE_URL'] . "' target='_blank'>**</a></li>";
							echo "</ul>";
				
					 } } ?>