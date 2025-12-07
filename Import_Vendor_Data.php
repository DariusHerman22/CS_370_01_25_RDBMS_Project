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

//                echo implode(", ", $parsedLine) . "<br>";
            }

        }
        catch(Exception $exception){
            $import_error_message = $exception->getMessage() . " at: " . $exception->getFile() . " (line: " . $exception->getLine() . ") <br/>";
        }

    }

}
?>

<?php include_once("Header.php") ?>
    <style>
        /* Custom Orange Theme Variables */
        :root {
            --theme-color: #fd7e14; /* Bootstrap Orange */
            --theme-hover: #e36d0a; /* Darker Orange */
        }

        .upload-zone {
            border: 2px dashed #dee2e6;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }
        .upload-zone:hover {
            border-color: var(--theme-color);
            background-color: #fff8f0; /* Light orange tint on hover */
        }
        .file-input-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        /* Theme specific text coloring */
        .text-theme { color: var(--theme-color) !important; }

        /* Theme specific button */
        .btn-theme {
            background-color: var(--theme-color);
            border-color: var(--theme-color);
            color: white;
        }
        .btn-theme:hover {
            background-color: var(--theme-hover);
            border-color: var(--theme-hover);
            color: white;
        }
    </style>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-top border-4" style="border-color: var(--theme-color) !important;">
                        <h3 class="mb-0 text-center fw-bold text-dark">Vendor Data Import</h3>
                    </div>

                    <div class="card-body p-4 p-md-5">

                        <?php if( $import_attempted ): ?>
                            <?php if( $import_succeeded ): ?>
                                <div class="alert alert-success text-center mb-4 shadow-sm" role="alert">
                                    <h4 class="alert-heading fw-bold">Import Successful!</h4>
                                    <p class="mb-0">The database has been updated with the new vendor records.</p>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger mb-4 shadow-sm" role="alert">
                                    <h4 class="alert-heading fw-bold">Import Failed!</h4>
                                    <hr>
                                    <p class="mb-0"><?php echo $import_error_message ?></p>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <form method="post" enctype="multipart/form-data">

                            <div class="upload-zone rounded p-5 text-center position-relative mb-4">
                                <div class="mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-building text-theme" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022zM6 8.694 1 10.36V15h5V8.694zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5V15z"/>
                                        <path d="M2 11h1v1H2v-1zm2 0h1v1H4v-1zm-2 2h1v1H2v-1zm2 0h1v1H4v-1zm4-4h1v1H8V9zm2 0h1v1h-1V9zm-2 2h1v1H8v-1zm2 0h1v1h-1v-1zm2-2h1v1h-1V9zm0 2h1v1h-1v-1zM8 7h1v1H8V7zm2 0h1v1h-1V7zm2 0h1v1h-1V7zM8 5h1v1H8V5zm2 0h1v1h-1V5zm2 0h1v1h-1V5zm0-2h1v1h-1V3z"/>
                                    </svg>
                                </div>
                                <h5 class="fw-bold">Drag and drop vendor file</h5>
                                <p class="text-muted small">or click to browse from computer</p>

                                <input class="file-input-overlay" type="file" name="importFile" id="importFile" onchange="updateFileName(this)" />
                            </div>

                            <div id="fileNameDisplay" class="text-center text-theme fw-bold mb-3 small" style="min-height: 20px;"></div>

                            <div class="d-grid">
                                <input class="btn btn-theme btn-lg" type="submit" value="Upload Vendor Data" />
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateFileName(input) {
            var display = document.getElementById('fileNameDisplay');
            if(input.files && input.files.length > 0) {
                display.textContent = "Selected: " + input.files[0].name;
            } else {
                display.textContent = "";
            }
        }
    </script>

<?php include_once("Footer.php") ?>