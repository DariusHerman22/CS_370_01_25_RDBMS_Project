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

    <style>
        /* Custom Green Theme Variables */
        :root {
            --theme-color: #198754; /* Bootstrap Success Green */
            --theme-hover: #157347;
        }

        .upload-zone {
            border: 2px dashed #dee2e6;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }
        .upload-zone:hover {
            border-color: var(--theme-color);
            background-color: #f0fff4; /* Light green tint on hover */
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
                    <div class="card-header bg-white py-3 border-top border-4 border-success">
                        <h3 class="mb-0 text-center fw-bold text-dark">Customer Data Import</h3>
                    </div>

                    <div class="card-body p-4 p-md-5">

                        <?php if( $import_attempted ): ?>
                            <?php if( $import_succeeded ): ?>
                                <div class="alert alert-success text-center mb-4 shadow-sm" role="alert">
                                    <h4 class="alert-heading fw-bold">Import Successful!</h4>
                                    <p class="mb-0">The database has been updated with the new customer records.</p>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-people-fill text-theme" viewBox="0 0 16 16">
                                        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7Zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216ZM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
                                    </svg>
                                </div>
                                <h5 class="fw-bold">Drag and drop customer file</h5>
                                <p class="text-muted small">or click to browse from computer</p>

                                <input class="file-input-overlay" type="file" name="importFile" id="importFile" onchange="updateFileName(this)" />
                            </div>

                            <div id="fileNameDisplay" class="text-center text-theme fw-bold mb-3 small" style="min-height: 20px;"></div>

                            <div class="d-grid">
                                <input class="btn btn-theme btn-lg" type="submit" value="Upload Customer Data" />
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