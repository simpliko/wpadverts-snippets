<?php
/**
Plugin Name: WPAdverts Snippets - Custom Search
Version: 1.0
Author: Greg Winiarski, Tim Sombroek
Description: Allows searching [adverts_list] by passing a 'custom' param with a JSON string for example [adverts_list custom='"cat": "Motorcycles", "type": "Sport", "adverts_location": "The Netherlands", "adverts_price": LE::NUMERIC::10000}'].
*/

// The code below you can paste in your theme functions.php or create
// new plugin and paste the code there.

add_filter( "shortcode_atts_adverts_list", function(  $out, $pairs, $atts  ) {
    if( isset( $atts["custom"] ) ) {
        $out["custom"] = $atts["custom"];
    } else {
        $out["custom"] = null;
    }
    return $out;
}, 10, 3 );

add_filter( "adverts_list_query", function( $args, $params ) {

    if( ! isset( $params["custom"] ) || empty( $params["custom"] ) ) {
        return $args;
    }

     /* Shortcode attribute 'custom' makes sure that WPAdverts can load adverts which's properties apply to specific conditions that can be set by the developer.
     *  Those property conditions are set in JSON-format. Here is an example:
     *  {"cat": "Motorcycles", "type": "Sport", "adverts_price": LE::NUMERIC::10000}
     *
     *  In the example above the adverts must be sport motorcycles with a price of € 10,000 or below.
     *  The way that the condition of the price range is written, causes the JSON to be invalid.
     *  Why this is important and how this can be used will be explained in a minute.
     *
     *  Such conditions are made up out of 3 parameters:
     *  - The operator, for instance: 'less than' (LT), 'less than or equal to' (LE) or 'greater than' (GT);
     *  - The type of the value (what the value needs to be treated as), for instance: CHAR (as text) or NUMERIC (as number / money value);
     *  - The value that needs to be compared to the value of the advert's property.
     *
     *  Parameters of such a condition are seperated by two double colons, for instance: LE:NUMERIC:10000.
     *  The reason that such a condition results in invalid JSON, is because such a condition is not embedded and should not be embedded by quotes ("...").
     *  It must be written as: LE::NUMERIC::10000, so not as this: "LE:NUMERIC:10000".
     *  I decided it should work this way, because this PHP-code should be able to spot the difference between strings and such conditions.
    */
    $custom = preg_replace(
        "/(LT|LE|EQ|GE|GT|NOT LIKE|LIKE|NOT IN|IN|NOT BETWEEN|BETWEEN|NOT EXISTS|EXISTS|NOT REGEXP|REGEXP|RLIKE)::(NUMERIC|BINARY|CHAR|DATE|DATETIME|DECIMAL|SIGNED|TIME|UNSIGNED)::([^,}]+)/",
        '[{"$1": $3, "type": "$2"}]',
        $params["custom"]
    );

    $customFilters = json_decode($custom, true);

    foreach($customFilters as $customField => $condition) {
        $leftOperand        = $customField;

        $conditionOperator  = "=";
        $fieldType          = "CHAR";

        if(gettype($condition) == "array") {
            if(sizeof($condition) >= 1) {
                $condition = $condition[0];
                $rightOperand = reset($condition);

                $conditionOperator = str_replace(
                    ["LT", "LE", "EQ", "GE", "GT"],
                    ['<', '<=', '=', '=>', '>'],
                    key($condition)
                );

                if(isset($condition["type"])) {
                    $fieldType = $condition["type"];
                }
            }
        } else {
            $rightOperand = $condition;
        }

        $newFilter = array(
            'key' => $leftOperand,
            'value' => $rightOperand,
            'compare' => $conditionOperator,
            'type' => $fieldType
        );

        if( ! is_array( $args["meta_query"] ) ) {
            $args["meta_query"] = array();
        }            

        $args["meta_query"][] = $newFilter;
    }
    
    return $args;
}, 10, 2 );