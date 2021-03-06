<?php
class ResourceType {
    /**
     * @var string
     */
	public $id;
    /**
     * @var string
     */
	public $title;
    /**
     * Тип ресурса
     * bonuce - дающий + к добыче клетки
     * luxury - роскошь
     * mineral - полезное ископаемое
     * @var string
     */
	public $type = 'bonuce';
    /**
     * Бонус к еде
     * @var int
     */
	public $eat = 0;
    /**
     * Бонус к производству
     * @var int
     */
	public $work = 0;
    /**
     * Бонус к деньгам
     * @var int
     */
	public $money = 0;
    /**
     * Требуемые исследования
     * @var array
     */
    public $req_research = [];
    /**
     * На каких типах местности может распологаться
     * @var array
     */
    public $cell_types = [];

    /**
     * Шанс генерации ресурса на клетках соответсвующих типов
     * @var float
     */
    public $chance = 0.01;
    /**
     * Сколько минимум появляется ресурса(на сколько ходов)
     * @var int
     */
    public $min_amount = 50;
    /**
     * На сколько максимумм появляется ресурса(на сколько ходов)
     * @var int
     */
    public $max_amount = 500;

	public static $all;
	
	public static function get($id) {
		if (isset(ResourceType::$all[$id])) {
			return ResourceType::$all[$id];
		} else {
			return false;
		}
	}
	
	public function __construct($data) {
        foreach ($data as $field => $value) {
            $this->$field = $value;
        }
        ResourceType::$all[$this->id] = $this;
	}
	
	public function get_title() {
		return $this->title;
	}

    /**
     * Проверяет может ли данный игрок видеть и использовать такой ресурс
     * @param User $user
     * @return bool
     */
	public function can_use($user) {
        if (count($this->req_research) == 0) {
            return true;
        }
        $uresearch = $user->get_research();
        foreach ($this->req_research as $research) {
            if (!isset($uresearch[$research->id])) {
                return false;
            }
        }
        return true;
    }
}

new ResourceType([  'id' => 'iron',
                    'title' => 'железо',
                    'type' => 'mineral',
                    'work' => 2,
                    'money' => 1,
                    'chance' => 0.015,
                    'req_research' => [
                        ResearchType::get(7) // Обработка железа
                    ],
                    'cell_types' => [
                        CellType::get('hills'),
                        CellType::get('mountains')
                    ]
                ]);
new ResourceType([  'id' => 'horse',
                    'title' => 'лошади',
                    'type' => 'mineral',
                    'work' => 1,
                    'money' => 1,
                    'chance' => 0.02,
                    'req_research' => [
                        ResearchType::get(4) // Верховая езда
                    ],
                    'cell_types' => [
                        CellType::get('plains'),
                        CellType::get('plains2')
                    ]
                ]);
new ResourceType([  'id' => 'coal',
                    'title' => 'уголь',
                    'type' => 'mineral',
                    'work' => 2,
                    'money' => 1,
                    'req_research' => [
                        //ResearchType::get(4) // Верховая езда
                    ],
                    'cell_types' => [
                        CellType::get('hills'),
                        CellType::get('mountains')
                    ]
                ]);
new ResourceType([  'id' => 'oil',
                    'title' => 'нефть',
                    'type' => 'mineral',
                    'work' => 2,
                    'money' => 2,
                    'req_research' => [
                        //ResearchType::get(4) // Верховая езда
                    ],
                    'cell_types' => [
                        CellType::get('desert'),
                        CellType::get('plains'),
                        CellType::get('plains2')
                    ]
                ]);
new ResourceType([  'id' => 'saltpetre',
                    'title' => 'селитра',
                    'type' => 'mineral',
                    'work' => 2,
                    'money' => 1,
                    'req_research' => [
                        //ResearchType::get(4) // Верховая езда
                    ],
                    'cell_types' => [
                        CellType::get('desert'),
                        CellType::get('plains'),
                        CellType::get('plains2'),
                        CellType::get('hills'),
                        CellType::get('mountains')
                    ]
                ]);
new ResourceType([  'id' => 'rubber',
                    'title' => 'резина',
                    'type' => 'mineral',
                    'work' => 1,
                    'money' => 2,
                    'req_research' => [
                        //ResearchType::get(4) // Верховая езда
                    ],
                    'cell_types' => [
                        CellType::get('desert'),
                        CellType::get('plains'),
                        CellType::get('plains2'),
                        CellType::get('mountains')
                    ]
                ]);
new ResourceType([  'id' => 'uranium',
                    'title' => 'уран',
                    'type' => 'mineral',
                    'work' => 1,
                    'money' => 1,
                    'req_research' => [
                        //ResearchType::get(4) // Верховая езда
                    ],
                    'cell_types' => [
                        CellType::get('desert'),
                        CellType::get('hills'),
                        CellType::get('mountains')
                    ]
                ]);
new ResourceType([  'id' => 'vine',
                    'title' => 'виноград',
                    'type' => 'luxury',
                    'eat' => 1,
                    'money' => 2,
                    'chance' => 0.02,
                    'cell_types' => [
                        CellType::get('plains'),
                        CellType::get('plains2')
                    ]
                ]);
new ResourceType([  'id' => 'ivory',
                    'title' => 'слоновая кость',
                    'type' => 'luxury',
                    'work' => 1,
                    'money' => 2,
                    'cell_types' => [
                        CellType::get('desert')
                    ]
                ]);
new ResourceType([  'id' => 'silk',
                    'title' => 'шёлк',
                    'type' => 'luxury',
                    'work' => 2,
                    'money' => 1,
                    'chance' => 0.02,
                    'cell_types' => [
                        CellType::get('plains'),
                        CellType::get('plains2'),
                        CellType::get('hills')
                    ]
                ]);
new ResourceType([  'id' => 'furs',
                    'title' => 'меха',
                    'type' => 'luxury',
                    'work' => 1,
                    'eat' => 1,
                    'money' => 1,
                    'cell_types' => [
                        CellType::get('forest')
                    ]
                ]);
new ResourceType([  'id' => 'fish',
                    'title' => 'рыба',
                    'type' => 'bonuce',
                    'chance' => 0.05,
                    'eat' => 2,
                    'cell_types' => [
                        CellType::get('water1')
                    ]
                ]);
new ResourceType([  'id' => 'whale',
                    'title' => 'киты',
                    'type' => 'bonuce',
                    'chance' => 0.03,
                    'eat' => 1,
                    'money' => 1,
                    'cell_types' => [
                        CellType::get('water2')
                    ]
                ]);