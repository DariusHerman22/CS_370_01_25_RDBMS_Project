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

                if(count($parsedLine) < 15){continue;}

                $VendorCompanyID = (int)$parsedLine[0];
                $VendorCompanyName = $parsedLine[1];

                $PhoneNumber = $parsedLine[5];
                $EmailAddress = $parsedLine[6];

                $VendorAddressID = (int)$parsedLine[8];
                $StreetNameNumber = $parsedLine[10];
                $City = $parsedLine[11];
                $State = $parsedLine[12];
                $ZipCode = $parsedLine[13];
                $Country = $parsedLine[14];
                $AddressType = (int)$parsedLine[15];

                $Table1 = $con->prepare("
                INSERT INTO VendorCompany(VendorCompanyID, VendorCompanyName)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE
                    VendorCompanyID = VALUES(VendorCompanyID),
                    VendorCompanyName = VALUES(VendorCompanyName)
                ");
                $Table1->bind_param(
                        "is",
                        $VendorCompanyID,
                        $VendorCompanyName);
                $Table1->execute();

                $Table2 = $con->prepare("
                INSERT INTO VendorContactInfo(VendorCompanyID, PhoneNumber, EmailAddress)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    PhoneNumber = VALUES(PhoneNumber),
                    EmailAddress = VALUES(EmailAddress)
                ");
                $Table2->bind_param(
                        "iss",
                        $VendorCompanyID,
                        $PhoneNumber,
                        $EmailAddress);
                $Table2->execute();

                $Table3 = $con->prepare("
                INSERT INTO VendorCompanyAddress(VendorAddressID, VendorCompanyID, StreetNameNumber,
                            City, State, ZipCode, Country, AddressType)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    StreetNameNumber = VALUES(StreetNameNumber),
                    City = VALUES(City),
                    State = VALUES(State),
                    Zipcode = VALUES(ZipCode),
                    Country = VALUES(Country),
                    AddressType = VALUES(AddressType)
                ");
                $Table3->bind_param("iisssisi",
                        $VendorAddressID,
                        $VendorCompanyID,
                        $StreetNameNumber,
                        $City,
                        $State,
                        $ZipCode,
                        $Country,
                        $AddressType
                );
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
		<h1>Vendor Data Import</h1>

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

