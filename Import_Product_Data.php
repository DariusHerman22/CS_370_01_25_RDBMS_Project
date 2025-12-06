<?php

mysqli_report(MYSQLI_REPORT_OFF);
$import_attempted = false;
$import_succeeded = false;
$import_error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $import_attempted = true;
    /* Replace with your own DB information */
    $con = @mysqli_connect("localhost", "EpicAwesomeStoreUser", "password", "EpicAwesomeStore");

    if (mysqli_connect_errno()) {
        $import_error_message = "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    else {
        $import_succeeded = true;

        try {
            $contents = file_get_contents($_FILES["importFile"]["tmp_name"]);
            $lines = explode("\n", $contents);

            $isFirstRow = true;

            foreach ($lines as $line) {

                if (trim($line) === "") continue;

                $parsedLine = str_getcsv($line);

                if ($isFirstRow) {
                    $isFirstRow = false;
                    continue;
                }

                if (count($parsedLine) < 14) continue;

                $ProductID = (int)$parsedLine[0];
                $VendorCompanyID = (int)$parsedLine[1];
                $ProductName = $parsedLine[2];
                $ProductDesc = $parsedLine[3];
                $ProductPrice = (double)$parsedLine[4];
                $ProductStock = (int)$parsedLine[5];

                $ShoppingCartID = (int)$parsedLine[7];
                $CustomerID = (int)$parsedLine[8];

                $ItemCartID = (int)$parsedLine[11];
                $ItemProductID = (int)$parsedLine[12];
                $Quantity = (int)$parsedLine[13];

                $Table1 = $con->prepare("
                    INSERT INTO product
                    (ProductID, VendorCompanyID, ProductName,
                     ProductDesc, ProductPrice, ProductStock)
                    VALUES (?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        ProductID = VALUES(ProductID),
                        VendorCompanyID = VALUES(VendorCompanyID),
                        ProductName = VALUES(ProductName),
                        ProductDesc = VALUES(ProductDesc),
                        ProductPrice = VALUES(ProductPrice),
                        ProductStock = VALUES(ProductStock)
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
                $Table1->close();

                $Table2 = $con->prepare("
                    INSERT INTO shoppingcart (ShoppingCartID, CustomerID, ProductID)
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        ShoppingCartID = VALUES(ShoppingCartID),
                        CustomerID = VALUES(CustomerID),
                        ProductID = VALUES(ProductID)
                ");

                $Table2->bind_param(
                        "iii",
                        $ShoppingCartID,
                        $CustomerID,
                            $ProductID);
                $Table2->execute();
                $Table2->close();

                $Table3 = $con->prepare("
                    INSERT INTO shoppingcartitem (ShoppingCartID, ProductID, Quantity)
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        ShoppingCartID = VALUES(ShoppingCartID),
                        ProductID = VALUES(ProductID),
                        Quantity = VALUES(Quantity)
                ");

                $Table3->bind_param(
                        "iii",
                        $ItemCartID,
                        $ItemProductID,
                        $Quantity);
                $Table3->execute();
                $Table3->close();

                echo implode(", ", $parsedLine) . "<br>";
            }

            $import_succeeded = true;

        }
        catch (Exception $exception) {
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

