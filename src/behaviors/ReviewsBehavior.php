<?php

namespace DotPlant\ReviewsExt\behaviors;

use app\models\Form;
use app\models\Submission;
use app\modules\review\models\Review;
use yii\base\Behavior;
use yii\base\Event;

class ReviewsBehavior extends Behavior {

    public function events() {
        return [
            Review::EVENT_AFTER_INSERT => 'handleAfterInsert',
        ];
    }

    static public function handleInit(Event $event) {
        $model = $event->sender;
        $model->attachBehaviors([static::className()]);
    }

    public function handleAfterInsert(Event $event) {

        $review = $this->owner;
        if (null === $form = Form::findById(1)) {
            return;
        }

        $submission = new Submission();
        $submission->loadDefaultValues();
        $submission->form_id = $form->id;
        $submission->user_agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/45.0.2454.101 Chrome/45.0.2454.101 Safari/537.36';
        $submission->date_received = Date("Y-m-d H:i:s");
        $submission->ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        $submission->submission_referrer = 'localhost';
        if (true === $submission->save()) {
            $submission->addPropertyGroup(3, true, true);
            $review->submission_id = $submission->id;
            $review->parent_id = 0;
            $review->root_id = $review->id;
            $review->save(true, ['submission_id', 'root_id', 'parent_id']);
        }
    }
}
