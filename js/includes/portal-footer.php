<script src="js/sweetalert.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/sweetalert.css">

<div class="general-fade"></div>

<div class="general-result"></div>

<div class="footer-wrapper">
<div class="footer container">

<div class="col-sm-4 nav-link share">
<div class="title btn">LOCATE US</div>
<a><i class="fa fa-map-marker" aria-hidden="true"></i> 3, Ubiaja Crescent, Garki II, Abuja.</a>
<div class="title btn">CONTACT US</div>
<a><i class="fa fa-envelope" aria-hidden="true"></i> <?php echo $gen_email; ?>,<br />alafrikiy@yahoo.com,<br />alafrikiy@gmail.com</a>
<a><i class="fa fa-phone" aria-hidden="true"></i> <?php echo $gen_phone; ?>,<br />+234 (0)805 558 4449</a>
</div>

<div class="col-sm-4 nav-link">
<div class="title btn">QUIK LINKS</div>
<a href="<?php directory(); ?>"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
<a href="<?php echo $privates; ?>our-services/"><i class="fa fa-cog" aria-hidden="true"></i> Our Services</a>
<a href="<?php echo $privates; ?>events-gallery/"><i class="fa fa-calendar" aria-hidden="true"></i> Events Gallery</a>
<a href="<?php echo $privates; ?>programme-schedule/"><i class="fa fa-clock-o" aria-hidden="true"></i> Programme Schedule</a>
<a href="<?php echo $privates; ?>events-videos/"><i class="fa fa-file-movie-o" aria-hidden="true"></i> Events Videos</a>
<a href="<?php echo $privates; ?>upcoming-events/"><i class="fa fa-calendar" aria-hidden="true"></i> Upcoming Events</a>
</div>

<div class="col-sm-4 subscribe">
<div class="title btn">NEWSLETTER</div>
<form  action="<?php directory(); ?>privates/process-data/" class="newsletter" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">

<input type="hidden" name="newsletter" value="1">

<div class="form-group input-group">
<span class="input-group-addon"><i class="fa"><label for="name">Name</label></i></span>
<input type="text" name="name" id="name" class="form-control" value="" placeholder="Your name" required>
</div>

<div class="form-group input-group">
<span class="input-group-addon"><i class="fa"><label for="email">Email</label></i></span>
<input type="text" name="email" id="email" class="form-control" value="" placeholder="Your email" required>
</div>
<div style="text-align:right">
<button  name="subscribe" id="subscribe"><i class="fa fa-send"></i> Subscribe</button>
</div>	
</form>
<div class="footer-social">
<a href="javascript:void(0);" title="Facebook" class="fa fa-facebook btn" target="_blank"></a>
<a href="javascript:void(0);" title="Twitter" class="fa fa-twitter btn"></a>
<a href="javascript:void(0);" title="Google +" class="fa fa-google-plus btn"></a>
<a href="javascript:void(0);" title="Pinterest" class="fa fa-pinterest-p btn"></a>
<a href="javascript:void(0);" title="Instagram" class="fa fa-instagram btn"></a>
</div>
</div>

</div>
</div>

<div class="copyright">Copyright &copy; <?php echo date("Y") . " " . $full_gen_name;?>. All Rights Reserved.<br />Developed by: <a href="http://reliancewisdom.com" target="_blank">Reliance Wisdom Digital.</a></div>

<script type="text/javascript" src="js/portal.js"></script>
<?php
$db->disconnect();
 detectCurrUserBrowser('</td></tr></table>','',7); ?>

<div class="modal fade" id="gen-modal" role="dialog">
<div class="modal-dialog">
<div class="modal-content modal-result">
</div>
</div></div> 
 
</body>
</html>