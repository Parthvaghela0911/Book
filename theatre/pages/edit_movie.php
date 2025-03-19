<?php
ob_start(); // Start output buffering

include('header.php');
include('../../form.php');
$frm = new formBuilder;

// Connect to your database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "movietheatredb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_id = $_POST['movie_id'] ?? 0;
    $name = $conn->real_escape_string($_POST['name']);
    $cast = $conn->real_escape_string($_POST['cast']);
    $desc = $conn->real_escape_string($_POST['desc']);
    $rdate = $conn->real_escape_string($_POST['rdate']);
    $video = $conn->real_escape_string($_POST['video']);
    $existing_image = $_POST['existing_image']; // Preserve the existing image if no new image is uploaded

    // Handle file upload
    $image = $existing_image;
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "C:/wamp64/www/BookYourShow/images/";
        $target_file = $target_dir . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = $_FILES['image']['name'];
        } else {
            echo "<div class='alert alert-danger'>Failed to upload image.</div>";
        }
    }

    // Prepare the SQL query to update the movie details
    $sql = "UPDATE tbl_movie SET
            movie_name = '$name',
            cast = '$cast',
            `desc` = '$desc',
            release_date = '$rdate',
            image = '$image',
            video_url = '$video'
            WHERE movie_id = $movie_id";

    if ($conn->query($sql) === TRUE) {
        // Redirect to view_movie.php after successful update
        header("Location: view_movie.php?id=" . $movie_id);
        ob_end_flush(); // Flush the output buffer
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error updating movie: " . $conn->error . "</div>";
    }
}

// Fetch movie details from the database using the movie ID passed in the URL
$movie_id = $_GET['id'] ?? 0; // Get movie ID from URL, default to 0 if not set
$movie = null;

if ($movie_id) {
    $sql = "SELECT * FROM tbl_movie WHERE movie_id = $movie_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $movie = $result->fetch_assoc();
    }
}

$conn->close();

if (!$movie) {
    echo "<div class='alert alert-danger'>Movie not found!</div>";
    exit;
}
?>

<link rel="stylesheet" href="../../validation/dist/css/bootstrapValidator.css"/>
<script type="text/javascript" src="../../validation/dist/js/bootstrapValidator.js"></script>

<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Edit Movie</h1>
        <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-home"></i> Home</a></li>
            <li class="active">Edit Movie</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box">
            <div class="box-body">
                <?php include('../../msgbox.php'); ?>
                <form action="edit_movie.php?id=<?php echo $movie_id; ?>" method="post" enctype="multipart/form-data" id="form1">
                    <!-- Include hidden input for movie ID and existing image -->
                    <input type="hidden" name="movie_id" value="<?php echo $movie['movie_id']; ?>" />
                    <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($movie['image']); ?>" />
                    
                    <div class="form-group">
                        <label class="control-label">Movie Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($movie['movie_name']); ?>" />
                        <?php $frm->validate("name", array("required", "label" => "Movie Name")); ?>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Cast</label>
                        <input type="text" name="cast" class="form-control" value="<?php echo htmlspecialchars($movie['cast']); ?>" />
                        <?php $frm->validate("cast", array("required", "label" => "Cast", "regexp" => "text")); ?>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Description</label>
                        <textarea name="desc" class="form-control"><?php echo htmlspecialchars($movie['desc']); ?></textarea>
                        <?php $frm->validate("desc", array("required", "label" => "Description")); ?>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Release Date</label>
                        <input type="date" name="rdate" class="form-control" value="<?php echo $movie['release_date']; ?>" />
                        <?php $frm->validate("rdate", array("required", "label" => "Release Date")); ?>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Image</label>
                        <input type="file" name="image" class="form-control" />
                        <?php $frm->validate("image", array("label" => "Image")); ?>
                        <!-- Show current image if available -->
                        <?php if (!empty($movie['image'])): ?>
                            <img src="../../images/<?php echo htmlspecialchars($movie['image']); ?>" alt="Current Image" style="max-width: 50px; margin-top: 10px;">
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Trailer Youtube Link</label>
                        <input type="text" name="video" class="form-control" value="<?php echo htmlspecialchars($movie['video_url']); ?>" />
                        <?php $frm->validate("video", array("label" => "Trailer Youtube Link", "max" => "500")); ?>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Update Movie</button>
                        <a href="view_movie.php?id=<?php echo $movie_id; ?>" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->
</div>

<?php include('footer.php'); ?>

<script>
    <?php $frm->applyvalidations("form1"); ?>
</script>

<?php
ob_end_flush(); // End output buffering
?>
