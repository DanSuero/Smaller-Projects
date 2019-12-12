<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Featured {
    
    private $screens = array(
		'post',
	);
	private $fields = array(
		array(
			'id' => 'featured',
			'label' => 'Featured',
			'type' => 'checkbox',
		),
	);
    
    private $query;
    
    
    public function __construct(){
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
        add_shortcode('Featured', array($this, 'output'));
    }
    
    public function add_meta_boxes() {
		foreach ( $this->screens as $screen ) {
			add_meta_box(
				'featured',
				'Featured?',
				array( $this, 'add_meta_box_callback' ),
				$screen,
				'advanced',
				'default'
			);
		}
	}
    
    public function add_meta_box_callback( $post ) {
		wp_nonce_field( 'featured_data', 'featured_nonce' );
		$this->generate_fields( $post );
	}
    
    public function generate_fields( $post ) {
		$output = '';
		foreach ( $this->fields as $field ) {
			$label = '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
			$db_value = get_post_meta( $post->ID, 'featured_' . $field['id'], true );
			switch ( $field['type'] ) {
				case 'checkbox':
					$input = sprintf(
						'<input %s id="%s" name="%s" type="checkbox" value="1">',
						$db_value === '1' ? 'checked' : '',
						$field['id'],
						$field['id']
					);
					break;
				default:
					$input = sprintf(
						'<input %s id="%s" name="%s" type="%s" value="%s">',
						$field['type'] !== 'color' ? 'class="regular-text"' : '',
						$field['id'],
						$field['id'],
						$field['type'],
						$db_value
					);
			}
			$output .= $this->row_format( $label, $input );
		}
		echo '<table class="form-table"><tbody>' . $output . '</tbody></table>';
	}
    
    public function row_format( $label, $input ) {
		return sprintf(
			'<tr><th scope="row">%s</th><td>%s</td></tr>',
			$label,
			$input
		);
	}
   
    public function save_post( $post_id ) {
		if ( ! isset( $_POST['featured_nonce'] ) )
			return $post_id;

		$nonce = $_POST['featured_nonce'];
		if ( !wp_verify_nonce( $nonce, 'featured_data' ) )
			return $post_id;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		foreach ( $this->fields as $field ) {
			if ( isset( $_POST[ $field['id'] ] ) ) {
				switch ( $field['type'] ) {
					case 'email':
						$_POST[ $field['id'] ] = sanitize_email( $_POST[ $field['id'] ] );
						break;
					case 'text':
						$_POST[ $field['id'] ] = sanitize_text_field( $_POST[ $field['id'] ] );
						break;
				}
				update_post_meta( $post_id, 'featured_' . $field['id'], $_POST[ $field['id'] ] );
			} else if ( $field['type'] === 'checkbox' ) {
				update_post_meta( $post_id, 'featured_' . $field['id'], '0' );
			}
		}
	}
    
    public function get_featured(){
        $this->query = new WP_Query(array(
            'post_type' => 'post',
            'posts_per_page' => 4,
            'meta_query' => array(array(
                'key' => 'featured_featured',
                'value' => 1,
                'compare' => 'LIKE',
            )),
        ));
        
        return $this->query->posts;
    }
    
    
    public function featured_rows(){
        $featured_post = $this->get_featured();
        
        foreach($featured_post as $data){
            $featured_cat = get_the_category($data->ID);
            $last_cat = end($featured_cat);
            ?>
                <div class="featured col">
                    <h4><?php foreach($featured_cat as $cat){ echo $cat->cat_name; echo (count($featured_cat) > 1 && $last_cat->cat_name != $cat->cat_name )? ", " : ""; } ?></h4>
                    <h3><a href="<?php echo get_permalink($data->ID); ?>"><?php echo ucfirst($data->post_title); ?></a></h3>
                    <?php echo substr($data->post_content, 0 , 150);  ?>
                </div>
            <?php
        }
    }
    
    public function output(){
?>
        <div class="featured-row">
            <?php
                $this->featured_rows();
            ?>
        </div>
<?php
        
    }
}

new Featured;