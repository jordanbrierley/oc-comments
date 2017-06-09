<?php

return [
    'plugin_name' => 'Comments',
    'plugin_desc' => 'Allows users to comment on and discuss published content.',
    'comments' => [
        'comment' => 'Comment',
        'comments' => 'Comments',
        'comments_description' => 'Outputs the comments and the comment form.',
        'created' => 'Created',
        'updated' => 'Updated',
        'published' => 'Published',
        'cancel' => 'Cancel',
        'access_other_comments' => 'Access other comments.'
    ],
    'settings' => [
        'slug' => 'Slug',
        'slug_description' => 'Look up the comments using the supplied slug value.',
        'menu_label' => 'Comments settings',
        'menu_description' => 'Manage comments settings',
        'comments' => 'Comments',
        'css' => 'Default CSS',
        'styles_tab' => 'Styles',
        'appearance_tab' => 'Appearance',
        'hide_reply_form' => 'Hide reply form',
        'hide_reply_form_description' => 'Hide reply form after send button is clicked.',
        'hide_main_form' => 'Hide main form',
        'hide_main_form_description' => 'Hide main form after reply button is clicked.',
        'depth' => 'Depth',
        'depth_description' => 'Comment Reply Depth.',
        'depth_validation_message' => 'The Depth property can contain only numeric symbols'
    ],
    'messages' => [
        'error' => 'Error!',
        'comment_description' => 'Look up the comments using the supplied slug value.',
        'edit_success' => 'Comment was updated!',
        'edit_error' => 'Error!',
    ],
    'backend' => [
        'id' => 'ID',
        'content' => 'Content',
        'author' => 'Author',
        'name' => 'Name',
        'created' => 'Created',
        'hide' => 'Hide',
        'published' => 'Published',
        'show' => 'Show',
        'delete_confirm' => 'Are you sure?',
        'chart_total' => 'Total',
        'chart_hidden' => 'Hidden',
        'chart_published' => 'Published',
    ]
];