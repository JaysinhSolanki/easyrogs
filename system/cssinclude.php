<link rel="stylesheet" href="<?php echo VENDOR_URL;?>fontawesome/css/font-awesome.css" />
<link rel="stylesheet" href="<?php echo VENDOR_URL;?>metisMenu/dist/metisMenu.css" />
<link rel="stylesheet" href="<?php echo VENDOR_URL;?>bootstrap/dist/css/bootstrap.css" />
<link rel="stylesheet" href="<?php echo VENDOR_URL;?>awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" />
<link rel="stylesheet" href="<?php echo VENDOR_URL;?>fooTable/css/footable.core.min.css" />
<link rel="stylesheet" href="<?php echo VENDOR_URL;?>pe-icons/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
<link rel="stylesheet" href="<?php echo VENDOR_URL;?>pe-icons/pe-icon-7-stroke/css/helper.css" />
<link rel="stylesheet" href="<?php echo VENDOR_URL;?>style.css">
<link rel="stylesheet" href="<?php echo VENDOR_URL;?>toastr/build/toastr.min.css" />
<link rel="stylesheet" href="<?php echo VENDOR_URL;?>customjscss/numslider.css" type="text/css" />
<link rel="stylesheet" href="<?php echo VENDOR_URL;?>bootstrap-datetimepicker.css" />
<link rel="stylesheet" href="<?php echo VENDOR_URL;?>daterangepicker.css" />
<link rel="stylesheet" href="<?php echo VENDOR_URL;?>bootstrap-clockpicker.min.css" />
<link rel="stylesheet" href="<?php echo VENDOR_URL;?>select2.min.css" />
<link rel="stylesheet" href="<?php echo VENDOR_URL;?>dropzone.css">
<link rel="stylesheet" href="<?php echo VENDOR_URL;?>fonts/MyFontsWebfontsKit.css">
<link rel="stylesheet" href="<?php echo VENDOR_URL;?>datepicker.css" />
<link rel="stylesheet" href="<?php echo VENDOR_URL;?>datatables/media/css/jquery.dataTables.css"/>

<style>
#screenfrmdiv {
  width: 100%;
}
table.dataTable thead th, table.dataTable thead td {
    padding: 10px 18px;
    border-bottom: 1px !important;
}
table.dataTable.no-footer {
    border-bottom: 1px !important;
}
.tooltip-inner {
	max-width: 500px;
	text-align: left;
}
.swal2-title
{
	font-size:24px !important
}
.swal-button--confirm { color: white; background-color: #187204; }
.swal-button--cancel { color: white; background-color: #C2391B; }
.swal-button--info { color: white; background-color: #4ea5e0; } /* 💣 this must follow the previous .swal-button--* */

.btn-success
{
	background-color:#187204 !important;
	border-color:#187204 !important;
	color:#fff !important;
}
.btn-success:hover
{
	background-color:#1f9305 !important;
	border-color:#1f9305 !important;
	color:#fff !important;
}
.btn-purple
{
	background-color:#8e24aa !important;
	border-color:#8e24aa !important;
	color:#fff !important;
}
.btn-purple:hover
{
	background-color:#a52ac6 !important;
	border-color:#a52ac6 !important;
	color:#fff !important;
}
.btn-danger
{
	background-color:#C2391B !important;
	border-color:#C2391B !important;
	color:#fff !important;
}
.btn-danger:hover
{
	background-color:#951f06 !important;
	border-color:#951f06 !important;
	color:#fff !important;
}
.btn-gray {
	
}
.btn-round {
  border-radius: 50%;
}
.btn-outline {
  border-width: 1px;
  border-style: solid;
}

.instruction-collapse [data-toggle="collapse"]:after {
    content: "Hide instructions";
    float: right;
    font-size: 14px;
    line-height: 20px;
}
.instruction-collapse [data-toggle="collapse"].collapsed:after {
    content: "Show instructions";
    color: #fff;
}

.main {
    display: flex;
    align-items: stretch;
}
.sidebar {
    min-width: 0px;
    max-width: 0px;
    transition: all 0.3s;
}
.sidebar>.fixed {
    position: fixed; 
    margin-top: 70px; /* skip the header */
    height: 100vh; 
	  width: 0;
    z-index: 5;
    overflow-x: hidden;
    transition: all 0.3s;
}
.sidebar.left>.fixed {
    left: 0;
}
.sidebar.right>.fixed {
    right: calc(100wh-280px);
}
body .sidebar.open:not(#-_-) {
    min-width: 280px;
    max-width: 280px;
    text-align: center;
}
.sidebar.open>.fixed:not(#-_-) {
	width: 280px;
}
@media screen and (min-width: 960px) {
  #screen-discovery .sidebar {
      min-width: 32px;
      max-width: 32px;
  }
}
#btn-definitions,
#btn-objections {
  margin-bottom: 0.5em;
}
#btn-definitions {
 }

@keyframes anim-glow {
  0% {
    box-shadow: 0 0 2px #fff;
  }
  50% {
    box-shadow: 0 0 10px #31b0d5;
  }
  100% {
    box-shadow: 0 0 2px #fff;
  }
}
.glowing {
	animation: anim-glow 2s ease infinite;
}
</style>