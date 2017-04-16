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
use yii\web\JsExpression;
use yii\web\View;
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
     * @var array additional HTML attributes that will be rendered in the div tag.
     */
    public $htmlOptions = [];

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
     * @var array additional options that can be passed to the constructor of the treeview js object.
     */
    public $events = [];

    protected $_id;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if(isset($this->htmlOptions['id']))
            $this->_id = $this->htmlOptions['id'];
        else
            $this->_id = $this->htmlOptions['id'] = $this->getId();
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

        // $options = Json::htmlEncode($this->options);
        $options = $this->_getEventsOptions();
        $options = $options === [] ? '{}' : Json::encode($options);

        $view->registerJs("$('#{$this->_id}').treeview($options);", View::POS_READY);
        echo $this->renderTree();
    }

    /**
     * @return array the javascript options
     */
    protected function _getEventsOptions()
    {
        $options=$this->options;
        foreach($this->events as $key=>$event)
        {
            $options[$key]=$_function = new JsExpression($event);
        }
        return $options;
    }

    /**
     * Render head element
     * @return string
     */
    private function renderTree()
    {
        return Html::tag($this->tag, '', $this->htmlOptions);
    }
}