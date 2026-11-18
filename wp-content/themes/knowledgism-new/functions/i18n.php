<?php
/**
 * Knowledgism Theme Internationalization (i18n) & Spanish Translations
 *
 * Provides complete Spanish translations for theme strings, navigation helpers,
 * and gettext integration for the 'knowledgism' text domain.
 *
 * @package Knowledgism
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check if the current request is for the Spanish version
 *
 * @return bool
 */
function knowledgism_is_spanish() {
    if (defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE === 'es') {
        return true;
    }
    $lang = apply_filters('wpml_current_language', null);
    if ($lang === 'es') {
        return true;
    }
    $locale = get_locale();
    return (strpos($locale, 'es') === 0);
}

/**
 * Get context-aware navigation URLs based on current active language
 *
 * @param string $key
 * @return string
 */
function knowledgism_get_nav_url($key) {
    $is_es = knowledgism_is_spanish();

    switch ($key) {
        case 'home':
            return $is_es ? home_url('/es/') : home_url('/');
        case 'about':
            return $is_es ? home_url('/es/acerca-de/') : home_url('/about/');
        case 'articles':
            return $is_es ? home_url('/es/articulos/') : home_url('/articles/');
        case 'glossary':
            return $is_es ? home_url('/es/glosario/') : home_url('/glossary/');
        case 'bookstore':
            if (function_exists('wc_get_page_permalink')) {
                return wc_get_page_permalink('shop');
            }
            return $is_es ? home_url('/es/tienda/') : home_url('/bookstore/');
        case 'events':
            return $is_es ? home_url('/es/events/') : home_url('/events/');
        case 'contact':
            return $is_es ? home_url('/es/contacto/') : home_url('/#contact');
        case 'privacy':
            return $is_es ? home_url('/es/politica-de-privacidad/') : home_url('/#privacy');
        case 'era':
            return $is_es ? home_url('/es/la-era-del-conocimiento/') : home_url('/about/the-knowledge-era-is-upon-us/');
        case 'alan':
            return $is_es ? home_url('/es/conoce-a-alan-c-walter/') : home_url('/about/meet-alan-c-walter/');
        default:
            return home_url('/');
    }
}

/**
 * Comprehensive Spanish translation dictionary for the knowledgism text domain
 */
