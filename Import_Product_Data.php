<?php

mysqli_report(MYSQLI_REPORT_OFF);
$import_attempted = false;
$import_succeeded = false;
$import_error_message = "";

if( $_SERVER[ "REQUEST_METHOD" ] == "POST" )
{
	$import_attempted = true;
	$con = @mysqli_connect("localhost", "pizza_user", "Password Here", "Database Name Here");

	if( mysqli_connect_errno() ){
		$import_error_message = "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	else{
		$import_succeeded = true;

		try{
			$contents = file_get_contents( $_FILES[ "importFile" ][ "tmp_name" ] );
			$lines = explode( "\n", $contents );

			foreach( $lines as $line){
				$parsedLine = str_getcsv( $line );

                if(count($parsedLine) < 13){continue;}

                $ProductID = (int)$parsedLine[0];
                $VendorCompanyID = (int)$parsedLine[1];
                $ProductName = $parsedLine[2];
                $ProductDesc = $parsedLine[3];
                $ProductPrice = (double)$parsedLine[4];
                $ProductStock = (int)$parsedLine[5];

                $ShoppingCartID = (int)$parsedLine[7];
                $CustomerID = (int)$parsedLine[8];

                $Quantity = (int)$parsedLine[12];

                $Table1 = $con->prepare("
                INSERT INTO Product(ProductID, VendorCompanyID, ProductName, ProductDesc, ProductPrice, ProductStock)
                VALUES (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    $ProductName = VALUES(ProducName),
                    $ProductDesc = VALUES(ProducDesc),
                    $ProductPrice = VALUES(ProductPrice),
                    $ProductStock = VALUES(ProductStock)
                ");
                $Table1->bind_param(
                        "iissdi",
                        $ProductID,
                        $VendorCompanyID,
                        $ProductName,
                        $ProductDesc,
                        $ProductPrice,
                        $ProductStock);
                $Table1->execute();

                $Table2 = $con->prepare("
                INSERT INTO ShoppingCart(ShoppingCartID, CustomerID)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE
                    $ShoppingCartID = VALUES(ShoppingCartID),
                    $CustomerID = VALUES(CustomerID) /* This might not need to be done */
                ");
                $Table2->bind_param(
                        "ii",
                        $ShoppingCartID,
                        $CustomerID);
                $Table2->execute();

                $Table3 = $con->prepare("
                INSERT INTO ShoppingCartItem(ShoppingCartID, ProductID, Quantity)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    ShoppingCartID = VALUES(ShoppingCartID),
                    ProductID = VALUES(ProductID) /* This might not need to be done */
                    Quanity = VALUES(Quanity),
                
                ");
                $Table3->bind_param("iii",
                $ShoppingCartID,
                        $ProductID,
                        $Quantity);
                $Table3->execute();

                echo implode(", ", $parsedLine) . "<br/>";
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
		<h1>Product Data Import</h1>

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

