<?php

namespace statikbe\hiddentab\services;

use Craft;
use craft\base\Component;
use craft\base\ElementInterface;
use craft\web\View;
use statikbe\hiddentab\assetbundles\hud\Hud;

class HudService extends Component
{
    public function hideFields(ElementInterface $element)
    {
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
    }
}