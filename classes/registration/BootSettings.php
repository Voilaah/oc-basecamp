<?php

namespace Voilaah\Basecamp\Classes\Registration;

use Voilaah\Basecamp\Models\GeneralSettings;

trait BootSettings {

  public function registerSettings()
  {
      return [
        //   'general_settings'          => [
        //       'label'       => 'voilaah.pcf::lang.general_settings.label',
        //       'description' => 'voilaah.pcf::lang.general_settings.description',
        //       'category'    => 'voilaah.pcf::lang.plugin.name',
        //       'icon'        => 'icon-shopping-cart',
        //       'class'       => GeneralSettings::class,
        //       'order'       => 0,
        //       'permissions' => ['voilaah.pcf.settings.manage_general'],
        //       'keywords'    => 'pcf registration form general',
        //   ]
      ];
  }
}
