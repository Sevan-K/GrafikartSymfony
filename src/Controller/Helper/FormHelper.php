<?php

namespace App\Controller\Helper;

use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\String\Slugger\AsciiSlugger;

class FormHelper
{
    public static function makeAutoSlugger(string $field = 'title'): callable
    {

        return
            function (PreSubmitEvent $event) use ($field) {
                $data = $event->getData();

                if (empty($data['slug'])) {
                    $slugger = new AsciiSlugger();
                    $data['slug'] = strtolower($slugger->slug($data[$field]));
                }

                $event->setData($data);
            };
    }
}
