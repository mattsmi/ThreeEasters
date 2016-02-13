<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Easter Date Calculated for a given Year</title>

<!-- Basic Twitter Bootstrap stylesheet -->
<link rel="stylesheet" href="./generic/css/bootstrap.min.css">

<!-- Theme stylesheet -->
<link rel="stylesheet" href="./generic/css/bootstrap-theme.min.css">

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>


<body>
	<div class="container">
	<?php
	include 'EasterCalcs.php';
	$value_select = $_POST ["yearValue"];
	$dDate1 = pF10_CalcEaster ( $value_select, 1 );
	if (! $dDate1) {
		// if there was a date error
		$sError = "<h1>Error!</h1>\n<p>&#xA0;</p>\n";
		$sError = $sError . "<p> Incorrect Year ($value_select) supplied. <br>";
		$sError = $sError . "The Year should be between 1583 and 4099.</p>\n";
		echo ($sError);
	} else {
		$dDate2 = pF10_CalcEaster ( $value_select, 2 );
		$dDate3 = pF10_CalcEaster ( $value_select, 3 );
		?>
	<h1>Calculate the Date of Easter</h1>
		<h2>for the year <?php echo($value_select); ?></h2>
		<p>&#xA0;</p>

		<table class="table table-striped table-condensed">
		<thead>
		<tr>
		<th>Easter Dating Method</th>
		<th>Date</th>
		</tr>
			
			<tr>
				<td>Julian Easter</td>
				<td><?php echo(date("j F Y", $dDate1)); ?>	</td>
			</tr>
			<tr>
				<td>Orthodox Easter</td>
				<td><?php echo(date("j F Y", $dDate2)); ?>	</td>
			</tr>
			<tr>
				<td>Western Easter</td>
				<td><?php echo(date("j F Y", $dDate3)); ?>	</td>
			</tr>
		</table>
<?php
	}
	?>
</div>
	<!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="./generic/js/jquery.min.js"></script>
    <script src="./generic/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="./generic/js/ie10-viewport-bug-workaround.js"></script>

</body>
</html>
