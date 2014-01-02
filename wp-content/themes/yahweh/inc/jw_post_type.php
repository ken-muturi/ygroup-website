<?php
session_start();

class JW_Post_Type
{
    public $post_type_name;
    public $post_type_args;
    public $post_type_labels;
    public $post_type_taxos;
    public $post_type_taxos_exits;
    public $post_type_meta_boxes;

    /* Class constructor */
    public function __construct($name, $args = array(),$labels = array())
    {
        if (!isset($_SESSION["taxonomy_data"])) 
        {
            $_SESSION['taxonomy_data'] = array();
        }

        // Set some important variables self::beautify( $string )
        $this->post_type_name = self::uglify($name);
        $this->post_type_args = $args;
        $this->post_type_labels = $labels;
        $this->post_type_taxos = array();
        $this->post_type_taxos_exits= array();
        $this->post_type_meta_boxes = array();

        // Add action to register the post type, if the post type does not already exist
        if(! post_type_exists($this->post_type_name))
        {
            add_action('init',array(&$this,'register_post_type'));
            add_action('init',array(&$this,'push_taxonomy'));
            // end update_edit_form
            add_action('post_edit_form_tag', array(&$this, 'jw_post_edit_form_tag'));
            add_action('admin_init',array(&$this, 'push_meta_box'));
        }

        add_action('save_post',array(&$this,'save_data'));
        $this->admin_column_filters();
    }

    /* Method which registers the post type */
    public function register_post_type()
    {
        // Capitilize the words and make it plural
        $name = self::beautify($this->post_type_name);
        $plural = $name.'s';

        // We set the default labels based on the post type name and plural. We overwrite them with the given labels.
        $labels = array_merge(
        //Default
            array(
                'name' => _x($plural,'post type general name'),
                'singular' => _x($name,'post type singular name'),
                'add_new' => _x('Add New',strtolower($name)),
                'add_new_item' => __('Add New '.$name),
                'edit_item' => __('Edit '.$name),
                'new_item' => __('New '.$name),
                'all_items' => __('All '.$plural),
                'view_item' => __('View '.$name),
                'search_items' => __('Search '.$plural),
                'not_found' => __('No '.strtolower($plural).' found'),
                'not_found_in_trash'=> __('No '.strtolower($plural).' found in Trash'),
                'parent_item_colon' => '',
                'menu_name' => $plural
                ),
            $this->post_type_labels
        );

        // Same principle as the labels. We set some defaults and overwrite them with the given arguments.
        $args = array_merge(
        // Default
            array(
                'label' => $plural,
                'labels' => $labels,
                'public' => true,
                'show_ui' => true,
                'supports' => array('title','editor'),
                'show_in_nav_menus' => true,
                '_builtin' => false
                ),
            $this->post_type_args
        );

        // Register the post type
        register_post_type($this->post_type_name,$args);
    }

    /* Method to attach the taxonomy to the post type */
    public function add_taxonomy($name, $args = array(), $labels = array())
    {
        if(! empty($name))
        {
            // We need to know the post type name, so the new taxonomy can be attached to it.
            $post_type_name = $this->post_type_name;

            // Taxonomy properties
            $taxonomy_name = self::uglify($name);
            if(! taxonomy_exists($taxonomy_name))
            {
                /* Create taxonomy and attach to the post type */
                $name = self::beautify($name);
                $plural = $name.'s';

                // Default labels, overwrite them with the given labels.
                $labels = array_merge(
                //Default
                    array(
                        'name' => _x($plural, 'taxonomy general name'),
                        'singular_name' => _x($name,'taxonomy singular name'),
                        'search_items' => __('Search '.$plural),
                        'all_items' => __('All '.$plural),
                        'parent_item' => __('Parent '.$name),
                        'parent_item_colon' => __('Parent '.$name.':'),
                        'edit_item' => __('Edit '.$name),
                        'update_item' => __('Update '.$name),
                        'add_new_item' => __('Add New '.$name),
                        'new_item_name' => __('New '.$name.' Name'),
                        'menu_name' => __($name)
                        ),
                    $labels
                );

                // Default arguments, overwritten with the given arguments
                $args = array_merge(
                // Default
                    array(
                        'label' => $plural,
                        'labels' => $labels,
                        'public' => true,
                        'show_ui' => true,
                        'show_in_nav_menus' => true,
                        '_builtin' => false
                        ),
                    $args
                );

                $temp = array(
                    'name' => $taxonomy_name,
                    'object_type' => $post_type_name,
                    'args' => $args
                );
                array_push($this->post_type_taxos, $temp);            
            }
            else
            {
                $temp = array(
                    'name' => $taxonomy_name,
                    'object_type' => $post_type_name
                );
                array_push($this->post_type_taxos_exits, $temp);
            }
        }
    }

