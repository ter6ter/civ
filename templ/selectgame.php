<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <script src="js/jquery.min.js"></script>
    <script src="js/functions.js"></script>
    <script src="js/forms.js"></script>
    <link type="text/css" href="css/style.css" rel="Stylesheet" />
</head>
<body>
<div class="login-content">
    <form action="index.php" method="post">
    <input type="hidden" name="method" value="login">
    <div class="form-field">
        <select id="select-game-select" name="gid">
        <?foreach ($gamelist as $game):?>
            <option value="<?=$game['id']?>"><?=$game['name']?> (<?=$game['map_w']?>x<?=$game['map_h']?>, <?=$game['ucount']?> игрока)</option>
        <?endforeach;?>
        </select>
    </div>
    <div class="form-field">
        <select id="select-game-user" name="uid">
        </select>
    </div>
    <div class="form-field">
        <input id="select-game-open" type="submit" value="Открыть">
    </div>
    <a href="index.php?method=creategame">Создать новую игру</a>
    </form>
</div>
<script>
    select_game_change();
</script>
</body>