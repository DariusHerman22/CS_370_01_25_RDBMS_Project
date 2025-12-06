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
    echo "<span style='color: red;'>\n";
    echo "<h2>" . $title . "</h2>";
    echo "<h4>" . $error . "</h4>";
    echo "</span>";

}

function VendorHeaderRow(){
    echo "<table class='table table-striped table-bordered'>\n";
    echo "<thead>\n";
    echo "<tr>\n";
    echo"    <th>Vendor Company ID</th>\n";
    echo"    <th>Vendor Company</th>\n";
    echo"    <th>Phone Number</th>\n";
    echo"    <th>Email Address</th>\n";
    echo "</tr>\n";
    echo "</thead>\n";
}

function VendorInfoRow($row) {
    echo "<tr>";
    echo "<td>{$row['VendorCompanyID']}</td>";
    echo "<td>{$row['VendorCompanyName']}</td>";
    echo "<td>{$row['PhoneNumber']}</td>";
    echo "<td>{$row['EmailAddress']}</td>";
    echo "</tr>";
}

function AddressRow($address){
    echo "<tr><td colspan='4'>";

    echo "<table class='table table-striped table-sm table-bordered' 
            style='margin-left:30px; width:95%; table-layout:fixed;'>";

    echo "<thead><tr>";
    echo "<th style='width:120px;'>Address Type</th>";
    echo "<th style='width:200px;'>Street</th>";
    echo "<th style='width:150px;'>City</th>";
    echo "<th style='width:80px;'>State</th>";
    echo "<th style='width:120px;'>Zipcode</th>";
    echo "<th style='width:150px;'>Country</th>";
    echo "</tr></thead>";

    echo "<tbody>";


    foreach ($address as $a) {
        echo "<tr>";
        echo "<td>{$a['AddressType']}</td>";
        echo "<td>{$a['StreetNameNumber']}</td>";
        echo "<td>{$a['City']}</td>";
        echo "<td>{$a['State']}</td>";
        echo "<td>{$a['ZipCode']}</td>";
        echo "<td>{$a['Country']}</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";

    echo "</td></tr>";
}

?>


<?php include_once("Header.php")?>

<h1>Vendor Data Report</h1>
<?php

if($connection_error){
    output_error("Database connection failure", $connection_error_message);
}else{
    $query =
            "SELECT t0.VendorCompanyID, t0.VendorCompanyName, t1.PhoneNumber,
            t1.EmailAddress, t2.VendorAddressID, t2.StreetNameNumber, t2.City,
            t2.State, t2.ZipCode, t2.Country, t2.AddressType"
            . " FROM VendorCompany t0"
            . " LEFT OUTER JOIN VendorContactInfo t1 ON t0.VendorCompanyID = t1.VendorCompanyID"
            . " LEFT OUTER JOIN VendorCompanyAddress t2 ON t0.VendorCompanyID = t2.VendorCompanyID"
    ;

    $result = mysqli_query($con, $query);

    if( !$result ){
        if(mysqli_errno($con)){
            output_error("Data retrieval failure!", mysqli_error($con));
        }
    }else{
        if (mysqli_num_rows($result) === 0) {
            echo "<h2 style='color:red; text-align:center; padding-bottom:615px;' >No Vendor Data Found!</h2>";
            include_once("Footer.php");
            exit;
        }
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

        AddressRow($CurrentAddress);
        echo "</table>";

    }

}

?>
<?php include_once("Footer.php")?>
