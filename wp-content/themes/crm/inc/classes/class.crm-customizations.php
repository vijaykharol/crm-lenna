<?php
if(!defined('ABSPATH')){
    exit();
}

/**
 * CLASS CRMCustomizations
 */
if(!class_exists('CRMCustomizations', false)){
    class CRMCustomizations{
        public static function init(){
            //Helper Classes
            include_once('class.crm-customizations-helper.php');

            //Actions And Filters
            add_action( 'init', [__CLASS__, 'crm_add_custom_roles'] );
            add_action( 'wp_ajax_crm_login_process', [__CLASS__, 'crm_login_process_callback'] );
            add_action( 'wp_ajax_nopriv_crm_login_process', [__CLASS__, 'crm_login_process_callback'] );
            add_action('template_redirect', [__CLASS__, 'crm_custom_redirect_user']);
            add_filter('forminator_field_markup', [__CLASS__, 'populate_users_dropdown'], 10, 3);
            add_filter('forminator_form_entry_meta', [__CLASS__, 'add_custom_entry_meta_column'], 10, 2);
            add_filter('forminator_form_entry_meta_value', [__CLASS__, 'display_selected_user_id_in_entry_list'], 10, 3);
            add_action('forminator_form_submit_before_set_posts', [__CLASS__, 'save_selected_user_id'], 10, 2);

            //staff admin rights
            add_action('show_user_profile', [__CLASS__, 'add_admin_role_checkbox']);
            add_action('edit_user_profile', [__CLASS__, 'add_admin_role_checkbox']);
            add_action('user_new_form', [__CLASS__, 'add_admin_role_checkbox']);
            
            //update profile
            add_action('profile_update', [__CLASS__, 'save_admin_checkbox_cb'], 10);
            add_action('user_register', [__CLASS__, 'save_admin_checkbox_cb'], 10);

            //Create a client
            add_action( 'wp_ajax_create-a-client', [__CLASS__, 'create_a_client_callback'] );
            add_action( 'wp_ajax_nopriv_create-a-client', [__CLASS__, 'create_a_client_callback'] );

            //Edit Client
            add_action( 'wp_ajax_edit-client', [__CLASS__, 'edit_client_callback'] );
            add_action( 'wp_ajax_nopriv_edit-client', [__CLASS__, 'edit_client_callback'] );
        }

        public static function crm_add_custom_roles(){
            // Add Staff role
            add_role(
                'staff',
                __( 'Staff' ),
                array(
                    'read'         => true,
                    'edit_posts'   => true,
                    'upload_files' => true,
                    // Add more capabilities as needed
                )
            );

            // Add Client role
            add_role(
                'client',
                __( 'Client' ),
                array(
                    'read'         => true,
                    'edit_posts'   => true,
                    'upload_files' => true,
                )
            );
        }

        /**
         * HANDLE LOLGIN REQUEST..
         */
        public static function crm_login_process_callback(){
            $username   =   (isset($_POST['username'])) ? $_POST['username'] : '';
            $password   =   (isset($_POST['password'])) ? $_POST['password'] : '';
            $remember   =   (isset($_POST['remember']) && $_POST['remember'] === 'true') ? true : false;

            $credentials = [
                'user_login'    => $username,
                'user_password' => $password,
                'remember'      => $remember
            ];

            $user = wp_signon($credentials, false);
            $return = [];
            if(is_wp_error($user)){
                $return['status']       =   false;
                $return['message']      =   'Wrong username/user email or password.';
            }else{
                wp_set_current_user($user->ID);
                wp_set_auth_cookie($user->ID, true);
                if(in_array('staff', $user->roles)){
                    $return['url']      =   site_url().'/staff-dashboard/';
                }else if(in_array('client', $user->roles)){
                    $return['url']      =   site_url().'/client-dashboard/';
                }else{
                    $return['url']      =   site_url();
                }
                $return['status']       =   true;
                $return['message']      =   'Login successful, redirecting...';
            }

            echo json_encode($return);
            exit;
        }

        public static function crm_custom_redirect_user(){
            if(is_front_page()){
                if(is_user_logged_in()){
                    $current_user = wp_get_current_user();
                    // Check if the user is an admin
                    if(in_array('administrator', $current_user->roles)){
                        // Redirect to the wp-admin dashboard
                        wp_redirect(admin_url());
                        exit;
                    }else if(in_array('client', $current_user->roles)){
                        // Redirect to the client's dashboard
                        wp_redirect(site_url('/client-dashboard'));
                        exit;
                    }else if(in_array('staff', $current_user->roles)){
                        // Redirect to the client's dashboard
                        wp_redirect(site_url('/staff-dashboard'));
                        exit;
                    }
                }else{
                    // Redirect to the custom home page if not logged in
                    wp_redirect(site_url('/login'));
                    exit;
                }
            }
        }

        public static function populate_users_dropdown($html, $field, $data){
            $form_id = 32;
            if($data->model->id == $form_id && $field['element_id'] == 'select-1'){
                $users          =   get_users(array('role' => 'client'));
                $options_html   =   '<option value="">Select User</option>';
                foreach($users as $user){
                    $options_html .= '<option value="'.esc_attr($user->ID).'">'.esc_html($user->display_name).'</option>';
                }
                $html = preg_replace('/<option[^>]*>.*?<\/option>/is', $options_html, $html);
            }
            return $html;
        }

        public static function save_selected_user_id($form_id, $response){
            if($form_id == 32){
                $selected_user_id = $response->get_entry_meta('select-1');
                if(!empty($selected_user_id)){
                    $response->set_entry_meta('selected_client_user_id', $selected_user_id);
                }
            }
        }

        public static function add_custom_entry_meta_column($meta, $form_id){
            if($form_id == 32){
                // Add the custom column for selected user ID
                $meta['selected_client_user_id'] = [
                    'label' => __('Selected Client ID', 'crm'),
                    'type'  => 'text'
                ];
            }
            return $meta;
        }

        public static function display_selected_user_id_in_entry_list($value, $meta_key, $entry_id){
            if($meta_key === 'selected_client_user_id'){
                $selected_user_id = get_post_meta($entry_id, 'selected_client_user_id', true);
                if($selected_user_id){
                    return $selected_user_id;
                }else{
                    return __('Not set', 'crm');
                }
            }
            return $value;
        }

        public static function add_admin_role_checkbox($user = null){
            if(current_user_can('create_users')){
                if($user && is_object($user)){
                    $checked = in_array('administrator', $user->roles) ? 'checked' : '';
                }else{
                    $checked = '';
                }
                ?>
                <h3><?php _e('Admin Rights'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th><label for="add_admin_role"><?php _e('Grant Admin Rights'); ?></label></th>
                        <td>
                            <input type="checkbox" name="add_admin_role" id="add_admin_role" <?php echo $checked; ?> />
                            <span class="description"><?php _e('Check to assign Administrator rights to this user.'); ?></span>
                        </td>
                    </tr>
                </table>
                <?php
            }
        }

        public static function save_admin_checkbox_cb($user_id){
            if(current_user_can('edit_user', $user_id)){
                if(isset($_POST['add_admin_role']) && $_POST['add_admin_role'] == 'on'){
                    $user = get_userdata($user_id);
                    $user->add_role('administrator');
                }else{
                    $user = get_userdata($user_id);
                    if($user && in_array('administrator', $user->roles)){
                        $user->remove_role('administrator');
                    }
                }
            }
        }

        //create a client..
        public static function create_a_client_callback(){
            $anc_firstname          =   (isset($_POST['anc_firstname']))            ?   sanitize_text_field($_POST['anc_firstname'])            : '';
            $anc_lastname           =   (isset($_POST['anc_lastname']))             ?   sanitize_text_field($_POST['anc_lastname'])             : '';
            $anc_countryresidence   =   (isset($_POST['anc_countryresidence']))     ?   sanitize_text_field($_POST['anc_countryresidence'])     : '';
            $entities               =   (isset($_POST['entities']))                 ?   $_POST['entities']                 : '';
            $return                 =   [];
            if(empty($anc_firstname) || empty($anc_lastname)){
                $return['status'] = false;
                $return['message'] = "Please fill out all required fields!";
            }else{
                $username   =   strtolower($anc_firstname. '.' .$anc_lastname);
                $password   =   wp_generate_password(12, false);
                if(username_exists($username)){
                    $username .= rand(100, 999);
                }
                $user_code  =   self::generate_unique_user_code();
                $user_id    =   wp_create_user($username, $password);
                if(!is_wp_error($user_id)){
                    $user = new WP_User($user_id);
                    $user->set_role('client');

                    wp_update_user(array(
                        'ID'            =>  $user_id,
                        'first_name'    =>  $anc_firstname,
                        'last_name'     =>  $anc_lastname
                    ));

                    update_user_meta($user_id, 'client_user_code', $user_code);
                    update_user_meta($user_id, 'country_of_residence', $anc_countryresidence);
                    update_user_meta($user_id, 'user_entities', $entities);
                    update_user_meta($user_id, 'first_name', $anc_firstname);
                    update_user_meta($user_id, 'last_name', $anc_lastname);

                    $return['status']   =   true;
                    $return['message']  =   "Client created successfully. Redirecting...";
                    $return['url']      =   site_url().'/staff-dashboard/?view=edit-client';
                }else{
                    $return['status']   =   false;
                    $return['message']  =   "Something is went wrong please try again. Thanks!";
                }
            }
            echo json_encode($return);
            exit();
        }

        public static function generate_unique_user_code(){
            $year = date('Y');
            do {
                $code = 'AA' . $year . sprintf('%03d', rand(1, 999));
            } while (self::user_code_exists($code));
        
            return $code;
        }

        public static function user_code_exists($code){
            global $wpdb;
            $query = $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'client_user_code' AND meta_value = %s", $code);
            return $wpdb->get_var($query);
        }

        public static function display_clients_with_pagination_table($paged = 1, $users_per_page = 10){
            $user_query = self::get_clients_with_pagination($paged, $users_per_page);
        
            if ($user_query && !empty($user_query->get_results())) {
                echo '<table class="client-list-table">';
                echo '<thead><tr><th>ID</th><th>Client Code</th><th>First Name</th><th>Last Name</th><th>Action</th></tr></thead>';
                echo '<tbody>';
                $counter = 1;
                foreach($user_query->get_results() as $user){
                    $user_id = $user->ID;
                    $user_code              =    get_user_meta($user_id, 'client_user_code', true);
                    $anc_countryresidence   =    get_user_meta($user_id, 'country_of_residence', true);
                    $anc_firstname          =    get_user_meta($user_id, 'first_name', true);
                    $anc_lastname           =    get_user_meta($user_id, 'last_name', true);
                    echo '<tr>';
                    echo '<td>' . esc_html($counter) . '</td>';
                    echo '<td>' . esc_html($user_code) . '</td>';
                    echo '<td>' . esc_html($anc_firstname) . '</td>';
                    echo '<td>' . esc_html($anc_lastname) . '</td>';
                    echo '<td><a href="/staff-dashboard/?view=edit-client-page&id='.$user->ID.'" class="btn" id="edit-btn">Edit</a></td>';
                    echo '</tr>';
                    $counter++;
                }
                echo '</tbody>';
                echo '</table>';
        
                // Pagination
                $total_users = $user_query->get_total();
                $total_pages = ceil($total_users / $users_per_page);
        
                if ($total_pages > 1) {
                    echo '<nav class="pagination">';
                    for ($i = 1; $i <= $total_pages; $i++) {
                        if ($i == $paged) {
                            echo '<span class="current">' . $i . '</span>';
                        } else {
                            echo '<a href="' . add_query_arg('paged', $i) . '">' . $i . '</a>';
                        }
                    }
                    echo '</nav>';
                }
            } else {
                echo '<p>No clients found.</p>';
            }
        }

        public static function get_clients_with_pagination($paged = 1, $users_per_page = 10){
            $args = array(
                'role'    => 'client',
                'number'  => $users_per_page,
                'paged'   => $paged,
            );
        
            $user_query = new WP_User_Query($args);
            if(!empty($user_query->get_results())){
                return $user_query;
            }else{
                return false;
            }
        }

        //Edit Client
        public static function edit_client_callback(){
            $anc_firstname          =   (isset($_POST['anc_firstname']))            ?   sanitize_text_field($_POST['anc_firstname'])            : '';
            $anc_lastname           =   (isset($_POST['anc_lastname']))             ?   sanitize_text_field($_POST['anc_lastname'])             : '';
            $anc_countryresidence   =   (isset($_POST['anc_countryresidence']))     ?   sanitize_text_field($_POST['anc_countryresidence'])     : '';
            $entities               =   (isset($_POST['entities']))                 ?   $_POST['entities']                 : '';
            $client_id               =   (isset($_POST['client_id']))                 ?   $_POST['client_id']                 : '';
            $return                 =   [];
            if(empty($anc_firstname) || empty($anc_lastname)){
                $return['status']   =   false;
                $return['message']  =   "Please fill out all required fields!";
            }else if(empty($client_id)){
                $return['status']   =   false;
                $return['message']  =   "Something went wrong please try again. Thanks!";
            }else{
                wp_update_user(array(
                    'ID'            =>  $client_id,
                    'first_name'    =>  $anc_firstname,
                    'last_name'     =>  $anc_lastname
                ));

                update_user_meta($client_id, 'country_of_residence', $anc_countryresidence);
                update_user_meta($client_id, 'user_entities', $entities);
                update_user_meta($client_id, 'first_name', $anc_firstname);
                update_user_meta($client_id, 'last_name', $anc_lastname);

                $return['status']   =   true;
                $return['message']  =   "Client updated successfully. Redirecting...";
                $return['url']      =   site_url().'/staff-dashboard/?view=edit-client';
            }
            echo json_encode($return);
            exit();
        }
        
    }

    CRMCustomizations::init();
}