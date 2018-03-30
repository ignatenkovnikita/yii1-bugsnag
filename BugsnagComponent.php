<?php
/**
 * @copyright Copyright (c) 2018 Ivan Orlov
 * @license   https://github.com/demisang/yii1-bugsnag/blob/master/LICENSE
 * @link      https://github.com/demisang/yii1-bugsnag#readme
 * @author    Ivan Orlov <gnasimed@gmail.com>
 */

namespace demi\bugsnag\yii1;

use Bugsnag\Client;
use MrClay\Cli;
use Yii;
use Bugsnag_Client;

/**
 * Bugsnag component
 *
 * @property-read Client $client
 */
class BugsnagComponent extends \CApplicationComponent
{
    /**
     * You bugsnag api key
     *
     * @var string
     */
    public $bugsnagApiKey;
    /**
     * Set which release stages should be allowed to notify Bugsnag
     * Eg. array("production", "development")
     *
     * @var string
     */
    public $releaseStage;
    /**
     * All possible release stages
     *
     * @var array
     */
    public $notifyReleaseStages = ['production', 'development'];
    /**
     * Set the strings to filter out from metaData arrays before sending then to Bugsnag.
     * Eg. array("password", "credit_card")
     *
     * @var array
     */
    public $filters = ['password'];
    /**
     * Absolute path to the root of your application.
     *
     * @var string
     */
    public $projectRoot;
    /**
     * Bugsnag client instance
     *
     * @var Client
     */
    protected $_client;

    /**
     * Initialize bugsnag client
     *
     * @throws \Exception
     */
    public function init()
    {
        parent::init();

        if (empty($this->bugsnagApiKey)) {
            throw new \Exception('You must set bugsnag API key');
        }

        // Config
        if ($this->releaseStage === null) {
            $this->releaseStage = defined('YII_DEBUG') && YII_DEBUG ? 'development' : 'production';
        }
    }

    /**
     * Get bugsnag client instance
     *
     * @return Client
     */
    public function getClient()
    {
        if ($this->_client) {
            return $this->_client;
        }

        // Client
        $client = Client::make($this->bugsnagApiKey);
//        $client->setNotifyReleaseStages($this->notifyReleaseStages);
//        $client->setReleaseStage($this->releaseStage);
//        $client->setFilters($this->filters);
        // Set project root
//        if ($this->projectRoot !== null) {
//            $client->setProjectRoot($this->projectRoot);
//        }
        // Set user info
        $user = $this->getUserData();
        if ($user) {
            $client->registerCallback(function ($report) {
                $report->setUser([
                    'id' => '123456',
                    'name' => 'Leeroy Jenkins',
                    'email' => 'leeeeroy@jenkins.com',
                ]);
            });
//            $client->setUser($user);
        }

        // Store client
        $this->_client = $client;

        return $this->_client;
    }

    /**
     * Notify Bugsnag of a non-fatal/handled throwable
     *
     * @param \Throwable $throwable the throwable to notify Bugsnag about
     * @param array $metaData optional metaData to send with this error
     * @param String $severity optional severity of this error (fatal/error/warning/info)
     */
    public function notifyException($throwable, array $metaData = null, $severity = null)
    {
        $this->client->notifyException($throwable, $callback = null);
    }

    /**
     * Notify Bugsnag of a non-fatal/handled error
     *
     * @param String $name the name of the error, a short (1 word) string
     * @param String $message the error message
     * @param array $metaData optional metaData to send with this error
     * @param String $severity optional severity of this error (fatal/error/warning/info)
     */
    public function notifyError($name, $message)
    {
        $this->client->notifyError($name, $message, $callback = null);
    }

    /**
     * Returns user information
     *
     * @return array
     */
    public function getUserData()
    {
        if (!Yii::app()->hasComponent('user') || Yii::app()->user->isGuest) {
            return null;
        }

        return ['id' => Yii::app()->user->id];
    }
}
