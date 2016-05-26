<?php
class JB_Taxonomy_Meta extends JB_Base{
    public static $instance;
    public $tax;
    public $meta;
    /**
     * getInstance method
     *
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * the constructor of this class
     *
     */
    public  function __construct( $tax = 'category', $meta = array() ){
        $this->add_action($tax.'_add_form_fields', 'jb_add_form_fields');
        $this->add_action( 'created_'.$tax, 'jb_save_tax_meta', 10, 2 );
        $this->add_action( $tax .'_edit_form_fields', 'jb_edit_tax_group_field', 10, 2 );
        $this->add_action( 'edited_'.$tax, 'jb_update_tax_meta', 10, 2 );
        $this->add_filter('manage_edit-'.$tax.'_columns', 'jb_add_tax_column' );
        $this->add_filter('manage_'.$tax.'_custom_column', 'jb_add_tax_column_content', 10, 3 );
        $this->add_action( 'admin_enqueue_scripts', 'jb_tax_enqueue_scripts'  );
        $this->tax = $tax;
        $this->meta = $meta;
    }
    /**
     * Description
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function jb_add_form_fields($taxonomy) {
        global $featured_tax, $user_ID, $jb_post_factory;
        $jb_pack = $jb_post_factory->get('pack');
        $packs = $jb_pack->fetch('pack');
        $term_id = 0;
        // Remove image URL
        $remove_url = add_query_arg( array(
            'action'   => 'remove-wp-term-images',
            'term_id'  => $term_id,
            '_wpnonce' => false,
        ) );
        // Get the meta value
        $value = get_term_meta($term_id, 'mjob_category_image', true);
        $hidden = empty( $value )
            ? ' style="display: none;"'
            : ''; ?>
        <div class="form-field term-group">
        <label><?php _e('Pricing plan', ET_DOMAIN) ?></label>
        <div>
            <select name="pricing_plan">
            <?php if(!empty($packs)):
                foreach($packs as $pack):
                    ?>
                    <option  value="<?php echo $pack->sku; ?>"><?php echo $pack->post_title; ?></option>
                <?php endforeach;
            endif; ?>
        </select>
        </div>
        <div class="form-field term-group">
        <label><?php _e('Taxonomy image', ET_DOMAIN) ?></label>
        <div>
            <img id="jb-tax-images-photo" src="<?php echo esc_url( wp_get_attachment_image_url( $value, 'full' ) ); ?>"<?php echo $hidden; ?> />
            <input type="hidden" name="<?php echo $taxonomy; ?>_image" id="<?php echo $taxonomy; ?>_image" value="<?php echo esc_attr( $value ); ?>" />
        </div>

        <a class="button-secondary jb-tax-images-media">
            <?php esc_html_e( 'Choose Image', ET_DOMAIN ); ?>
        </a>

        <a href="<?php echo esc_url( $remove_url ); ?>" class="button jb-tax-images-remove"<?php echo $hidden; ?>>
            <?php esc_html_e( 'Remove', 'wp-user-avatars' ); ?>
        </a>
        <div class="clearfix"></div>
            <br/>
        <div class="featured-tax">
            <input type="checkbox" name="featured-tax" class="left margin-20 margin-top-3" value="true" />
            <label for="featured-tax" class="left"><?php _e('Featured taxonomy', ET_DOMAIN); ?></label>
        </div>
        <br/>
        <br/>
                <p>
                    <label for="cat_bottom_title"><?php _e('Bottom Title', ET_DOMAIN) ?></label>
                    <input type="text" name="cat_bottom_title" />
                </p>
                <p>
                    <label for="cat_bottom_block1_title"><?php _e('Bottom block 1 title', ET_DOMAIN) ?></label>
                    <input type="text" name="cat_bottom_block1_title" />
                </p>
                <p>
                    <label for="cat_bottom_block1_content"><?php _e('Bottom block 1 content', ET_DOMAIN) ?></label>
                    <textarea name="cat_bottom_block1_content" rows="5"> </textarea>
                </p>
                <p>
                    <label for="cat_bottom_block2_title"><?php _e('Bottom block 2 title', ET_DOMAIN) ?></label>
                    <input type="text" name="cat_bottom_block2_title" />
                </p>
                <p>
                    <label for="cat_bottom_block2_content"><?php _e('Bottom block 2 content', ET_DOMAIN) ?></label>
                    <textarea name="cat_bottom_block2_content" rows="5"> </textarea>
                </p>
                <p>
                    <label for="cat_bottom_block3_title"><?php _e('Bottom block 3 title', ET_DOMAIN) ?></label>
                    <input type="text" name="cat_bottom_block3_title" />
                </p>
                <p>
                    <label for="cat_bottom_block3_content"><?php _e('Bottom block 3 content', ET_DOMAIN) ?></label>
                    <textarea name="cat_bottom_block3_content" rows="5"> </textarea>
                </p>
        </div>
        <div class="clearfix"></div>
        <br/>
        <?php
    }
    /**
     * save tax meta
     *
     * @param integer $term_id
     * @param integer $tt_id
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function jb_save_tax_meta( $term_id, $tt_id ){
        $request = $_REQUEST;
        foreach( $this->meta as $key=> $value){
            if( isset($request[$value]) ){
                $group =  $request[$value] ;
                update_term_meta($term_id, $value, $group);
            }
        }
//        if( isset( $_POST['featured-tax'] ) && '' !== $_POST['featured-tax'] ){
//            $group = sanitize_title( $_POST['featured-tax'] );
//            add_term_meta( $term_id, 'featured-tax', $group, true );
//        }
//        if( isset( $_POST['mjob_category_image'] ) && '' !== $_POST['mjob_category_image'] ){
//            $group = sanitize_title( $_POST['mjob_category_image'] );
//            update_term_meta( $term_id, 'mjob_category_image', $group );
//        }
    }
    /**
     * edit form tax
     *
     * @param object $term
     * @param string $taxonomy
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function jb_edit_tax_group_field( $term, $taxonomy ){
        global $featured_tax, $user_ID, $jb_post_factory;
        $jb_pack = $jb_post_factory->get('pack');
        $packs = $jb_pack->fetch('pack');
        // get current group
        $check = '';
        $featured_tax = get_term_meta( $term->term_id, 'featured-tax', true );
        if( $featured_tax ){
            $check = 'checked';
        }
        $remove_url = add_query_arg( array(
            'action'   => 'remove-jb-tax-images',
            'term_id'  => $term->term_id,
            '_wpnonce' => false,
        ) );
        $value = get_term_meta($term->term_id, 'mjob_category_image', true);
        $hidden = empty( $value )
            ? ' style="display: none;"'
            : '';
        $arr = array();
        foreach( $this->meta as $key=>$value ){
            $arr[$value] = get_term_meta($term->term_id, $value, true);
        }
        ?>
        <tr class="form-field term-group-wrap">
            <th scope="row"><label for="featured-tax"><?php _e( 'Pricing plan', ET_DOMAIN ); ?></label></th>
            <td>
                <select name="pricing_plan">
                <?php if(!empty($packs)):
                    foreach($packs as $pack):
                        $selected = '';
                        if( $pack->sku == $arr['pricing_plan']):
                            $selected = 'selected';
                        endif;
                    ?>
                    <option <?php echo $selected; ?> value="<?php echo $pack->sku; ?>"><?php echo $pack->post_title; ?></option>
                <?php endforeach;
                    endif; ?>
                </select>
            </td>
        </tr>
        <tr class="form-field term-group-wrap">
        <th scope="row"><label for="featured-tax"><?php _e( 'Featured taxonomy', ET_DOMAIN ); ?></label></th>
        <td><input type="checkbox" name="featured-tax" value="true" <?php echo $check; ?>/> <label for="featured-tax"><?php _e('Featured taxonomy', ET_DOMAIN); ?></label></td>
        </tr>
        <tr>
            <th scope="row"><label for="tax-image"><?php _e( 'taxonomy image', ET_DOMAIN ); ?></label></th>
            <td>
                <div>
                    <img id="jb-tax-images-photo" src="<?php echo esc_url( wp_get_attachment_image_url( $value, 'full' ) ); ?>"<?php echo $hidden; ?> />
                    <input type="hidden" name="<?php echo $taxonomy; ?>_image" id="<?php echo $taxonomy; ?>_image" value="<?php echo esc_attr( $value ); ?>" />
                </div>

                <a class="button-secondary jb-tax-images-media">
                    <?php esc_html_e( 'Choose Image', ET_DOMAIN ); ?>
                </a>

                <a href="<?php echo esc_url( $remove_url ); ?>" class="button jb-tax-images-remove"<?php echo $hidden; ?>>
                    <?php esc_html_e( 'Remove', 'wp-user-avatars' ); ?>
                </a>
            </td>
        </tr>
        <tr class="form-field term-slug-wrap">
            <th scope="row"><label for="cat_bottom_title"><?php _e('Bottom Title', ET_DOMAIN) ?></label></th>
            <td><input type="text" name="cat_bottom_title" size="40" value="<?php echo $arr['cat_bottom_title']; ?>"/></td>
        </tr>
        <tr class="form-field term-slug-wrap">
            <th scope="row"><label for="cat_bottom_block1_title"><?php _e('Bottom block 1 title', ET_DOMAIN) ?></label></th>
            <td><input type="text" name="cat_bottom_block1_title" value="<?php echo $arr['cat_bottom_block1_title']; ?>"/></td>
        </tr>
        <tr class="form-field term-slug-wrap">
            <th scope="row"><label for="cat_bottom_block1_content"><?php _e('Bottom block 1 content', ET_DOMAIN) ?></label></th>
            <td><textarea name="cat_bottom_block1_content" rows="5"><?php echo $arr['cat_bottom_block1_content']; ?> </textarea></td>
        </tr>
        <tr class="form-field term-slug-wrap">
            <th scope="row"><label for="cat_bottom_block2_title"><?php _e('Bottom block 2 title', ET_DOMAIN) ?></label></th>
            <td><input type="text" name="cat_bottom_block2_title" value="<?php echo $arr['cat_bottom_block2_title']; ?>" /></td>
        </tr>
        <tr class="form-field term-slug-wrap">
            <th scope="row"><label for="cat_bottom_block2_content"><?php _e('Bottom block 2 content', ET_DOMAIN) ?></label></th>
            <td><textarea name="cat_bottom_block2_content" rows="5"> <?php echo $arr['cat_bottom_block2_content']; ?></textarea></td>
        </tr>
        <tr class="form-field term-slug-wrap">
            <th scope="row"><label for="cat_bottom_block3_title"><?php _e('Bottom block 3 title', ET_DOMAIN) ?></label></th>
            <td><input type="text" name="cat_bottom_block3_title" value="<?php echo $arr['cat_bottom_block3_title']; ?>" /></td>
        </tr>
        <tr class="form-field term-slug-wrap">
            <th scope="row"><label for="cat_bottom_block3_content"><?php _e('Bottom block  content', ET_DOMAIN) ?></label></th>
            <td><textarea name="cat_bottom_block3_content" rows="5"> <?php echo $arr['cat_bottom_block3_content']; ?></textarea></td>
        </tr>
        <?php
    }
    /**
     * save edit
     *
     * @param integer $term_id
     * @param integer $tt_id
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function jb_update_tax_meta( $term_id, $tt_id ){
//        if( isset( $_POST['featured-tax'] ) && '' !== $_POST['featured-tax'] ){
//            $group = sanitize_title( $_POST['featured-tax'] );
//            update_term_meta( $term_id, 'featured-tax', $group );
//        }
//        else{
//            update_term_meta($term_id, 'featured-tax', false);
//        }
//        if( isset( $_POST['mjob_category_image'] ) && '' !== $_POST['mjob_category_image'] ){
//            $group = sanitize_title( $_POST['mjob_category_image'] );
//            update_term_meta( $term_id, 'mjob_category_image', $group );
//        }
//        else{
//            update_term_meta($term_id, 'mjob_category_image', false);
//        }
        $request = $_REQUEST;
        foreach( $this->meta as $key=> $value){
            if( isset($request[$value]) && !empty($request[$value])  ){
                $group =  $request[$value] ;
                update_term_meta($term_id, $value, $group);
            }
            else{
                update_term_meta($term_id, $value, false);
            }
        }
    }
    /**
     * Displaying The Term Meta Data In The Term List
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function jb_add_tax_column( $columns ){
        $columns['featured_tax'] = __( 'Featured tax', ET_DOMAIN );
        return $columns;
    }
    /**
     * update
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function jb_add_tax_column_content( $content, $column_name, $term_id ){
        global $featured_tax, $mjob_category_image;
        if( $column_name !== 'featured_tax' || $column_name !== 'mjob_category_image' ){
            return $content;
        }
        $term_id = absint( $term_id );
        $featured_tax = get_term_meta( $term_id, 'featured-tax', true );
        if( !empty( $featured_tax ) ){
            $content .= esc_attr( $featured_tax );
        }
        $mjob_category_image = get_term_meta( $term_id, 'mjob_category_image', true );
        $content.='<img id="jb-tax-images-photo" src="'.esc_url( wp_get_attachment_image_url( $mjob_category_image, 'full' ) ).'"<?php echo $hidden; ?> />';
        return $content;
    }
    /**
     * enqueue script
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function jb_tax_enqueue_scripts(){
        wp_enqueue_media();
        wp_enqueue_style( 'jb-tax-images-css',  get_template_directory_uri() . '/includes/modules/jb_Taxonomy_Meta/assets/jb-tax.css', array(), ET_VERSION);
        wp_enqueue_script( 'jb-tax-images', get_template_directory_uri() . '/includes/modules/jb_Taxonomy_Meta/assets/jb-tax.js',   array(
            'jquery',
            'underscore',
            'backbone',
            'appengine'
        ), 1.0, true );
        $term_id = ! empty( $_GET['tag_ID'] )
            ? (int) $_GET['tag_ID']
            : 0;
        // Localize
        wp_localize_script( 'jb-tax-images', 'i10n_WPTermImages', array(
            'insertMediaTitle' => esc_html__( 'Choose an Image', 'wp-user-avatars' ),
            'insertIntoPost'   => esc_html__( 'Set as image',    'wp-user-avatars' ),
            'deleteNonce'      => wp_create_nonce( 'remove_jb_tax_images_nonce' ),
            'mediaNonce'       => wp_create_nonce( 'assign_jb_tax_images_nonce' ),
            'term_id'          => $term_id,
        ) );
    }
    /**
      * convert
      *
      * @param object $term
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function convert($term){
        foreach( $this->meta as $key=>$value ){
            $val = get_term_meta($term->term_id, $value, true);
            $term->$value = $val;
        }
        return $term;
    }

}
/**
 * class jb_PostFact
 * factory class to generate jb post object
 */
class JB_TaxFact
{

    static $objects;

    /**
     * contruct init post type
     */
    function __construct() {
        self::$objects = array(
            'tax' => JB_Taxonomy_Meta::getInstance()
        );
    }

    /**
     * set a post type object to machine
     * @param String $post_type
     * @param JB_Post object $object
     */
    public function set($tax, $object) {
        self::$objects[$tax] = $object;
    }

    /**
     * get post type object in class object instance
     * @param String $post_type The post type want to use
     * @return Object
     */
    public function get($tax) {
        if (isset(self::$objects[$tax])) return self::$objects[$tax];
        return null;
    }
    /**
     * Description
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function get_all(){
        if ( isset( self::$objects ) ) {
            return self::$objects;
        }
        return NULL;
    }
}

/**
 * set a global object factory
 */
global $JB_tax_factory;
$jb_tax_factory = new JB_TaxFact();
$jb_tax_factory->set('category', new JB_Posts('category'));