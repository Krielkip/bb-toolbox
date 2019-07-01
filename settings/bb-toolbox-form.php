<?php

//
//FLBuilder::
/**
 * Register the Settings form for the Toolbox.
 */
FLBuilder::register_settings_form('bb_toolbox_form', array(
  'title' => __('Page / Post Config', 'bb-toolbox'),
  'tabs' => array(
    'general' => array( // Tab
      'title' => __('Post / Page settings', 'bb-toolbox'), // Tab title
      'sections' => array( // Tab Sections
        'general' => array( // Section
          'title' => __('General', 'fl-builder'), // Section Title
          'fields' => array( // Section Fields
            'post_title' => array(
              'type' => 'text',
              'label' => __('Post / Page Title', 'bb-toolbox'),
              'default' => '',
              'size' => '30',
              'placeholder' => __('Public title of the page', 'bb-toolbox')
            ),
            'post_permalink' => array(
              'type' => 'text',
              'label' => __('Post / Page Permalink', 'bb-toolbox'),
              'default' => '',
              'size' => '30',
              'placeholder' => __('Permalink of the post / page', 'bb-toolbox'),
              'description' => __('Use a unique name for your post or page.', 'bb-toolbox'),
              'help' => __('When you choice for an parent below, the parent permalink will be placed before the current permalink.', 'bb-toolbox')
            ),
            'post_parent' => array(
              'type' => 'select',
              'label' => __('Parent', 'bb-toolbox'),
              'options' => array(
                'default' => __('Main Page (no parent)', 'bb-toolbox')
              ) // This will be made dynamic
            ),
            'post_template' => array(
              'type' => 'select',
              'label' => __('Page Template', 'bb-toolbox'),
              'default' => 'default',
              'options' => array(
                'default' => apply_filters('default_page_template_title', __('Default Template'), 'quick-edit')
              ) // This will be made dynamic
            ),
            'post_featuredImage' => array(
              'type' => 'photo',
              'label' => __('Featured Image', 'bb-toolbox'),
              'show_remove' => true
            ),
            'bb-toolbox-can-parent' => array(
              'type' => 'select',
              'label' => __('Can this post type use parent?', 'bb-toolbox'),
              'options' => array(
                '0' => __('No', 'bb-toolbox'),
                '1' => __('Yes', 'bb-toolbox')
              ),
              'toggle' => array(
                '1' => array(
                  'fields' => array('post_parent')
                )
              ),
              'description' => __('This is only for showing page parent settings.. Ignore if visible.', 'bb-toolbox')
            ),
            'bb-toolbox-has-post-thumbnails' => array(
              'type' => 'select',
              'label' => __('Has this page got any post-tumbnails', 'bb-toolbox'),
              'options' => array(
                '0' => __('No', 'bb-toolbox'),
                '1' => __('Yes', 'bb-toolbox')
              ),
              'toggle' => array(
                '1' => array(
                  'fields' => array('post_featuredImage')
                )
              ),
              'description' => __('This is only for showing page template settings.. Ignore if visible.', 'bb-toolbox')
            ),
            'bb-toolbox-has-templates' => array(
              'type' => 'select',
              'label' => __('Has this page got any templates', 'bb-toolbox'),
              'options' => array(
                '0' => __('No', 'bb-toolbox'),
                '1' => __('Yes', 'bb-toolbox')
              ),
              'toggle' => array(
                '1' => array(
                  'fields' => array('post_template')
                )
              ),
              'description' => __('This is only for showing page template settings.. Ignore if visible.', 'bb-toolbox')
            ),
            'bb-toolbox-type-seo' => array(
              'type' => 'select',
              'label' => __('Is there a SEOplugin', 'bb-toolbox'),
              'options' => array(
                '0' => __('No', 'bb-toolbox'),
                '1' => __('Yes', 'bb-toolbox')
              ),
              'toggle' => array(
                '1' => array(
                  'tabs' => array('seo')
                )
              ),
              'description' => __('This is only for showing seo settings.. Ignore if visible.', 'bb-toolbox')
            )
          ),
        ),
      ),
    ),
    'seo' => array(
      'title' => __('SEO settings', 'bb-toolbox'), // Tab title
      'sections' => array(
        'seo' => array( // Section
          'title' => __('SEO Tools', 'bb-tools'), // Section Title
          'fields' => array( // Section Fields
            'meta_title' => array(
              'type' => 'text',
              'label' => __('Meta title', 'bb-toolbox'),
              'default' => '',
              'maxlength' => '60',
              'size' => '30',
              'help' => __('Max input value is 60 characters.', 'bb-toolbox')

            ),
            'meta_description' => array(
              'type' => 'textarea',
              'label' => __('Meta description', 'bb-toolbox'),
              'default' => '',
              'maxlength' => '160',
              'size' => '30',
              'rows' => '6',
              'help' => __('Max input value is 160 characters.', 'bb-toolbox')
            ),
          ),
        ),
      )
    )
  )
));