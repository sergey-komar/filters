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





<!-- ПОЛЯ ACF -->
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
