<?php

namespace DotPlant\ReviewsExt\handlers;

use app\models\Submission;
use app\modules\review\models\Review;
use Yii;
use yii\base\Object;
use yii\db\AfterSaveEvent;

class ReviewsHandler extends Object {

    public static function saveReviewProperties(AfterSaveEvent $event) {

        $submission = null;
        $reviewId = Yii::$app->request->get('id');
        $post = Yii::$app->request->post();

        if (null !== $review = Review::findOne(['id' => $reviewId])) {
            $submission = Submission::findOne(['id' => $review->submission_id]);
        } else {
            $m = preg_grep('%Properties_Submission_([\d]+)$%', array_keys($post));
            if (false === empty($m)) {
                $submissionId = array_pop(explode('_', array_shift($m)));
                $submission = Submission::findOne(['id' => $submissionId]);
            }
        }

        if (null !== $submission) {
            if (isset($post['Submission'])) {
                $submission->attributes = $post['Submission'];
                $submission->save();
            }
            $submission->saveProperties($post);
        }
    }
}
