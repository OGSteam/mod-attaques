<style type="text/css">
/*This is where the magic happens!*/
div.attack_box {
     border: 1px solid #000000;
     position: relative;
     width: 100%;
}
div.attack_box_contents {
     background-color:transparent;
     height: 100%;
     position: relative;
     width: 100%;
     z-index: 101;
}
div.attack_box_background {
     background-color: black;
     height: 100%;
     filter:alpha(opacity=<?php echo $config['transp']; ?>); /* IE's opacity*/
     left: 0px;
     opacity: <?php echo $config['transp']/100; ?>;
     position: absolute;
     top: 0px;
     width: 100%;
     z-index: 99;
}
</style>
<?php

?>
