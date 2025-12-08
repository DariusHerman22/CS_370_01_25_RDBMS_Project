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

function CartHeaderRow(){
    echo "<div class='table-responsive'>";
    echo "<table class='table table-hover align-middle border text-dark'>";
    echo "<thead class='text-dark border-bottom border-2'>";
    echo "<tr>\n";
    echo"    <th class='py-3'>Customer ID</th>\n";
    echo"    <th class='py-3'>Shopping Cart ID</th>\n";
    echo "</tr>\n";
    echo "</thead>\n";
    echo "<tbody>";
}

function CartInfoRow($row) {
    echo "<tr class='fw-semibold'>";
    $custId = isset($row['CustomerID']) ? "#" . $row['CustomerID'] : "<span class='text-muted fw-normal'>Not in cart</span>";
    $cartId = isset($row['ShoppingCartID']) ? "#" . $row['ShoppingCartID'] : "<span class='text-muted fw-normal'>-</span>";

    echo "<td>" . $custId . "</td>";
    echo "<td>" . $cartId . "</td>";
    echo "</tr>";
}

function DataRow($data){
    echo "<tr><td colspan='2' class='p-0 border-0'>";

    echo "<div class='bg-light p-4 border-bottom'>";
    echo "<h6 class='text-theme fw-bold mb-3'><i class='bi bi-box-seam'></i> Product Details</h6>";

    echo "<table class='table table-sm table-bordered bg-white mb-0 shadow-sm text-dark' style='font-size: 0.9em;'>";
    echo "<thead class='table-light text-dark'><tr>";
    echo "<th style='width:85px;' >ID</th>";
    echo "<th style='width:85px;' >Vendor ID</th>";
    echo "<th style='width:85px;' >Name</th>";
    echo "<th style='width:85px;' >Description</th>";
    echo "<th style='width:85px;' >Price</th>";
    echo "<th style='width:85px;' >Stock</th>";
    echo "<th style='width:85px;' >Quantity in Cart</th>";
    echo "</tr></thead>";
    echo "<tbody>";

    foreach ($data as $a) {
        echo "<tr>";
        echo "<td>" . $a['ProductID'] . "</td>";
        echo "<td>" . $a['VendorCompanyID'] . "</td>";
        echo "<td><span class='fw-bold text-theme'>" . $a['ProductName'] . "</span></td>";
        echo "<td>" . $a['ProductDesc'] . "</td>";
        echo "<td>$" . number_format((float)$a['ProductPrice'], 2) . "</td>";
        echo "<td>" . $a['ProductStock'] . "</td>";
        echo "<td>" . ($a['Quantity'] ? $a['Quantity'] : "-") . "</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";

    echo "</td></tr>";
}
?>

<?php include_once("Header.php")?>

    <style>
        :root {
            --theme-color: #0d6efd;
        }
        .text-theme { color: var(--theme-color) !important; }
        .border-theme { border-color: var(--theme-color) !important; }
        .btn-theme { background-color: var(--theme-color); color: white; border: none; }
        .btn-theme:hover { background-color: #0b5ed7; color: white; }
    </style>

    <div class="container my-5">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white py-4 border-top border-4 border-theme d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0 fw-bold text-dark">Product & Shopping Cart Report</h2>
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
                            "SELECT t0.ProductID, t0.VendorCompanyID, t0.ProductName, t0.ProductDesc, 
            t0.ProductPrice, t0.ProductStock, t1.ShoppingCartID, t1.CustomerID, t2.Quantity "
                            . " FROM Product t0"
                            . " LEFT OUTER JOIN ShoppingCartItem t2 ON t0.ProductID = t2.ProductID"
                            . " LEFT OUTER JOIN ShoppingCart t1 ON t2.ShoppingCartID = t1.ShoppingCartID"
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
                            echo "<h4 class='text-muted'>No Product & Cart Data Found</h4>";
                            echo "<p class='text-muted'>Please run the Import tool to add products.</p>";
                            echo "</div>";
                        }
                        else {
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