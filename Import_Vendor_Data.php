<?php

mysqli_report(MYSQLI_REPORT_OFF);
$import_attempted = false;
$import_succeeded = false;
$import_error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $import_attempted = true;
    /* Replace with your own DB information */
    $con = mysqli_connect("localhost", "EpicAwesomeStoreUser", "password", "EpicAwesomeStore");

    if( mysqli_connect_errno() ){
        $import_error_message = "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    else{
        $import_succeeded = true;

        try{

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

                if (count($parsedLine) < 15) continue;

                $VendorCompanyID  = (int)$parsedLine[0];
                $VendorCompanyName = $parsedLine[1];

                $PhoneNumber = $parsedLine[4];
                $EmailAddress = $parsedLine[5];

                $VendorAddressID = (int)$parsedLine[7];
                $StreetNameNumber = $parsedLine[9];
                $City = $parsedLine[10];
                $State = $parsedLine[11];
                $ZipCode = $parsedLine[12];
                $Country = $parsedLine[13];
                $AddressType = $parsedLine[14];

                $Table1 = $con->prepare("
                INSERT INTO vendorcompany(VendorCompanyID, VendorCompanyName)
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
                $Table1->close();

                $Table2 = $con->prepare("
                INSERT INTO vendorcontactinfo (VendorCompanyID, PhoneNumber, EmailAddress)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    VendorCompanyID = VALUES(VendorCompanyID),
                    PhoneNumber = VALUES(PhoneNumber),
                    EmailAddress = VALUES(EmailAddress)
        ");
                $Table2->bind_param(
                        "iss",
                        $VendorCompanyID,
                        $PhoneNumber,
                        $EmailAddress);
                $Table2->execute();
                $Table2->close();

                $Table3 = $con->prepare("
                INSERT INTO vendorcompanyaddress (VendorAddressID, VendorCompanyID, StreetNameNumber, 
                City, State, ZipCode, Country, AddressType)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    VendorAddressID = VALUES(VendorAddressID),
                    VendorCompanyID = VALUES(VendorCompanyID),
                    StreetNameNumber = VALUES(StreetNameNumber),
                    City = VALUES(City),
                    State = VALUES(State),
                    ZipCode = VALUES(ZipCode),
                    Country = VALUES(Country),
                    AddressType = VALUES(AddressType)
                ");

                $Table3->bind_param("iissssss",
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
                $Table3->close();

                echo implode(", ", $parsedLine) . "<br>";
            }

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

