<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<?php include_once("head.php"); ?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
</head>

<body class="light">
	<?php include_once("Header.php"); ?>
	<div class="content">
		<div class="horizontalMenu">
			<input id="yearGraph" name="yearGraph" style="text-align:center;" class="menuButton rectangular" type="number" value="2023" min="1900">
			<button style="text-align:center;" class="menuButton rectangular" onlick="refreshDate();">Valider</button>
		</div>
		<div class="graphsContainer">
			<canvas id="visits"></canvas>
			<canvas id="expenses"></canvas>
		</div>
		<?php include_once("Footer.php"); ?>
	</div>
	<script>
		let months=["janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre"];
		let year = "2023";

		function createVisitsGraph(visitsValues){
			var visits = new Chart("visits", {
				type: "bar",
				data:{
					labels:months,
					datasets: [{
						data: visitsValues,
						backgroundColor:"blue"
					}]
				},
				options:{
					responsive: true,
					legend: {display: false},
					title: {
						display: true,
						text: 'Nombre de stationnements par mois'
					},
					scales: {
						yAxes: [{
							display: true,
							ticks: {
								beginAtZero: true
			
							}
						}]
					}
				}
			});
		}

		function createExpensesGraph(expensesValues){
			var expenses = new Chart("expenses", {
				type: "line",
				data:{
					labels:months,
					datasets: [{
						borderColor: 'blue',
						fill:false,
						data: expensesValues,
					}]
				},
				options:{
					responsive: true,
					legend: {display: false},
					title: {
						display: true,
						text: 'Dépenses par mois'
					},
					scales: {
						yAxes: [{
							display: true,
							ticks: {
								beginAtZero: true
							}
						}]
					}
				}
			});
		}

		function sendQuery(url){
			let result = [0,0,0,0,0,0,0,0,0,0,0,0];
			// example : [{"d":"4","n":"12.00"}]
			fetch(url).then(function(response) {
				if(response.status >= 200 && response.status < 300) {
					return response.json();
				}
				throw new Error(response.statusText);
			})
			.then(function(response) {
				if(response == "0"){
					alert("Vous n'êtes pas connecté");
				} else{
					for(let i=0; i<response.length; i++){
						result[parseInt(response[i]["d"])] = parseInt(response[i]["n"]);
					}
				}
			})
			return result;
		}

		function getExpensesValues(){
			return sendQuery('./PHP/queryMsqli.php?d=expenses&y='+year);
		}

		function getVisitsValues(){
			return sendQuery('./PHP/queryMsqli.php?d=visits&y='+year);
		}

		function refreshDate(){
			year = document.getElementById('yearGraph').value;
		}

		let expensesValues = getExpensesValues();
		let visitsValues = getVisitsValues();
			
		createVisitsGraph(visitsValues);
		createExpensesGraph(expensesValues);

		/*
		To predict the next expenses:
		- Use data of the previous year if there are any
		- Use the previous months to get the slope expected
		- Display it on the same graph ?
		*/
	</script>
</body>
</html>