<script>
    $(document).ready(function () { // Ждём загрузки страницы

        $(".imageSmall").click(function () { 	// Событие клика на маленькое изображение
            var img = $(this);	// Получаем изображение, на которое кликнули
            var src = img.attr('src'); // Достаем из этого изображения путь до картинки
            $("body").append("<div class='popup'>" + //Добавляем в тело документа разметку всплывающего окна
                    "<div class='popup_bg'></div>" + // Блок, который будет служить фоном затемненным
                    "<img src='" + src + "' class='popup_img' />" + // Само увеличенное фото
                    "</div>");
            $(".popup").fadeIn(800); // Медленно выводим изображение
            $(".popup").click(function () {	// Событие клика на затемненный фон	   
                $(".popup").fadeOut(800);	// Медленно убираем всплывающее окно
                setTimeout(function () {	// Выставляем таймер
                    $(".popup").remove(); // Удаляем разметку всплывающего окна
                }, 800);
            });
        });

    });
</script>
<style>
    .popup {
        position: fixed;
        height:100%;
        width:100%;
        top:0;
        left:0;
        display:none;
        text-align:center;
    }

    .popup_bg {
        background:rgba(0,0,0,0.4);
        position:fixed;
        z-index:1;
        height:100%;
        width:100%;
    }


    .popup_img {
        position: relative;
        margin:0 auto;
        z-index:2;
        max-height:94%;
        max-width:94%;
        margin:1% 0 0 0;
    }
</style>