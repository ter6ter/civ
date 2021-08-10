<?php
if (isset($_REQUEST['name']) && is_array($_REQUEST['users'])) {
    $data = ['name' => htmlspecialchars($_REQUEST['name']),
             'map_w' => (int)$_REQUEST['map_w'] ? (int)$_REQUEST['map_w'] : 100,
             'map_h' => (int)$_REQUEST['map_h'] ? (int)$_REQUEST['map_h'] : 100,
             'turn_type' => ($_REQUEST['turn_type'] == 'concurrently') ? 'concurrently' : 'byturn',
        ];
    $users = [];
    $num = 0;
    foreach ($_REQUEST['users'] as $user) {
        $num++;
        $color = '#';
        $sym = 'ff';
        if ($num > 8) {
            $sym = '88';
        }
        if (($num & 4) > 0) {
            $color .= $sym;
        } else {
            $color .= '00';
        }
        if (($num & 2) > 0) {
            $color .= $sym;
        } else {
            $color .= '00';
        }
        if (($num & 1) > 0) {
            $color .= $sym;
        } else {
            $color .= '00';
        }
        $users[] = [
            'login' => $user,
            'color' => $color,
            'order' => $num,
        ];
    }
    if (count($users) > 1) {
        $game = new Game($data);
        $game->save();
        foreach ($users as $user) {
            $data = ['login' => $user['login'],
                    'color' => $user['color'],
                    'game' => $game->id,
                    'turn_order' => $user['order']
                    ];
            $u = new User($data);
            $u->save();
        }
        $game->create_new_game();
        Header("Location: index.php?method=selectgame");
    }
}