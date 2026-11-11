<?php
/**
 * Convocatoria Artística "Abrigando Corazones".
 *
 * Fuente: afiche oficial del Consejo Provincial de la Juventud de Tacna (CPJ)
 * y texto de difusión de la campaña. Todo el contenido editable vive aquí para
 * que actualizar la convocatoria no obligue a tocar la vista.
 */

return [
    'is_open' => true,

    // Cierre de inscripciones. Zona horaria de la app: America/Lima.
    'deadline' => '2026-09-07 23:59:59',
    'deadline_label' => 'Lunes 07 de septiembre',
    'deadline_short' => '07 SET',

    'form_url' => 'https://forms.gle/Ab1MarSTNxaoifhm7',

    /*
     * Coordinacion de la convocatoria.
     *
     * Es un numero distinto al general de la campana: quien pregunta por la
     * convocatoria artistica llega directo a quien la coordina, sin rebotes.
     * Para cambiarlo basta editar aqui; la vista lo toma de este archivo y
     * arma sola el enlace de WhatsApp con el mensaje ya escrito.
     */
    'coordinator' => [
        'label' => 'Coordinación de la convocatoria',
        'phone' => '+51 972 034 003',
        'message' => 'Hola, vengo de la convocatoria artística de Abrigando Corazones. Me gustaría participar como voluntario, ¿me puedes dar más información?',
    ],

    'location' => 'Comunidades altoandinas de Candarave, Tacna',
    'organizer' => 'Consejo Provincial de la Juventud de Tacna',

    'lead' => '¿Cantas, bailas, actúas o tienes un talento especial que quieres compartir? Esta es tu oportunidad de formar parte de una jornada artística solidaria que llevará alegría, esperanza y apoyo a las comunidades altoandinas de Candarave, Tacna.',

    'sub_lead' => 'Buscamos artistas que quieran unir su talento a una causa que transforma realidades.',

    /**
     * Categorías del afiche. El icono debe existir en site_icon() (header.php).
     */
    'categories' => [
        [
            'slug' => 'cantantes',
            'label' => 'Cantantes',
            'text' => 'Solistas, dúos y bandas. Voz en vivo o con pista, en cualquier género.',
            'icon' => 'mic',
            'tone' => 'ember',
        ],
        [
            'slug' => 'danza',
            'label' => 'Grupos de danza y folclore',
            'text' => 'Elencos y agrupaciones de danza moderna, urbana, típica o folclórica.',
            'icon' => 'dance',
            'tone' => 'forest',
        ],
        [
            'slug' => 'teatro',
            'label' => 'Actuaciones teatrales',
            'text' => 'Sketches, monólogos, teatro breve, clown y narración escénica.',
            'icon' => 'masks',
            'tone' => 'violet',
        ],
        [
            'slug' => 'cultural',
            'label' => 'Presentaciones culturales o tradicionales',
            'text' => 'Expresiones andinas, música tradicional y puestas en escena de nuestra identidad.',
            'icon' => 'globe',
            'tone' => 'sky',
        ],
        [
            'slug' => 'otros',
            'label' => 'Otros talentos artísticos',
            'text' => 'Magia, circo, poesía, dibujo en vivo, instrumentistas y toda propuesta creativa.',
            'icon' => 'star',
            'tone' => 'gold',
        ],
    ],

    'benefits' => [
        [
            'icon' => 'check',
            'title' => 'Certificado de participación',
            'text' => 'Se entrega certificado a todos los artistas colaboradores de la jornada.',
        ],
        [
            'icon' => 'megaphone',
            'title' => 'Difusión de tu propuesta',
            'text' => 'Tu presentación se comparte en los canales oficiales de la campaña.',
        ],
        [
            'icon' => 'heart-handshake',
            'title' => 'Una causa que transforma',
            'text' => 'Tu arte acompaña la entrega de abrigo a familias de zonas altoandinas.',
        ],
        [
            'icon' => 'users',
            'title' => 'Red de artistas y voluntarios',
            'text' => 'Compartes escenario con organizaciones y voluntarios de toda la región.',
        ],
    ],

    'steps' => [
        [
            'title' => 'Llena el formulario',
            'text' => 'Registra tus datos, tu categoría artística y una breve descripción de tu propuesta.',
        ],
        [
            'title' => 'Te contactamos',
            'text' => 'El equipo de la campaña se comunica contigo para confirmar tu participación.',
        ],
        [
            'title' => 'Coordinamos tu presentación',
            'text' => 'Definimos juntos el orden, la duración y los requerimientos técnicos.',
        ],
        [
            'title' => 'Súbete al escenario',
            'text' => 'Participas en el gran cierre de campaña y recibes tu certificado.',
        ],
    ],

    'faq' => [
        [
            'question' => '¿Tiene algún costo participar?',
            'answer' => 'No. La convocatoria es gratuita y abierta. Es una participación solidaria y voluntaria.',
        ],
        [
            'question' => '¿Puedo inscribir a un grupo completo?',
            'answer' => 'Sí. En el formulario puedes registrar a tu elenco, banda o agrupación indicando el nombre y a la persona que coordina.',
        ],
        [
            'question' => '¿Hay un límite de edad?',
            'answer' => 'No hay límite. Si eres menor de edad, la inscripción debe realizarla o autorizarla tu padre, madre o apoderado.',
        ],
        [
            'question' => '¿Cuánto dura cada presentación?',
            'answer' => 'La duración se coordina con cada artista según el programa del día. Cuéntanos tu propuesta en el formulario y lo definimos juntos.',
        ],
    ],

    /**
     * Afiche oficial. Se busca en images/convocatoria/ con estos nombres; si no
     * existe ninguno, la vista muestra la versión compuesta en HTML/CSS, que es
     * responsive y legible en cualquier pantalla.
     */
    'poster_candidates' => [
        'images/convocatoria/afiche-convocatoria-artistica.webp',
        'images/convocatoria/afiche-convocatoria-artistica.png',
        'images/convocatoria/afiche-convocatoria-artistica.jpg',
        'images/convocatoria/afiche-convocatoria-artistica.jpeg',
    ],
];
