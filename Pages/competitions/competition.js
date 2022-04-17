$('#sub_navigation_separator').show();
$('#sub_navigation').html('<a href="<?= PageIndex() ?>competitions/<?=$secret?>"><?= $comp->name ?></a>');