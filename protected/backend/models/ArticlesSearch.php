<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Articles;

/**
 * ArticlesSearch represents the model behind the search form about `common\models\Articles`.
 */
class ArticlesSearch extends Articles
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_id', 'publication_id', 'written_date', 'total_spots', 'total_delights', 'status'], 'integer'],
            [['article_template', 'cover_photo', 'hashtag', 'hashtag_description', 'title', 'author', 'article_description', 'category', 'created_at', 'updated_at'], 'safe'],
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
    public function search($params,$publication_id)
    {
        $query = Articles::find();

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
            'article_id' => $this->article_id,
            'publication_id' => $publication_id,
            'written_date' => $this->written_date,
            'total_spots' => $this->total_spots,
            'total_delights' => $this->total_delights,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'article_template', $this->article_template])
            ->andFilterWhere(['like', 'cover_photo', $this->cover_photo])
            ->andFilterWhere(['like', 'hashtag', $this->hashtag])
            ->andFilterWhere(['like', 'hashtag_description', $this->hashtag_description])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'author', $this->author])
            ->andFilterWhere(['like', 'article_description', $this->article_description])
            ->andFilterWhere(['like', 'category', $this->category]);

        return $dataProvider;
    }
}
