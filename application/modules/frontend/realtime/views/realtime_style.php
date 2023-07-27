<link href="<?php echo theme_assets_url(); ?>css/jquery.jscrollpane.css" rel="stylesheet">
<link href="<?php echo theme_assets_url(); ?>vendors/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<!-- filterOther -->
<link href="<?php echo theme_assets_url(); ?>vendors/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css" rel="stylesheet" type="text/css">

<style>

/* #PriorityBox .mentions-list {
    animation: changeBackgroundColor 7s infinite;
} */

/* #formPeriod .btn.selected {
  color: #fff !important;
  background-color: #1C6CB9 !important;;
  border: #1C6CB9;
}

#formPeriod .btn {
  color: #1C6CB9 !important;
  background-color: rgba(255, 255, 255, 0.7) !important;
  border: #1C6CB9;
  border-style: solid;
} */

.filter-label {
  font-size: 11px;
}

#animation_main_div{
  position:absolute;
  margin:0px auto;
  /* display:inline-block; */
  display:none;
  right: 9px;
}


.circle{
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #f3392385;
     position: absolute;
    -webkit-animation-timing-function: linear;
    -webkit-animation: scaler 4s infinite;  /* Chrome, Safari, Opera */
    -webkit-animation-timing-function: linear;  /* Chrome, Safari, Opera */
    animation: scaler 4s infinite;
    animation-timing-function: linear;
}

.circle2{
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #f3392385;
     position: absolute;
    -webkit-animation-timing-function: linear;
    -webkit-animation: scaler 4s infinite;  /* Chrome, Safari, Opera */
    -webkit-animation-timing-function: linear;  /* Chrome, Safari, Opera */
    animation: scaler 4s infinite;
    animation-timing-function: linear;
    -webkit-animation-delay: 1s;
}

.circle3{
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #f3392385;
     position: absolute;
    -webkit-animation-timing-function: linear;
    -webkit-animation: scaler 4s infinite;  /* Chrome, Safari, Opera */
    -webkit-animation-timing-function: linear;  /* Chrome, Safari, Opera */
    animation: scaler 4s infinite;
    animation-timing-function: linear;
    -webkit-animation-delay: 2s;
}


.circle4{
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #f3392385;
     position: absolute;
    -webkit-animation-timing-function: linear;
    -webkit-animation: scaler 4s infinite;  /* Chrome, Safari, Opera */
    -moz-animation: scaler 4s infinite;  /* Chrome, Safari, Opera */
    animation: scaler 4s infinite;  /* Chrome, Safari, Opera */
    -webkit-animation-timing-function: linear;  /* Chrome, Safari, Opera */
    animation: scaler 4s infinite;
    animation-timing-function: linear;
    -webkit-animation-delay: 3s;
}



@-webkit-keyframes scaler{

    0%{
        -webkit-transform: scale(1);
    }

    33%{
        -webkit-transform: scale(4);
        opacity: 0.20;
    }

    /* 67%{
        -webkit-transform: scale(7);
        opacity: 0.10;
    }

    100%{
        -webkit-transform: scale(10);
        opacity: 0;
    } */


}

@-moz-keyframes scaler{

    0%{
        -moz-transform: scale(1);
    }

    33%{
        -moz-transform: scale(4);
        opacity: 0.20;
    }

    /* 67%{
        -moz-transform: scale(7);
        opacity: 0.10;
    }

    100%{
        -moz-transform: scale(10);
        opacity: 0;
    } */


}

@keyframes scaler{

    0%{
        transform: scale(1);
    }

    33%{
        transform: scale(4);
        opacity: 0.20;
    }

    /* 67%{
        transform: scale(7);
        opacity: 0.10;
    }

    100%{
        transform: scale(10);
        opacity: 0;
    } */


}

@keyframes changeBackgroundColor {
  0% {
    background-color: #fff6eb;
  }
  75% {
    background-color: #ffe4c4;
  }
}
</style>