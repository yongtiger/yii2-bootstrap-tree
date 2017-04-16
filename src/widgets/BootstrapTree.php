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

namespace yongtiger\bootstraptree\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yongtiger\bootstraptree\BootstrapTreeAsset;

/**
 * Class BootstrapTree
 *
 * @package yongtiger\bootstraptree\widgets
 */
class BootstrapTree extends Widget
{
    /**
     * Head element tag
     * @var string
     */
    public $tag = 'div';

    /**
     * Head element options
     * @var array
     */
    public $elementOptions = [];

    /**
     * Options
     * Default options:
     * 		injectStyle: true,
     *
     *       levels: 2,
     *       
     *       expandIcon: 'glyphicon glyphicon-plus',
     *       collapseIcon: 'glyphicon glyphicon-minus',
     *       emptyIcon: 'glyphicon',
     *       nodeIcon: '',
     *       selectedIcon: '',
     *       checkedIcon: 'glyphicon glyphicon-check',
     *       uncheckedIcon: 'glyphicon glyphicon-unchecked',
     *       
     *       color: undefined, // '#000000',
     *       backColor: undefined, // '#FFFFFF',
     *       borderColor: undefined, // '#dddddd',
     *       onhoverColor: '#F5F5F5',
     *       selectedColor: '#FFFFFF',
     *       selectedBackColor: '#428bca',
     *       searchResultColor: '#D9534F',
     *       searchResultBackColor: undefined, //'#FFFFFF',
     *       
     *       enableLinks: false,
     *       highlightSelected: true,
     *       highlightSearchResults: true,
     *       showBorder: true,
     *       showIcon: true,
     *       showCheckbox: false,
     *       showTags: false,
     *       multiSelect: false,
     * @var array
     */
    public $options = [
    ];

    /**
     * @inheritdoc
     */
    public static $autoIdPrefix = 'tree';

    /**
     * @var TreeNode[]
     */
    public $nodes = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public static function begin($config = [])
    {
        $tree = parent::begin($config);
        if (!($tree->nodes)) {
            throw new \Exception('Node is not found');
        }
        return $tree;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $view = $this->getView();
        BootstrapTreeAsset::register($view);
        $this->options['data'] = $this->nodes;
        $options = Json::htmlEncode($this->options);
        $view->registerJs("$('#{$this->getId()}').treeview($options);");
        echo $this->renderTree();
    }

    /**
     * Render head element
     * @return string
     */
    private function renderTree()
    {
        $this->elementOptions['id'] = $this->getId();
        return Html::tag($this->tag, '', $this->elementOptions);
    }
}