<?php
/**
 * Allows one to view all tables and their data in a database
 */
require_once("conn.php");
$sql = "SHOW TABLES";
$tables = $dbConn->query($sql);

$tablesAndTheirData = array();
while($tableName = $tables->fetch_array()) {
	$sql = "SELECT * FROM $tableName[0]";
	$data = $dbConn->query($sql) or die ('Problem with query: ' . $dbConn->error);
    array_push($tablesAndTheirData, array(
        'name' => $tableName[0],
        'fields' => $data->fetch_fields(),
        'data' => $data
    ));
}

$queryData = array();
if(isset($_POST['query'])) {
	$sql = $_POST['query'];
	$data = $dbConn->query($sql) or die ('Problem with query: ' . $dbConn->error);
	$queryData = array(
		'fields' => $data->fetch_fields(),
		'data' => $data
	);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Database Tables</title>
  <link rel="stylesheet" href="css/projectMaster.css">
</head>
<body>

<br>
<form class="" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<textarea type="text" name="query" rows=10 cols=100><?php if(isset($_POST['query'])) echo $_POST['query']; ?>
	</textarea>
	<button type="submit" name="button">Submit</button>
</form>
<br>

<?php 
if(isset($_POST['query'])):
	?>
<h2><?php echo $_POST['query']; ?></h2>
	<?php if($queryData['data']->num_rows):?>
		<table>
			<thead>
				<tr style="font-weight:bold">
				<?php foreach($queryData['fields'] as $field): ?>

					<td><?php echo $field->name;?></td>

				<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
		<?php while($row = $queryData['data']->fetch_assoc()): ?>
			<tr>
				<?php foreach($row as $key => $value):?>
					<td><?php echo $value; ?>
					</td>
				<?php endforeach; ?>
			</tr>
		<?php endwhile;?>
			</tbody>
		</table>
	<?php else:?>
		<p>Table does not have any data</p>
	<?php endif;?>
<?php endif;?>

<?php foreach($tablesAndTheirData as $table): ?>
<h2><code><?php echo $table['name'];?></code> Table</h2>
	<?php if($table['data']->num_rows):?>
		<table>
			<thead>
				<tr style="font-weight:bold">
				<?php foreach($table['fields'] as $field): ?>

					<td><?php echo $field->name;?></td>

				<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
		<?php while($row = $table['data']->fetch_assoc()): ?>
			<tr>
				<?php foreach($row as $key => $value):?>
					<td><?php echo $value; ?>
					</td>
				<?php endforeach; ?>
			</tr>
		<?php endwhile;?>
			</tbody>
		</table>
	<?php else:?>
		<p>Table does not have any data</p>
	<?php endif;?>
<?php endforeach;
$dbConn->close();
?>
</body>
</html>
