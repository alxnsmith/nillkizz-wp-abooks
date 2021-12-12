<?php
if (function_exists('acf_add_local_field_group')) :

  acf_add_local_field_group(array(
    'key' => 'group_61b406c49eb05',
    'title' => 'Audio Book',
    'fields' => array(
      array(
        'key' => 'field_61b406d0d6a3d',
        'label' => 'Главы',
        'name' => 'chapters',
        'type' => 'repeater',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'hide_field' => '',
        'hide_label' => '',
        'hide_instructions' => '',
        'hide_required' => '',
        'instruction_placement' => '',
        'acfe_permissions' => '',
        'acfe_repeater_stylised_button' => 0,
        'collapsed' => '',
        'min' => 0,
        'max' => 0,
        'layout' => 'table',
        'button_label' => '',
        'acfe_settings' => '',
        'acfe_field_group_condition' => 0,
        'sub_fields' => array(
          array(
            'key' => 'field_61b4070ed6a3f',
            'label' => 'Аудиофайл',
            'name' => 'file',
            'type' => 'file',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'hide_field' => '',
            'hide_label' => '',
            'hide_instructions' => '',
            'hide_required' => '',
            'instruction_placement' => '',
            'acfe_permissions' => '',
            'required_message' => '',
            'uploader' => '',
            'return_format' => 'array',
            'min_size' => '',
            'max_size' => '',
            'mime_types' => 'mp3,wav',
            'upload_folder' => 'abooks/chapters/files/',
            'multiple' => 0,
            'acfe_settings' => '',
            'acfe_validate' => '',
            'acfe_field_group_condition' => 0,
            'library' => 'all',
          ),
        ),
      ),
    ),
    'location' => array(
      array(
        array(
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'abook',
        ),
      ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'seamless',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'acfe_autosync' => '',
    'acfe_form' => 1,
    'acfe_display_title' => '',
    'acfe_permissions' => '',
    'acfe_meta' => '',
    'acfe_note' => '',
  ));

endif;
