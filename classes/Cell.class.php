<?php
class Cell {
	public $x, $y, $planet;
    /**
     * @var CellType
     */
	public $type;
	//Unit[]
	public $units = [];
    /**
     * @var City
     */
	public $city = false;
    /**
     * @var User
     */
	public $owner = null;
    /**
     * @var Resource
     */
	public $resource = null;
    /**
     * @var int
     */
	public $owner_culture = 0;
    /**
     * Наличие дороги(false, 'road', 'iron')
     * @var bool | string
     */
	public $road = false;
    /**
     * Имеющееся улучшение(false, 'mine', 'irrigation')
     * @var string | string
     */
	public $improvement = false;
	
	private static $_all = [];
							
	public static $map_planet = 1;
	public static $map_width = 100;
	public static $map_height = 100;

	public static $UPGRADE_NAMES = ['mine' => 'рудник',
                                    'irrigation' => 'орошение'];

    /**
     * @param $x
     * @param $y
     * @return Cell
     */
	public static function get($x, $y) {
		$cell = Cell::load_cells([['x' => $x, 'y' => $y]], Cell::$map_planet);
		return (isset($cell[0])) ? $cell[0] : false;
	}
							
	public static function load_cells($coords) {
		$query = [];
		$result = [];
		foreach ($coords as $coord) {
			if (isset(Cell::$_all[$coord['x']][$coord['y']])) {
				$result[] = Cell::$_all[$coord['x']][$coord['y']];
			} else {
				$query[] = '(x = '.(int)$coord['x'].' AND y = '.(int)$coord['y'].')';
			}
		}
		if (count($query) > 0) {
			$query = implode(' OR ', $query);
			$query = 'SELECT * FROM `cell` WHERE ('.$query.') AND planet = '.Cell::$map_planet;
			$cells = MyDB::query($query);
			foreach ($cells as $cell) {
				$result[] = new Cell($cell);
			}
		}
		return $result;
	}
	
	public function __construct($data) {
		$this->x = $data['x'];
		$this->y = $data['y'];
		$this->planet = (isset($data['planet'])) ? $data['planet'] : Cell::$map_planet;
		$this->type = CellType::get($data['type']);
		$this->city = City::by_coords($this->x, $this->y, $this->planet);
		if (isset($data['owner']) && (int)$data['owner']) {
		    $this->owner = User::get($data['owner']);
		    $this->owner_culture = $data['owner_culture'];
        } else {
            $this->owner = null;
        }
		if (!isset($data['road']) || $data['road'] == 'none') {
		    $this->road = false;
        } else {
            $this->road = $data['road'];
        }
        if (!isset($data['improvement']) || $data['improvement'] == 'none') {
            $this->improvement = false;
        } else {
            $this->improvement = $data['improvement'];
        }
		$resource = Resource::get($this->x, $this->y);
		if ($resource) {
            $this->resource = $resource;
        } else {
            $this->resource = null;
        }
		Cell::$_all[$this->x][$this->y] = $this;
	}

	public function get_units() {
		$rows = MyDB::query("SELECT * FROM unit WHERE x = ?x AND y = ?y AND planet = ?planet", 
					['x' => $this->x, 'y' => $this->y, 'planet' => Cell::$map_planet]);
		$this->units = [];
		foreach ($rows as $row) {
			$this->units[] = new Unit($row);
		}
		return $this->units;
	}
	
	public function get_title() {
		return $this->type->get_title();;
	}
	
	public function save() {
	    if ($this->owner) {
            $owner_id = $this->owner->id;
        } else {
            $owner_id = 'NULL';
        }
	    $road = $this->road;
	    if (!$road) {
	        $road = 'none';
        }
        $improvement = $this->improvement;
        if (!$improvement) {
            $improvement = 'none';
        }
		MyDB::query("INSERT INTO `cell` SET `x` = '?x', `y` = '?y', `planet` = '?planet', 
            `type` = '?type', `owner` = ?owner, `owner_culture` = '?culture', `road` = '?road', `improvement` = '?improvement'
			ON DUPLICATE KEY UPDATE `type` = '?type', `owner` = ?owner, `owner_culture` = '?culture', `road` = '?road', `improvement` = '?improvement'",
			['x' => $this->x, 'y' => $this->y, 'planet' => Cell::$map_planet, 'type' => $this->type->id,
             'owner' => $owner_id, 'culture' => $this->owner_culture, 'road' => $road, 'improvement' => $improvement]);
	}

    /**
     * Складывает координаты x и dx и y и dy и возвращает клетку с этих координат
     * @param $x int
     * @param $y int
     * @param $dx int
     * @param $dy int
     * @param bool $load подгружать ли клетку из БД если она ещё не загружена
     * @return Cell
     */
	public static function d_coord($x, $y, $dx, $dy, $load = true) {
        Cell::calc_coord($x, $y, $dx, $dy);
		if (isset(Cell::$_all[$x][$y])) {
			return Cell::$_all[$x][$y];
		} elseif ($load) {
			return Cell::get($x, $y);
		} else {
			return false;
		}
	}

