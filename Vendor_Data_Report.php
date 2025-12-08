<?php
mysqli_report(MYSQLI_REPORT_OFF);
$connection_error = false;
$connection_error_message = "";

$con = @mysqli_connect("localhost", "EpicAwesomeStoreUser", "password", "EpicAwesomeStore");

if(mysqli_connect_errno()){
    $connection_error = true;
    $connection_error_message = "Failed to connect to MySQL: " . mysqli_connect_error();
}

function output_error($title, $error){
    echo "<div class='alert alert-danger shadow-sm text-center my-4'>";
    echo "<h4 class='alert-heading fw-bold'><i class='bi bi-exclamation-triangle-fill'></i> " . $title . "</h4>";
    echo "<hr>";
    echo "<p class='mb-0'>" . $error . "</p>";
    echo "</div>";
}

function VendorHeaderRow(){
    echo "<div class='table-responsive'>";
    echo "<table class='table table-hover align-middle border text-dark'>";
    echo "<thead class='text-dark border-bottom border-2'>";
    echo "<tr>";
    echo "    <th class='py-3'>ID</th>";
    echo "    <th class='py-3'>Company Name</th>";
    echo "    <th class='py-3'>Phone Number</th>";
    echo "    <th class='py-3'>Email Address</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
}

function VendorInfoRow($row) {
    echo "<tr class='fw-semibold'>";
    echo "<td>#" . $row['VendorCompanyID'] . "</td>";
    echo "<td class='text-dark'>" . $row['VendorCompanyName'] . "</td>";
    echo "<td>" . $row['PhoneNumber'] . "</td>";
    echo "<td>" . $row['EmailAddress'] . "</td>";
    echo "</tr>";
}

function AddressRow($address){
    echo "<tr>";
    echo "<td colspan='4' class='p-0 border-0'>";

    echo "<div class='bg-light p-4 border-bottom'>";
    echo "<h6 class='fw-bold mb-3' style='color: #fd7e14;'><i class='bi bi-building'></i> Company Locations</h6>";

    echo "<table class='table table-sm table-bordered bg-white mb-0 shadow-sm text-dark' style='font-size: 0.9em;'>";
    echo "<thead class='table-light text-dark'>";
    echo "<tr>";
    echo "<th style='width:85px;' >Type</th>";
    echo "<th style='width:85px;' >Street</th>";
    echo "<th style='width:85px;' >City</th>";
    echo "<th style='width:85px;' >State</th>";
    echo "<th style='width:85px;' >Zip</th>";
    echo "<th style='width:85px;' >Country</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($address as $a) {
        echo "<tr>";
        echo "<td><span class='badge' style='background-color: #fd7e14; color: white;'>" . $a['AddressType'] . "</span></td>";
        echo "<td>" . $a['StreetNameNumber'] . "</td>";
        echo "<td>" . $a['City'] . "</td>";
        echo "<td>" . $a['State'] . "</td>";
        echo "<td>" . $a['ZipCode'] . "</td>";
        echo "<td>" . $a['Country'] . "</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";

    echo "</td>";
    echo "</tr>";
}
?>

<?php include_once("Header.php")?>

    <div class="container my-5">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white py-4 border-top border-4 d-flex justify-content-between align-items-center" style="border-color: #fd7e14 !important;">
                <div>
                    <h2 class="mb-0 fw-bold text-dark">Vendor Data Report</h2>
                </div>
                <button onclick="window.print()" class="btn btn-outline-secondary btn-sm d-print-none">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>

            <div class="card-body p-0">

                <?php
                if($connection_error){
                    echo "<div class='p-4'>";
                    output_error("Database connection failure", $connection_error_message);
                    echo "</div>";
                }else{

                    $query =
                            "SELECT t0.VendorCompanyID, t0.VendorCompanyName, t1.PhoneNumber,
        t1.EmailAddress, t2.VendorAddressID, t2.StreetNameNumber,
        t2.City, t2.State, t2.ZipCode, t2.Country, t2.AddressType"
                            . " FROM VendorCompany t0"
                            . " LEFT OUTER JOIN VendorContactInfo t1 ON t0.VendorCompanyID = t1.VendorCompanyID"
                            . " LEFT OUTER JOIN VendorCompanyAddress t2 ON t0.VendorCompanyID = t2.VendorCompanyID"
                    ;

                    $result = mysqli_query($con, $query);

                    if( !$result ){
                        if(mysqli_errno($con)){
                            echo "<div class='p-4'>";
                            output_error("Data retrieval failure!", mysqli_error($con));
                            echo "</div>";
                        }
                    }else{
                        if (mysqli_num_rows($result) === 0) {
                            echo "<div class='text-center py-5'>";
                            echo "<div class='mb-3'><i class='bi bi-folder-x text-muted' style='font-size: 3rem;'></i></div>";
                            echo "<h4 class='text-muted'>No Vendor Data Found</h4>";
                            echo "<p class='text-muted'>Please run the Import tool to add vendors.</p>";
                            echo "</div>";
                        } else {

                            VendorHeaderRow();

                            $CurrentVendor = null;
                            $CurrentAddress = null;

                            while($row = $result->fetch_assoc()){

                                if ($CurrentVendor !== $row["VendorCompanyID"]) {

                                    if ($CurrentVendor !== null) {
                                        AddressRow($CurrentAddress);
                                    }

                                    VendorInfoRow($row);

                                    $CurrentVendor = $row["VendorCompanyID"];
                                    $CurrentAddress = [];
                                }

                                if ($row["VendorAddressID"] !== null) {
                                    $CurrentAddress[] = $row;
                                }
                            }

                            if ($CurrentVendor !== null) {
                                AddressRow($CurrentAddress);
                            }

                            echo "</tbody>";
                            echo "</table>";
                            echo "</div>";
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>

<?php include_once("Footer.php")?>