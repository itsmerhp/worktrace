<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Publications;

/**
 * PublicationSearch represents the model behind the search form about `common\models\Publications`.
 */
class PublicationSearch extends Publications
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['publication_id', 'targeted_radius', 'status'], 'integer'],
            [['title', 'cover_photo', 'created_at', 'updated_at'], 'safe'],
            [['latitude', 'longitude'], 'number'],
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
        $query = Publications::find();

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
            'publication_id' => $this->publication_id,
            'targeted_radius' => $this->targeted_radius,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title]);
            //->andFilterWhere(['like', 'cover_photo', $this->cover_photo]);

        return $dataProvider;
    }
}
