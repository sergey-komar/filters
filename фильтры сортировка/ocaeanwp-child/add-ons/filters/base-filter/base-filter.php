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

    //ДЛЯ КАТЕГОРИЙ
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

    //ДЛЯ ТЕГОВ
    if (isset($_GET['events_tags'])) {
        $tax_query = $query->get('tax_query');
        $tax_query = is_array($tax_query) ? $tax_query : [];
        $tax_query[] = [
            'taxonomy' => 'events_tags',
            'field' => 'slug',
            'terms' => explode(',', $_GET['events_tags'])
        ];

        $query->set('tax_query', $tax_query);

    }


    //ДЛЯ ACF
    if (isset($_GET['location_event'])) {
        $meta_query = $query->get('meta_query');
        $meta_query = is_array($meta_query) ? $meta_query : [];
        $multiple_metas = count(explode(',', $_GET['location_event'])) > 1;
        if ($multiple_metas) {
            $meta_query = array_merge($meta_query, ['relation' => 'OR']);
            foreach (explode(',', $_GET['location_event']) as $location) {
                $meta_query[] = [

                    'key' => 'location_event',
                    'value' => $location,
                    'compare' => '='

                ];
            }
        } else {
            $meta_query[] = [

                'key' => 'location_event',
                'value' => wp_unslash(wc_clean($_GET['location_event'])),
                'compare' => '='

            ];
        }

        $query->set('meta_query', $meta_query);
    }


 // Безумие
 if (isset($_GET['madres__events'])) {

    $meta_query = $query->get('meta_query');
    $meta_query = is_array($meta_query) ? $meta_query : [];
    $meta_query[] = [

        'key' => 'madres__events',
        'value' => explode('-', $_GET['madres__events']),
        'compare' => 'BETWEEN',
        'type' => 'numeric'

    ];
    $meta_query = $query->set('meta_query', $meta_query);
}


//СОРТИРОВКА
if (isset($_GET['orderby'])) {
    $orderby = $_GET['orderby'];
    switch ($orderby) {
        case 'date-asc':
            $query->set('orderby', ['date' => 'ASC']);
            break;

        case 'date-desc':
            $query->set('orderby', ['date' => 'DESC']);
            break;
            
        case 'madness':
            $meta_query = $query->get('meta_query');
            $meta_query = is_array($meta_query) ? $meta_query : [];
            $meta_query['madness'] = [

                [
                    'key' => 'madres__events',
                    'type' => 'numeric'
                ]
            ];
            $query->set('meta_query', $meta_query);
            $query->set('orderby', ['meta_value_num' => 'ASC']);
            break;
        case 'last_30':
            $date_query = $query->get('date_query');
            $date_query = is_array($date_query) ? $date_query : [];

            $date_query = [
                [
                    'column' => 'post_date_gmt',
                    'after' => '30 days ago',
                ]

            ];
            $query->set('date_query', $date_query);
            break;

        case 'title':
            $query->set('orderby', ['title' => 'ASC']);
            break;
    }
}



}





//Позволяет изменить запрос WP_Query. Срабатывает перед запросом.
add_action('pre_get_posts', 'events_base_filter');
?>









