<?php

use app\models\Form;
use app\models\Object;
use app\models\PropertyGroup;
use kartik\form\ActiveForm;
use kartik\icons\Icon;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Review show');
$this->params['breadcrumbs'] = [
    ['label' => Yii::t('app', 'Reviews'), 'url' => ['index']],
    $this->title,
];

$submission = $review->submission;

if (null !== $submission) {
    $formObject = Object::getForClass(Form::className());
    $groups = PropertyGroup::getForModel($formObject->id, $submission->form_id);
    $submission->getPropertyGroups(true);
} else {
    $groups = [];
}
?>

<?php $this->beginBlock('submit'); ?>
    <div class="form-group no-margin">
        <?php if (false === $review->isNewRecord): ?>
            <?= Html::a(Icon::show('minus-square') . Yii::t('app', 'Mark spam'),
                [
                    'mark-spam',
                    'id' => $review->submission_id,
                ],
                ['class' => 'btn btn-danger']
            ); ?>
        <?php endif; ?>
        <div class="btn-group">
            <?php if (false === $review->isNewRecord): ?>
                <?= Html::a(Icon::show('file') . Yii::t('app', 'New'),
                    \yii\helpers\Url::toRoute(['create', 'parent_id' => $review->id]),
                    ['class' => 'btn btn-success']
                ); ?>
            <?php endif; ?>
            <?= Html::a(Icon::show('arrow-circle-left') . Yii::t('app', 'Back'),
                Yii::$app->request->get('returnUrl', ['/review/backend/index']),
                ['class' => 'btn btn-default']
            ); ?>
            <?= Html::submitButton(Icon::show('save') . Yii::t('app', 'Save'), [
                'class' => 'btn btn-primary',
                'name' => 'action',
                'value' => 'save',
            ]); ?>
        </div>
    </div>
<?php $this->endBlock('submit'); ?>

<?php
$form = ActiveForm::begin([
    'id' => 'product-form',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'options' => [
        'enctype' => 'multipart/form-data'
    ]
]);
?>
    <div class="review-show">
        <?php if (false === $review->isNewRecord) : ?>
            <div class="row">
                <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6"></article>
                <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <?=
                    \app\properties\PropertiesWidget::widget([
                        'model' => $submission,
                        'form' => $form,
                    ]);
                    ?>
                </article>
            </div>
        <?php endif; ?>
    </div>
<?php
$form->end();

$_js = <<<'JSCODE'
$(function(){
    $('select#review-object_id').on('change', function(event) {
        $('select#review-object_model_id').val(0).trigger('change');
    });
});
JSCODE;

$this->registerJs($_js, \yii\web\View::POS_END);
