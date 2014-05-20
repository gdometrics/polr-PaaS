<!-- polr -->

<!DOCTYPE html>
<html>
    <head>
        <title>Polr</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="bootstrap.css"/>
        <link rel="stylesheet" href="main.css"/>
        <link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
        <script src='http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>
        <script src='mpjs.js'></script>
        <link rel="shortcut icon" href="favicon.ico">
        <link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>

        <script>
            $(function() {
                // Setup drop down menu
                $('.dropdown-toggle').dropdown();

                // Fix input element click problem
                $('.dropdown input, .dropdown label').click(function(e) {
                    e.stopPropagation();
                });
            });
        </script>
    </head>
    <body style="padding-top:60px">
        <div class="container-fluid">
            <div class="navbar navbar-inverse navbar-fixed-top"><div class="navbar-header"><a class="navbar-brand" href="index.php">Polr</a></div>
                <!--<a class="btn btn-navbar btn-default" data-toggle="collapse" data-target="#nbc">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>-->

                <ul class="nav navbar-collapse navbar-nav" id="nbc">
                    <li><a href="//github.com/Cydrobolt/polr">Github</a></li>
                    <li><a href="//project.polr.cf">Source</a></li>
                    <li><a href="about.php">About</a></li><li><a href="contact.php">Contact</a></li>
                </ul>
                <ul class="nav pull-right navbar-nav">
                    <?php include('polrauth.php');
                    $polrauth = new polrauth();
                    $polrauth->headblock(); ?>
                    <li><a href="register.php">Sign Up</a></li>
                    <li class="divider-vertical"></li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" href="#" data-toggle="dropdown">Sign In <strong class="caret"></strong></a>
                        <div class="dropdown-menu" id="dropdown" style="padding: 15px; padding-bottom: 0px; color:white;">
                            <h2>Login</h2>
                            <form action="loginproc.php" method="post" accept-charset="UTF-8">
                                <input id="user_username" style="margin-bottom: 15px;" type="text" name="username" placeholder='Username' size="30" class="form-control">
                                <input id="user_password" style="margin-bottom: 15px;" type="password" name="password" placeholder='Password' size="30" class="form-control">
                                <input id="remember_me" style="margin-botton: 15px" type="checkbox" name="remember_me" value="remember_me" size='30' /> <label>Remember Me</label>
                                <input class="btn btn-success form-control" style="clear: left; width: 100%; height: 32px; font-size: 13px;" type="submit" name="login" value="Sign In">
                                <br><br>
                            </form>
                        </div>
                    </li>
<?php $polrauth->headendblock(); ?>

                </ul>
            </div>
        </div>
        <div class="container">
            <div class="jumbotron" style="text-align:center; padding-top:80px; background-color: rgba(0,0,0,0);">
                <h1 class='title'>Polr</h1>
                <form id='shortenform' method="POST" action="createurl.php" role="form">
                    <input type="text" class="form-control" placeholder="URL" id="url" value="http://" name="urlr" />
                    <div id='options'>
                        <br>Customize link: <br><div style='color: green'><h2 style='display:inline'>polr.cf/</h2><input type='text' id='custom' title='After entering your custom ending, if the ending is available, enter your long URL into box above and press "Shorten"!' name='custom' /><br>
                            <a href="#" class="btn btn-inverse btn-sm" id='checkavail'>Check Availability</a><div id='status'></div></div>
                    </div>
                    <br><input type="submit" class="btn btn-info" id='shorten' value="Shorten!"/>   <a href="#" class="btn btn-warning" id='showoptions'>Link Options</a>
                    <input type="hidden" id="hp" name="hp" value="<?php echo $hp; ?>"/>
                </form>
                <br><br><div id="tips" class='text-muted'><i class="fa fa-spinner"></i> Loading Tips...</div>
            </div>
            <div id="polrfooter">
            <footer>
                <p id="footer-pad">&copy; Copyright 2014 Polr - <a href='privacypolicy.php'>Privacy Policy</a> - <a href='tos.php'>Terms of Service</a> - <a href="//cydrobolt.com/contact.html">Contact</a></p>
            </footer>
            </div>
        </div>
    </body>
</html>
