<?php ///[yongtiger/yii2-bootstrap-tree]

/**
 * Yii2 Bootstrap Tree
 *
 * @link        http://www.brainbook.cc
 * @see         https://github.com/yongtiger/yii2-tree-manager
 * @author      Tiger Yong <tigeryang.brainbook@outlook.com>
 * @copyright   Copyright (c) 2017 BrainBook.CC
 * @license     http://opensource.org/licenses/MIT
 */

namespace yongtiger\bootstraptree;

use yii\web\AssetBundle;

/**
 * Class TreeAsset
 *
 * @package yongtiger\bootstraptree
 */
class BootstrapTreeAsset extends AssetBundle
{
    public $sourcePath = '@bower/bootstrap-treeview/dist';

    public $js = [
        'bootstrap-treeview.min.js'
    ];

    public $css = [
        'bootstrap-treeview.min.css'
    ];

    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset'
    ];
}