    /**
     * Считает координаты с учётом зацикливания
     * @param $x int
     * @param $y int
     * @param $dx int
     * @param $dy int
     */
	public static function calc_coord(&$x, &$y, $dx, $dy) {
        $x += $dx;
        if ($x >= Cell::$map_width) $x -= Cell::$map_width;
        if ($x < 0) $x += Cell::$map_width;
        $y += $dy;
        if ($y >= Cell::$map_height) $y -= Cell::$map_height;
        if ($y < 0) $y += Cell::$map_height;
    }

    /**
     * Вычисляем расстояние между координатами "по прямой"
     * @param $x1 int
     * @param $y1 int
     * @param $x2 int
     * @param $y2 int
     * @return int
     */
    public static function calc_distance($x1, $y1, $x2, $y2) {
        $dx1 = $x1 - $x2;
        if ($dx1 < 0) $dx1 += Cell::$map_width;
        $dx2 = $x2 - $x1;
        if ($dx2 < 0) $dx2 += Cell::$map_width;
        $dx = min($dx1, $dx2);
        $dy1 = $y1 - $y2;
        if ($dy1 < 0) $dy1 += Cell::$map_height;
        $dy2 = $y2 - $y1;
        if ($dy2 < 0) $dy2 += Cell::$map_height;
        $dy = min($dy1, $dy2);
        return max($dx, $dy);
    }

	public static function generate_map() {
		MyDB::query("DELETE FROM `unit` WHERE `planet` = '?planet'", ['planet' => Cell::$map_planet]);
        $data = MyDB::query("SELECT id FROM city WHERE planet = ?planet", ['planet' => Cell::$map_planet]);
        foreach ($data as $row) {
            MyDB::query("DELETE FROM `building` WHERE `city_id` = '?cid'", ['cid' => $row['id']]);
        }
        MyDB::query("DELETE FROM `city_people` WHERE `planet` = '?planet'", ['planet' => Cell::$map_planet]);
		MyDB::query("DELETE FROM `city` WHERE `planet` = '?planet'", ['planet' => Cell::$map_planet]);
		$data = MyDB::query("SELECT id FROM user WHERE game = ?game", ['game' => Cell::$map_planet]);
		foreach ($data as $row) {
            MyDB::query("DELETE FROM `research` WHERE `user_id` = '?uid'", ['uid' => $row['id']]);
        }
        MyDB::query("DELETE FROM `resource` WHERE `planet` = '?planet'", ['planet' => Cell::$map_planet]);
		MyDB::query("DELETE FROM `cell` WHERE `planet` = '?planet'", ['planet' => Cell::$map_planet]);
		
		for ($x = 0; $x < Cell::$map_width; $x++) {
			for ($y = 0; $y < Cell::$map_height; $y++) {
				$cell_type = Cell::generate_type($x, $y);
				$cell = new Cell(['x' => $x,
								  'y' => $y,
								  'planet' => Cell::$map_planet,
								  'type' => $cell_type->id]);
                $cell->save();
				foreach (ResourceType::$all as $resource_type) {
				    if (in_array($cell_type, $resource_type->cell_types)) {
				        if (mt_rand(0, 10000) < $resource_type->chance * 10000) {
				            $resource = new Resource([  'x' => $x,
                                                        'y' => $y,
                                                        'planet' => Cell::$map_planet,
                                                        'type' => $resource_type->id,
                                                        'amount' => mt_rand($resource_type->min_amount, $resource_type->max_amount)]);
				            $resource->save();
				            break;
                        }
                    }
                }
			}
		}
	}

