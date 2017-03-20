<?php

class FlowChart extends CWidget
{

    public $actions;
    public $conditions;
    public $idDataFlowchart;
    public $disabled;
    public $urlDetails;
    public $htmlDataField;

    public function init()
    {
        return parent::init();
    }

    public function run()
    {
        if (!is_array($this->actions)) {
            throw new Exception("actions propertie is not array", 500);
        }
        if (!is_array($this->conditions)) {
            throw new Exception("conditions propertie is not array", 500);
        }
        if (empty($this->idDataFlowchart)) {
            throw new Exception("property idDataFlowchart is undefined", 500);
        }
        $this->importAssests();
        $this->render('fidelize.widgets.flowchart.views.index', [
            'actions' => $this->actions,
            'conditions' => $this->conditions,
            'idDataFlowchart' => $this->idDataFlowchart,
            'disabled' => $this->disabled,
            'urlDetails' => $this->urlDetails,
            'htmlDataField' => $this->htmlDataField
        ]);
    }

    private function importAssests()
    {
        $assetsFlowChart = Yii::app()->getAssetManager()->publish(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets', false, -1, YII_DEBUG
        );
        Yii::app()->clientScript->registerCssFile($assetsFlowChart . '/plugins/jsPlumb/css/jsPlumbToolkit-defaults.css');
        Yii::app()->clientScript->registerCssFile($assetsFlowChart . '/plugins/jsPlumb/css/jsPlumbToolkit-demo.css');
        Yii::app()->clientScript->registerCssFile($assetsFlowChart . '/css/flowchart.css');
        Yii::app()->clientScript->registerCssFile($assetsFlowChart . '/css/shapes.css');

        Yii::app()->clientScript->registerScriptFile($assetsFlowChart . '/plugins/jsPlumb/lib/mottle-0.7.1.js',
            CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile($assetsFlowChart . '/plugins/jsPlumb/lib/biltong-0.2.js',
            CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile($assetsFlowChart . '/plugins/jsPlumb/lib/katavorio-0.13.0.js',
            CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile($assetsFlowChart . '/plugins/jsPlumb/src/util.js',
            CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile($assetsFlowChart . '/plugins/jsPlumb/src/browser-util.js',
            CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile($assetsFlowChart . '/plugins/jsPlumb/src/jsPlumb.js',
            CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile($assetsFlowChart . '/plugins/jsPlumb/src/dom-adapter.js',
            CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile($assetsFlowChart . '/plugins/jsPlumb/src/overlay-component.js',
            CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile($assetsFlowChart . '/plugins/jsPlumb/src/endpoint.js',
            CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile($assetsFlowChart . '/plugins/jsPlumb/src/connection.js',
            CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile($assetsFlowChart . '/plugins/jsPlumb/src/anchors.js',
            CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile($assetsFlowChart . '/plugins/jsPlumb/src/defaults.js',
            CClientScript::POS_HEAD);

        Yii::app()->clientScript->registerScriptFile($assetsFlowChart . '/plugins/jsPlumb/src/connectors-flowchart.js',
            CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile($assetsFlowChart . '/plugins/jsPlumb/src/renderers-svg.js',
            CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile($assetsFlowChart . '/plugins/jsPlumb/src/base-library-adapter.js',
            CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile($assetsFlowChart . '/plugins/jsPlumb/src/dom.jsPlumb.js',
            CClientScript::POS_HEAD);

        Yii::app()->clientScript->registerScriptFile($assetsFlowChart . '/js/fflowchart.js', CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile($assetsFlowChart . '/js/fflowchart-base.js', CClientScript::POS_END);
    }
}
