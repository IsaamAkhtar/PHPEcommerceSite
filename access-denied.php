<?php
session_start();
include 'includes/header.php';
include 'includes/nav.php';
?>
<div id="main">
	<div class="container">
	<div class="alert alert-danger">
		<h4>Access Denied!</h4>
	  	<p>You do not have access to this page. Either you are not <a href="customerLogin.php">logged in</a> or you do not have the privileges needed for vieweing the requested page.</p>
	</div>
	
</div>
	</div>
<?php
include 'includes/footer.php';
?>