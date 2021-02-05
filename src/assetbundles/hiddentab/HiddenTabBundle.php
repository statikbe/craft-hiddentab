<?php

namespace statikbe\hiddentab\assetbundles\hiddentab;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class HiddenTabBundle extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = "@statikbe/hiddentab/assetbundles/hiddentab";

        $this->depends = [
            CpAsset::class,
        ];

        $this->css = [
            'css/HiddenTab.css'
        ];

        parent::init();
    }
}