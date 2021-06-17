$(function () {
    $('.datepicker').datepicker({
        firstDay: 1,
        dateFormat: 'dd.mm.yy',
        dayNames: [
            'Воскресенье',
            'Понедельник',
            'Вторник',
            'Среда',
            'Четверг',
            'Пятница',
            'Суббота'
        ],
        dayNamesMin: [
            'Вс',
            'Пн',
            'Вт',
            'Ср',
            'Чт',
            'Пт',
            'Сб'
        ],
        dayNamesShort: [
            'Вос',
            'Пон',
            'Вто',
            'Сре',
            'Чет',
            'Пят',
            'Суб'
        ],
        monthNames: [
            'Январь',
            'Февраль',
            'Март',
            'Апрель',
            'Май',
            'Июнь',
            'Июль',
            'Август',
            'Сентябрь',
            'Октябрь',
            'Ноябрь',
            'Декабрь'
        ],
        monthNamesShort: [
            'Янв',
            'Фев',
            'Мар',
            'Апр',
            'Май',
            'Июн',
            'Июл',
            'Авг',
            'Сен',
            'Окт',
            'Ноя',
            'Дек'
        ],
        nextText: 'Вперед',
        prevText: 'Назад',
        showAnim: 'slideDown',
        showOtherMonths: true,
        weekHeader: 'Нед',
        changeMonth: true,
        changeYear: true
    });

    $(document).on('click', '.delete_daily_report', function (e) {
        if (!confirm('Удалить запись?')) {
            e.preventDefault();
        }
    })

    $('#downloadJournal').on('click', function (e) {
        e.preventDefault();

        var date = $('#date').val();

        window.location = '/download?date=' + date;
    })

    $('.once').on('hide', function () {
        var id = $(this).data('id');

        $.ajax({
            url: '/alerts/delete',
            method: 'post',
            data: {
                id: id
            }
        })
    })
})
