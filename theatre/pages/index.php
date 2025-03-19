<?php
include('header.php');
?>
<!-- =============================================== -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Theatre Assistance
    </h1>
    <ol class="breadcrumb">
      <li><a href="index"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Home</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">

    <!-- Default box -->
    <div class="box">
      <div class="box-body">

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Running Movies</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body no-padding">
            <table class="table table-condensed">
              <tr>
                <th class="col-md-1">No</th>
                <th class="col-md-3">Show Time</th>
                <th class="col-md-4">Screen</th>
                <th class="col-md-4">Movie</th>
              </tr>
              <?php 
              $qry8 = mysqli_query($con, "SELECT * FROM tbl_shows WHERE r_status=1 AND theatre_id='" . $_SESSION['theatre'] . "'");
              $no = 1;
              while ($mn = mysqli_fetch_array($qry8)) {
                // Fetch movie details
                $qry9 = mysqli_query($con, "SELECT * FROM tbl_movie WHERE movie_id='" . $mn['movie_id'] . "'");
                $mov = mysqli_fetch_array($qry9);

                // Fetch show time details
                $qry10 = mysqli_query($con, "SELECT * FROM tbl_show_time WHERE st_id='" . $mn['st_id'] . "'");
                $scr = mysqli_fetch_array($qry10);

                // Fetch screen details
                $qry11 = mysqli_query($con, "SELECT * FROM tbl_screens WHERE screen_id='" . $scr['screen_id'] . "'");
                $scn = mysqli_fetch_array($qry11);

                // Ensure the fetched data is not null before accessing
                if ($mov && $scr && $scn) {
              ?>
              <tr>
                <td><?php echo $no; ?></td>
                <td><span class="badge bg-green"><?php echo htmlspecialchars($scn['screen_name']); ?></span></td>
                <td><span class="badge bg-light-blue"><?php echo htmlspecialchars($scr['start_time']); ?> (<?php echo htmlspecialchars($scr['name']); ?>)</span></td>
                <td><?php echo htmlspecialchars($mov['movie_name']); ?></td>
              </tr>
              <?php
                  $no++;
                }
              }
              ?>
            </table>
          </div>
          <!-- /.box-body -->
        </div>

      </div>
      <!-- /.box-footer-->
    </div>
    <!-- /.box -->

  </section>
  <!-- /.content -->
</div>
<?php
include('footer.php');
?>  