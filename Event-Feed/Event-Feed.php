<?php 
// Creating custom elementor friendly short code for Events.
function junction_center_event_shortcode( $attr = array() ){
    
    global $post;
    
    
    extract(shortcode_atts(array(
        'front_page' => false,
        'post_amount' => 4
    ),$attr));
    
    if($front_page){
        $page = $post_amount;
        $style = "front-page";
    }else{
        $page = -1;
        $style = "column-row";
    }

    //Retrieving all event posts.
    $event_args = array(
        'posts_per_page' => $page,
	    'post_type' => 'events',
	    'post_status' => 'publish',
        'meta_key' => 'date',
        'meta_query' => array(
            array(
                'key'       => 'date',
                'value'     => date("Y-m-d"),
                'compare'   => '>=',
                'type' => 'DATE'
            )
        ),
        'orderby' => 'meta_value',
        'order' => 'ASC',
    );
    
    $events = new WP_Query($event_args);
    
    $eventPosts = $events->posts;
    
    
    ?>
    <div id="events" class="row <?php echo $style; ?>">
    <?php
    foreach( $eventPosts as $data ){
        $currentMeta = get_post_meta($data->ID);
        //print_r($data);
        ?>
        <div class="col">
            <div class="card">
                <div class="img-section">
                    <?php 
                        
                        echo ($front_page)? "<a href=\"$data->guid\" class=\"img-link\">":"";
                        
                        echo (isset($currentMeta["photo_large"][0]))? wp_get_attachment_image( $currentMeta["photo_large"][0],'large') : "<img class='placeholder' src='//via.placeholder.com/800x533' />"; 
                        
                        echo ($front_page)? "</a>":"";
                    ?>
                </div>
                <div class="text-section">
					
                    <h2><?php echo date("m/d/Y",strtotime($currentMeta["date"][0])); ?></h2>
                    <h3>
                    <?php echo ($front_page)? "<a href=\"$data->guid\" class=\"title-link\">":""; ?>
                    <?php echo $data->post_title; ?>
                    <?php echo ($front_page)? "</a>":""; ?>
                    </h3>
                    <?php if($currentMeta["subtitle"][0]): ?>
                    
                    <span class="subtitle"><?php echo $currentMeta["subtitle"][0]; ?>, </span>
                        
                    <?php endif; ?>

                    <span class="location"><?php echo $currentMeta["location"][0]; ?></span>
                    
                    <?php if(false): ?>
                    <div class="txt-body-secion">
                        <p class="description">
                            <?php echo $currentMeta["description"][0]; ?>
                        </p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="event-buttons">
                        <div class="buy-wrapper">
                            <a class="btn buy-ticket" href="<?php echo $currentMeta['event-url'][0]; ?>" target="_blank">Tickets</a>
                        </div>
                        <div class="more-wrapper">
                            <a class="btn more-details" href="<?php echo $data->guid; ?>">
                                <?php echo ($front_page)? "Event Details »" : "Details"; ?> 
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
    </div>
    <?php
}
add_shortcode("JunctionEvents","junction_center_event_shortcode");
?>
