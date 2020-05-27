
<?php foreach ($sectionData as $section) { ?>
    <div class="shadow">
        <table>
            <tr>
                <td> 
                    <img class='logo' src='<?= PageIndex() ?>Logo/<?= $section->logo ?>.png'> 
                </td>
                <td>
                    <h1>
                        <a href='<?= PageIndex() ?><?= $section->link ?>'><?= $section->title ?></a>
                    </h1>
                    <?= $section->descrption ?>
                </td>
            </tr>
            <tr class='no_border'><td>&nbsp;</td></tr>
        </table>    
    </div>
<?php } ?>

