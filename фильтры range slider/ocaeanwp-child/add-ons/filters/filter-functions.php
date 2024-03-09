<?php
//ФУНКЦИЯ ПОЛУЧЕНИЯ ССЫЛОК НА КАТЕГОРИИ
function get_filter_by_taxonomy_links($taxonomy = '', $title = '', $class = '')
{
    global $wp_query, $wpdb;

    $terms = get_terms([
        'taxonomy' => $taxonomy,
        'hide_empty' => false
    ]);

    

    $fn = '';
    if (strpos($taxonomy, 'events') !== false) {
        $fn = "get_events_string_url";
    }
    // var_dump($fn());

?>
    <div class="event_filter_block">
        <h3><?php _e($title, 'oceanwp'); ?></h3>
        <ul class="<?php echo $class; ?>">
            <?php foreach ($terms as $term) : ?>
                <?php
                    $option_is_set = false;
                    $link = remove_query_arg($taxonomy, $fn());
                    $link_terms = isset($_GET[$taxonomy]) ? explode(',', $_GET[$taxonomy]) : [];
                    // var_dump($link_terms);
                    if (in_array($term->slug, $link_terms)) {
                        $option_is_set = true;
                        $key = array_search($term->slug, $link_terms);
                        unset($link_terms[$key]);
                        // var_dump($key);
                    } else {
                        $link_terms[] = $term->slug;
                    }
                    if (!empty($link_terms)) {
                        $link = add_query_arg($taxonomy, implode(',', $link_terms), $link);
                    }
                 
                ?>
                <li><a href="<?php echo $link; ?>" class="<?php echo $option_is_set ? 'active' : ''; ?>"><?php echo $term->name; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php
}




//ФУНКЦИЯ ПОЛУЧЕНИЯ URL КАТЕГОРИЙ И ТЕГОВ
function get_events_string_url()
{
    //Возвращает URL (постоянную ссылку) на страницу архива произвольного типа записи.
    $link = get_post_type_archive_link('events');

    //add_query_arg Позволяет добавить один или несколько параметров в URL-строку # Первый аргумент - название параметра, второй - его значение , третий - Старый запрос или URL. То есть мы вставляем в адресную строку $link -это url кастомного типа записей events, потом events_category , а потом $_GET['events_category']- ТО ЕСТЬ САМО НАЗВАНИЕ КАТЕГОРИИ 
    if (isset($_GET['events_category'])) {
        $link = add_query_arg('events_category',wp_unslash($_GET['events_category']), $link);
    }
      //wp_unslash - Удаляет слэши из переданной строки, или из строковых элементов переданного массива или свойств объекта. Массив может быть любой вложенности.

    //дЛЯ ТЕГОВ
    if (isset($_GET['events_tags'])) {
        $link = add_query_arg('events_tags', wp_unslash($_GET['events_tags']), $link);
    }

    //ДЛЯ ACF
    if (isset($_GET['location_event'])) {
        $link = add_query_arg('location_event', wp_unslash($_GET['location_event']), $link);
    }
    return $link;
}
// ФУНКЦИЯ ПОЛУЧЕНИЯ URL КАТЕГОРИЙ И ТЕГОВ 


//ОЩИЩАЕМ GET ПАРАМЕТР
if (!function_exists('wc_clean')) {
    function wc_clean($var)
    {
        if (is_array($var)) {
            return array_map('wc_clean', $var);
        } else {
            return is_scalar($var) ? sanitize_text_field($var) : $var;
        }
    }
}

?>





<!-- ПОЛЯ ACF переносим в самый низ по этой причине не загружались картинки в админке -->
<?php function get_filter_by_meta_field_links($field = '', $title = '', $class = '', $query_type = 'AND', $page = '')
{

    global  $wpdb;


    //ПИШЕМ ЗАПРОС К БАЗЕ ДАННЫХ
    $fields = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key = %s", $field), ARRAY_A);

  
    // var_dump($fields);
    if (!$page) {
        return new WP_Error('empty_arg', 'Не указан важный аргумент');
    }
    $fn = "get_{$page}_string_url";


?>
   <div class="event_filter_block">
        <h3><?php _e($title, 'oceanwp'); ?></h3>
        <ul class="<?php echo $class; ?>">
            <?php foreach($fields as $field_key => $field_name) :?>
                <?php if(!$field_name['meta_value']){
                    continue;
                }
                $option_is_set = false;
                $link = remove_query_arg($field, $fn());
                $link_terms = isset($_GET[$field]) ? explode(',', $_GET[$field]) : [];
                // var_dump($link_terms);
                if (in_array($field_name['meta_value'], $link_terms)) {
                    $option_is_set = true;
                    $key = array_search($field_name['meta_value'], $link_terms);
                    unset($link_terms[$key]);
                    // var_dump($key);
                } else {
                    $link_terms[] = $field_name['meta_value'];
                }
                if (!empty($link_terms)) {
                    $link = add_query_arg($field, implode(',', $link_terms), $link);
                }
                ?>
                 <li><a href="<?php echo $link; ?>" class="<?php echo $option_is_set ? 'active' : ''; ?>"><?php echo $field_name['meta_value']; ?></a></li>
            <?php endforeach;?>
        </ul>
    </div>
<?php
}
// ПОЛЯ ACF



