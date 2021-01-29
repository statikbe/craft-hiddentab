<?php

namespace statikbe\hiddentab\assetbundles\hud;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class Hud extends AssetBundle {
    public function init()
    {
        $this->sourcePath = "@statikbe/hiddentab/assetbundles/hud";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/Hud.js'
        ];

        parent::init();
    }
}