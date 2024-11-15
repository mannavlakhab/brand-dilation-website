
<?php 

include('../db_connect.php');
    
if (!isset($_GET['order_id'])) {
	echo "No order ID or tracking ID provided.";
	exit();
}
// Retrieve tracking_id and pd (payment method) from the query parameter
$order_id = intval($_GET['order_id']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="refresh" content="8; url=../profile.php?page=orders">
    <title>payment successful </title>
</head>
<body>
<audio autoplay>
  <source src="../assets/audio/print.ogg" type="audio/ogg">
  <source src="../assets/audio/print.mp3" type="audio/mpeg">
  Your browser does not support the audio element.
</audio>
	<div class="container">
		<div class="printer-top"></div>
		  
		<div class="paper-container">
		  <div class="printer-bottom"></div>
	  
		  <div class="paper">
			<div class="main-contents">
			  <div class="success-icon">&#10004;</div>
			  <div class="success-title">
				Payment Complete
			  </div>
			  <div class="success-description">
				Payment for your order has been successfully processed. Now no worry about your order your order will deliver soon.
			  </div>
			  <div class="order-details">
				<div class="order-number-label">Order Number</div>
				<div class="order-number"><?php echo $order_id; ?></div>
			  </div>
			  <div class="order-footer">Thank you!</div>
			</div>
			<div class="jagged-edge"></div>
		  </div>
		</div>
	  </div>

<style>

@import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');

body {
    font-family: Montserrat;
	 background: #b9b9b9;
}
 .container {
	 max-width: 380px;
	 margin: 30px auto;
	 overflow: hidden;
}
 .printer-top {
	 z-index: 1;
	 border: 6px solid #666;
	 height: 6px;
	 border-bottom: 0;
	 border-radius: 6px 6px 0 0;
	 background: #333;
}
 .printer-bottom {
	 z-index: 0;
	 border: 6px solid #666;
	 height: 6px;
	 border-top: 0;
	 border-radius: 0 0 6px 6px;
	 background: #333;
}
 .paper-container {
	 position: relative;
	 overflow: hidden;
	 height: 467px;
}
 .paper {
	 background: #fff;
	 height: 447px;
	 position: absolute;
	 z-index: 2;
	 margin: 0 12px;
	 margin-top: -12px;
	 animation: print 4800ms cubic-bezier(0.68, -0.55, 0.265, 0.9) ;
	 -moz-animation: print 4800ms cubic-bezier(0.68, -0.55, 0.265, 0.9);
}
 .main-contents {
	 margin: 0 12px;
	 padding: 24px;
}
 .jagged-edge {
	 position: relative;
	 height: 20px;
	 width: 100%;
	 top: -5px;
}
.jagged-edge:after {
	 content: "";
	 display: block;
	 position: absolute;
	 left: 0;
	 top: -5px;
	 right: 0;
	 height: 20px;
	 background: linear-gradient(45deg, transparent 33.333%, #fff 33.333%, #fff 66.667%, transparent 66.667%), linear-gradient(-45deg, transparent 33.333%, #fff 33.333%, #fff 66.667%, transparent 66.667%);
	 background-size: 16px 40px;
	 background-position: 0 -20px;
}
 
 .success-icon {
	 text-align: center;
	 font-size: 48px;
	 height: 72px;
	 background: #359d00;
	 border-radius: 50%;
	 width: 72px;
	 height: 72px;
	 margin: 16px auto;
	 color: #fff;
}
 .success-title {
	 font-size: 22px;
	 text-align: center;
	 color: #666;
	 font-weight: bold;
	 margin-bottom: 16px;
}
 .success-description {
	 font-size: 15px;
	 line-height: 21px;
	 color: #999;
	 text-align: center;
	 margin-bottom: 24px;
}
 .order-details {
	 text-align: center;
	 color: #333;
	 font-weight: bold;
}
 .order-details .order-number-label {
	 font-size: 18px;
	 margin-bottom: 8px;
}
 .order-details .order-number {
	 border-top: 1px solid #ccc;
	 border-bottom: 1px solid #ccc;
	 line-height: 48px;
	 font-size: 48px;
	 padding: 8px 0;
	 margin-bottom: 24px;
}
 .order-footer {
	 text-align: center;
	 line-height: 18px;
	 font-size: 18px;
	 margin-bottom: 8px;
	 font-weight: bold;
	 color: #999;
}
 @keyframes print {
	 0% {
		 transform: translateY(-90%);
	}
	 100% {
		 transform: translateY(0%);
	}
}
 @-webkit-keyframes print {
	 0% {
		 -webkit-transform: translateY(-90%);
	}
	 100% {
		 -webkit-transform: translateY(0%);
	}
}
 @-moz-keyframes print {
	 0% {
		 -moz-transform: translateY(-90%);
	}
	 100% {
		 -moz-transform: translateY(0%);
	}
}
 @-ms-keyframes print {
	 0% {
		 -ms-transform: translateY(-90%);
	}
	 100% {
		 -ms-transform: translateY(0%);
	}
}
 
</style>
</body>
</html>