<?php

add_action( 'init', 'tfw_create_congressman' );
function tfw_create_congressman() {
  register_post_type( 'congressman',
    array(
      'labels' => array(
        'name' => __( 'Congressmen' ),
        'singular_name' => __( 'Congressman' )
      ),
      'public' => true,
      'has_archive' => true,
      'rewrite' => array('slug' => 'congressman'),
      'taxonomies' => array('state', 'district'),
    )
  );
	// create a new taxonomy
	register_taxonomy(
		'state',
		'congressman',
		array(
			'label' => __( 'State' ),
			'rewrite' => array( 'slug' => 'state','with_front' => false ),
			'hierarchical' => true,
					'show_admin_column'     => true,
      'show_ui' => true,

			
		)
	);
	register_taxonomy(
		'district',
		'congressman',
		array(
			'label' => __( 'District' ),
			'rewrite' => array( 'slug' => 'district', 'with_front' => false ),
			'hierarchical' => falst,
					'show_admin_column'     => true,
      'show_ui' => true,

			
		)
	);


}

$feature_groups = array(
    'bedroom' => __('Bedroom', 'my_plugin'),
    'living' => __('Living room', 'my_plugin'),
    'kitchen' => __('Kitchen', 'my_plugin')
);

add_action( 'district_feature_add_form_fields', 'add_feature_group_field', 10, 2 );
function add_feature_group_field($taxonomy) {
    global $feature_groups;
    ?><div class="form-field term-group">
        <label for="featuret-group"><?php _e('Feature Group', 'my_plugin'); ?></label>
        <select class="postform" id="equipment-group" name="feature-group">
            <option value="-1"><?php _e('none', 'my_plugin'); ?></option><?php foreach ($feature_groups as $_group_key => $_group) : ?>
                <option value="<?php echo $_group_key; ?>" class=""><?php echo $_group; ?></option>
            <?php endforeach; ?>
        </select>
    </div><?php
}