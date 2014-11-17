<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="assets/ico/favicon.png">

    <title>FriendFeed - просматривайте ленту друзей</title>

    <link href="http://192.168.1.141/assets/css/hover_pack.css" rel="stylesheet">

    <!-- Bootstrap core CSS -->
    <link href="http://192.168.1.141/assets/css/bootstrap.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700' rel='stylesheet' type='text/css'> 
    <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>
    <!-- Custom styles for this template -->
    <link href="http://192.168.1.141/assets/css/main404.css" rel="stylesheet">
    <link href="http://192.168.1.141/assets/css/colors/color-74c9be.css" rel="stylesheet">    
    <link href="http://192.168.1.141/assets/css/animations.css" rel="stylesheet">
    <link href="http://192.168.1.141/assets/css/font-awesome.min.css" rel="stylesheet">
      <link href='http://fonts.googleapis.com/css?family=Kaushan+Script' rel='stylesheet' type='text/css'>
   
    
    
    <!-- Main Jquery & Hover Effects. Should load first -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="http://192.168.1.141/assets/js/hover_pack.js"></script>
    

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>

  <! ========== HEADERWRAP ==================================================================================================== 
	=============================================================================================================================>
    <div id="headerwrap">    	 
    
    	<div class="container">
			<div class="row centered">
				<div class="col-md-8 col-lg-8 col-lg-offset-2 col-md-offset-2 mt">
						<br><br><br><br>
					<center><font class="headertext3">&nbsp;&nbsp;<b>404 :( </b></font></center><br><center><font class="headertext1">&nbsp;&nbsp;<b>Страница не найдена</b></font></center><br><br>
    				<p class="mt"><button type="button" class="btn btn-cta btn-lg" onclick="window.location.href='http://192.168.1.141/profile'"><span class="glyphicon glyphicon-ok"></span>&nbsp;<b>Вернуться</b></button></p>
				</div>
				
			</div><!-- /row -->
    	</div><!-- /container -->
    </div> <!-- /headerwrap -->



			

		

	
	
	<! ========== FOOTER ======================================================================================================== 
	=============================================================================================================================>    
	
	<div id="f">
		<div class="container">
			<div class="row">
				<!-- ADDRESS -->
				<div class="col-lg-6 footerhop">
					
				<p><font class="headertext1"><span class="glyphicon glyphicon-th-list brandimg"></span>&nbsp;&nbsp;<b>FriendFeed</b></font></p>
				<p><font class="newfootertextbrand">Просмотр ленты друзей</font></p>
		

				</div><! --/col-lg-3 -->
				
				<!-- TWEETS -->
				<div class="col-lg-6 footerhop">
					<center>
				<p align="left"><font class="newfootertextbrand"><u>В социальных сетях </u></font></p>
				<p align="left"><font class="newfootertextbrand">Twitter</font></p>
				<p align="left"><font class="newfootertextbrand">Вконтакте</font></p>
				<p align="left"><font class="newfootertextbrand">email: robotomize@gmail.com</font></p>

			</center>
				</div><!-- /col-lg-3 -->
				
				<!-- LATEST POSTS -->
				<div class="col-lg-3">
					
				</div><!-- /col-lg-3 -->
				
				<!-- NEW PROJECT -->
				<div class="col-lg-3">
					
				</div><!-- /col-lg-3 -->
				
				
			</div><! --/row -->
		</div><!-- /container -->
	</div><!-- /f -->
	


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/retina.js"></script>


  	<script>
		$(window).scroll(function() {
			$('.si').each(function(){
			var imagePos = $(this).offset().top;
	
			var topOfWindow = $(window).scrollTop();
				if (imagePos < topOfWindow+400) {
					$(this).addClass("slideUp");
				}
			});
		});
	</script>    



  
  </body>
</html>