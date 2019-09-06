<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PublisherPost;

/**
 * PublisherPostSearch represents the model behind the search form about `common\models\PublisherPost`.
 */
class PublisherPostSearch extends PublisherPost
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['publisher_post_id', 'article_id', 'restaurant_id', 'menu_item_id', 'status'], 'integer'],
            [['post_title', 'restaurant_photo', 'address', 'city', 'state', 'phone_number', 'website', 'establishment_type', 'support_title', 'supporting_photo', 'supporting_text', 'additional_info', 'created_at', 'updated_at'], 'safe'],
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
    public function search($params,$article_id)
    {
        $query = PublisherPost::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['updated_at' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'publisher_post_id' => $this->publisher_post_id,
            'article_id' => $article_id,
            'restaurant_id' => $this->restaurant_id,
            'menu_item_id' => $this->menu_item_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'post_title', $this->post_title])
            ->andFilterWhere(['like', 'restaurant_photo', $this->restaurant_photo])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'state', $this->state])
            ->andFilterWhere(['like', 'phone_number', $this->phone_number])
            ->andFilterWhere(['like', 'website', $this->website])
            ->andFilterWhere(['like', 'establishment_type', $this->establishment_type])
            ->andFilterWhere(['like', 'support_title', $this->support_title])
            ->andFilterWhere(['like', 'supporting_photo', $this->supporting_photo])
            ->andFilterWhere(['like', 'supporting_text', $this->supporting_text])
            ->andFilterWhere(['like', 'additional_info', $this->additional_info]);

        return $dataProvider;
    }
}
