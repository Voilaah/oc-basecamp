<?php namespace Voilaah\Basecamp;

use System\Classes\PluginBase;
use Voilaah\Basecamp\Classes\Registration\BootSettings;
use Voilaah\Basecamp\Classes\Registration\BootComponents;
use Voilaah\Basecamp\Classes\Registration\BootExtensions;

class Plugin extends PluginBase
{
    use BootExtensions;
    use BootComponents;
    use BootSettings;

    public function boot() {
        $this->registerExtensions();
    }
}
