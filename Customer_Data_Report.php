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
    echo "<h4 class='alert-heading fw-bold'><i class='bi bi-exclamation-octagon-fill'></i> " . $title . "</h4>";
    echo "<hr>";
    echo "<p class='mb-0'>" . $error . "</p>";
    echo "</div>";
}

function CustomerHeaderRow(){
    echo "<div class='table-responsive'>";
    // Added 'text-dark' here to force black text everywhere in the table
    echo "<table class='table table-hover align-middle border text-dark'>";
    echo "<thead class='table-success text-dark'>";
    echo "<tr>";
    echo "    <th class='py-3'>ID</th>";
    echo "    <th class='py-3'>Full Name</th>";
    echo "    <th class='py-3'>Phone Number</th>";
    echo "    <th class='py-3'>Email Address</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
}

function CustomerInfoRow($row) {
    // Removed 'text-secondary' so it defaults to standard dark text
    echo "<tr class='fw-semibold'>";
    echo "<td>#" . $row['CustomerID'] . "</td>";
    echo "<td>" . $row['FName'] . " " . $row['LName'] . "</td>";
    echo "<td>" . $row['PhoneNumber'] . "</td>";
    echo "<td>" . $row['EmailAddress'] . "</td>";
    echo "</tr>";
}

function AddressRow($address){
    echo "<tr>";
    echo "<td colspan='4' class='p-0 border-0'>";

    echo "<div class='bg-light p-4 border-bottom'>";
    echo "<h6 class='text-success fw-bold mb-3'><i class='bi bi-geo-alt-fill'></i> Address Details</h6>";

    echo "<table class='table table-sm table-bordered bg-white mb-0 shadow-sm text-dark' style='font-size: 0.9em;'>";
    echo "<thead class='table-light text-dark'>";
    echo "<tr>";
    echo "<th>Type</th>";
    echo "<th>Street</th>";
    echo "<th>City</th>";
    echo "<th>State</th>";
    echo "<th>Zip</th>";
    echo "<th>Country</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($address as $a) {
        echo "<tr>";
        echo "<td><span class='badge bg-success'>" . $a['AddressType'] . "</span></td>";
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

            <div class="card-header bg-white py-4 border-top border-4 border-success d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0 fw-bold text-dark">Customer Data Report</h2>
                    <p class="text-muted small mb-0 mt-1">Generated from current database records</p>
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
                            "SELECT t0.CustomerID, t0.FName, t0.LName, t1.PhoneNumber,
        t1.EmailAddress, t2.CustomerAddressID, t2.StreetNameNumber,
        t2.City, t2.State, t2.ZipCode, t2.Country, t2.AddressType"
                            . " FROM Customer t0"
                            . " LEFT OUTER JOIN CustomerContactInfo t1 ON t0.CustomerID = t1.CustomerID"
                            . " LEFT OUTER JOIN CustomerAddress t2 ON t0.CustomerID = t2.CustomerID"
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
                            echo "<h4 class='text-muted'>No Customer Data Found</h4>";
                            echo "<p class='text-muted'>Please run the Import tool to add customers.</p>";
                            echo "</div>";
                        } else {

                            CustomerHeaderRow();

                            $CurrentCustomer = null;
                            $CurrentAddress = null;

                            while($row = $result->fetch_assoc()){

                                if ($CurrentCustomer !== $row["CustomerID"]) {

                                    if ($CurrentCustomer !== null) {
                                        AddressRow($CurrentAddress);
                                    }

                                    CustomerInfoRow($row);

                                    $CurrentCustomer = $row["CustomerID"];
                                    $CurrentAddress = [];
                                }

                                if ($row["CustomerAddressID"] !== null) {
                                    $CurrentAddress[] = $row;
                                }
                            }

                            if ($CurrentCustomer !== null) {
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