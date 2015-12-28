<?php

namespace DotPlant\ReviewsExt;

use app\backend\events\BackendEntityEditFormEvent;
use app\components\ExtensionModule;
use app\modules\review\controllers\BackendReviewController;
use app\modules\review\models\Review;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\web\View;
use yii\db\ActiveRecord;
use \DotPlant\Reviews\behaviors\ReviewsBehavior;
use \DotPlant\Reviews\handlers\ReviewsHandler;

class Module extends ExtensionModule implements BootstrapInterface
{
    public static $moduleId = 'ReviewsExt';

    public function bootstrap($app)
    {

        $app->on(
            Application::EVENT_BEFORE_ACTION,
            function () use ($app) {
                /**
                 * Этот кусок отвечает за добавление пустого submission к отзыву,
                 * когда отзыв создается в админке
                 */
                Event::on(
                    Review::className(),
                    ActiveRecord::EVENT_INIT,
                    [ReviewsBehavior::className(), 'handleInit']
                );
                /**
                 * Здесь мы прикручиваем логику при сохранении отзывов в админке, чтобы иметь возможность
                 * добавлять свойства к отзыву из админки
                 */
                Event::on(
                    BackendReviewController::className(),
                    BackendReviewController::EVENT_BEFORE_ACTION,
                    [ReviewsHandler::className(), 'saveReviewProperties']
                );
                if ($app->requestedAction->controller instanceof BackendReviewController) {
                    BackendEntityEditFormEvent::on(
                        View::className(),
                        BackendReviewController::BACKEND_REVIEW_EDIT_FORM,
                        [$this, 'renderEditForm']
                    );
                }
            }
        );
    }

    public function renderEditForm(BackendEntityEditFormEvent $event)
    {

        if (isset($event->model) === false) {
            return null;
        }

        $params = [
            'form' => $event->form,
            'review' => $event->model
        ];

        $view = $event->sender;

        echo $view->render('@ReviewsExt/views/edit', $params);
    }

}
