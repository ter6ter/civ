$(document).on('click', '#create-game-add-player', function (e) {
    var count = $('.create-player').length + 1;
    var el = $('.create-player:first').clone();
    el.find('input').val('');
    var color = '#';
    var sym = 'ff';
    if (count > 8) {
        sym = '88';
        count -= 8;
    }
    if ((count & 4) > 0) {
        color += sym;
    } else {
        color += '00';
    }
    if ((count & 2) > 0) {
        color += sym;
    } else {
        color += '00';
    }
    if ((count & 1) > 0) {
        color += sym;
    } else {
        color += '00';
    }
    el.find('span').css('background-color', color);
    $('.create-player:last').after(el);
});
function select_game_change() {
    var val = $('#select-game-select').val();
    $.post('index.php?method=gameinfo', {'json': 1, 'id': val}, function(data) {
        resp = $.parseJSON(data);
        if (resp.status == 'ok') {
            var users = resp.data.users;
            $('#select-game-user').empty();
            for (var i in users) {
                $('#select-game-user').append('<option value="' + users[i].id + '" ' +
                    'style="color:' + users[i].color + '">' + users[i].login + '</option>');
            }
            select_user_change();
        } else {
            window.alert(resp.error);
        }
    });
}
$(document).on('change', '#select-game-select', function (e) {
    select_game_change();
});
function select_user_change() {
    $('#select-game-user').css('color', $('#select-game-user option:selected')[0].style.color);
}
$(document).on('change', '#select-game-user', function (e) {
    select_user_change();
});