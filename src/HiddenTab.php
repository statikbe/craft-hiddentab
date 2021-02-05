<?php
/**
 * Hidden Tab plugin for Craft CMS 3.x
 *
 * For old time's sake ;)
 *
 * @link      https://www.statik.be
 * @copyright Copyright (c) 2021 Statik.be
 */

namespace statikbe\hiddentab;

use Craft;
use craft\base\Plugin;
use craft\elements\Entry;
use craft\events\PluginEvent;
use craft\services\Plugins;
use statikbe\hiddentab\assetbundles\hiddentab\HiddenTabBundle;
use statikbe\hiddentab\services\HudService;
use yii\base\Event;

/**
 *
 * @author    Statik.be
 * @package   HiddenTab
 * @since     1.0.0
 * @property HudService hud
 *
 */
class HiddenTab extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * HiddenTab::$plugin
     *
     * @var HiddenTab
     */
    public static $plugin;

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );

        $shouldRun = false;
        $currentUser = Craft::$app->getUser()->getIdentity();
        if (($currentUser && !$currentUser->admin) && Craft::$app->getRequest()->isCpRequest) {
            $shouldRun = true;
        }

        if ($shouldRun) {
            Craft::$app->view->hook('cp.elements.element', function (array &$context) {
                /** @var Entry $element */
                $element = $context['element'];
                if (!$element) {
                    return;
                }
                $this->hud->hideFields($element);
            });
        }

        if ($shouldRun) {
            Craft::$app->view->hook('cp.entries.edit', function (array &$context) {
                self::enableHiddenTab();
            });
            Craft::$app->view->hook('cp.categories.edit', function (array &$context) {
                self::enableHiddenTab();
            });
            Craft::$app->view->hook('cp.assets.edit', function (array &$context) {
                self::enableHiddenTab();
            });
            Craft::$app->view->hook('cp.users.edit', function (array &$context) {
                self::enableHiddenTab();
            });
            Craft::$app->view->hook('cp.globals.edit', function (array &$context) {
                self::enableHiddenTab();
            });
            // Support for craftcms/commerce Products
            Craft::$app->view->hook('cp.commerce.product.edit.content', function (array &$context) {
                self::enableHiddenTab();
            });
            // Support for craftcms/commerce Orders
            Craft::$app->view->hook('cp.commerce.order.edit', function (array &$context) {
                self::enableHiddenTab();
            });
            // Support for solspace/craft3-calendar Events
            Craft::$app->view->hook('cp.solspace.calendar.events.edit.details', function (array &$context) {
                self::enableHiddenTab();
            });
        }

        $this->setComponents([
            'hud' => HudService::class
        ]);

    }

    public static function enableHiddenTab()
    {
        Craft::$app->getView()->registerAssetBundle(HiddenTabBundle::class);
    }
}
