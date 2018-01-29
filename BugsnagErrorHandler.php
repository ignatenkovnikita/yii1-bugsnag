<?php
/**
 * @copyright Copyright (c) 2018 Ivan Orlov
 * @license   https://github.com/demisang/yii1-bugsnag/blob/master/LICENSE
 * @link      https://github.com/demisang/yii1-bugsnag#readme
 * @author    Ivan Orlov <gnasimed@gmail.com>
 */

namespace demi\bugsnag\yii1;

use Yii;
use CErrorHandler;

/**
 * Bugsnag error handler class for Yii1
 * Send exceptions to bugsnag
 */
class BugsnagErrorHandler extends CErrorHandler
{
    /**
     * Name of bugsnag component: Yii::app()->bugsnag
     *
     * @var string
     */
    public $bugsnagComponentName = 'bugsnag';

    /**
     * @inheritdoc
     */
    protected function handleException($exception)
    {
        // Do not track 404-errors
        if ($exception instanceof \CHttpException && $exception->statusCode == 404) {
            parent::handleException($exception);

            return;
        }

        /** @var \demi\bugsnag\yii1\BugsnagComponent $bugsnag */
        $bugsnag = Yii::app()->getComponent($this->bugsnagComponentName);

        // Notify bugsnag exception
        $bugsnag->notifyException($exception);

        parent::handleException($exception);
    }
}
