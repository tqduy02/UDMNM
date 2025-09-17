<?php
// ===== CPT: project + Taxonomies: project_cat, tech_stack =====
add_action('init', function () {
  // CPT: project
  register_post_type('project', [
    'labels' => [
      'name'               => 'Projects',
      'singular_name'      => 'Project',
      'menu_name'          => 'Projects',
      'name_admin_bar'     => 'Project',
      'add_new'            => 'Add New',
      'add_new_item'       => 'Add New Project',
      'new_item'           => 'New Project',
      'edit_item'          => 'Edit Project',
      'view_item'          => 'View Project',
      'all_items'          => 'Projects',
      'search_items'       => 'Search Projects',
      'not_found'          => 'No projects found',
      'not_found_in_trash' => 'No projects found in Trash',
    ],
    'public'             => true,
    'show_in_rest'       => true,
    'show_in_nav_menus'  => true,
    'menu_icon'          => 'dashicons-portfolio',
    'supports'           => ['title','editor','thumbnail','excerpt','author','revisions','comments'],
    'has_archive'        => true,                                   // /projects/
    'rewrite'            => ['slug' => 'projects', 'with_front' => false],
    'publicly_queryable' => true,
  ]);

  // Taxonomy phân cấp: project_cat
  register_taxonomy('project_cat', ['project'], [
    'labels' => [
      'name'          => 'Project Categories',
      'singular_name' => 'Project Category',
      'search_items'  => 'Search Categories',
      'all_items'     => 'All Categories',
      'edit_item'     => 'Edit Category',
      'update_item'   => 'Update Category',
      'add_new_item'  => 'Add New Category',
      'menu_name'     => 'Categories',
    ],
    'hierarchical'      => true,
    'show_admin_column' => true,
    'show_in_rest'      => true,
    'rewrite'           => ['slug' => 'project-category', 'with_front' => false],
  ]);

  // Taxonomy không phân cấp: tech_stack
  register_taxonomy('tech_stack', ['project'], [
    'labels' => [
      'name'          => 'Tech Stack',
      'singular_name' => 'Tech',
      'search_items'  => 'Search Tech',
      'all_items'     => 'All Tech',
      'edit_item'     => 'Edit Tech',
      'update_item'   => 'Update Tech',
      'add_new_item'  => 'Add New Tech',
      'menu_name'     => 'Tech Stack',
    ],
    'hierarchical'      => false,
    'show_admin_column' => true,
    'show_in_rest'      => true,
    'rewrite'           => ['slug' => 'tech', 'with_front' => false],
  ]);
}, 0);

// Flush rewrite khi kích hoạt theme (1 lần)
add_action('after_switch_theme', function(){ flush_rewrite_rules(); });

// Mark menu item 'Projects' active trên CPT Project
add_filter('nav_menu_css_class', function($classes, $item){
  if ( (is_post_type_archive('project') || is_singular('project') || is_tax(['project_cat','tech_stack'])) &&
       isset($item->url) && str_contains($item->url, home_url('/projects/')) ) {
    $classes[] = 'current-menu-item';
  }
  return $classes;
}, 10, 2);
