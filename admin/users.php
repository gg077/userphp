<?php
require_once("includes/header.php");
require_once("includes/sidebar.php");
require_once("includes/content-top2.php");

if(isset($_GET['delete'])){
    $user = User::find_user_by_id($_GET['delete']);
    if($user){
        $user->delete();
        echo "<script>window.location.href='users.php';</script>";
        exit;
    }
}


?>

    <div id="main" class="m-0">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h3>DataTable</h3>
                        <p class="text-subtitle text-muted">A sortable, searchable, paginated table without dependencies thanks to simple-datatables.</p>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">DataTable</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            Simple Datatable
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped" id="table1">
                            <thead>
                            <tr>
                                <th>nr</th>
                                <th>Email</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $users = User::find_all_users(); ?>
                            <?php foreach($users as $user):?>
                                <tr>
                                    <td><?= $user->id; ?></td>
                                    <td style="width: 350px;"><span><img height="40" width="40" class="avatar me-3" src="../admin/assets/static/images/faces/8.jpg" alt=""></span><?= $user->email; ?></td>
                                    <td><?= $user->first_name;?></td>
                                    <td><?= $user->last_name;?></td>
                                    <td style="width: 250px;"><?= $user->username; ?></td>
                                    <td><?= $user->password; ?></td>
                                    <td class="d-flex justify-content-around">
                                        <a href="users.php?delete=<?php echo $user->id; ?>" onclick="return confirm("weet je zeker dat je deze gebruiker wil verwijderen?")">
                                        <i class="bi bi-trash text-danger"></i>
                                        </a>
                                        <a href="edit_user.php?id=<?php echo $user->id; ?>">
                                            <i class="bi bi-eye text-primary"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </section>
        </div>

        <footer>
            <div class="footer clearfix mb-0 text-muted">
                <div class="float-start">
                    <p>2023 &copy; Mazer</p>
                </div>
                <div class="float-end">
                    <p>Crafted with <span class="text-danger"><i class="bi bi-heart-fill icon-mid"></i></span>
                        by <a href="https://saugi.me">Saugi</a></p>
                </div>
            </div>
        </footer>
    </div>
</div>
<script src="assets/static/js/components/dark.js"></script>
<script src="assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>


<script src="assets/compiled/js/app.js"></script>



<script src="assets/extensions/simple-datatables/umd/simple-datatables.js"></script>
<script src="assets/static/js/pages/simple-datatables.js"></script>

</body>

</html>