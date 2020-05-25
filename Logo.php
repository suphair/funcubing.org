<style>
    img {
        margin:5px;
        border:1px solid white;
        background-color: #EEE;
    }
    img:hover{
        border:1px solid gray;
        background-color: #FFF;
    }
</style>
<h2>JPG</h2>
<a target='_blank'  href='Logo/Full_Ru_Color.jpg'><img src='Logo/Full_Ru_Color.jpg' height='100px;'></a>
<a target='_blank'  href='Logo/Full_En_Color.jpg'><img src='Logo/Full_En_Color.jpg' height='100px;'></a>
<br>
<a target='_blank'  href='Logo/Full_Ru_Black.jpg'><img src='Logo/Full_Ru_Black.jpg' height='100px;'></a>
<a target='_blank'  href='Logo/Full_En_Black.jpg'><img src='Logo/Full_En_Black.jpg' height='100px;'></a>
<br>
<a target='_blank'  href='Logo/Logo_Color.jpg'><img src='Logo/Logo_Color.jpg' height='100px;'></a>
<a target='_blank'  href='Logo/Logo_Black.jpg'><img src='Logo/Logo_Black.jpg' height='100px;'></a>
<h2>PNG</h2>
<a target='_blank'  href='Logo/Full_Ru_Color.png'><img src='Logo/Full_Ru_Color.png' height='100px;'></a>
<a target='_blank'  href='Logo/Full_En_Color.png'><img src='Logo/Full_En_Color.png' height='100px;'></a>
<br>
<a target='_blank'  href='Logo/Full_Ru_Black.png'><img src='Logo/Full_Ru_Black.png' height='100px;'></a>
<a target='_blank'  href='Logo/Full_En_Black.png'><img src='Logo/Full_En_Black.png' height='100px;'></a>
<br>
<a target='_blank'  href='Logo/Logo_Color.png'><img src='Logo/Logo_Color.png' height='100px;'></a>
<a  target='_blank' href='Logo/Logo_Black.png'><img src='Logo/Logo_Black.png' height='100px;'></a>
<h2>SVG</h2>
<?php
    foreach (scandir('Svg') as $filename){
        if(strpos($filename,".svg")){ ?>
        <a target='_blank'  href='Svg/<?= $filename ?>'><img title='<?= $filename ?>' src='Svg/<?= $filename ?>' height='100px;'></a>
        <?php }
    }
?>