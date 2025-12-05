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
        /* Custom Blue Theme Variables */
        :root {
            --theme-color: #0d6efd; /* Bootstrap Primary Blue */
            --theme-hover: #0b5ed7;
        }

        .upload-zone {
            border: 2px dashed #dee2e6;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }
        .upload-zone:hover {
            border-color: var(--theme-color);
            background-color: #f0f7ff; /* Light blue tint on hover */
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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-box-seam text-theme" viewBox="0 0 16 16">
                                        <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2l-2.218-.887zm3.564 1.426L5.596 5 8 5.961 14.154 3.5l-2.404-.961zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z"/>
                                    </svg>
                                </div>
                                <h5 class="fw-bold">Drag and drop product file</h5>
                                <p class="text-muted small">or click to browse from computer</p>

                                <input class="file-input-overlay" type="file" name="importFile" id="importFile" onchange="updateFileName(this)" />
                            </div>

                            <div id="fileNameDisplay" class="text-center text-theme fw-bold mb-3 small" style="min-height: 20px;"></div>

                            <div class="d-grid">
                                <input class="btn btn-theme btn-lg" type="submit" value="Upload Product Data" />
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