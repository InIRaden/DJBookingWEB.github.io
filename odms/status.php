<?php session_start();
//error_reporting(0);
include('includes/dbconnection.php');

  

?>
<!DOCTYPE html>
<html>
<head>
<title>Online DJ Management System || Request Status</title>
<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
<!-- Custom Theme files -->
<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
<link rel="stylesheet" href="css/touchTouch.css" type="text/css" media="all" />
<!-- Custom Theme files -->
<script src="js/jquery.min.js"></script>

<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!--webfont-->
<link href='http://fonts.googleapis.com/css?family=Monoton' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>
<!---//End-css-style-switecher----->
<script type="text/javascript" src="js/jquery.fancybox.js"></script>
		<link rel="stylesheet" type="text/css" href="css/jquery.fancybox.css" media="screen" />
	   <script type="text/javascript">
			$(document).ready(function() {
				/*
				 *  Simple image gallery. Uses default settings
				 */

				$('.fancybox').fancybox();

			});
		</script>

</head>
<body>
<?php include_once('includes/header.php');?>
<div class="contact content">
	 <div class="container"> 		 
		 <ol class="breadcrumb">
		  <li><a href="index.php">Home</a></li>
		  <li class="active">Request Status</li>	  
		 </ol>

		 <h2>Request Status</h2>
		 <div class="contact-main">


			 <div class="contact-grids">
				 <div class="col-md-6 contact-left">
					 <form method="post">
						 <ul>
			<li class="text-info">Name : </li>
							 <li><input type="text" class="text" name="name" required="true" ></li>
						 </ul>					 				 
			
						 <ul>
							 <li class="text-info">Mobile Number : </li>
							 <li><input type="text" class="text" name="mobnum" required="true" maxlength="10" pattern="[0-9]+"></li>
						 </ul>					 
									
						 <input type="submit" name="submit" value="Submit">					 
					 </form>
				 </div>
			<br />
 <div class="clearfix"></div>
		

<?php if(isset($_POST['submit'])){
$mno=$_POST['mobnum'];
$fname=$_POST['name'];
$sql="SELECT tblbooking.ID,tblbooking.BookingID,tblbooking.Name,tblbooking.MobileNumber,tblbooking.Email,tblbooking.EventDate,tblbooking.EventStartingtime,tblbooking.EventEndingtime,tblbooking.VenueAddress,tblbooking.EventType,tblbooking.AdditionalInformation,tblbooking.BookingDate,tblbooking.Remark,tblbooking.Status,tblbooking.UpdationDate,tblservice.ServiceName,tblservice.SerDes,tblservice.ServicePrice from tblbooking join tblservice on tblbooking.ServiceID=tblservice.ID  where tblbooking.MobileNumber=:mno and tblbooking.Name=:fname";
$query = $dbh -> prepare($sql);
$query-> bindParam(':mno', $mno, PDO::PARAM_STR);
$query-> bindParam(':fname', $fname, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{ ?>
 <table border="1" class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
<tr>
<th>Booking Number</th>
<th>Client Name</th>
<th>Mobile Number</th>
 <th>Email</th>
 <th>Action</th>
</tr>
<?php	
foreach($results as $row)
{               ?>
                           
<tr>
    <td style="color:#fff !important"><?php  echo $row->BookingID;?></td> 
    <td style="color:#fff !important"><?php  echo $row->Name;?></td>
    <td style="color:#fff !important"><?php  echo $row->MobileNumber;?></td>
    <td style="color:#fff !important"><?php  echo $row->Email;?></td>
    <td><a href="request-details.php?bid=<?php echo htmlentities ($row->ID);?>&&bookingid=<?php echo htmlentities ($row->BookingID);?>" class="btn btn-info">View Details</a></td>
  </tr>


  
 
<?php $cnt=$cnt+1;} ?>
</table> 
 <?php }else { ?>
 	<hr />
<h3 style="color:red">No Record found</h3>
<?php } ?>


<?php } ?>

		

			 </div>
		 </div>
		<?php include_once('includes/footer.php');?>
	 </div>
</div>
<!---->

<!---->
</body>
</html>