    /**
     * @param $x
     * @param $y
     * @return CellType
     */
	public static function generate_type($x, $y) {
		$c1 = []; $c2 = [];
		$chance = [];
		foreach (CellType::$all as $ctype) {
			$chance[$ctype->id] = $ctype->base_chance;
		}
		$c1[] = Cell::d_coord($x, $y, 0, -1, false);
		$c1[] = Cell::d_coord($x, $y, 0, 1, false);
		$c1[] = Cell::d_coord($x, $y, -1, 0, false);
		$c1[] = Cell::d_coord($x, $y, 1, 0, false);
		$c2[] = Cell::d_coord($x, $y, -1, -1, false);
		$c2[] = Cell::d_coord($x, $y, 1, -1, false);
		$c2[] = Cell::d_coord($x, $y, -1, 1, false);
		$c2[] = Cell::d_coord($x, $y, 1, 1, false);
		foreach (CellType::$all as $ctype) {
			$next = false;
			foreach ($c1 as $cell) {
				if (!$cell) continue;
				if (in_array($cell->type->id, $ctype->border_no)) {
					$chance[$ctype->id] = 0;
					$next = true;
					break;
				}
				if ($cell->type == $ctype) {
					$chance[$ctype->id] += $ctype->chance_inc1;
				}
				if (isset($ctype->chance_inc_other[$cell->type->id])) {
					$chance[$ctype->id] += $ctype->chance_inc_other[$cell->type->id][0];
				}
			}
			if ($next) continue;
			foreach ($c2 as $cell) {
				if (!$cell) continue;
				if (in_array($cell->type->id, $ctype->border_no)) {
					$chance[$ctype->id] = 0;
					break;
				}
				if ($cell->type == $ctype) {
					$chance[$ctype->id] += $ctype->chance_inc2;
				}
				if (isset($ctype->chance_inc_other[$cell->type->id])) {
					$chance[$ctype->id] += $ctype->chance_inc_other[$cell->type->id][1];
				}
			}
		}
		if ($x == 0 || $y == 0) {
			$chance['water3'] = 0; //Костыль но подругому могут получится клетки для которых нет подходящего типа
		}
		$chance_sum = 0;
		$chance_interval = [];
		foreach ($chance as $key => $val) {
			$chance_interval[$key] = ['start' => $chance_sum, 'end' => $chance_sum + $val];
			$chance_sum += $val;
		}
		$rand = mt_rand(0, $chance_sum - 1);
		foreach ($chance_interval as $type => $val) {
			if ($val['start'] <= $rand && $val['end'] > $rand) {
				return CellType::get($type);
			}
		}
		die();
	}

    /**
     * Возвращает область клеток вокруг данной точки
     * @param $x int
     * @param $y int
     * @param $width int
     * @param $height int
     * @return array
     */
	public static function get_cells_around($x, $y, $width, $height) {
		$rx = (int)($width - 1)/2;
		$ry = (int)($height - 1)/2;
		$x1 = $x - $rx; $x2 = $x + $rx;
		$y1 = $y - $ry; $y2 = $y + $ry;
		$intx = [];
		for ($x = $x1;$x <= $x2;$x++) {
			if ($x < 0) {
				$intx[] = Cell::$map_width + $x;
			} elseif ($x >= Cell::$map_width) {
				$intx[] = $x - Cell::$map_width;
			} else {
				$intx[] = $x;
			}
		}
		$inty = [];
		for ($y = $y1;$y<=$y2;$y++) {
			if ($y < 0) {
				$inty[] = Cell::$map_height + $y;
			} elseif ($y >= Cell::$map_height) {
				$inty[] = $y - Cell::$map_height;
			} else {
				$inty[] = $y;
			}
		}
		$result = [];
		foreach ($intx as $x) {
			$row = [];
			foreach ($inty as $y) {
				$cell = Cell::get($x, $y);
                $cell->get_units();
				$row[] = $cell;
			}
			$result[] = $row;
		}
		
		return $result;
	}

    /**
     * @param null $city City
     * @return int
     */
	public function get_work($city = null) {
	    $work = $this->type->work;
	    if ($this->resource) {
	        $work += $this->resource->type->work;
        }
	    if ($this->improvement == 'mine') {
	        $work++;
        }
		return $work;
	}
    /**
     * @param null $city City
     * @return int
     */
	public function get_eat($city = null) {
	    $eat = $this->type->eat;
	    if ($city) {
	        if ($this->type->id == 'water' && isset($city->buildings[8])) { //Гавань
	            $eat++;
            }
        }
        if ($this->resource) {
            $eat += $this->resource->type->eat;
        }
        if ($this->improvement == 'irrigation') {
            $eat++;
        }
		return $eat;
	}
    /**
     * @param null $city City
     * @return int
     */
	public function get_money($city = null) {
	    $money = $this->type->money;
        if ($this->resource) {
            $money += $this->resource->type->money;
        }
        if ($this->road) {
            $money++;
        }
		return $money;
	}

    /**
     * @param $mission MissionType
     */
	public function get_mission_need_points($mission) {
        $need_points = 0;
        if (count($mission->need_points) > 0) {
            if (isset($mission->need_points[$this->type->id])) {
                $need_points = $mission->need_points[$this->type->id];
            } else {
                return 0;
            }
        }
        $complete_points = MyDB::query("SELECT sum(mission_points) FROM `unit` WHERE x = '?x' AND y = '?y' AND planet = '?planet' AND mission = '?mission'",
            [   'x' => $this->x,
                'y' => $this->y,
                'planet' => $this->planet,
                'mission' => $mission->id], 'elem');
        $need_points = $need_points - $complete_points;
        if ($need_points < 0) {
            $need_points = 0;
        }
        return $need_points;
    }
}
?>