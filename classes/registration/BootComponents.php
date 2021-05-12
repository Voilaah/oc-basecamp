<?php

namespace Voilaah\Basecamp\Classes\Registration;

trait BootComponents
{
    public function registerComponents()
    {
        return [
            '\Voilaah\Basecamp\Components\Channels'     => 'bcForumChannels',

        ];
    }
}
