<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <script src="js/jquery.min.js"></script>
    <script src="js/functions.js"></script>
    <script src="js/forms.js"></script>
    <link type="text/css" href="css/style.css" rel="Stylesheet" />
</head>
<body>
<form action="index.php?method=creategame" method="post">
    <div class="login-content">
        <div class="form-field">
            <span class="form-label">Название</span>
            <input type="text" name="name">
        </div>
        <div class="form-field">
            <span class="form-label">Ширина карты</span>
            <input type="text" name="map_w" value="100">
        </div>
        <div class="form-field">
            <span class="form-label">Высота карты</span>
            <input type="text" name="map_h" value="100">
        </div>
        <div class="form-field">
            <span class="form-label">Порядок ходов</span>
            <select name="turn_type">
                <option value="concurrently">Одновременно</option>
                <option value="byturn">По очереди</option>
                <option value="onewindow">По очереди за одним компьютером</option>
            </select>
        </div>
        <h3>Игроки:</h3>
        <div class="form-field create-player">
            <input type="text" name="users[]" value="">
            <span style="background-color: #0000ff">&nbsp;</span>
        </div>
        <div class="form-field">
            <input type="button" id="create-game-add-player" value="Добавить">
        </div>
        <div class="form-field">
            <input type="submit">
        </div>
    </div>
</form>
</body>