<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Dishes;

/**
 * DishesSearch represents the model behind the search form about `common\models\Dishes`.
 */
class DishesSearch extends Dishes
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'cuisine_id', 'meal_id', 'quick_food_id', 'status'], 'integer'],
            [['title', 'user_id', 'user_name', 'cuisine_name', 'meal_name', 'quick_food_name', 'description', 'price', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Dishes::find()
			->select([
				'dishes.*',
				'IFNULL(avg(ratings.dish_rating),0) as avg_dish_rating',
				'IFNULL(avg(ratings.quality_rating),0) as avg_quality_rating',
				'IFNULL(avg(ratings.appearance_rating),0) as avg_appearance_rating',
				'count(ratings.id) as total_ratings'
			])
			->leftJoin('ratings','ratings.dish_id = dishes.id')
			->groupBy(['dishes.id'])
			->with(['user' => function($query) {
				$query->with([
					'restaurantDetails' =>  function($query) {
						$query->with(['country']);
					}
				]);
			},
			'dishPrimaryImage' => function($query){
				$query->where(['type' => 1]);
			}]);
		$query->joinWith(['user']);
		$query->joinWith(['cuisine','meal','quickFood']);
		
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		
		$dataProvider->setSort([
            'attributes' => [
				'title','created_at','status',
				'user_name' => [
                    'asc' => ['users.name' => SORT_ASC],
                    'desc' => ['users.name' => SORT_DESC],
                ],
				'cuisine_name' => [
                    'asc' => ['cuisines.name' => SORT_ASC],
                    'desc' => ['cuisines.name' => SORT_DESC],
                ],
				'meal_name' => [
                    'asc' => ['meals.name' => SORT_ASC],
                    'desc' => ['meals.name' => SORT_DESC],
                ],
				'quick_food_name' => [
                    'asc' => ['quick_foods.name' => SORT_ASC],
                    'desc' => ['quick_foods.name' => SORT_DESC],
                ],
				'price' => [
                    'asc' => ['CAST(price AS DECIMAL)' => SORT_ASC],
                    'desc' => ['CAST(price AS DECIMAL)' => SORT_DESC],
                ],
				'avg_dish_rating' => [
                    'asc' => ['CAST(IFNULL(avg(ratings.dish_rating),0) AS DECIMAL)' => SORT_ASC],
                    'desc' => ['CAST(IFNULL(avg(ratings.dish_rating),0) AS DECIMAL)' => SORT_DESC],
                ],
				'avg_quality_rating' => [
                    'asc' => ['CAST(IFNULL(avg(ratings.quality_rating),0) AS DECIMAL)' => SORT_ASC],
                    'desc' => ['CAST(IFNULL(avg(ratings.quality_rating),0) AS DECIMAL)' => SORT_DESC],
                ],
				'avg_appearance_rating' => [
                    'asc' => ['CAST(IFNULL(avg(ratings.appearance_rating),0) AS DECIMAL)' => SORT_ASC],
                    'desc' => ['CAST(IFNULL(avg(ratings.appearance_rating),0) AS DECIMAL)' => SORT_DESC],
                ],
				'total_ratings' => [
                    'asc' => ['CAST(count(ratings.id) AS DECIMAL)' => SORT_ASC],
                    'desc' => ['CAST(count(ratings.id) AS DECIMAL)' => SORT_DESC],
                ]
			],
			'defaultOrder' => ['created_at' => SORT_DESC]
        ]);
		
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'dishes.user_id' => $this->user_id,
            'dishes.cuisine_id' => $this->cuisine_id,
            'dishes.meal_id' => $this->meal_id,
            'dishes.quick_food_id' => $this->quick_food_id,
            'dishes.status' => $this->status,
            'dishes.created_at' => $this->created_at,
            'dishes.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'dishes.title', $this->title])
            ->andFilterWhere(['like', 'dishes.description', $this->description])
            ->andFilterWhere(['like', 'dishes.price', $this->price])
			->andFilterWhere(['like', 'users.name', $this->user_name])
			->andFilterWhere(['like', 'cuisines.name', $this->cuisine_name])
			->andFilterWhere(['like', 'meals.name', $this->meal_name])
			->andFilterWhere(['like', 'quick_foods.name', $this->quick_food_name]);
		
        return $dataProvider;
    }
}