    /* only for under php 5.3 */
    public function push_taxonomy()
    {
        if(NULL != $this->post_type_taxos)
        {
            foreach($this->post_type_taxos as $taxo)
            {
                register_taxonomy($taxo['name'],$taxo['object_type'],$taxo['args']);
            }
        }

        if(NULL != $this->post_type_taxos_exits)
        {
            foreach($this->post_type_taxos_exits as $taxo)
            {
                register_taxonomy_for_object_type($taxo['name'], $taxo['object_type']);
            }
        }
    }

    /* Attaches meta boxes to the post type */
    public function add_meta_box($title, $fields = array(), $context='normal', $priority = 'default')
    {
        if(!empty($title))
        {
            $temp = array(
                'title' => $title,
                'box_id' => self::uglify($title),
                'box_title' => self::beautify($title),
                'context' => $context,
                'priority' => $priority,
                'fields' => $fields
            );
            
            array_push($this->post_type_meta_boxes, $temp);
        }
    }

    public function jw_post_edit_form_tag()
    {
        echo ' enctype="multipart/form-data"';
    }

    public function push_meta_box()
    {
        if(NULL != $this->post_type_meta_boxes)
        {
            // Make the fields global
            global $custom_fields;
            $post_type_name = $this->post_type_name;

            foreach($this->post_type_meta_boxes as $meta_box)
            {
                $title = $meta_box['title'];
                $fields = $meta_box['fields'];
                $custom_fields[$title] = $fields;

                add_meta_box(
                    $meta_box['box_id'],
                    $meta_box['box_title'],
                    array(&$this, 'callback_metabox'),
                    $post_type_name,
                    $meta_box['context'],
                    $meta_box['priority'],
                    array($fields)
                );
            }
        }
    }

    public function callback_metabox($post, $metabox)
    {
        global $post;
        // Nonce field for some validation
        wp_nonce_field(plugin_basename(__FILE__), 'jw_post_type');
        // Get all inputs from $data
        $custom_fields = $metabox['args'][0];
        // Get the saved values
        $meta = get_post_custom($post->ID);

        // Check the array and loop through it
        if(! empty($custom_fields))
        {
            echo '<style> ul.admin-checkboxes { border: 1px solid #DFDFDF; background-color: #FFFFFF; margin:0px; 5px; padding: 5px; list-style-type:none; max-height: 80px; overflow-y: auto; } </style>';
            // Loop through $custom_fields
            foreach($custom_fields as $label => $type)
            {
                $field_id_name = self::uglify($this->post_type_name.'_'.$metabox['id'].'_'.$label);
                $select = '';
                $checkbox = '';
                if(is_array($type))
                {
                    if (strtolower($type['type']) == 'select') 
                    {
                        // filter through them, and create options
                        $select .= "<select name='$field_id_name' class='widefat'>";
                        foreach ($type['options'] as $key => $option) 
                        {
                           $set_selected = (isset($meta[$field_id_name]) && $meta[$field_id_name][0] == $key) ? "selected = 'selected'" : '';

                            $select .= "<option value='$key' $set_selected> $option </option>";
                        }
                        $select .= "</select>";
                        array_push($_SESSION['taxonomy_data'], $field_id_name);
                    }                     

                    if (strtolower($type['type']) == 'checkbox') 
                    {
                        // filter through them, and create options
                        $checkbox .= "<ul class='admin-checkboxes'>";
                        foreach ($type['options'] as $key => $option) 
                        {
                            $answer_value = isset($meta[$field_id_name]) ? $meta[$field_id_name][0] : '';
                            $answer_value = explode(':::', $answer_value);
                            $checked = in_array($key, $answer_value) ? "checked = 'checked'" : '';
                            $checkbox .= "<li><input type='checkbox' name='{$field_id_name}[]' value='$key' $checked /> {$option}</li>";
                        }
                        $checkbox .= "</ul>";
                        array_push($_SESSION['taxonomy_data'], $field_id_name);
                    }                    
                }
                else
                {                    
                    $value = isset($meta[$field_id_name][0]) ? $meta[$field_id_name][0] : '';
                    $checked = ($type == 'checkbox' && !empty($value) ? 'checked' : '');
                    $checkbox .= "<input type='checkbox' name='{$field_id_name}' value='$value' $checked />";
                    array_push($_SESSION['taxonomy_data'], $field_id_name);
                }

                $lookup = array(
                    "text" => "<input type='text' name='$field_id_name' value='$value' class='widefat' />",
                    "textarea" => "<textarea name='$field_id_name' class='widefat' rows='10'>$value</textarea>",
                    "checkbox" => isset($checkbox) && ! empty($checkbox) ? $checkbox : $checkbox,
                    "select" => isset($select) && ! empty($select) ? $select : '',
                    "file" => "<input type='file' class='widefat' name='$field_id_name' id='$field_id_name' />"
                );
                ?>

                <p>
                    <label><?php echo ucwords($label) . ':'; ?></label>
                    <?php echo $lookup[is_array($type) ? $type['type'] : $type]; ?>
                </p>
               
                <?php
                    // If a file was uploaded, display it below the input.
                    $file = get_post_meta($post->ID, $field_id_name, true);
                    if ( $type === 'file' ) 
                    {
                        // display the image
                        $file = get_post_meta($post->ID);
                        $file = get_post_meta($post->ID, $field_id_name, true);
                        $file_type = wp_check_filetype($file);
                        $image_types = array('jpeg', 'jpg', 'bmp', 'gif', 'png');
                        if ( isset($file) ) 
                        {
                            if ( in_array($file_type['ext'], $image_types) ) 
                            {
                                echo " <p><img src='$file' alt='' style='max-width: 400px;' /> </p>";
                            } 
                            else 
                            {
                                echo " <p><a href='$file'>$file</a></p> ";
                            }
                        }
                    }
                ?>                               
           <?php 
            }
        }
    }

