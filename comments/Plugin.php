<?php namespace Jab\Comments;

use Backend;
use Controller;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public function pluginDetails()
    {
        return [
            'name' => 'Comments',
            'description' => 'Allows users to comment on and discuss published content.',
            'author' => 'Jordan Brierley',
            'icon' => 'icon-comments'
        ];
    }

    public function registerComponents()
    {
        return [
            'Jab\Comments\Components\Comments' => 'comments',
        ];
    }

    public function registerPermissions()
    {
        return [
            'jab.comments.access_other_comments' => ['tab' => 'jab.comments::lang.comments.comments', 'label' => 'jab.comments::lang.comments.access_other_comments'],
        ];
    }

    public function registerNavigation()
    {
        return [
            'comments' => [
                'label' => 'jab.comments::lang.comments.comments',
                'url' => Backend::url('jab/comments/comments'),
                'icon' => 'icon-comments',
                'permissions' => ['jab.comments.*'],
                'order' => 500,
            ]
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'jab.comments::lang.settings.menu_label',
                'description' => 'jab.comments::lang.settings.menu_description',
                'category'    => 'jab.comments::lang.settings.comments',
                'icon'        => 'icon-comments',
                'class'       => 'Jab\Comments\Models\Settings',
                'order'       => 500,
                'permissions' => ['jab.comments.*']
            ]
        ];
    }
}
