<?php
/**
 * Template Name: Client Dashboard
 */
if(!is_user_logged_in()){
    header('Location: /login/');
    exit();
}
$user       =   wp_get_current_user();
$user_id    =   get_current_user_id();
if(!in_array('client', $user->roles)){
    header('Location: /');
    exit();
}
get_header();
global $wpdb;
$view = (isset($_GET['view']) && !empty($_GET['view'])) ? $_GET['view'] : 'myaccount';
?>
<link rel="stylesheet" href="<?= CRM_THEME_DIR_URI ?>/css/dashboard-templates.css?<?= time() ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Client Dashboard</h2>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li class="list <?php if($view == 'myaccount') echo 'active'; ?>"><a href="?view=myaccount">My Account</a></li>
                <li><a href="<?= wp_logout_url() ?>">Logout</a></li>
            </ul>
        </nav>
    </aside>
    <main class="main-content">
        <header class="header">
            <h1>Welcome, <?= $user->display_name ?></h1>
        </header>
        <section class="cards" id="view-section">
            <?php
            if($view == 'myaccount'){
                ?>
                <div class="card">
                    <div class="card-header"><h2>Protected: Account</h2></div>
                    <div class="card-body">
                        <div class="multi-card-section-1">
                            <div class="head-multi-card">
                                <h3>Existing</h3>
                            </div>
                            <div class="multi-card">
                                <h4 class="card_subhead">Your Entities:</h4>
                                <div class="client-list-container">
                                    <?php 
                                    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                                    $args = [
                                        'post_type'         =>  'client_document',
                                        'post_status'       =>  'publish',
                                        'meta_query'        =>  [
                                            [
                                                'key'       =>  'document_client_id', 
                                                'value'     =>  $user_id,
                                                'compare'   =>  '='
                                            ]
                                        ],
                                        'posts_per_page'    =>  10, 
                                        'paged'             =>  $paged
                                    ];
                                    $docQuery = new WP_Query($args);
                                    ?>
                                    <table class="client-list-table">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Entity</th>
                                                <th>Document Ref:</th>
                                                <th>Download</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            if($docQuery->have_posts()){
                                                $counter = 1;
                                                while($docQuery->have_posts()){
                                                    $docQuery->the_post();
                                                    $postid             =   get_the_ID();
                                                    $client_code        =   get_post_meta($postid, 'document_client_code', true);
                                                    $document_files     =   get_post_meta($postid, 'document_files', true);
                                                    $document_ref_no    =   get_post_meta($postid, 'document_ref_no', true);
                                                    $document_entity_name    =   get_post_meta($postid, 'document_entity_name', true);
                                                    //Category
                                                    $docCategory    =   wp_get_post_terms($postid, 'document-category');
                                                    $docategories   =   [];
                                                    if(!empty($docCategory)){
                                                        foreach($docCategory as $dc){
                                                            $docategories[] = $dc->name;
                                                        }
                                                    }
                                                    //Type
                                                    $docType    =   wp_get_post_terms($postid, 'document-type');
                                                    $docTypes   =   [];
                                                    if(!empty($docType)){
                                                        foreach($docType as $dt){
                                                            $docTypes[] = $dt->name;
                                                        }
                                                    }
                                                    $publish_date = get_the_date('d/m/Y');
                                                    ?>
                                                    <tr>
                                                        <td><?= $publish_date ?></td>
                                                        <td><?= $document_entity_name ?></td>
                                                        <td><?= $document_ref_no ?></td>
                                                        <td>
                                                            <?php 
                                                            if(!empty($document_files) && is_array($document_files)){
                                                                ?>
                                                                <div class="files"  style="display: flex; flex-wrap: wrap; gap: 10px;">
                                                                    <?php
                                                                    foreach($document_files as $file_url){
                                                                        ?>
                                                                        <span class="file"><a href="<?= $file_url ?>" target="_blank"><?= implode(',', $docTypes) ?></a></span>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    $counter++;
                                                }
                                                wp_reset_postdata();
                                            }else{
                                                echo 'No client documents found with the specified meta value.';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                    <div class="pagination">
                                        <?php 
                                        // Pagination
                                        $big = 999999999; 
                                        echo paginate_links(array(
                                            'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                                            'format'    => '?paged=%#%',
                                            'current'   => max(1, get_query_var('paged')),
                                            'total'     => $docQuery->max_num_pages
                                        ));
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="multi-card">
                                <h4 class="card_subhead">Billing:</h4>
                                <div class="client-list-container">
                                    <?php
                                    $form_id = 30;
                                    $submitted_entities = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."frmt_form_entry` AS p
                                                                                LEFT JOIN `".$wpdb->prefix."frmt_form_entry_meta` AS c ON p.entry_id=c.entry_id 
                                                                                WHERE (c.meta_key='hidden-1' AND c.meta_value='$user_id') AND form_id='$form_id' ORDER BY p.entry_id DESC");
                                    ?>
                                    <table class="client-list-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 10px;">Invoice No.</th>
                                                <th style="width: 70px;">Amount - USD</th>
                                                <th style="width: 10px;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            if(!empty($submitted_entities) && is_array($submitted_entities)){
                                                $counter = 1;
                                                foreach($submitted_entities as $se){
                                                    $datecreated            =   (!empty($se->date_created)) ? date('Y-m-d', strtotime($se->date_created)) : '';
                                                    $entry_id               =   $se->entry_id;
                                                    $entryData              =   Forminator_API::get_entry( $form_id, $entry_id );
                                                    $costindications        =   $entryData->meta_data['calculation-1']['value']['result'];
                                                    $selectedCheckboxes     =   $entryData->meta_data['checkbox-1']['value'];
                                                    $invoicenumber          =   $entryData->meta_data['entry_invoice_number']['value'];
                                                    $class                  =   'due';
                                                    ?>
                                                    <tr class="<?= $class ?>">
                                                        <td><?= $invoicenumber ?></td>
                                                        <td><?= 'USD'.number_format($costindications) ?></td>
                                                        <td>Due</td>
                                                    </tr>
                                                    <?php
                                                    $counter++;
                                                }
                                            }else{
                                                ?>
                                                <tr>
                                                    <td colspan="6">Nothing found!</td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="multi-card-section-2">
                            <div class="head-multi-card">
                                <h3>New Entities Formation & Licensing</h3>
                            </div>
                            <div class="multi-card">
                                <h4 class="card_subhead">Setup a New Entity</h4>
                                <div class="forminator-form">
                                    <?php echo do_shortcode('[forminator_form id="30"]'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </section>
    </main>
</div>
<?php
get_footer();