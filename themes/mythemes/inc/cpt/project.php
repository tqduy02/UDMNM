<?php
// ===== CPT: project + Taxonomies: project_cat, tech_stack (i18n) =====
add_action('init', function () {

  // --- CPT: project ---
  $project_labels = [
    'name'                  => _x('Projects', 'post type general name', 'mythemes'),
    'singular_name'         => _x('Project',  'post type singular name', 'mythemes'),
    'menu_name'             => esc_html__('Projects', 'mythemes'),
    'name_admin_bar'        => esc_html__('Project', 'mythemes'),
    'add_new'               => esc_html__('Add New', 'mythemes'),
    'add_new_item'          => esc_html__('Add New Project', 'mythemes'),
    'new_item'              => esc_html__('New Project', 'mythemes'),
    'edit_item'             => esc_html__('Edit Project', 'mythemes'),
    'view_item'             => esc_html__('View Project', 'mythemes'),
    'all_items'             => esc_html__('Projects', 'mythemes'),
    'search_items'          => esc_html__('Search Projects', 'mythemes'),
    'parent_item_colon'     => esc_html__('Parent Project:', 'mythemes'),
    'not_found'             => esc_html__('No projects found.', 'mythemes'),
    'not_found_in_trash'    => esc_html__('No projects found in Trash.', 'mythemes'),
    'archives'              => esc_html__('Project archives', 'mythemes'),
    'attributes'            => esc_html__('Project attributes', 'mythemes'),
    'insert_into_item'      => esc_html__('Insert into project', 'mythemes'),
    'uploaded_to_this_item' => esc_html__('Uploaded to this project', 'mythemes'),
    'featured_image'        => esc_html__('Featured image', 'mythemes'),
    'set_featured_image'    => esc_html__('Set featured image', 'mythemes'),
    'remove_featured_image' => esc_html__('Remove featured image', 'mythemes'),
    'use_featured_image'    => esc_html__('Use as featured image', 'mythemes'),
    'items_list'            => esc_html__('Projects list', 'mythemes'),
    'items_list_navigation' => esc_html__('Projects list navigation', 'mythemes'),
    'filter_items_list'     => esc_html__('Filter projects list', 'mythemes'),
  ];

  register_post_type('project', [
    'labels'             => $project_labels,
    'public'             => true,
    'show_in_rest'       => true,
    'show_in_nav_menus'  => true,
    'menu_icon'          => 'dashicons-portfolio',
    'supports'           => ['title','editor','thumbnail','excerpt','author','revisions','comments'],
    'has_archive'        => true, // /projects/
    // Lưu ý: base slug đa ngữ cần Polylang Pro. Bản free nên giữ 1 slug cho cả EN/VI.
    'rewrite'            => ['slug' => 'projects', 'with_front' => false],
    'publicly_queryable' => true,
  ]);

  // --- Taxonomy phân cấp: project_cat ---
  $cat_labels = [
    'name'              => _x('Project Categories', 'taxonomy general name', 'mythemes'),
    'singular_name'     => _x('Project Category', 'taxonomy singular name', 'mythemes'),
    'search_items'      => esc_html__('Search Categories', 'mythemes'),
    'all_items'         => esc_html__('All Categories', 'mythemes'),
    'parent_item'       => esc_html__('Parent Category', 'mythemes'),
    'parent_item_colon' => esc_html__('Parent Category:', 'mythemes'),
    'edit_item'         => esc_html__('Edit Category', 'mythemes'),
    'update_item'       => esc_html__('Update Category', 'mythemes'),
    'add_new_item'      => esc_html__('Add New Category', 'mythemes'),
    'new_item_name'     => esc_html__('New Category Name', 'mythemes'),
    'menu_name'         => esc_html__('Categories', 'mythemes'),
  ];

  register_taxonomy('project_cat', ['project'], [
    'labels'            => $cat_labels,
    'hierarchical'      => true,
    'show_admin_column' => true,
    'show_in_rest'      => true,
    // Bản free của Polylang không dịch base slug. Giữ 1 slug chung.
    'rewrite'           => ['slug' => 'project-category', 'with_front' => false],
  ]);

  // --- Taxonomy không phân cấp: tech_stack ---
  $tech_labels = [
    'name'              => _x('Tech Stack', 'taxonomy general name', 'mythemes'),
    'singular_name'     => _x('Tech', 'taxonomy singular name', 'mythemes'),
    'search_items'      => esc_html__('Search Tech', 'mythemes'),
    'all_items'         => esc_html__('All Tech', 'mythemes'),
    'edit_item'         => esc_html__('Edit Tech', 'mythemes'),
    'update_item'       => esc_html__('Update Tech', 'mythemes'),
    'add_new_item'      => esc_html__('Add New Tech', 'mythemes'),
    'new_item_name'     => esc_html__('New Tech Name', 'mythemes'),
    'menu_name'         => esc_html__('Tech Stack', 'mythemes'),
  ];

  register_taxonomy('tech_stack', ['project'], [
    'labels'            => $tech_labels,
    'hierarchical'      => false,
    'show_admin_column' => true,
    'show_in_rest'      => true,
    'rewrite'           => ['slug' => 'tech', 'with_front' => false],
  ]);

}, 0);

// Flush rewrite khi kích hoạt theme (1 lần)
add_action('after_switch_theme', function(){ flush_rewrite_rules(); });

// Mark menu item 'Projects' active trên CPT Project (tương thích đa ngữ)
add_filter('nav_menu_css_class', function($classes, $item){
  if (is_post_type_archive('project') || is_singular('project') || is_tax(['project_cat','tech_stack'])) {
    $archive = trailingslashit( get_post_type_archive_link('project') ); // Polylang sẽ tự thêm /en/ /vi/
    $item_url = isset($item->url) ? trailingslashit($item->url) : '';
    if ($archive && $item_url && strpos($item_url, $archive) === 0) {
      $classes[] = 'current-menu-item';
    }
  }
  return $classes;
}, 10, 2);
