<?php

    include 'config/db_connect.php';

    // Check GET request ID parameter
    if(isset($_GET['supplier_id']) ) {
        
        // Escape SQL characters
        $supplier_id = mysqli_real_escape_string($conn, $_GET['supplier_id']);
        // Make SQL
        $sql = "SELECT * FROM supplier WHERE supplier_id = $supplier_id";
        // Get the query result
        $result = mysqli_query($conn, $sql);
        // Fetch result in array format
        $supplier = mysqli_fetch_assoc($result);

        mysqli_free_result($result);
        mysqli_close($conn);
        
    }

    $errors = array('supplier-name' => '', 'address' => '', 'tin' => '', 'products' => '');

    // POST check
    if(isset($_POST['update'] ) ) {

        // Check supplier Name
        if(empty($_POST['supplier-name']) ) {
            $errors['supplier-name'] = 'A supplier name is required. <br />';
        } else {
            // echo htmlspecialchars($_POST['supplier-name']);
            $supplierName = $_POST['supplier-name'];
        }

        // // Check Address
        if(empty($_POST['address']) ) {
            $errors['address'] = 'An address is required. <br />';
        } else {
            // echo htmlspecialchars($_POST['address']);
            $address = $_POST['address'];
        }

        // // Check TIN Number
        if(empty($_POST['tin']) ) {
            $errors['tin'] = 'A TIN number is required. <br />';
        } else {
            // echo htmlspecialchars($_POST['tin']);
            $tin = $_POST['tin'];
            if (!preg_match('^(\d{9}|\d{12})$^', $tin) ) {
                $errors['tin'] = 'A TIN number is should be at least 9 digits and no more than 12 digits.';
            }
        }

        // // Check Products
        if(empty($_POST['products']) ) {
            $errors['products'] = 'At least one product is required. <br />';
        } else {
            // echo htmlspecialchars($_POST['product']);
            $products = $_POST['products'];
            if(!preg_match('/^([a-zA-Z\s]+)(,\s*[a-zA-Z\s]*)*$/', $products) ) {
                $errors['products'] = 'Products must be a comma separated list.';
            }
        }

        // Page Redirect
        if(! array_filter($errors) ) {

            // reassign variables to prevent sql injection
            $supplierName = mysqli_real_escape_string($conn, $_POST['supplier-name']);
            $address = mysqli_real_escape_string($conn, $_POST['address']);
            $tin = mysqli_real_escape_string($conn, $_POST['tin']);
            $products = mysqli_real_escape_string($conn, $_POST['products']);

            $id_to_update = mysqli_real_escape_string($conn, $_POST['id_to_update']);

            $sql = "UPDATE supplier
                    SET supplier_name = '$supplierName', supplier_address = '$address', supplier_tin = '$tin', supplier_prod = '$products'
                    WHERE supplier_id = $id_to_update";

            // Save to DB and check
            if(mysqli_query($conn, $sql)) {
                header('Location: suppliers.php');
                exit;
            } else {
                echo 'Query error: ' . mysqli_error($conn);
            }
        }
    }
    // End of POST check

?>

<!DOCTYPE html>
<html lang="en">
    
<?php require 'templates/header.php'?>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <?php include 'templates/sidebar.php'?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <?php include 'templates/topbar.php'?>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Suppliers</h1>
                    <p class="mb-4"></p>

                    <!-- Edit Supplier Form-->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Edit Supplier</h6>
                        </div>
                        <div class="card-body">
                            <?php if($supplier): ?>
                                <form class="needs-validation" action="edit-supplier.php" method="POST">
                                    <div class="mb-3">
                                        <label for="inputsupplierName" class="form-label">Supplier Name *</label>
                                        <input type="text" class="form-control" name="supplier-name" id="supplier-name" value="<?php echo htmlspecialchars($supplier['supplier_name']) ?>">
                                        <div class="mt-2 text-danger"> <?php echo $errors['supplier-name'] ?></div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="inputAddress" class="form-label">Address *</label>
                                        <input type="text" class="form-control" name="address" id="address" value="<?php echo htmlspecialchars($supplier['supplier_address']) ?>">
                                        <div class="mt-2 text-danger"> <?php echo $errors['address'] ?></div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="inputTIN" class="form-label">TIN Number *</label>
                                        <input type="text" class="form-control" name="tin" id="tin" value="<?php echo htmlspecialchars($supplier['supplier_tin']) ?>">
                                        <div class="mt-2 text-danger"> <?php echo $errors['tin'] ?></div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="inputProducts" class="form-label">Products (comma separated) *</label>
                                        <input type="text" class="form-control" name="products" id="products" value="<?php echo htmlspecialchars($supplier['supplier_prod']) ?>">
                                        <div class="mt-2 text-danger"> <?php echo $errors['products'] ?></div>
                                    </div>
                                    
                                    <hr class="hr" />

                                    <div class="mb-3">
                                        <a class="btn btn-secondary" href="suppliers.php">Cancel</a>
                                        <form action="edit-supplier.php" method="POST" class="mr-1">
                                            <input type="hidden" name="id_to_update" value="<?php echo $supplier['supplier_id'] ?>">
                                            <input type="submit" name="update" value="Update" class="btn btn-success">
                                        </form>
                                    </div>
                                </form>
                            <?php else: ?>
                            <?php endif ?>
                        </div>
                    </div>
                    <!-- End of Edit Supplier Form-->

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <?php include 'templates/footer.php'?>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <?php include 'templates/scroll-to-top.php'?>
    <?php require 'templates/logout-modal.php'?>
    <?php require 'templates/plugins.php'?>

</body>

</html>