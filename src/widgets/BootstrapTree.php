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
use yii\helpers\Url;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
use yongtiger\bootstraptree\BootstrapTreeAsset;

/**
 * Class BootstrapTree for both jonmiles bootstrap treeview 1.2.0 and 2.0.0
 *
 * @see https://github.com/jonmiles/bootstrap-treeview
 * @package yongtiger\bootstraptree\widgets
 */
class BootstrapTree extends Widget
{
    /**
     * Head tag
     * @var string
     */
    public $tag = 'div';

    /**
     * @var array additional HTML attributes that will be rendered in the tag.
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
     *       enableLinks: false,    ///only jonmiles bootstrap-treeview 1.2.0, obsoleted in jonmiles bootstrap-treeview 2.0.0
     *       highlightSelected: true,
     *       highlightSearchResults: true,
     *       showBorder: true,
     *       showIcon: true,
     *       showCheckbox: false,
     *       showTags: false,
     *       multiSelect: false,
     *       preventUnselect: false,    ///from jonmiles bootstrap-treeview 2.0.0
     *
     * @see //https://github.com/jonmiles/bootstrap-treeview#options
     * @var array
     */
    public $options = [];

    /**
     * @var string
     */
    public static $autoIdPrefix = 'treeview_';

    /**
     * @var array additional options that can be passed to the constructor of the treeview js object.
     */
    public $events = [];

    /**
     * @var bool whether the texts for nodes should be HTML-encoded.
     */
    public $encodeTexts = true;
    /**
     * @var string the template used to render the body of a node which is NOT a link.
     * In this template, the token `{text}` will be replaced with the text of the node.
     * Note: For using jonmiles bootstrap-treeview 2.0.0, must specify it as '<a href="{href}">{text}</a>' in the treeview widget options.
     */
    public $textTemplate = '{text}';
    /**
     * @var bool whether to automatically select nodes according to whether their route setting
     * matches the currently requested route.
     * @see isNodeSelected()
     */
    public $selectNodes = true;
    /**
     * @var bool whether to select parent nodes when one of the corresponding child nodes is selected.
     * Note: when it is true, you must also set `multiSelect` of the treeview widget options to true!
     */
    public $selectParents = false;
    /**
     * @var bool whether to hide empty nodes. An empty node is one whose `href` option is not
     * set and which has no visible child nodes.
     */
    public $hideEmptyNodes = true;
    /**
     * @var string the route used to determine if a node is selected or not.
     * If not set, it will use the route of the current request.
     * @see params
     * @see isNodeSelected()
     */
    public $route;
    /**
     * @var array the parameters used to determine if a node is selected or not.
     * If not set, it will use `$_GET`.
     * @see route
     * @see isNodeSelected()
     */
    public $params;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }
        if ($this->params === null) {
            $this->params = Yii::$app->request->getQueryParams();
        }
        $this->options['data'] = $this->normalizeNodes($this->options['data'], $hasSelectedChild);

        $view = $this->getView();
        BootstrapTreeAsset::register($view);

        $options = $this->_getEventsOptions();
        $options = $options === [] ? '{}' : Json::encode($options);
        $id = ArrayHelper::getValue($this->htmlOptions, 'id', $this->getId());
        $view->registerJs("$('#{$id}').treeview($options);", View::POS_READY);

        echo Html::tag($this->tag, '', $this->htmlOptions);;
    }

    /**+
     * @return array the javascript options
     */
    protected function _getEventsOptions()
    {
        $options = $this->options;
        foreach($this->events as $key => $event)
        {
            $options[$key] = new JsExpression($event);
        }
        return $options;
    }

    /**
     * Normalizes the [[nodes]] property to remove invisible nodes and select certain nodes.
     * @param array $nodes the nodes to be normalized.
     * @param bool $select whether there is an select child node.
     * @return array the normalized nodes
     */
    protected function normalizeNodes($nodes, &$select)
    {
        foreach ($nodes as $i => $node) {
            if (isset($node['visible']) && !$node['visible']) {
                unset($nodes[$i]);
                continue;
            }
            if (!isset($node['text'])) {
                $node['text'] = '';
            }
            $encodeText = isset($node['encode']) ? $node['encode'] : $this->encodeTexts;
            $nodes[$i]['text'] = $encodeText ? Html::encode($node['text']) : $node['text'];
            $hasSelectedChild = false;
            if (isset($node['nodes'])) {
                $nodes[$i]['nodes'] = $this->normalizeNodes($node['nodes'], $hasSelectedChild);
                if (empty($nodes[$i]['nodes']) && $this->hideEmptyNodes) {
                    unset($nodes[$i]['nodes']);
                    if (!isset($node['href'])) {
                        unset($nodes[$i]);
                        continue;
                    }
                }
            }
            if (!isset($node['state']['selected'])) {
                if ($this->selectParents && $hasSelectedChild || $this->selectNodes && $this->isNodeSelected($node)) {
                    $select = $nodes[$i]['state']['selected'] = true;
                } else {
                    $nodes[$i]['state']['selected'] = false;
                }
            } elseif ($node['state']['selected'] instanceof Closure) {
                $select = $nodes[$i]['state']['selected'] = call_user_func($node['state']['selected'], $node, $hasSelectedChild, $this->isNodeSelected($node), $this);
            } elseif ($node['state']['selected']) {
                $select = true;
            }

            ///Normalize `$nodes[$i]['text']` and `$nodes[$i]['href']` (if set).
            $template = ArrayHelper::getValue($nodes[$i], 'template', $this->textTemplate);
            if (isset($nodes[$i]['href'])) {
                $nodes[$i]['href'] = Url::to($nodes[$i]['href']);
            }
            $nodes[$i]['text'] = strtr($template, [
                '{href}' => $nodes[$i]['href'],
                '{text}' => $nodes[$i]['text'],
            ]);
        }

        return array_values($nodes);
    }

    /**
     * Checks whether a node is selected.
     * This is done by checking if [[route]] and [[params]] match that specified in the `href` option of the node.
     * When the `href` option of a node is specified in terms of an array, its first element is treated
     * as the route for the node and the rest of the elements are the associated parameters.
     * Only when its route and parameters match [[route]] and [[params]], respectively, will a node
     * be considered selected.
     * @param array $node the node to be checked
     * @return bool whether the node is selected
     */
    protected function isNodeSelected($node)
    {
        if (isset($node['href']) && is_array($node['href']) && isset($node['href'][0])) {
            $route = Yii::getAlias($node['href'][0]);
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }
            if (ltrim($route, '/') !== $this->route) {
                return false;
            }
            unset($node['href']['#']);
            if (count($node['href']) > 1) {
                $params = $node['href'];
                unset($params[0]);
                foreach ($params as $name => $value) {
                    if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }
}