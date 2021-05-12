<?php namespace Voilaah\Basecamp\Components;

use Cms\Classes\ComponentBase;
use RainLab\Forum\Components\Channels as ComponentsChannels;

class Channels extends ComponentsChannels
{
    public function componentDetails()
    {
        return [
            'name'        => 'Basecamp Channels Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return parent::defineProperties();
    }
}
