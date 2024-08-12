<?php
if(!defined('ABSPATH')){
    exit();
}
/**
 * CLASS CRMCustmizationsHelper
 */
if(!class_exists('CRMCustmizationsHelper', false)){
    class CRMCustmizationsHelper{
        public static function init(){
            add_action( 'init', [__CLASS__, 'create_client_documents_post_type'] );

            //upload Documents
            add_action( 'wp_ajax_upload-client-documents', [__CLASS__, 'upload_client_documents_cb'] );
            add_action( 'wp_ajax_nopriv_upload-client-documents', [__CLASS__, 'upload_client_documents_cb'] );
            add_action( 'after_setup_theme', [__CLASS__, 'hide_admin_bar_for_non_admins'] );
            
            //Forminator Form Save
            add_action('forminator_custom_form_mail_before_send_mail', [__CLASS__, 'my_custom_function_for_CForm'], 10, 4);
        }
        public static function create_client_documents_post_type(){
            $labels = array(
                'name'               => _x('Client Documents', 'post type general name', 'crm'),
                'singular_name'      => _x('Client Document', 'post type singular name', 'crm'),
                'menu_name'          => _x('Client Documents', 'admin menu', 'crm'),
                'name_admin_bar'     => _x('Client Document', 'add new on admin bar', 'crm'),
                'add_new'            => _x('Add New', 'book', 'crm'),
                'add_new_item'       => __('Add New Client Document', 'crm'),
                'new_item'           => __('New Client Document', 'crm'),
                'edit_item'          => __('Edit Client Document', 'crm'),
                'view_item'          => __('View Client Document', 'crm'),
                'all_items'          => __('All Client Documents', 'crm'),
                'search_items'       => __('Search Client Documents', 'crm'),
                'parent_item_colon'  => __('Parent Client Documents:', 'crm'),
                'not_found'          => __('No client documents found.', 'crm'),
                'not_found_in_trash' => __('No client documents found in Trash.', 'crm'),
            );
        
            $args = array(
                'labels'             => $labels,
                'public'             => false,
                'publicly_queryable' => false,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'show_in_admin_bar'  => true,
                'show_in_nav_menus'  => false,
                'has_archive'        => false,
                'exclude_from_search'=> true,
                'supports'           => array('title', 'editor', 'author', 'thumbnail', 'comments', 'revisions'),
                'capability_type'    => 'post',
            );
            register_post_type('client_document', $args);

            //Document Category
            $tax_args = array(
                'label'             => 'Document Category',
                'public'            => false, 
                'show_ui'           => true, 
                'show_in_nav_menus' => false, 
                'show_tagcloud'     => false, 
                'show_in_rest'      => false, 
                'hierarchical'      => true,
                'rewrite'           => false, 
            );
            register_taxonomy('document-category', array('client_document'), $tax_args);

            //Document Type
            $tax_args = array(
                'label'             => 'Document Type',
                'public'            => false, 
                'show_ui'           => true, 
                'show_in_nav_menus' => false, 
                'show_tagcloud'     => false, 
                'show_in_rest'      => false, 
                'hierarchical'      => true,
                'rewrite'           => false, 
            );
            register_taxonomy('document-type', array('client_document'), $tax_args);
        }
        public static function upload_client_documents_cb(){
            $return = [];
            if(isset($_POST['client_document']) && !empty($_POST['client_document'])){
                $documents = $_POST['client_document'];
                foreach($documents as $index => $document){

                    $client             =   sanitize_text_field($document['client_code']);
                    $clientsData        =   (!empty($client)) ? explode('-', $client)   : [];
                    $client_id          =   (isset($clientsData[0])) ? $clientsData[0]     : '';
                    $client_code        =   (isset($clientsData[1])) ? $clientsData[1]     : '';
                    $document_date      =   sanitize_text_field($document['document_date']);
                    $unique_suffix      =   time(); 
                    $unique_title       =   "Client Document-$client_code-$document_date-$unique_suffix";
                    $current_user_id    =   get_current_user_id();

                    $post_id = wp_insert_post(array(
                        'post_title'                =>  $unique_title,
                        'post_type'                 =>  'client_document',
                        'post_status'               =>  'publish',
                        'post_date'                 =>  sanitize_text_field($document['document_date']),
                        'post_author'               =>  $current_user_id,
                        'meta_input'                =>  array(
                            'document_client_id'    =>  $client_id,
                            'document_client_code'  =>  $client_code,
                            'document_ref_no'       =>  self::generate_random_reference_number(),
                            'document_entity_name'  =>  $document['entity_name'],
                        )
                    ));
        
                    if($post_id && !is_wp_error($post_id)){
                        $fileinputname = 'document_files_'.$index;
                        if(isset($_FILES[$fileinputname]) && !empty($_FILES[$fileinputname]['name'])){
                            $file_key       =   'document_files_'.$index;
                            $files          =   $_FILES[$file_key];
                            $upload_dir     =   wp_upload_dir();
                            $upload_path    =   $upload_dir['basedir'] . '/clients-documents/';
                            if(!file_exists($upload_path)){
                                mkdir($upload_path, 0755, true);
                            }
                            $uploaded_urls = [];
                            foreach($files['name'] as $key => $filename){
                                if($files['error'][$key] == UPLOAD_ERR_OK){
                                    $tmp_name           =   $files['tmp_name'][$key];
                                    $file_ext           =   pathinfo($filename, PATHINFO_EXTENSION);
                                    $unique_file_name   =   $client_code.'_'.time().'_'.uniqid().'.'.$file_ext;
                                    $file_path          =   $upload_path.$unique_file_name;
        
                                    if(move_uploaded_file($tmp_name, $file_path)){
                                        $file_url           =   $upload_dir['baseurl'].'/clients-documents/'.$unique_file_name;
                                        $uploaded_urls[]    =   $file_url;
                                    }
                                }
                            }
                            //update post documents
                            if(!empty($uploaded_urls)){
                                update_post_meta($post_id, 'document_files', $uploaded_urls);
                            }
                        }
        
                        // Step 4: Manage selected category and type
                        if(!empty($document['doc_category'])){
                            wp_set_object_terms($post_id, intval($document['doc_category']), 'document-category');
                        }
        
                        if(!empty($document['doc_type'])){
                            wp_set_object_terms($post_id, intval($document['doc_type']), 'document-type');
                        }
                    }
                }
                $return['status'] = true;
                $return['message'] = 'Documents Submitted Successfully!';
            }else{
                $return['status'] = false;
                $return['message'] = 'Something is went wrong please try again. Thanks!';
            }
            echo json_encode($return);
            exit();
        }
        public static function hide_admin_bar_for_non_admins(){
            if(!is_admin()){
                show_admin_bar(false);
            }
        }

        public static function my_custom_function_for_CForm($formid, $custom_form, $data, $entry){
            $entry_id = (isset($entry->entry_id)) ? $entry->entry_id : '';
            $formid   = (isset($entry->form_id)) ? $entry->form_id : '';
            if(!empty($formid) && !empty($entry_id) && $formid == 30){
                $invoice_number = 'C'.$entry_id.rand(100000, 999999);
                $entry_meta[] = array(
                    'name'  =>  'entry_invoice_number',
                    'value' =>  $invoice_number
                );
                Forminator_API::update_entry_meta($formid, $entry_id, $entry_meta);
            }
        }

        public static function generate_random_reference_number($prefix = 'TABC', $length = 3){
            $random_number      =   rand(1, 999);
            $padded_number      =   str_pad($random_number, $length, '0', STR_PAD_LEFT);
            $reference_number   =   $prefix.'-'.$padded_number;
            return $reference_number;
        }
    }
    CRMCustmizationsHelper::init();
}