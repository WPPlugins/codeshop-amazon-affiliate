<div class="wrap">    		
	<table class="widefat" style="border:none;">
    	<thead>
        </thead>        
        <tbody>
        	<tr>
            	<td style="width:30%;">
                	<label for="caaps_search_kword"><?php esc_html_e('Search Keyword', 'codeshop-amazon-affiliate');?></label>
                </td>
                <td>
                	<input type="text" id="caaps_search_kword" name="caaps_search_kword" class="caaps_search_kword" size="100" style="width:70%;" placeholder="Search Keyword" />
                </td>
                <td style="text-align:right;">
                    <?php submit_button( __( 'Search', 'codeshop-amazon-affiliate' ), 'primary caaps_searchby_kword_btn', 'caaps_searchby_kword_btn', false, $other_attributes = array( 'id' => 'caaps_searchby_kword_btn' ) );?>
                </td>
            </tr>               
        </tbody>        
    </table>    
</div><!-- /.wrap -->