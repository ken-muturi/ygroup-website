<?php
session_start();
class Add_Metabox
{
    public $post_type_name;
    public $post_type_meta_boxes;

    /* Class constructor */
    public function __construct($name)
    {
        if (!isset($_SESSION["taxonomy_data"])) 
        {
            $_SESSION['taxonomy_data'] = array();
        }

        // Set some important variables self::beautify( $string )
        $this->post_type_name = self::uglify($name);
        $this->post_type_meta_boxes = array();

        add_action('post_edit_form_tag', array(&$this, 'this_theme_form_enctype'));
        add_action('admin_init',array(&$this, 'push_meta_box'));

        add_action('save_post',array(&$this,'save_data'));
    }

    /* Attaches meta boxes to the post type */
    public function add_meta_box($title, $fields = array(), $context='normal', $priority = 'default')
    {
        if(! empty($title))
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

    public function this_theme_form_enctype()
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
                    array(&$this, 'this_theme_callback_metabox'),
                    $post_type_name,
                    $meta_box['context'],
                    $meta_box['priority'],
                    array($fields)
                );
            }
        }
    }

    public function this_theme_callback_metabox($post, $metabox)
    {
        global $post;
        // Nonce field for some validation
        wp_nonce_field(plugin_basename(__FILE__), 'this_theme_add_metabox');
        // Get all inputs from $data
        $custom_fields = $metabox['args'][0];
        // Get the saved values
        $meta = get_post_meta($post->ID);

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

        if(isset($_POST['this_theme_add_metabox']) && ! wp_verify_nonce($_POST['this_theme_add_metabox'], plugin_basename(__FILE__))) return;

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

    public static function beautify($string)
    {
        return ucwords(str_replace('_',' ',$string));
    }

    public static function uglify($string)
    {
        return strtolower(str_replace(' ','_',$string));
    }
}