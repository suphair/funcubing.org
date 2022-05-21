<?php include 'competitions.menu.php'; ?>
<?php if (!($me->wca_id ?? FALSE)) {
    ?>    
    <div class="details_header"> 
        <i class="error far fa-hand-paper"></i> 
        <?=
        t('To create competition you need to sign in with WCA and have a WCA ID.',
                'Для создания соревнования требуется авторизоваться на WCA и иметь WCA ID.')
        ?>
    </div> 
<?php } else { ?>
    <div class="details_header"> 
        <?=
        t('<ul><li>Competitions is created privately.</li>' .
                '<li>You can make them public later in the settings.</li>' .
                '<li>Or leave them hidden for your testing or fun.</li></ul>',
                '<ul><li>Соревнования создаются приватными.</li>' .
                '<li>Вы можете сделать их публичными позже.</li>' .
                '<li>Или оставить спрятанными для тестирования или развлечения.</li></ul>')
        ?>   
    </div>    
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script>
        $(function () {
            $("#datepicker").datepicker({dateFormat: "dd.mm.yy"});
        });
    </script>

    <form method="POST" action="?create">
        <table class="table_info">

            <tr>
                <td>
                    <?= t('Competition', 'Наименование') ?>
                </td>
                <td>
                    <input required type="text" name="name" value=""  autocomplete="off" />
                </td>
            </tr>


            <tr>
                <td>
                    <?= t('Date', 'Дата') ?>
                </td>
                <td>
                    <input style="width:140px" placeholder="<?= t('Select date', 'Выберите дату') ?>" required type="text" id="datepicker" name="date" autocomplete="off">
                </td>
            </tr>
            <tr>
                <td>
                </td>
                <td>
                    <button>
                        <i class="fas fa-plus-circle"></i> 
                        <?= t('Create competition', 'Создать соревнование') ?>
                    </button>
                </td>
            </tr>
        </table>

    </form>
<?php } ?>