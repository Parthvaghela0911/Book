<?php include('header.php');
if(!isset($_SESSION['user']))
{
	header('location:login.php');
}
$qry2=mysqli_query($con,"select * from tbl_movie where movie_id='".$_SESSION['movie']."'");
$movie=mysqli_fetch_array($qry2);
?>
<div class="content">
	<div class="wrap">
		<div class="content-top">
			<div class="section group">
				<div class="about span_1_of_2">	
					<h3><?php echo $movie['movie_name']; ?></h3>	
					<div class="about-top">	
						<div class="grid images_3_of_2">
							<img src="<?php echo $movie['image']; ?>" alt=""/>
						</div>
						<div class="desc span_3_of_2">
							<p class="p-link" style="font-size:15px"><b>Cast : </b><?php echo $movie['cast']; ?></p>
							<p class="p-link" style="font-size:15px"><b>Release Date : </b><?php echo date('d-M-Y',strtotime($movie['release_date'])); ?></p>
							<p style="font-size:15px"><?php echo $movie['desc']; ?></p>
							<a href="<?php echo $movie['video_url']; ?>" target="_blank" class="watch_but">Watch Trailer</a>
						</div>
						<div class="clear"></div>
					</div>
					<table class="table table-hover table-bordered text-center">
					<?php
						$s=mysqli_query($con,"select * from tbl_shows where s_id='".$_SESSION['show']."'");
						$shw=mysqli_fetch_array($s);
						
						$t=mysqli_query($con,"select * from tbl_theatre where id='".$shw['theatre_id']."'");
						$theatre=mysqli_fetch_array($t);
					?>
					<tr>
						<td class="col-md-6">
							Theatre
						</td>
						<td>
							<?php echo $theatre['name'].", ".$theatre['place'];?>
						</td>
					</tr>
					<tr>
						<td>
							Screen
						</td>
						<td>
							<?php 
								$ttm=mysqli_query($con,"select  * from tbl_show_time where st_id='".$shw['st_id']."'");
								$ttme=mysqli_fetch_array($ttm);
								$sn=mysqli_query($con,"select  * from tbl_screens where screen_id='".$ttme['screen_id']."'");
								$screen=mysqli_fetch_array($sn);
								echo $screen['screen_name'];
							?>
						</td>
					</tr>
					<tr>
						<td>
							Date
						</td>
						<td>
							<?php 
							if(isset($_GET['date']))
							{
								$date=$_GET['date'];
							}
							else
							{
								if($shw['start_date']>date('Y-m-d'))
								{
									$date=date('Y-m-d',strtotime($shw['start_date'] . "-1 days"));
								}
								else
								{
									$date=date('Y-m-d');
								}
								$_SESSION['dd']=$date;
							}
							?>
							<div class="col-md-12 text-center" style="padding-bottom:20px">
								<?php if($date>$_SESSION['dd']){?><a href="booking.php?date=<?php echo date('Y-m-d',strtotime($date . "-1 days"));?>"><button class="btn btn-default"><i class="glyphicon glyphicon-chevron-left"></i></button></a> <?php } ?><span style="cursor:default" class="btn btn-default"><?php echo date('d-M-Y',strtotime($date));?></span>
								<?php if($date!=date('Y-m-d',strtotime($_SESSION['dd'] . "+4 days"))){?>
								<a href="booking.php?date=<?php echo date('Y-m-d',strtotime($date . "+1 days"));?>"><button class="btn btn-default"><i class="glyphicon glyphicon-chevron-right"></i></button></a>
								<?php }
								$av=mysqli_query($con,"select sum(no_seats) from tbl_bookings where show_id='".$_SESSION['show']."' and ticket_date='$date'");
								$avl=mysqli_fetch_array($av);
								?>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							Show Time
						</td>
						<td>
							<?php echo date('h:i A',strtotime($ttme['start_time']))." ".$ttme['name'];?> Show
						</td>
					</tr>
					<tr>
						<td>
							Select Seats
						</td>
						<td>
							<?php
							$totalSeats = $screen['seats']; // Total seats in the screen
							$bookedSeats = [];

							// Fetch booked seats from the database
							$bookedSeatsQuery = mysqli_query($con, "SELECT seat_numbers FROM tbl_bookings WHERE show_id='" . $_SESSION['show'] . "' AND ticket_date='$date'");
							while ($row = mysqli_fetch_assoc($bookedSeatsQuery)) {
								$bookedSeats = array_merge($bookedSeats, explode(',', $row['seat_numbers']));
							}

							$availableSeats = $totalSeats - count($bookedSeats); // Calculate available seats
							?>
							<?php if ($availableSeats <= 0): // Check if all seats are booked ?>
								<div class="alert alert-danger text-center" style="font-weight:bold;">Housefull</div>
							<?php else: ?>
								<form action="process_booking.php" method="post" id="bookingForm"> <!-- Redirect to process_booking.php -->
									<input type="hidden" name="screen" value="<?php echo $screen['screen_id']; ?>"/>
									<input type="hidden" name="amount" id="hm" value="0"/>
									<input type="hidden" name="date" value="<?php echo $date; ?>"/>
									<style>
										.seat {
											display: inline-block;
											width: 30px;
											height: 30px;
											border-radius: 5px;
											margin: 5px;
											text-align: center;
											line-height: 30px;
											font-size: 14px;
											font-weight: bold;
											cursor: pointer;
											border: 1px solid #ccc;
										}
										.seat.available {
											background-color: #ffffff; /* White for unbooked seats */
											color: black;
											border: 2px solid #28a745;
										}
										.seat.booked {
											background-color:rgba(194, 200, 205, 0.89); /* Gray for booked seats */
											color: white;
											cursor: not-allowed;
										}
										.seat.selected {
											background-color: #28a745; /* Green for selected seats */
											color: white;
											
										}
										.seat-container {
											display: flex;
											flex-wrap: wrap;
											justify-content: center;
											max-width: 350px;
											margin: 0 auto;
										}
									</style>
									<div class="screen">SCREEN</div> 
									<div class="seat-container">
										<?php
										$columns = 10; // Number of seats per row
										for ($i = 1; $i <= $totalSeats; $i++) {
											$isBooked = in_array($i, $bookedSeats);
											$seatClass = $isBooked ? 'seat booked' : 'seat available';
											$disabled = $isBooked ? 'disabled' : '';
											echo "<label class='$seatClass' id='seat-$i'>
													<input type='checkbox' name='seats[]' value='$i' $disabled style='display:none;'/>
													<span>$i</span>
												  </label>";
											if ($i % $columns == 0) {
												echo "<br/>";
											}
										}
										?>
									</div>
									<button type="submit" class="btn btn-info" style="width:100%" id="bookNow" disabled>Book Now</button>
								</form>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td>
							Amount
						</td>
						<td id="amount" style="font-weight:bold;font-size:18px">
							Rs 0
						</td>
					</tr>
					<script type="text/javascript">
						// Update amount dynamically based on selected seats
						const seatCharge = <?php echo $screen['charge']; ?>;
						const seats = document.querySelectorAll('.seat.available input');
						const amountElement = document.getElementById('amount');
						const hiddenAmount = document.getElementById('hm');
						const bookNowButton = document.getElementById('bookNow');

						seats.forEach(seat => {
							seat.addEventListener('change', () => {
								const selectedSeats = Array.from(document.querySelectorAll('.seat.available input:checked')).map(seat => seat.value);
								const totalAmount = selectedSeats.length * seatCharge;

								// Update amount and enable/disable the Book Now button
								amountElement.textContent = "Rs " + totalAmount;
								hiddenAmount.value = totalAmount;
								bookNowButton.disabled = selectedSeats.length === 0;
							});
						});
					</script>
					<script type="text/javascript">
						// Highlight selected seats in a different color
						const availableSeats = document.querySelectorAll('.seat.available input');
						availableSeats.forEach(seat => {
							seat.addEventListener('change', () => {
								const parentLabel = seat.parentElement;
								if (seat.checked) {
									parentLabel.classList.add('selected'); // Add green color for selected seats
								} else {
									parentLabel.classList.remove('selected'); // Revert to white for unselected seats
								}
							});
						});
					</script>
					<table>
						<tr>
							<td></td>
						</tr>
					</table>
				</div>			
				<?php include('movie_sidebar.php');?>
			</div>
			<div class="clear"></div>		
		</div>
	</div>
</div>
<?php include('footer.php');?>
