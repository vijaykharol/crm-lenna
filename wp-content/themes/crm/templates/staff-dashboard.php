<?php
/**
 * Template Name: Staff Dashboard
 */
if(!is_user_logged_in()){
    header('Location: /login/');
    exit();
}
$user       =   wp_get_current_user();
$user_id    =   get_current_user_id();

if(!in_array('staff', $user->roles)){
    header('Location: /');
    exit();
}
require_once(CRM_THEME_DIR.'/inc/countries.php');
get_header();
$view = (isset($_GET['view']) && !empty($_GET['view'])) ? $_GET['view'] : 'dashboard';
?>
<link rel="stylesheet" href="<?= CRM_THEME_DIR_URI ?>/css/dashboard-templates.css?<?= time() ?>">
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Staff Dashboard</h2>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li class="list <?php if($view == 'dashboard') echo 'active'; ?>"><a href="?view=dashboard">Dashboard</a></li>
                <?php 
                if(in_array('administrator', $user->roles)){
                    ?>
                    <li class="list <?php if($view == 'add-new-client') echo 'active'; ?>"><a href="?view=add-new-client">Add New Client</a></li>
                    <li class="list <?php if($view == 'edit-client' || $view == 'edit-client-page') echo 'active'; ?>"><a href="?view=edit-client">Edit Client</a></li>
                    <?php
                }
                ?>
                <li class="list <?php if($view == 'client-doc-upload') echo 'active'; ?>"><a href="?view=client-doc-upload">Upload Documents</a></li>
                <li><a href="<?= wp_logout_url() ?>">Logout</a></li>
            </ul>
        </nav>
    </aside>
    <main class="main-content">
        <header class="header">
            <h1>Welcome, <?= $user->display_name ?></h1>
        </header>
        <section class="">
            <div class="cards">
                <?php
                if($view == 'dashboard'){
                    ?>
                    <div class="card-header">
                        <div class="heading"><h3>Dashboard content</h3></div>
                    </div>
                    <?php
                }else if($view == 'add-new-client'){
                    ?>
                    <div class="card">
                        <div class="card-header"><h3>Add New Client</h3></div>
                        <div class="card-body">
                            <form class="dashform" method="POST" id="add-new-client">
                                <div class="form-row">
                                    <div class="form-group w-33">
                                        <label for="anc_firstname">First Name of UBO/Controller <span style="color: red;">*</span></label>
                                        <input class="form-control" type="text" name="anc_firstname" id="anc_firstname">
                                    </div>
                                    <div class="form-group w-33">
                                        <label for="anc_lastname">Last Name of UBO/Controller <span style="color: red;">*</span></label>
                                        <input class="form-control" type="text" name="anc_lastname" id="anc_lastname">
                                    </div>
                                    <div class="form-group w-33">
                                        <label for="anc_countryresidence">Country of Residence</label>
                                        <select  class="form-control" name="anc_countryresidence" id="anc_countryresidence">
                                            <option value="">Select Country of Residence</option>
                                            <?php 
                                            if(isset($countries) && !empty($countries)){
                                                foreach($countries as $k => $v){
                                                    ?>
                                                    <option value="<?= $k.'-'.$v ?>"><?= $v ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group w-100">
                                        <label>Associated Entity(ies)</label>
                                        <div class="repeater-container">
                                            <div class="repeater-item">
                                                <input  class="form-control" type="text" class="entity-name" name="entities[0][name]" placeholder="Name of Associated Entity(ies)">
                                                <select  class="form-control" class="entity-type" name="entities[0][type]">
                                                    <option value="" disabled selected>Choose Legal Form</option>
                                                    <?php 
                                                    if(have_rows('type_of_entity', 'option')){
                                                        while(have_rows('type_of_entity', 'option')) : the_row();
                                                            $entity_name = get_sub_field('entity_name');
                                                            ?>
                                                            <option value="<?= $entity_name ?>"><?= $entity_name ?></option>
                                                            <?php
                                                        endwhile;
                                                    }
                                                    ?>
                                                </select>
                                                <button type="button" class="remove-button">
                                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M19 7a1 1 0 0 0-1 1v11.191A1.92 1.92 0 0 1 15.99 21H8.01A1.92 1.92 0 0 1 6 19.191V8a1 1 0 0 0-2 0v11.191A3.918 3.918 0 0 0 8.01 23h7.98A3.918 3.918 0 0 0 20 19.191V8a1 1 0 0 0-1-1ZM20 4h-4V2a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2H4a1 1 0 0 0 0 2h16a1 1 0 0 0 0-2ZM10 4V3h4v1Z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path><path d="M11 17v-7a1 1 0 0 0-2 0v7a1 1 0 0 0 2 0ZM15 17v-7a1 1 0 0 0-2 0v7a1 1 0 0 0 2 0Z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path></g>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group w-100" id="subtmit-section">
                                        <div class="button-group">
                                            <button type="button" id="add-button">Add</button>
                                            <button id="create-a-client-btn" class="form-btn">Generate Client Code & Close</button>  
                                        </div>                                      
                                        <div class="error" id="l-error" style="color:red; display:none;"></div>
                                        <div class="success" id="l-success" style="color:green; display:none;"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php
                }else if($view == 'edit-client-page'){
                    $client_id = (isset($_GET['id'])) ? $_GET['id'] : '';
                    ?>
                <div class="content-view-section">
                    <div class="heading"><h3>Edit Client</h3></div>
                    <div class="formdata">
                        <?php 
                        if(!empty($client_id)){
                            $userdata               =    get_userdata($client_id);
                            $user_id                =    $userdata->ID;
                            $user_code              =    get_user_meta($user_id, 'client_user_code', true);
                            $anc_countryresidence   =    get_user_meta($user_id, 'country_of_residence', true);
                            $entities               =    get_user_meta($user_id, 'user_entities', true);
                            $anc_firstname          =    get_user_meta($user_id, 'first_name', true);
                            $anc_lastname           =    get_user_meta($user_id, 'last_name', true);
                            ?>
                            <form class="dashform" method="POST" id="edit-client">
                                <input type="hidden" name="client_id" value="<?= $user_id ?>">
                                <div class="form-group">
                                    <label for="user_code">Client Code <span style="color: red;">*</span></label>
                                    <input class="form-control" type="text" value="<?= $user_code ?>" name="user_code" id="user_code" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="anc_firstname">First Name of UBO/Controller <span style="color: red;">*</span></label>
                                    <input class="form-control" type="text" value="<?= $anc_firstname ?>" name="anc_firstname" id="anc_firstname">
                                </div>
                                <div class="form-group">
                                    <label for="anc_lastname">Last Name of UBO/Controller <span style="color: red;">*</span></label>
                                    <input class="form-control" type="text" value="<?= $anc_lastname ?>" name="anc_lastname" id="anc_lastname">
                                </div>
                                <div class="form-group">
                                    <label for="anc_countryresidence">Country of Residence</label>
                                    <select class="form-control" name="anc_countryresidence" id="anc_countryresidence">
                                        <option value="">Select Country of Residence</option>
                                        <?php 
                                        if(isset($countries) && !empty($countries)){
                                            foreach($countries as $k => $v){
                                                ?>
                                                <option value="<?= $k.'-'.$v ?>" <?php if($anc_countryresidence == $k.'-'.$v) echo 'selected'; ?>><?= $v ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Associated Entity(ies)</label>
                                    <div class="repeater-container">
                                        <?php 
                                        if(!empty($entities) && is_array($entities)){
                                            foreach($entities as $key => $value){
                                                $name = (isset($value['name'])) ? $value['name'] : '';
                                                $type = (isset($value['type'])) ? $value['type'] : '';
                                                ?>
                                                <div class="repeater-item">
                                                    <?php 
                                                    if(!empty($name)){
                                                        ?>
                                                        <input class="form-control" type="text" class="entity-name" value="<?= $name ?>" name="entities[<?= $key ?>][name]" placeholder="Name of Associated Entity(ies)" readonly>
                                                        <?php
                                                    }else{
                                                        ?>
                                                        <input class="form-control" type="text" class="entity-name" name="entities[<?= $key ?>][name]" placeholder="Name of Associated Entity(ies)">
                                                        <?php
                                                    }
                                                    ?>
                                                    <select class="entity-type form-control" name="entities[<?= $key ?>][type]">
                                                        <option value="" disabled selected>Choose Legal Form</option>
                                                        <?php 
                                                        if(have_rows('type_of_entity', 'option')){
                                                            while(have_rows('type_of_entity', 'option')) : the_row();
                                                                $entity_name = get_sub_field('entity_name');
                                                                ?>
                                                                <option value="<?= $entity_name ?>" <?php if($type == $entity_name) echo 'selected'; ?>><?= $entity_name ?></option>
                                                                <?php
                                                            endwhile;
                                                        }
                                                        ?>
                                                    </select>
                                                    <button type="button" class="remove-button">
                                                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M19 7a1 1 0 0 0-1 1v11.191A1.92 1.92 0 0 1 15.99 21H8.01A1.92 1.92 0 0 1 6 19.191V8a1 1 0 0 0-2 0v11.191A3.918 3.918 0 0 0 8.01 23h7.98A3.918 3.918 0 0 0 20 19.191V8a1 1 0 0 0-1-1ZM20 4h-4V2a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2H4a1 1 0 0 0 0 2h16a1 1 0 0 0 0-2ZM10 4V3h4v1Z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path><path d="M11 17v-7a1 1 0 0 0-2 0v7a1 1 0 0 0 2 0ZM15 17v-7a1 1 0 0 0-2 0v7a1 1 0 0 0 2 0Z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path></g>
                                                        </svg>
                                                    </button>
                                                </div>
                                                <?php   
                                            }
                                        }else{
                                            ?>
                                            <div class="repeater-item">
                                                <input class="form-control" type="text" class="entity-name" name="entities[0][name]" placeholder="Name of Associated Entity(ies)">
                                                <select class="form-control" class="entity-type" name="entities[0][type]">
                                                    <option value="" disabled selected>Choose Legal Form</option>
                                                    <?php 
                                                    if(have_rows('type_of_entity', 'option')){
                                                        while(have_rows('type_of_entity', 'option')) : the_row();
                                                            $entity_name = get_sub_field('entity_name');
                                                            ?>
                                                            <option value="<?= $entity_name ?>"><?= $entity_name ?></option>
                                                            <?php
                                                        endwhile;
                                                    }
                                                    ?>
                                                </select>
                                                <button type="button" class="remove-button">
                                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M19 7a1 1 0 0 0-1 1v11.191A1.92 1.92 0 0 1 15.99 21H8.01A1.92 1.92 0 0 1 6 19.191V8a1 1 0 0 0-2 0v11.191A3.918 3.918 0 0 0 8.01 23h7.98A3.918 3.918 0 0 0 20 19.191V8a1 1 0 0 0-1-1ZM20 4h-4V2a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2H4a1 1 0 0 0 0 2h16a1 1 0 0 0 0-2ZM10 4V3h4v1Z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path><path d="M11 17v-7a1 1 0 0 0-2 0v7a1 1 0 0 0 2 0ZM15 17v-7a1 1 0 0 0-2 0v7a1 1 0 0 0 2 0Z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path></g>
                                                </button>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <button type="button" id="add-button">Add</button>
                                </div>
                                <div class="form-group">
                                    <div class="error" id="l-error" style="color:red; display:none;"></div>
                                    <div class="success" id="l-success" style="color:green; display:none;"></div>
                                </div>
                                <div class="form-group" id="subtmit-section">
                                    <button id="edit-client-btn" class="form-btn">Save & Close</button>
                                </div>
                            </form>
                            <?php
                        }else{
                            ?>
                            <p style="color:red;">Wrong client url please go back and try again. Thanks!</p>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php
                }else if($view == 'edit-client'){
                    ?>
                    <div class="content-view-section">
                        <div class="heading"><h3>Edit Client</h3></div>
                        <div class="formdata">
                            <div class="client-list-container">
                                <?php 
                                $paged          = (get_query_var('paged')) ? get_query_var('paged') : 1;
                                $users_per_page = 10;
                                if(class_exists('CRMCustomizations')){
                                    CRMCustomizations::display_clients_with_pagination_table($paged, $users_per_page); 
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }else if($view == 'client-doc-upload'){
                    ?>
                    <div class="card content-view-section">
                        <div class="card-header"><h3>Upload Documents</h3></div>
                        <div class="card-body">
                            <form method="POST" id="form-document-uploader" enctype="multipart/form-data">
                                <div id="documentContainer">
                                    <div class="documentRow form-row">
                                        <div class="form-group w-33">
                                            <?php 
                                            $users = get_users(array('role' => 'client'));
                                            ?>
                                            <label for="">Client Code</label>
                                            <select class="form-control" name="client_document[0][client_code]" class="client-code" required>
                                                <option value="">Seclect Client Code</option>
                                                <?php 
                                                if(!empty($users)){
                                                    foreach($users as $u){
                                                        $userid     =   $u->ID;
                                                        $usercode   =   get_user_meta($userid, 'client_user_code', true);
                                                        ?>
                                                        <option value="<?= $userid.'-'.$usercode ?>"><?= $usercode ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group w-33">
                                            <label for="">Date of Document</label>
                                            <input class="form-control" type="date" name="client_document[0][document_date]" placeholder="Date of Document" required>
                                        </div>
                                        <div class="form-group w-33">
                                            <label for="">Category</label>
                                            <select class="form-control" name="client_document[0][doc_category]" class="doc-categories" required>
                                                <option value="">Choose Category</option>
                                                <?php 
                                                $documentCategories = get_terms(array(
                                                    'taxonomy'      =>  'document-category',
                                                    'hide_empty'    =>  false,
                                                ));
                                                if(!empty($documentCategories) && !is_wp_error($documentCategories)){
                                                    foreach($documentCategories as $docCat){
                                                        ?>
                                                        <option value="<?= $docCat->term_id ?>"><?= $docCat->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group w-33 align-self-end">
                                            <label for="">Document Type</label>
                                            <select class="form-control" name="client_document[0][doc_type]" class="doc-type" required>
                                                <option value="">Choose Document Type</option>
                                                <?php 
                                                $documentTypes = get_terms(array(
                                                    'taxonomy'      =>  'document-type',
                                                    'hide_empty'    =>  false,
                                                ));
                                                if(!empty($documentTypes) && !is_wp_error($documentTypes)){
                                                    foreach($documentTypes as $docType){
                                                        ?>
                                                        <option value="<?= $docType->term_id ?>"><?= $docType->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group w-33 align-self-end">
                                            <input class="form-control" type="file" name="document_files_0[]" class="document-files" accept=".pdf, .doc, .docx, .xls, .xlsx, .ppt, .pptx, .txt, .rtf, .jpg, .jpeg, .png, .gif, .svg, .webp" multiple>
                                        </div>
                                        <div class="form-group align-self-end">
                                            <button type="button" class="doc-row-removeBtn remove-button">
                                                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M19 7a1 1 0 0 0-1 1v11.191A1.92 1.92 0 0 1 15.99 21H8.01A1.92 1.92 0 0 1 6 19.191V8a1 1 0 0 0-2 0v11.191A3.918 3.918 0 0 0 8.01 23h7.98A3.918 3.918 0 0 0 20 19.191V8a1 1 0 0 0-1-1ZM20 4h-4V2a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2H4a1 1 0 0 0 0 2h16a1 1 0 0 0 0-2ZM10 4V3h4v1Z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path><path d="M11 17v-7a1 1 0 0 0-2 0v7a1 1 0 0 0 2 0ZM15 17v-7a1 1 0 0 0-2 0v7a1 1 0 0 0 2 0Z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path></g>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group w-100 mt-24">
                                    <div class="button-group">
                                        <button type="button" id="addMore-documentBtn">Add More</button>
                                        <button class="form-btn" id="submit-documents-btn">Submit Documents</button>
                                    </div>
                                    <div class="error" id="l-error" style="color:red; display:none;"></div>
                                    <div class="success" id="l-success" style="color:green; display:none;"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php
                }else{
                    ?>
                    <div class="content-view-section">
                        <div class="heading"><h3>Dashboard content</h3></div>
                        <div class="formdata"></div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </section>
    </main>
</div>
<?php
get_footer();
?>
<script>
    jQuery(document).ready(function(){
        jQuery(document).on('click', '#addMore-documentBtn', function(){
            let index = new Date().getTime();
            var html = `
            <div class="documentRow form-row">
                <div class="form-group w-33">
                    <?php 
                    $users = get_users(array('role' => 'client'));
                    ?>
                    <label for="">Client Code</label>
                    <select name="client_document[${index}][client_code]" class="client-code form-control" required>
                        <option value="">Seclect Client Code</option>
                        <?php 
                        if(!empty($users)){
                            foreach($users as $u){
                                $userid     =   $u->ID;
                                $usercode   =   get_user_meta($userid, 'client_user_code', true);
                                ?>
                                <option value="<?= $userid.'-'.$usercode ?>"><?= $usercode ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group w-33">
                    <label for="">Date of Document</label>
                    <input class="form-control"  type="date" name="client_document[${index}][document_date]" placeholder="Date of Document" required>
                </div>
                <div class="form-group w-33">
                    <label for="">Category</label>
                    <select name="client_document[${index}][doc_category]" class="doc-categories form-control" required>
                        <option value="">Choose Category</option>
                        <?php 
                        $documentCategories = get_terms(array(
                            'taxonomy'      =>  'document-category',
                            'hide_empty'    =>  false,
                        ));
                        if(!empty($documentCategories) && !is_wp_error($documentCategories)){
                            foreach($documentCategories as $docCat){
                                ?>
                                <option value="<?= $docCat->term_id ?>"><?= $docCat->name ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group w-33">
                    <label for="">Document Type</label>
                    <select name="client_document[${index}][doc_type]" class="doc-type form-control" required>
                        <option value="">Choose Document Type</option>
                        <?php 
                        $documentTypes = get_terms(array(
                            'taxonomy'      =>  'document-type',
                            'hide_empty'    =>  false,
                        ));
                        if(!empty($documentTypes) && !is_wp_error($documentTypes)){
                            foreach($documentTypes as $docType){
                                ?>
                                <option value="<?= $docType->term_id ?>"><?= $docType->name ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group w-33">
                    <input type="file" name="document_files_${index}[]" class="document-files form-control" accept=".pdf, .doc, .docx, .xls, .xlsx, .ppt, .pptx, .txt, .rtf, .jpg, .jpeg, .png, .gif, .svg, .webp" multiple>
                </div>
                <div class="form-group w-33">
                    <button type="button" class="doc-row-removeBtn remove-button">
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M19 7a1 1 0 0 0-1 1v11.191A1.92 1.92 0 0 1 15.99 21H8.01A1.92 1.92 0 0 1 6 19.191V8a1 1 0 0 0-2 0v11.191A3.918 3.918 0 0 0 8.01 23h7.98A3.918 3.918 0 0 0 20 19.191V8a1 1 0 0 0-1-1ZM20 4h-4V2a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2H4a1 1 0 0 0 0 2h16a1 1 0 0 0 0-2ZM10 4V3h4v1Z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path><path d="M11 17v-7a1 1 0 0 0-2 0v7a1 1 0 0 0 2 0ZM15 17v-7a1 1 0 0 0-2 0v7a1 1 0 0 0 2 0Z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path></g>
                                                </svg>
                    </button>
                </div>
            </div>`;
            jQuery('div#documentContainer').last().append(html);
            if(jQuery('.documentRow').length > 0){
                jQuery('button#submit-documents-btn').show(); 
            }
        });

        //remove document row
        jQuery(document).on('click', '.doc-row-removeBtn', function(){
            jQuery(this).parent().parent().remove();
            if(jQuery('.documentRow').length <= 0){
                jQuery('button#submit-documents-btn').hide(); 
            }
        });
    });
</script>