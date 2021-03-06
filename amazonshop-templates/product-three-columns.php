<?php
/**
 * This template display three columns view of products
 * This template can be overridden by copying it to your active theme
 * If this template isn't yet under your active theme then
 * Copy wp-content/plugins/amazon-product-shop/amazonshop-templates whole folder to your active theme folder
 * which path should be as wp-content/themes/{your-active-theme}/amazonshop-templates/product-three-columns.php
 * You may now edit this template file as you want to display products
 * REMEMBER You need to copy 'amazonshop-templates' whole folder into your active theme to work properly
 * NEVER just only copy this template file into your active theme folder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $amazon_products;
$total_products = ( isset( $amazon_products ) && count( $amazon_products ) > 0 )? count( $amazon_products ) : 0;
$i = 0;
if ( $total_products > 0 ) { ?>
<?php    
	while ( $i < $total_products ) {
		?>
        <div class="caaps-amazon-products-row"> 
            <ul class="caaps-amazon-products">
                <?php 
                for ( $p = 0; $p < 3; $p++ ) {
                    if ( isset( $amazon_products[$i]['ASIN'] ) && ! empty( $amazon_products[$i]['ASIN'] )  ) {?>
                    <li>
                        <a href="<?php echo $amazon_products[$i]['DetailPageURL'];?>" target="_new">
                        <?php
                            echo ( isset( $amazon_products[$i]['MediumImage'] ) && ( ! empty( $amazon_products[$i]['MediumImage'] )) )? '<img class="medium-image" src="'.$amazon_products[$i]['MediumImage'].'" alt="'.$amazon_products[$i]['Title'].'" />' : '';
							?>
                            <p>
                            	<button class="caaps-amazonbuy-btn">Amazon Buy</button>
                            </p>
                            
                            <h4>
                            <?php
							echo ( isset( $amazon_products[$i]['Title'] ) && ! empty( $amazon_products[$i]['Title'] ) )?$amazon_products[$i]['Title'] : '';
							?>
                            </h4>
                            <p>
                            <?php
							echo ( isset( $amazon_products[$i]['PriceFormattedPrice'] ) && ! empty( $amazon_products[$i]['PriceFormattedPrice'] ) )? $amazon_products[$i]['PriceFormattedPrice'] : '';																				
							?>
                            </p>
                        </a>
                    </li>
                    <?php 
                    }	
                // Next product	  	  
                $i++; 	  
                } // End for
                ?>
            </ul>	  
        </div> <!-- /.caaps-amazon-products-row --> 		    	 
<?php	  
	} // End while
	?>
    
    <div class="caaps-pgination">
    <?php
		// Display pagination if available
		echo Caaps_Template_Helper::get_amazonshop_pagination();
	?>
    </div>
    
<?php	
}
else {
	_e( 'No Products Found', 'codeshop-amazon-affiliate' );
}
?>         