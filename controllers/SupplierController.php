<?php

namespace app\controllers;

use app\models\Supplier;
use app\models\SupplierSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii2tech\csvgrid\CsvGrid;

/**
 * SupplierController implements the CRUD actions for Supplier model.
 */
class SupplierController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Supplier models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SupplierSearch();

        $dataProvider = $searchModel->search($this->request->queryParams);

        $dataProvider->pagination->pageSize=5;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSearchCode()
    {
        $listdata = SupplierSearch::find()
            ->andFilterWhere(['like', 'code', $this->request->queryParams['query']])
            ->select(['code as value', 'code as label'])
            ->distinct('code')
            ->asArray()
            ->all();


        return $this->asJson(['listdata' => $listdata]);
    }

    public function actionSearchName()
    {
        $listdata = SupplierSearch::find()
            ->andFilterWhere(['like', 'name', $this->request->queryParams['query']])
            ->select(['name as value', 'name as label'])
            ->distinct('name')
            ->asArray()
            ->all();


        return $this->asJson(['listdata' => $listdata]);
    }

    /**
     * Displays a single Supplier model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
//    public function actionView($id)
//    {
//        return $this->render('view', [
//            'model' => $this->findModel($id),
//        ]);
//    }

    /**
     * Creates a new Supplier model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Supplier();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['index']);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Supplier model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        }
//
//        return $this->render('update', [
//            'model' => $model,
//        ]);
//    }

    /**
     * Deletes an existing Supplier model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * Finds the Supplier model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Supplier the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Supplier::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(\Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionExport()
    {
//        print_r($this->request->getBodyParams());exit;
        $getAll = $this->request->getBodyParam('checkall', false);
        $getIds = $this->request->getBodyParam('rows', '');
        $data = $this->request->getBodyParam('data', '');
        $getIds = json_decode($getIds,true);
        $data = json_decode($data,true);
        $name = $data['SupplierSearch']['name'] ?? '';
        $code = $data['SupplierSearch']['code'] ?? '';
        $t_status = $data['SupplierSearch']['code'] ?? '';
        $id = $data['SupplierSearch']['id'] ?? '';

        $query = SupplierSearch::find();
        $query->andFilterWhere(['like', 'name', $name])
            ->andFilterWhere(['like', 'code', $code])
            ->andFilterWhere(['like', 't_status', $t_status]);

        // grid filtering conditions
        $operator = SupplierSearch::getOperator($id);
        $query->andFilterWhere([$operator, 'id', str_replace($operator,'',$id)]);

        if ($getAll != true) {
            $query->andFilterWhere(['in', 'id', $getIds]);
        }
        if (empty($getIds)) {
            throw new NotFoundHttpException(\Yii::t('app', 'The requested page does not exist.'));
        }
        $exporter = new CsvGrid([
            'dataProvider' => new ActiveDataProvider([
                'query' => $query,
            ]),
        ]);
        return $exporter->export()->send(sprintf('suppliers%s.csv',date("YmdHis")));
    }
}
