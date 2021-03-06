<div id="caaps-addproducts-withpost-container" style="display:none">    	
    <?php 
	global $post;
	$countries = Caaps_Amazon_Shop::supported_countries();
	?>
	<input type="hidden" class="caaps-products-toadd-postid" value="<?php echo $post->ID;?>" />    	    
	<table class="widefat" style="border:none;">
    	<thead>
        </thead>        
        <tbody>
        	
            <tr>
            	<td style="width:30%;">
                	<label for="caaps_country_field"><?php esc_html_e('Selected Country', 'codeshop-amazon-affiliate');?></label>
                </td>
                <td>
                <?php $options = get_option('caaps_amazon-product-shop-settings'); ?>
                   <select name="caaps_country_field" id="caaps_country_field" style="width:80%;" disabled="disabled">
					   <?php 
                       foreach ( $countries as $cc => $country) {
						    $selected = isset( $options['caaps_settings_field_country'] ) ? selected( $options['caaps_settings_field_country'], $cc, false) : '';
                           echo '<option value="'.$cc.'" ' .$selected.'>'.$country.'</option>';
                       }
                       ?>
                   </select>                        
                </td>
            </tr>                       	
            
            <tr>
            	<td style="width:30%;">
                	<label for="caaps_shortcode_template"><?php esc_html_e('Select Template', 'codeshop-amazon-affiliate');?></label>
                </td>
                <td>
                   <select name="caaps_shortcode_template" id="caaps_shortcode_template" style="width:80%;">
                         <option value="product-one-column">One Column Template</option>
                         <option value="product-two-columns" selected="selected">Two Columns Template</option>
                         <option value="product-three-columns">Three Columns Template</option>
                         <option value="product-four-columns">Four Columns Template</option>
                   </select>                        
                </td>
            </tr>                                                   
                        
        	<tr>
            	<td style="width:30%;">
                	<label for="caaps_thickbox_search_kword"><?php esc_html_e('Search Keyword', 'codeshop-amazon-affiliate');?></label>
                </td>
                <td>
                	<input type="text" id="caaps_thickbox_search_kword" name="caaps_thickbox_search_kword" class="caaps_thickbox_search_kword" size="100" style="width:80%;" placeholder = "Search Keyword" />
                </td>
                <td style="text-align:right;">
                    <?php submit_button( __( 'Search', 'codeshop-amazon-affiliate' ), 'primary caaps_thickbox_searchby_kword_btn', 'caaps_thickbox_searchby_kword_btn', false, $other_attributes = array( 'id' => 'caaps_thickbox_searchby_kword_btn' ) );?>
                </td>
            </tr>   
                        
        </tbody>        
    </table>    
    
    <div class="caaps-display-thickbox-results"></div>
</div><!-- #caaps-addproducts-withpost-container -->