<?php

namespace Voilaah\Basecamp\Classes\Registration;

use Illuminate\Support\Facades\Event;
use RainLab\Forum\Controllers\Channels;
use RainLab\Forum\Models\Channel;
use RainLab\User\Models\UserGroup;
use System\Classes\PluginManager;

trait BootExtensions
{

  public function registerExtensions()
  {

    if (PluginManager::instance()->exists('RainLab.User')
        && PluginManager::instance()->exists('RainLab.Forum')) {
      $this->extendRainLabForum();
    }
  }

  public function extendRainLabForum(): void
  {

    Channel::extend(function ($model) {
        $model->addFillable(['permission_group_id']);

        $model->addDynamicMethod('groupOptions', function () use ($model) {
            $groups = UserGroup::lists('name', 'id');
            return [ '' => 'All groups' ] + $groups;
        });
    });

     // extend the post form
     Channels::extendFormFields(function($form, $model, $context) {
        if (!$model instanceof Channel) {
            return;
        }

        $form->addFields([
            'permission_group_id' => [
                'label' => 'Group permission',
                'commentAbove' => 'Give access permission  to a specific user group.',
                'type'  => 'dropdown',
                'span'  => 'auto',
                'options'  => 'groupOptions',
            ]
        ]);
    });
  }


}