function knowledgism_get_spanish_dictionary() {
    static $dict = null;
    if ($dict !== null) {
        return $dict;
    }

    $dict = [
        // Navigation & Actions
        'What Is Knowledgism' => 'Qué es Knowledgism',
        'Articles' => 'Artículos',
        'Glossary' => 'Glosario',
        'Bookstore' => 'Librería',
        'Events' => 'Eventos',
        'Find your Zone' => 'Descubre tu Zona',
        'Find your Zone — free' => 'Descubre tu Zona — gratis',
        'Cart' => 'Carrito',
        'View shopping cart' => 'Ver carrito de compras',
        'Select Language' => 'Seleccionar idioma',

        // Hero Block
        'The Knowledge Era' => 'La Era del Conocimiento',
        'Knowledge without action does not produce results.' => 'El conocimiento sin acción no produce resultados.',
        'The written and recorded work of Alan C. Walter, organized so you can actually use it. Start by finding out which Zone you are operating from today.' => 'La obra escrita y grabada de Alan C. Walter, organizada para que realmente puedas usarla. Comienza descubriendo desde qué Zona estás operando hoy.',
        'Download the free chapter' => 'Descarga el capítulo gratis',
        'Upcoming events' => 'Próximos eventos',
        'See all events' => 'Ver todos los eventos',

        // Eras Block
        'The game is knowledge versus ignorance' => 'El juego es conocimiento contra ignorancia',
        'The game being played is knowledge versus ignorance' => 'El juego que se está jugando es conocimiento contra ignorancia',
        'Read the foundations' => 'Lee los fundamentos',
        'The Warrior Era' => 'La Era del Guerrero',
        'Nations and relationships were conquered, dominated and forced to comply.' => 'Las naciones y relaciones eran conquistadas, dominadas y forzadas a someterse.',
        'The Literacy Era' => 'La Era de la Alfabetización',
        'A near-worldwide push to read, write and reckon. Lifespan almost doubled.' => 'Un esfuerzo casi mundial por leer, escribir y calcular. La esperanza de vida casi se duplicó.',
        'Knowledge becomes the primary source of power — and the basis for survival.' => 'El conocimiento se convierte en la fuente principal de poder — y la base para la supervivencia.',

        // Articles Block & Page
        'Articles by Alan C. Walter' => 'Artículos de Alan C. Walter',
        'Bulletins by Alan' => 'Boletines de Alan',
        'Boletines de Alan' => 'Boletines de Alan',
        'All ten categories' => 'Las diez categorías',
        'Ten categories, one body of work' => 'Diez categorías, un solo cuerpo de trabajo',
        'Everything below was written or recorded by Alan C. Walter. Filter by the theme you need today.' => 'Todo lo que sigue fue escrito o grabado por Alan C. Walter. Filtra por el tema que necesitas hoy.',
        'Article Categories' => 'Categorías de artículos',
        'ALL' => 'TODOS',
        'THE ZONES OF LIFE' => 'LAS ZONAS DE LA VIDA',
        'SPIRITUAL LITERACY' => 'ALFABETIZACIÓN ESPIRITUAL',
        'PRESENCE' => 'PRESENCIA',
        'LOVE' => 'AMOR',
        'TRUTH' => 'VERDAD',
        'RELATIONSHIPS' => 'RELACIONES',
        'KNOWLEDGE' => 'CONOCIMIENTO',
        'INTEGRITY' => 'INTEGRIDAD',
        'MASTERY' => 'MAESTRÍA',
        'Read article' => 'Leer artículo',
        'mins' => 'min',
        'min read' => 'min de lectura',
        'By Alan C. Walter' => 'Por Alan C. Walter',

        // Zone Assessment CTA Block
        'Not sure where to start?' => '¿No estás seguro de por dónde empezar?',
        'Fourteen questions tell you which Zone you operate from, which of the three areas is holding you back, and which book to read first. Free, no card.' => 'Catorce preguntas te indican desde qué Zona operas, cuál de las tres áreas te está frenando y qué libro leer primero. Gratis, sin tarjeta.',
        'Name and email only' => 'Solo nombre y correo electrónico',

        // Newsletter Block
        'Free weekly' => 'Semanal gratuito',
        'The Word of the Week' => 'La Palabra de la Semana',
        'One term from the Knowledgism Dictionary, defined plainly, every Monday. Plus new articles and event dates before they are announced.' => 'Un término del Diccionario de Knowledgism, definido con sencillez, cada lunes. Además de nuevos artículos y fechas de eventos antes de su anuncio.',
        'Subscribe' => 'Suscribirse',
        'I agree to the Privacy Policy. Unsubscribe anytime.' => 'Acepto la Política de Privacidad. Cancela tu suscripción en cualquier momento.',

        // Glossary Page
        'Training glossary' => 'Glosario de entrenamiento',
        'Every word has an exact definition' => 'Cada palabra tiene una definición exacta',
        'Compiled from the Knowledgism Dictionary. This is where the power of knowing begins: if the word is blurry, so is the concept.' => 'Compilado del Diccionario de Knowledgism. Aquí es donde comienza el poder de saber: si la palabra es borrosa, también lo es el concepto.',
        'Search %d terms…' => 'Buscar entre %d términos…',
        'Search terms…' => 'Buscar términos…',
        'Search' => 'Buscar',
        'Showing %d definitions' => 'Mostrando %d definiciones',
        'Showing %d terms' => 'Mostrando %d términos',
        'definition' => 'definición',
        'definitions' => 'definiciones',
        'words' => 'palabras',
        'word' => 'palabra',
        'Letter %s (%d terms)' => 'Letra %s (%d términos)',
        'Jump to letter %s' => 'Ir a la letra %s',
        'Alphabet navigation' => 'Navegación alfabética',
        'No terms found matching "%s"' => 'No se encontraron términos para "%s"',
        'Clear search' => 'Limpiar búsqueda',

        // Zones
        'Red' => 'Roja',
        'Yellow' => 'Amarilla',
        'Amber' => 'Amarilla',
        'Green' => 'Verde',
        'Golden Magic' => 'Magia Dorada',
        'Red Zone' => 'Zona Roja',
        'Yellow Zone' => 'Zona Amarilla',
        'Amber Zone' => 'Zona Amarilla',
        'Green Zone' => 'Zona Verde',
        'Gold Zone' => 'Zona Dorada',
        'The Zones of Life' => 'Las Zonas de la Vida',

        // Footer
        'The written and recorded work of Alan C. Walter.' => 'La obra escrita y grabada de Alan C. Walter.',
        'Follow' => 'Síguenos',
        'Knowledgism' => 'Knowledgism',
        'The Knowledge Era' => 'La Era del Conocimiento',
        'Meet Alan C. Walter' => 'Alan C. Walter',
        'Learn' => 'Aprender',
        'Shop' => 'Tienda',
        'Books in Spanish' => 'Libros en español',
        'Company' => 'Compañía',
        'Contact' => 'Contacto',
        'Privacy policy' => 'Política de privacidad',
        'Cookie policy' => 'Política de cookies',
        'Coaching delivered by ACLC at Letoli Ranch, Texas' => 'Coaching impartido por ACLC en Letoli Ranch, Texas',

        // Cookie Bar
        'We use cookies' => 'Usamos cookies',
        'Necessary cookies keep the site working. Analytics and marketing cookies help us understand what people need. You choose.' => 'Las cookies necesarias mantienen el funcionamiento del sitio. Las cookies de análisis y marketing nos ayudan a entender lo que las personas necesitan. Tú eliges.',
        'Manage' => 'Gestionar',
        'Reject non-essential' => 'Rechazar no esenciales',
        'Accept all' => 'Aceptar todas',
    ];

    return $dict;
}

/**
 * Filter gettext strings for the 'knowledgism' domain when in Spanish
 */
function knowledgism_filter_gettext_spanish($translation, $text, $domain) {
    if ($domain === 'knowledgism' && knowledgism_is_spanish()) {
        $dict = knowledgism_get_spanish_dictionary();
        if (isset($dict[$text])) {
            return $dict[$text];
        }
    }
    return $translation;
}
add_filter('gettext', 'knowledgism_filter_gettext_spanish', 20, 3);
add_filter('ngettext', function($translation, $single, $plural, $number, $domain) {
    if ($domain === 'knowledgism' && knowledgism_is_spanish()) {
        $dict = knowledgism_get_spanish_dictionary();
        if ($number === 1 && isset($dict[$single])) {
            return $dict[$single];
        } elseif ($number !== 1 && isset($dict[$plural])) {
            return $dict[$plural];
        }
    }
    return $translation;
}, 20, 5);
