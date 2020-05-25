<h1>Транслитерация имени<br></h1><h2>и варианты использования в WCA</h2>

<table>
    <tr>
    <td>
        <div class="form">
Напишите имя по-русски<br>
<input tabindex=1 autofocus ID="name" autocomplete="off" style="width:160px" value="" 
                        onkeyup="
                            $('#TranslitName').val('');
                            $('#TranslitName').load('<?= PageIndex() ?>Actions/TranslitAJAX?name=' + encodeURI($(this).val()));
                        " />
<button onclick="$('#name').keyup();">>></button>
</div>
<span id="TranslitName"></span>
<br><br>
</td>
<td>
    <div class="form">
Напишите фамилию по-русски<br>
<input tabindex=2 ID="surname" autocomplete="off" style="width:160px" value="" 
                        onkeyup="
                            $('#TranslitSurname').val('');
                            $('#TranslitSurname').load('<?= PageIndex() ?>Actions/TranslitAJAX?surname=' + encodeURI($(this).val()));
                        " />
<button onclick="$('#surname').keyup();">>></button>
</div>
<span id="TranslitSurname"></span>
<br><br>
</td>
</tr>
</table>

