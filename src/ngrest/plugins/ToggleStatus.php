<?php

namespace luya\admin\ngrest\plugins;

use luya\admin\Module;
use luya\admin\ngrest\base\Plugin;

/**
 * Create toggle checkbox for a given field.
 *
 * You can change the value for true/false state by using the `trueValue` and `falseValue` porperties while confiure the plugin for the given field.
 *
 * Example of using init value preselection:
 *
 * ```php
 * 'is_downloadable' => ['toggleStatus', 'initValue' => 1],
 * ```
 *
 * Now the checkbox is set the 1 by default (which is equals to $trueValue).
 *
 * Checkbox is by default interactive toggleable in the crud overview in order to disable this behavior set $interactive to false.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class ToggleStatus extends Plugin
{
    /**
     * @var boolean If enabled the list tag will transform into an interactive scheduling overlay. Keep in mind to turn on the queue system in order to enable time based scheduling
     * @see {{luya\admin\Module::$autoBootstrapQueue}}
     * @since 2.0.3
     */
    public $scheduling = false;

    /**
     * @var string|integer The value which shoud be picked for angular true state
     */
    public $trueValue = 1;

    /**
     * @var string|integer The value which shoud be picked for angular false state
     */
    public $falseValue = 0;

    /**
     * @var string The class which is used in the grid overview when state is true.
     */
    public $trueClass = 'material-icons';

    /**
     * @var string The class which is used in the grid overview when state is false.
     */
    public $falseClass = 'material-icons';

    /**
     * @var string The icon name which is used in the grid overview when state is true.
     */
    public $trueIcon = 'check';

    /**
     * @var string The icon name which is used in the grid overview when state is false.
     */
    public $falseIcon = 'close';

    /**
     * @var string|integer The default value which should be assigned to the field on creation
     */
    public $initValue = 0;

    /**
     * @var boolean Whether the interactive mode is enabled which allows you to toggle the status of the field within the crud list overview.
     */
    public $interactive = true;

    /**
     * Get the options for a scheduler returned as array.
     *
     * @return array
     * @since 2.0.3
     */
    private function getSchedulerValuesAsArray()
    {
        return [
            $this->falseValue => Module::t('status_false'),
            $this->trueValue => Module::t('status_true'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function renderList($id, $ngModel)
    {
        if ($this->scheduling && $this->renderContext->canUpdate()) {
            return $this->createSchedulerListTag($ngModel, $this->getSchedulerValuesAsArray(), 'item');
        }

        if (!$this->interactive || !$this->renderContext->canUpdate()) {
            return [
                $this->createTag('i', $this->trueIcon, ['ng-if' => "$ngModel == $this->trueValue", 'class' => $this->trueClass]),
                $this->createTag('i', $this->falseIcon, ['ng-if' => "$ngModel == $this->falseValue", 'class' => $this->falseClass]),
            ];
        }

        return [
            $this->createTag('i', $this->trueIcon, ['style' => 'cursor:pointer;', 'ng-if' => "$ngModel == $this->trueValue", 'class' => $this->trueClass, 'ng-click' => "toggleStatus(item, '{$this->name}', '{$this->alias}', $ngModel)"]),
            $this->createTag('i', $this->falseIcon, ['style' => 'cursor:pointer;', 'ng-if' => "$ngModel == $this->falseValue", 'class' => $this->falseClass, 'ng-click' => "toggleStatus(item, '{$this->name}', '{$this->alias}', $ngModel)"]),
        ];
    }

    /**
     * @inheritdoc
     */
    public function renderCreate($id, $ngModel)
    {
        return $this->createFormTag('zaa-checkbox', $id, $ngModel, ['options' => json_encode(['true-value' => $this->trueValue, 'false-value' => $this->falseValue]), 'initvalue' => $this->initValue]);
    }

    /**
     * @inheritdoc
     */
    public function renderUpdate($id, $ngModel)
    {
        if ($this->scheduling && $this->renderContext->canUpdate()) {
            return [
                '<div class="crud-loader-tag crud-loader-tag-for-checkbox">' . $this->createSchedulerListTag($ngModel, $this->getSchedulerValuesAsArray(), 'data.update', ['only-icon' => 1]) . '</div>',
                $this->renderCreate($id, $ngModel),
            ];
        }

        return $this->renderCreate($id, $ngModel);
    }
}
