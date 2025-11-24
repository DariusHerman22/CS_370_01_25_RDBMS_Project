<?php

mysqli_report(MYSQLI_REPORT_OFF);
$import_attempted = false;
$import_succeeded = false;
$import_error_message = "";

if( $_SERVER[ "REQUEST_METHOD" ] == "POST" )
{
	$import_attempted = true;
	$con = @mysqli_connect("localhost", "pizza_user", "password", "pizza_db");

	if( mysqli_connect_errno() ){
		$import_error_message = "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	else{
		$import_succeeded = true;

		try{
			$contents = file_get_contents( $_FILES[ "importFile" ][ "tmp_name" ] );
			$lines = explode( "\n", $contents );

			foreach( $lines as $line){
				$parsed_csv_line = str_getcsv( $line );
				// TODO: normalize the unnormalized data
				echo implode(", ", $parsed_csv_line) . "<br/>";
			}
			$import_succeeded = true;
		}
		catch(Exception $exception){
			$import_error_message = $exception->getMessage() . " at: " . $exception->getFile() . " (line: " . $exception->getLine() . ") <br/>";
		}

	}

}

?>

<?php include_once("Header.php") ?>
		<h1>Pizza Data Import</h1>

		<?php

			if( $import_attempted ){
				if( $import_succeeded ){
					?>
						<h1>
						    <span class="text-success">Import Successful!</span>
						</h1>
					<?php
				}
				else{
					?>
						<span class="text-failure">
							<h1>Import Failed!</h1>
							<?php echo $import_error_message ?>
						</span>
					<?php
				}
			}

		?>

		<form method="post" enctype="multipart/form-data">
            <div class="input-group mb-3">
                <span class="input-group-text">
			        File:
                </span>
                <input class="form-control" type="file" name="importFile" />
            </div>
			<input class="btn btn-primary" type="submit" value="Upload Data" />
		</form>
<?php include_once("Footer.php") ?>

