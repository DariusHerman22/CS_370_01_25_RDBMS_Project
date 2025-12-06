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

function CartHeaderRow(){
    echo "<table class='table table-striped table-bordered'>\n";
    echo "<thead>\n";
    echo "<tr>\n";
    echo"    <th>Customer ID</th>\n";
    echo"    <th>Shopping Cart ID</th>\n";
    echo "</tr>\n";
    echo "</thead>\n";
}

function CartInfoRow($row) {
    echo "<tr>";
    echo "<td>{$row['CustomerID']}</td>";
    echo "<td>{$row['ShoppingCartID']}</td>";
    echo "</tr>";
}

function DataRow($data){
    echo "<tr><td colspan='4'>";

    echo "<table class='table table-striped table-sm table-bordered' 
            style='margin-left:30px; width:95%; table-layout:fixed;'>";

    echo "<thead><tr>";
    echo "<th style='width:85px; text-align:center;'>Product ID</th>";
    echo "<th style='width:85px; text-align:center;'>Vendor Company ID</th>";
    echo "<th style='width:85px; text-align:center;'>Product Name</th>";
    echo "<th style='width:100px; text-align:center;'>Product Description</th>";
    echo "<th style='width:85px; text-align:center;'>Product Price</th>";
    echo "<th style='width:85px; text-align:center;'>Product Stock</th>";
    echo "<th style='width:85px; text-align:center;'>Product Quantity</th>";
    echo "</tr></thead>";

    echo "<tbody>";


    foreach ($data as $a) {
        echo "<tr>";
        echo "<td>{$a['ProductID']}</td>";
        echo "<td>{$a['VendorCompanyID']}</td>";
        echo "<td>{$a['ProductName']}</td>";
        echo "<td>{$a['ProductDesc']}</td>";
        echo "<td>{$a['ProductPrice']}</td>";
        echo "<td>{$a['ProductStock']}</td>";
        echo "<td>{$a['Quantity']}</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";

    echo "</td></tr>";
}

?>


<?php include_once("Header.php")?>

<h1>Product & Shopping Cart Data Report</h1>
<?php

if($connection_error){
    output_error("Database connection failure", $connection_error_message);
}else{
    $query =
            "SELECT t0.ProductID, t0.VendorCompanyID, t0.ProductName, t0.ProductDesc, 
            t0.ProductPrice, t0.ProductStock, t1.ShoppingCartID, t1.CustomerID, t2.Quantity "
            . " FROM Product t0"
            . " LEFT OUTER JOIN ShoppingCart t1 ON t0.ProductID = t1.ProductID"
            . " LEFT OUTER JOIN ShoppingCartItem t2 ON t1.ShoppingCartID = t2.ShoppingCartID"
    ;

    $result = mysqli_query($con, $query);

    if( !$result ){
        if(mysqli_errno($con)){
            output_error("Data retrieval failure!", mysqli_error($con));
        }
    }else{
        if (mysqli_num_rows($result) === 0) {
            echo "<h2 style='color:red; text-align:center; padding-bottom:615px;' >No Product & Shopping Cart Data Found!</h2>";
            include_once("Footer.php");
            exit;
        }
        CartHeaderRow();

        $CurrentProduct = null;
        $Data = null;

        while($row = $result->fetch_assoc()){

            if ($CurrentProduct !== $row["ProductID"]) {

                if ($CurrentProduct !== null) {
                    DataRow($Data);
                }

                CartInfoRow($row);

                $CurrentProduct = $row["ProductID"];
                $Data = [];
            }

            if ($row["ProductID"] !== null) {
                $Data[] = $row;
            }

        }

        DataRow($Data);
        echo "</table>";

    }

}

?>
<?php include_once("Footer.php")?>
