<?php
$sectionData = (object) [
            'unofficial' => (object) [
                'title' => 'Competitions',
                'description' => 'To organize competitions. Any speedcuber can register a competition.'
            ],
            'mosaic' => (object) [
                'title' => 'Mosaic Building',
                'description' => 'Upload the image. Get the PDF. Create picture.'
            ],
            'goals' => (object) [
                'title' => 'Competition Goals',
                'description' => 'To set personal goals for official disciplines in official competitions.',
                'stop' => true
            ],
            'friends' => (object) [
                'title' => 'Friends\' Competitions',
                'description' => 'Shows the competitions your friends have registered for.',
                'stop' => true
            ],
            'announcements' => (object) [
                'title' => 'Competitions\' Announcements',
                'description' => 'Subscription to announcements of WCA competitions.',
                'stop' => true
            ],
];
?>
<?php foreach ($sectionData as $name => $section) { ?>
    <div class="shadow" <?php if ($section->stop ?? false) { ?> style="background-color: lightgrey" <?php } ?> >
        <table <?php if ($section->stop ?? false) { ?> style="background-color: lightgrey" <?php } ?> >
            <tr>
                <td> 
                    <img class='logo' src='<?= PageIndex() ?>Pages/<?= $name ?>/icon.png'> 
                </td>
                <td>
                    <h1>
                        <?php if ($section->stop ?? false) { ?>
                            <?= $section->title ?></a>
                        <?php } else { ?>
                            <a href='<?= PageIndex() ?><?= $name ?>'><?= $section->title ?></a>
                        <?php } ?>
                    </h1>
                    <?php if ($section->stop ?? false) { ?>
                        <p style="color:red">
                            Disabled until the WCA returns the events to Russia.
                        </p>
                    <?php } ?>
                    <?= $section->description ?>
                </td>
            </tr>
            <tr class='no_border'><td>&nbsp;</td></tr>
        </table>    
    </div>
<?php } ?>

