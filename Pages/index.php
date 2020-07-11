<?php
$sectionData = (object) [
            'unofficial' => (object) [
                'title' => 'Unofficial Competitions',
                'description' => 'For WCA events at unofficial competitions. Any speedcuber can register a competition.'
            ],
            'goals' => (object) [
                'title' => 'Competition Goals',
                'description' => 'To set personal goals for official disciplines in official competitions.'
            ],
            'mosaic' => (object) [
                'title' => 'Mosaic Building',
                'description' => 'Upload the image. Get the PDF. Create picture.'
            ],
            'friends' => (object) [
                'title' => 'Friends\' Competitions',
                'description' => 'Shows the competitions your friends have registered for.'
            ],
            'announcements' => (object) [
                'title' => 'Competitions\' Announcements',
                'description' => 'Subscription to announcements of WCA competitions.'
            ],
];
?>
<?php foreach ($sectionData as $name => $section) { ?>
    <div class="shadow">
        <table>
            <tr>
                <td> 
                    <img class='logo' src='<?= PageIndex() ?>Pages/<?= $name ?>/icon.png'> 
                </td>
                <td>
                    <h1>
                        <a href='<?= PageIndex() ?><?= $name ?>'><?= $section->title ?></a>
                    </h1>
                    <?= $section->description ?>
                </td>
            </tr>
            <tr class='no_border'><td>&nbsp;</td></tr>
        </table>    
    </div>
<?php } ?>

