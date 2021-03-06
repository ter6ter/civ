<?php
class UnitType {
    /**
     * @var int
     */
    public $id;
    /**
     * Название
     * @var string
     */
	public $title;
    /**
     * Максимальные очки движений
     * @var int
     */
	public $points;
    /**
     * стоимость постройки, производство
     * @var int
     */
	public $cost;
    /**
     * Стоимость постройки населения
     * @var int
     */
	public $population_cost = 0;
    /**
     * Тип юнита - land, water, air
     * @var string
     */
	public $type = 'land';
    /**
     * @var int
     */
    public $attack = 0;
    /**
     * @var int
     */
    public $defence = 0;
    /**
     * По каким типам местности может ходить и с какими затратами перемещения
     * @var array
     */
	public $can_move = ['plains' => 1, 
						'plains2' => 1, 
						'forest' => 1,
						'hills' => 1,
						'mountains' => 2,
						'desert' => 1,
                        'city' => 1];
    /**
     * Доступные типы миссий
     * @var array
     */
	public $missions = ['move_to'];
    /**
     * Требуемые исследования
     * @var ResourceType[]
     */
    public $req_research = [];
    /**
     * Требуемые ресурсы
     * @var ResourceType[]
     */
    public $req_resources = [];
	public static $all;
	
	public static function get($id) {
		return (isset(UnitType::$all[$id])) ? UnitType::$all[$id] : false;
	}
	
	public function __construct($data) {
		if (isset($data['id'])) {
			$this->id = $data['id'];
		}
        foreach ($data as $field => $val) {
            $this->$field = $val;
        }
			
		if (isset($data['id'])) {
			UnitType::$all[$this->id] = $this;
		}
	}
	
	public function get_title() {
		return $this->title;
	}
}

new UnitType([	'id' => 1,
				'type' => 'land',
				'cost' => 20,
				'population_cost' => 2,
				'title' => 'поселенец',
				'points' => 1,
				'attack' => 0,
				'defence' => 0,
				'missions' => ['move_to', 'build_city']]);
new UnitType([	'id' => 2,
                'type' => 'land',
				'cost' => 5,
                'population_cost' => 0,
				'title' => 'воин',
				'points' => 1,
                'attack' => 1,
                'defence' => 1]);
new UnitType([	'id' => 3,
                'type' => 'land',
				'cost' => 10,
                'population_cost' => 1,
				'title' => 'рабочий',
				'points' => 1,
                'attack' => 0,
                'defence' => 0,
				'missions' => ['move_to', 'build_road', 'mine', 'irrigation']]);
new UnitType([	'id' => 4,
                'type' => 'land',
                'cost' => 10,
                'population_cost' => 0,
                'title' => 'лучник',
                'points' => 1,
                'attack' => 2,
                'defence' => 1,
                'req_research' => [
                    ResearchType::get(1) // Обработка бронзы
                ]]);
new UnitType([	'id' => 5,
                'type' => 'land',
                'cost' => 10,
                'population_cost' => 0,
                'title' => 'воин с копьём',
                'points' => 1,
                'attack' => 1,
                'defence' => 2]);
new UnitType([	'id' => 6,
                'type' => 'land',
                'cost' => 20,
                'population_cost' => 0,
                'title' => 'воин с мечём',
                'points' => 1,
                'attack' => 3,
                'defence' => 2,
                'req_research' => [
                    ResearchType::get(7) // Обработка железа
                ],
                'req_resources' => [
                    ResourceType::get('iron')
                ]]);
new UnitType([	'id' => 7,
                'type' => 'water',
                'cost' => 20,
                'population_cost' => 0,
                'title' => 'лодка',
                'points' => 2,
                'attack' => 0,
                'defence' => 1,
                'req_research' => [
                    ResearchType::get(3) // Алфавит
                ],
                'can_move' => [ 'water1' => 1,
                                'water2' => 1,
                                'water3' => 1,
                                'city' => 1]
                ]);
new UnitType([	'id' => 8,
                'type' => 'water',
                'cost' => 40,
                'population_cost' => 0,
                'title' => 'галера',
                'points' => 3,
                'attack' => 1,
                'defence' => 1,
                'req_research' => [
                    ResearchType::get(16) // Создание карт
                ],
                'can_move' => [ 'water1' => 1,
                                'water2' => 1,
                                'water3' => 1,
                                'city' => 1]]);
?>