<?php
/**
 * Use this file for all your template filters and actions.
 * Requires WooCommerce PDF Invoices & Packing Slips 1.4.13 or higher
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function clean_lbl_fmt($label_string)
{
    $cln_label_brackety = str_replace("[", "", $label_string);
    return $cln_label_brackety;
}

function replace_currency($replace)
{ 
    $cln_label_brackety = str_replace("&#8369;", get_woocommerce_currency_symbol() , $replace);
	 return $cln_label_brackety;
}

add_action( 'wpo_wcpdf_before_item_meta', 'wpo_wcpdf_show_product_description', 10, 3 );
function wpo_wcpdf_show_product_description ( $template_type, $item, $order ) {

	if (!empty($item['product'])) {
        if ( method_exists( $item['product'], 'get_image_id' ) ) {
            $_product = $item['product']->is_type( 'variation' ) ? wc_get_product( $item['product']->get_parent_id() ) : $item['product'];
            $image = $_product->get_image( array( 100, 100 ) ); 
        } 
        $product_id =  $item['product'];
        $custom_items = get_post_meta( $product_id->id, 'mb_product_content', true );
    }
    
    $array2 = [
        'image' => "<p>".$item['name']."</p><br>"."<p style='padding-bottom:10px;'>".$image."</p>",
        'custom_field' => $custom_items,
        'quantity' => $item['quantity'],
        'price' => $item['price']
    ];

    echo "<tr>";
   foreach($array2 as $var)
   {
       echo "<td>".$var."</td>";
   }
   echo "</tr>";
    
}

add_action( 'wpo_wcpdf_after_item_meta', 'wpo_wcpdf_price_breakdown', 10, 3 );
function wpo_wcpdf_price_breakdown ( $template_type, $item, $order ) {
       
    $order = wc_get_order($order->id);
	foreach( $order->get_items() as $item_id => $items ){
        $product = $items->get_product();
        $item_meta_data = $items->get_meta_data();
    }
    
  //   $html = "";
  //   $html .="<tr>";
  //   $html .="<td>".$item['name']."</td>";
  //   $html .="<td>".$item['quantity']."</td>";
  //   $html .="<td>".get_woocommerce_currency_symbol().number_format($product->price, 2, '.', ',')."</td>";
 	// $html .="</tr>";
    
    $order = wc_get_order( $order->id );

    // The loop to get the order items which are WC_Order_Item_Product objects since WC 3+
 
    foreach( $order->get_items() as $item_id => $item ){
        
        if($item->get_meta('add_a_teddy_bear') ){
        	$add_a_teddy_bear = $item->get_meta('add_a_teddy_bear');
        }
        
        if($item->get_meta('add_a_chocolates') ){
        	$add_a_chocolates = $item->get_meta('add_a_chocolates');
        }
              
        if($item->get_meta('add_a_bottle_of_wine') ){
        	$add_a_bottle_of_wine = $item->get_meta('add_a_bottle_of_wine');
        }
        
        if($item->get_meta('add_a_balloon') ){
        	$add_a_balloon = $item->get_meta('add_a_balloon');
        }
        
        $item_product_data_array = $item->get_data();
        $item_product_meta_data_array = $item->get_meta_data();
       	$product = $item->get_product();    
        $product_name = $item->get_name(); // … OR: $product->get_name();
        $item_product_data_array = $item->get_data();
        
        $ppom_fields = $item->get_meta('_ppom_fields');
               
        if($ppom_fields){
        	
            $add_a_teddy_bea = array();
            foreach($ppom_fields as $value)
            {                                                                  
                $decode = json_decode($value); 
                                         
                foreach($decode as $d => $val){                                            
					if($d == 0){                                       
                       $teddy_price = $val->price;
                	}   
                    if($d == 1){                                       
                       $choco_price = $val->price;
                	}
                    if($d == 2){                                       
                       $wine_price = $val->price;
                	}
                    if($d == 3){                                       
                       $balloon_price = $val->price;
                	}
 				}
                                                      
            } // $ppom_fields     
        } // $ppom_fields      
    }
    
    if($add_a_teddy_bear){
        $new_title = replace_currency($add_a_teddy_bear);
        if($new_title=="select"){
            $new_title = "Teddy Bear";
        } else{
            $new_title;
        }
        $html.="<tr>";
        	$html .= "<td>".$new_title."</td>";
       		$html .= "<td>".''."</td>";
            $html .= "<td>".''."</td>";
        	$html .= "<td>".get_woocommerce_currency_symbol().number_format($teddy_price, 2, '.', ',')."</td>";
        $html.="</tr>"; 
    }    
    
     if($add_a_chocolates){
        $new_title = replace_currency($add_a_chocolates);
        if($new_title=="select"){
            $new_title = "Chocolates";
        } else{
            $new_title;
        }     
        $html.="<tr>";
        	$html .= "<td>".$new_title."</td>";
       		$html .= "<td>".''."</td>";
            $html .= "<td>".''."</td>";
        	$html .= "<td>".get_woocommerce_currency_symbol().number_format($choco_price, 2, '.', ',')."</td>";
        $html .="</tr>"; 
    }  
    
    if($add_a_bottle_of_wine){
        $new_title = replace_currency($add_a_bottle_of_wine); 
        if($new_title=="select"){
            $new_title = "Wine";
        } else{
            $new_title;
        } 
        $html.="<tr>";
        	$html .= "<td>".$new_title."</td>";
       		$html .= "<td>".''."</td>";
            $html .= "<td>".''."</td>";
        	$html .= "<td>".get_woocommerce_currency_symbol().number_format($wine_price, 2, '.', ',')."</td>";
        $html .="</tr>"; 
    } 
    
    if($add_a_balloon){
        $new_title = replace_currency($add_a_balloon);  
        if($new_title=="select"){
            $new_title = "Balloon";
        } else{
            $new_title;
        }   
        $html.="<tr>";
        	$html .= "<td>".$new_title."</td>";
       		$html .= "<td>".''."</td>";
            $html .= "<td>".''."</td>";
        	$html .= "<td>".get_woocommerce_currency_symbol().number_format($balloon_price, 2, '.', ',')."</td>";
        $html .="</tr>"; 
    } 
    
    echo $html; 
}