    public function save_data()
    {
        // Deny the Wordpress autosave function
        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        if(isset($_POST['jw_post_type']) && ! wp_verify_nonce($_POST['jw_post_type'], plugin_basename(__FILE__))) return;

        global $post;
        if(isset($_POST) && isset($post->ID) && get_post_type($post->ID) == $this->post_type_name)
        {
            if (isset($_SESSION['taxonomy_data'])) 
            {
                foreach ($_SESSION['taxonomy_data'] as $form_name) 
                {
                    if (!empty($_FILES[$form_name]) ) 
                    {
                        if ( !empty($_FILES[$form_name]['tmp_name']) ) 
                        {
                            $upload = wp_upload_bits($_FILES[$form_name]['name'], null, file_get_contents($_FILES[$form_name]['tmp_name']));

                            if (isset($upload['error']) && $upload['error'] != 0) 
                            {
                                wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
                            } 
                            else 
                            {
                                update_post_meta($post->ID, $form_name, $upload['url']);
                            }
                        }
                   } 
                   else 
                   {
                        // Make better. Have to do this, because I can't figure
                        // out a better way to deal with checkboxes. If deselected,
                        // they won't be represented here, but I still need to
                        // update the value to false to blank in the table. Hmm...
                        if (!isset($_POST[$form_name])) $_POST[$form_name] = '';
                        if (isset($post->ID) ) 
                        {  
                            // checkboxes and multiple selects
                            if(is_array($_POST[$form_name])) 
                            {
                                $_POST[$form_name] = join(':::', $_POST[$form_name]);    
                            } 
                            update_post_meta($post->ID, $form_name, $_POST[$form_name]);
                        }
                    }
                }

                $_SESSION['taxonomy_data'] = array();
            }
        }
    }

    public function admin_column_filters() 
    {
        add_filter("manage_edit-{$this->post_type_name}_columns", array($this, "set_custom_post_columns")); 
        //add_filter("manage_edit-{$this->post_type_name}_sortable_columns", array($this, "set_custom_post_columns")); 
        
        add_action("manage_{$this->post_type_name}_posts_custom_column",array($this, "manage_custom_columns")); 

    }

    public function set_custom_post_columns($columns)
    {       
        global $post;
        $post_type = get_post_type( $post->ID);
        $custom_post_types = get_post_custom($post->ID);
        $date = $columns['date'];
        unset($columns['date']);
        foreach ($custom_post_types as $field => $value) 
        {
            if(preg_match("#^{$post_type}+\_#", trim(strtolower($field)))) 
            {
                //hack here, am only displaying first 4 columns
                if(count($meta_columns) < 4) 
                { 
                   $meta_column = explode('_', $field);
                   array_shift($meta_column);                   
                   $meta_columns [$field] = self::beautify(join('_', $meta_column));
                }   
            }
        }
        $meta_columns['date'] = $date;
        return array_merge($columns, $meta_columns);
    }   

    public function manage_custom_columns($column)
    {
        global $post;
        $post_type = get_post_type( $post->ID);
        $custom_post_types = get_post_custom($post->ID);

        if ("ID" == $column) echo $post->ID;
        elseif ("description" == $column) echo $post->post_content;
        elseif($value = get_post_meta($post->id, $column, true)) echo $value; 
        elseif($value = get_the_term_list( $post->id , $column , '' , ',' , '' ) && is_string($value)) echo $value;
        elseif($column == 'rapporteur_rapporteur_country' && $custom_post_types[$column][0]) 
        {
            list($latitude, $longitude, $country) = explode(":::",$custom_post_types[$column][0]);
            echo $country;
        }            
        else echo $custom_post_types[$column][0];        
    }

    public static function beautify($string)
    {
        return ucwords(str_replace('_',' ',$string));
    }

    public static function uglify($string)
    {
        return strtolower(str_replace(' ','_',$string));
    }
}