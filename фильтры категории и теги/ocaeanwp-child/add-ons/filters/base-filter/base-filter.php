<?php


function events_base_filter($query) {
    //чтобы срабатывал не в админке и не на основной запрос
    if(is_admin() || !$query->is_main_query()){
        return;
    }
    //проверяем архивная(кастомный тип записей events) эта страница events  или нет, если нет, то прекращаем работу функции
    if(!$query->is_post_type_archive('events')){
        return;
    }

    if (isset($_GET['events_category'])) {
        $tax_query = $query->get('tax_query');
        $tax_query = is_array($tax_query) ? $tax_query : [];
        $tax_query[] = [
            'taxonomy' => 'events_category',
            'field' => 'slug',
            'terms' => explode(',', $_GET['events_category'])
        ];

        $query->set('tax_query', $tax_query);

    }

   
}

//Позволяет изменить запрос WP_Query. Срабатывает перед запросом.
add_action('pre_get_posts', 'events_base_filter');
?>









