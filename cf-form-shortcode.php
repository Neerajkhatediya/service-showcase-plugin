<?php
add_shortcode( 'input', 'cf_input' );

function cf_input( $attr, $content = null )
{
   extract(shortcode_atts(array(
		'type' => '',
		'name' => '',
		'class' => ''
	), $attr));

   echo '<input type="'.$type.'" name="'.$name.'" class="'.$class.'">';
	 
} 

add_shortcode( 'textarea', 'cf_textarea' );

function cf_textarea( $attr, $content = null )
{
   extract(shortcode_atts(array(
		'name' => '',
		'class' => ''
	), $attr));

   echo '<textarea name="'.$name.'" class="'.$class.'"></textarea>';

	 
}

add_shortcode( 'submit', 'cf_submit' );

function cf_submit( $attr, $content = null )
{
   extract(shortcode_atts(array(
		'class' => '',
		'title' => ''
	), $attr));

   echo '<input type="submit" title="'.$title.'" class="'.$class.'">';

}

add_shortcode( 'cf_form', 'cf_form' );

function cf_form( $attr, $content = null )
{
   extract(shortcode_atts(array(
		'id' => ''
	), $attr)); ?>

   <form method="post" action="">
   <?php echo apply_filters('the_content', get_the_content($id)); ?>   
   </form>
<?php }?>