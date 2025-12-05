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
        .upload-zone {
            border: 2px dashed #dee2e6;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }
        .upload-zone:hover {
            border-color: #0d6efd;
            background-color: #e9ecef;
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
    </style>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h3 class="mb-0 text-center fw-bold text-dark">Product Data Import</h3>
                    </div>

                    <div class="card-body p-4 p-md-5">

                        <?php if( $import_attempted ): ?>
                            <?php if( $import_succeeded ): ?>
                                <div class="alert alert-success text-center mb-4 shadow-sm" role="alert">
                                    <h4 class="alert-heading fw-bold">Import Successful!</h4>
                                    <p class="mb-0">The database has been updated with the new product records.</p>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-cloud-arrow-up text-primary" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M7.646 5.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708l2-2z"/>
                                        <path d="M4.406 3.342A5.53 5.53 0 0 1 8 2c2.69 0 4.923 2 5.166 4.579C14.758 6.804 16 8.137 16 9.773 16 11.569 14.502 13 12.687 13H3.781C1.708 13 0 11.366 0 9.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383zm.653.757c-.757.653-1.153 1.44-1.153 2.056v.448l-.445.049C2.064 6.805 1 7.952 1 9.318 1 10.785 2.23 12 3.781 12h8.906C13.98 12 15 10.988 15 9.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 4.825 10.328 3 8 3a4.53 4.53 0 0 0-2.941 1.1z"/>
                                    </svg>
                                </div>
                                <h5 class="fw-bold">Drag and drop product file</h5>
                                <p class="text-muted small">or click to browse from computer</p>

                                <input class="file-input-overlay" type="file" name="importFile" id="importFile" onchange="updateFileName(this)" />
                            </div>

                            <div id="fileNameDisplay" class="text-center text-primary fw-bold mb-3 small" style="min-height: 20px;"></div>

                            <div class="d-grid">
                                <input class="btn btn-primary btn-lg" type="submit" value="Upload Product Data" />
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