//RANGE SLIDER
function get_events_range_slider($field = '', $class = '', $title = '', $desc = '')
{
    global $wp, $wpdb;
    $form_action = home_url($wp->request);
    $current_filters = isset($_GET[$field]) ? explode('-', wc_clean(wp_unslash($_GET[$field]))) : [];
    $fields = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key = %s ", $field), ARRAY_A);

    $fields_for_slider = wp_list_pluck($fields, 'meta_value');

?>
    <div class="event_filter_block event_filter_<?php echo $field; ?>">
        <h3><?php _e($title, 'oceanwp'); ?></h3>
        <form action="<?php echo $form_action; ?>" method="GET" id="form-<?php echo $field; ?>">


            <?php
            $option_is_set = in_array(0, $current_filters);
            ?>
            <div class="form-group form-group-<?php echo $field; ?>">

            </div>
            <div class="desc-group desc-group-<?php echo $field; ?>">
                <input type="hidden" id="min_madness">
                <input type="hidden" id="max_madness">
            </div>
            <input type="hidden" name="<?php echo esc_attr($field); ?>" id="hidden-<?php echo esc_attr($field); ?>" value="<?php echo esc_attr(implode('-',  $current_filters)); ?>">

            <?php echo wc_query_string_form_fields(null, [$field], '', true);
            ?>

        </form>
    </div>
    <script>
        function initMadnessSlider() {
            let sliderMadness = document.querySelector('.form-group-<?php echo $field; ?>');
            let inputValues = [
                document.getElementById('min_madness'),
                document.getElementById('max_madness')
            ];
            noUiSlider.create(sliderMadness, {
                tooltips: true,
                step: 1,
                format: {
                    to: (v) => parseFloat(v).toFixed(0),
                    from: (v) => parseFloat(v).toFixed(0)
                },
                start: [<?php echo count($current_filters) > 1 ? $current_filters[0] : min($fields_for_slider); ?>, <?php echo count($current_filters) > 1 ? $current_filters[1] : max($fields_for_slider); ?>],

                connect: true,
                range: {
                    'min': 0,
                    'max': 100
                }
            });

            sliderMadness.noUiSlider.on('update', function(values, handle) {
                inputValues[handle].value = values[handle];
                let combinedValues = jQuery('#min_madness').val() + '-' + jQuery('#max_madness').val()
                jQuery('#hidden-<?php echo esc_attr($field); ?>').val(combinedValues);

            });
            sliderMadness.noUiSlider.on('end', function() {
                // jQuery(document.body).trigger('madness_ajax_request');
                jQuery('#form-<?php echo $field; ?>').submit();

            });
        }
        initMadnessSlider()
    </script>
    <?php
}
//RANGE SLIDER




//wc_query_string_form_fields ЭТА ФУНКЦИЯ НУЖНА ДЛЯ КОРРЕКТНОЙ РАБОТЫ RANGE SLIDER  ПОЗВОЛЯЕТ СОХРАНЯТЬ ВСЕ ПРЕДЫДУЩИЕ НАСТРОЙКИ ФИЛЬТРА
if (!function_exists('wc_query_string_form_fields')){
    function wc_query_string_form_fields( $values = null, $exclude = array(), $current_key = '', $return = false ) {
        if ( is_null( $values ) ) {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $values = $_GET;
        } elseif ( is_string( $values ) ) {
            $url_parts = wp_parse_url( $values );
            $values    = array();
    
            if ( ! empty( $url_parts['query'] ) ) {
                // This is to preserve full-stops, pluses and spaces in the query string when ran through parse_str.
                $replace_chars = array(
                    '.' => '{dot}',
                    '+' => '{plus}',
                );
    
                $query_string = str_replace( array_keys( $replace_chars ), array_values( $replace_chars ), $url_parts['query'] );
    
                // Parse the string.
                parse_str( $query_string, $parsed_query_string );
    
                // Convert the full-stops, pluses and spaces back and add to values array.
                foreach ( $parsed_query_string as $key => $value ) {
                    $new_key            = str_replace( array_values( $replace_chars ), array_keys( $replace_chars ), $key );
                    $new_value          = str_replace( array_values( $replace_chars ), array_keys( $replace_chars ), $value );
                    $values[ $new_key ] = $new_value;
                }
            }
        }
        $html = '';
    
        foreach ( $values as $key => $value ) {
            if ( in_array( $key, $exclude, true ) ) {
                continue;
            }
            if ( $current_key ) {
                $key = $current_key . '[' . $key . ']';
            }
            if ( is_array( $value ) ) {
                $html .= wc_query_string_form_fields( $value, $exclude, $key, true );
            } else {
                $html .= '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( wp_unslash( $value ) ) . '" />';
            }
        }
    
        if ( $return ) {
            return $html;
        }
    
        echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    
}
