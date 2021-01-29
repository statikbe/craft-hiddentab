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
use craft\events\TemplateEvent;
use craft\services\Plugins;
use craft\web\View;
use statikbe\hiddentab\assetbundles\hiddentab\HiddenTabBundle;
use statikbe\hiddentab\assetbundles\hud\Hud;
use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    Statik.be
 * @package   HiddenTab
 * @since     1.0.0
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

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public $hasCpSettings = false;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * HiddenTab::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
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
        $currentUser = Craft::$app->getUser()->getIdentity();
        if (!$currentUser->admin) {

            Craft::$app->view->hook('cp.elements.element', function (array &$context) {
                /** @var Entry $element */
                $element = $context['element'];
                if (!$element) {
                    return;
                }
                $tabs = array_filter($element->getFieldLayout()->getTabs(), function ($tab) {
                    if (strtolower($tab->name) === 'hidden') {
                        return $tab;
                    }
                    return;
                });
                if (!$tabs) {
                    return;
                }
                $key = array_key_first($tabs);
                $handles = [];
                foreach ($tabs[$key]->elements as $e) {
                    $handles[] = "fields-" . $e->getField()->handle;
                }
                $handles = json_encode($handles);
                Craft::$app->getView()->registerJs("fieldsToHide = $handles;", View::POS_BEGIN);
                Craft::$app->getView()->registerAssetBundle(Hud::class);
            });
        }

        $currentUser = Craft::$app->getUser()->getIdentity();
        if (!$currentUser->admin) {
            Craft::$app->view->hook('cp.entries.edit', function (array &$context) {
                Craft::$app->getView()->registerAssetBundle(HiddenTabBundle::class);
            });
            Craft::$app->view->hook('cp.categories.edit', function (array &$context) {
                Craft::$app->getView()->registerAssetBundle(HiddenTabBundle::class);
            });
            Craft::$app->view->hook('cp.assets.edit', function (array &$context) {
                Craft::$app->getView()->registerAssetBundle(HiddenTabBundle::class);
            });
            Craft::$app->view->hook('cp.users.edit', function (array &$context) {
                Craft::$app->getView()->registerAssetBundle(HiddenTabBundle::class);
            });
            Craft::$app->view->hook('cp.globals.edit', function (array &$context) {
                Craft::$app->getView()->registerAssetBundle(HiddenTabBundle::class);
            });
        }

        /**
         * Logging in Craft involves using one of the following methods:
         *
         * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
         * Craft::info(): record a message that conveys some useful information.
         * Craft::warning(): record a warning message that indicates something unexpected has happened.
         * Craft::error(): record a fatal error that should be investigated as soon as possible.
         *
         * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
         *
         * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
         * the category to the method (prefixed with the fully qualified class name) where the constant appears.
         *
         * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
         * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
         *
         * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
         */
        Craft::info(
            Craft::t(
                'hidden-tab',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

}
