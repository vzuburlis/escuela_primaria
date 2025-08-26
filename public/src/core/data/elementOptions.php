<?php

return [
  [
    'label' => __('Basic', ['es' => 'Básicos']),
    'options' => [
      ['g' => 'extra-basic'],
      ['g' => 'img-src',
        'label' => __('Image', ['es' => 'Imagen','el' => 'Εικονα'])],
      ['g' => 'svg'],
      ['g' => 'gallery', 'label' => __('Gallery', ['es' => 'Galería','el' => 'Eiκονες'])],
      ['g' => 'prop','attr' => 'alt', 'label' => __('Alt text', ['es' => 'Texto alt'])],
      ['g' => 'prop','attr' => 'name', 'label' => __('Name', ['es' => 'Nombre']), 'placeholder' => __('required value', ['es' => 'valor requerido']),'required' => true, 'list'=> 'field_name'],
      ['g' => 'data','attr' => 'i_name', 'label' => __('Name', ['es' => 'Nombre']), 'placeholder' => __('required value', ['es' => 'valor requerido']),'required' => true, 'list'=> 'field_name'],
      ['g' => 'prop','attr' => 'placeholder', 'label' => __('Placeholder', ['es' => 'Marcador'])],
      ['g' => 'datahref','attr' => 'ihref', 'label' => __('URL'), 'in' => 'url'],
      ['g' => 'datahref','attr' => 'href', 'tag' => 'A', 'label' => __('URL'), 'in' => 'url'],
      ['g' => 'data','attr' => 'ip_id', 'label' => __('Widget ID') . ' (<a target=_blank href=./admin/content/widget>' . __('List') . '</a>)'],
      //['g'=>'data','attr'=>'ip_type', 'label'=>__('Placeholder Type')],
      ['g' => 'data','attr' => 'i_height', 'label' => __('Height')],
      ['g' => 'data','attr' => 'i_src', 'label' => __('Source')],
      ['g' => 'data','attr' => 'ms', 'label' => __('Time', ['es' => 'Tiempo']) . ' (ms)'],
      ['g' => 'data','attr' => 'counter', 'label' => __('Counter speed', ['es' => 'Velocidad del contador'])],
      ['g' => 'data','attr' => 'address','in' => 'text', 'label' => __('Address', ['es' => 'Dirección','el' => 'Διευθυνση'])],
      ['g' => 'data','attr' => 'video_url','in' => 'text', 'label' => __('Video URL')],
      ['g' => 'data','attr' => 'video-url','in' => 'text', 'label' => __('Video URL')],
      //['g'=>'data','attr'=>'action','list'=>['webform/submit', 'crm/addEmail'], 'label'=>__('Action', ['es'=>'Acción'])],
      ['g' => 'action','attr' => 'action', 'label' => __('Action', ['es' => 'Acción'])],
      ['g' => 'datahref','attr' => 'callback_url', 'label' => __('Callback URL', ['es' => 'URL de devolución'])],
      ['g' => 'data','attr' => 'url_params','label' => __('URL Parameters', ['es' => 'Parametros URL'])],
      ['g' => 'data','attr' => 'msg','label' => __('Success text', ['es' => 'Texto de exito'])],
      ['g' => 'html'],
      ['g' => 'audio'],
      ['g' => 'ul'],
      ['g' => 'svg-size', 'label' => __('Size', ['es' => 'Tamaño'])],
      ['g' => 'style','field' => 'basic-color','attr' => 'color','in' => 'color', 'ci' => 0, 'label' => __('Color', ['el' => 'Χρωμα'])],
      ['g' => 'fa', 'label' => __('FA icon', ['es' => 'FA icon'])],
      ['g' => 'bgimage','attr' => 'backgroundImage','childTag' => 'DIV','label' => __('Image')],
      ['g' => 'bgimage','attr' => 'backgroundImage','childTag' => 'A','label' => __('Image')],
      ['g' => 'options','label' => __('Options'),'common' => true],
      ['g' => 'text-tag','in' => 'text-tag'],
      ['g' => 'style','attr' => 'text-align','in' => 'text-align', 'label' => __('Text align', ['es' => 'Alineacion de texto'])],
      ['g' => 'style','attr' => 'text-align','in' => 'select',
        'options' => ['','left','right','center','justify'],
      //'options'=>[
      //  ''=>'','text-align-left'=>__('Left'),'text-align-right'=>__('Right'),'text-align-center'=>__('Center'),'text-align-justify'=>__('Justify')],
        'label' => __('Text align', ['es' => 'Alineacion de texto'])],
      ['g' => 'tag-select','attr' => 'text-align','tags' => [
        'P' => __('Paragraph', ['es' => 'Párrafo']), 'H6' => __('Heading 6', ['es' => 'Título 6']), 'H5' => __('Heading 5', ['es' => 'Título 5']), 'H4' => __('Heading 4', ['es' => 'Título 4']), 'H3' => __('Heading 3', ['es' => 'Título 3']), 'H2' => __('Heading 2', ['es' => 'Título 2']),
      ], 'label' => __('Text type', ['es' => 'Tipo de texto'])],
      ['g' => 'prop','attr' => 'required','label' => 'Required','in' => 'checkbox','tag' => 'INPUT'],
      ['g' => 'prop','attr' => 'required','label' => 'Required','in' => 'checkbox','tag' => 'SELECT'],
      ['g' => 'prop','attr' => 'rows','label' => 'Rows','in' => 'number','tag' => 'TEXTAREA'],
      ['g' => 'style', 'if' => 'grid', 'in' => 'text','attr' => 'grid-column', 'label' => __('Grid column', ['es' => 'Columna red'])],
      ['g' => 'style', 'if' => 'grid', 'in' => 'text','attr' => 'grid-row', 'label' => __('Grid row', ['es' => 'Fila red'])],
      ['g' => 'style','label' => __('Column width', ['es' => 'Ancho de columna']),'attr' => 'column-width','tag' => 'P','in' => 'select','options' => [
        '','250px','300px','350px','400px','450px','500px',
      ]],
    ],
  ],
  [
    'label' => __('Styling & ID', ['es' => 'Estilo y ID']),
    'options' => [
      ['g' => 'class'],
      ['g' => 'prop','attr' => 'id','label' => 'ID','common' => true],
    ],
    'css' => false,
  ],
  [
    'label' => __('Text', ['es' => 'Texto']),
    'basic' => 3,
    'options' => [
      ['g' => 'style','attr' => 'color','in' => 'color', 'ci' => 0, 'label' => __('Color', ['el' => 'Χρωμα'])],
      ['g' => 'style','attr' => 'font-family','in' => 'font-family',
        'label' => __('Font family', ['es' => 'Fuente'])],
      ['g' => 'style','attr' => 'fontSize','in' => 'range', 'min' => 8, 'max' => 80, 'step' => 1,'u' => 'px',
        'label' => __('Font size', ['es' => 'Tamaño de fuente'])],
      ['g' => 'style','attr' => 'lineHeight','in' => 'range', 'min' => 0, 'max' => 2, 'step' => 0.2, 'u' => '',
        'label' => __('Line height', ['es' => 'Altura de la línea'])],
      ['g' => 'style','attr' => 'font-weight','in' => 'select', 'options' => [
        '','lighter','bolder','normal'
      ], 'label' => __('Text weight', ['es' => 'Peso del texto'])],
      ['g' => 'style','attr' => 'text-transform','in' => 'select', 'options' => [
        '','capitalize','uppercase','lowercase','full-width','full-size-kana'
      ], 'label' => __('Text transform', ['es' => 'Transformación de texto'])],
      ['g' => 'style','attr' => 'textShadow','in' => 'text','placeholder' => '#BBF 1px 0 10px', 'label' => __('Text shadow', ['el' => 'Sombra de texto'])],
      ['g' => 'style','attr' => 'white-space','in' => 'select', 'options' => [
        '','normal','nowrap','pre','pre-wrap','pre-line','break-spaces',
      ], 'label' => __('Espacio en blanco', ['es' => 'Espacio en blanco'])],
      ['g' => 'style','attr' => 'letterSpacing','in' => 'range', 'min' => 0, 'max' => 10, 'step' => 1, 'u' => 'px', 'label' => __('Letter spacing', ['es' => 'Espaciado de letras'])],
      ['g' => 'style','attr' => 'wordSpacing','in' => 'range', 'min' => 0, 'max' => 20, 'step' => 1, 'u' => 'px', 'label' => __('Word spacing', ['es' => 'Espacio entre palabras de letras'])],
      ['g' => 'area', 'attr' => 'columns', 'label' => __('Columns', ['es' => 'Columnas'])],
      ['g' => 'prop', 'attr' => 'dir','in' => 'select', 'options' => [
        '','ltr','rtl',
      ], 'label' => __('Direction', ['es' => 'Dirección'])],
      //['g'=>'style','attr'=>'color-scheme','in'=>'select', 'options'=>[
      //  '','light','dark','light dark','only light'
      //]],
    ],
  ],
  [
    'label' => __('Size', ['es' => 'Tamaño']),
    //'basic'=>6,
    'options' => [
      ['g' => 'style','attr' => 'maxWidth','in' => 'range', 'min' => 0, 'max' => 100, 'u' => '%',
        'label' => __('Max width', ['es' => 'Ancho max','el' => 'Μέγ. πλάτος'])],
      ['g' => 'style','attr' => 'maxHeight','in' => 'range', 'min' => 0, 'max' => 100, 'u' => 'vh',
        'label' => __('Max height', ['es' => 'Altura max','el' => 'Μέγ. ύψος'])],
      ['g' => 'style','attr' => 'minWidth','in' => 'range', 'min' => 0, 'max' => 100, 'u' => '%',
        'label' => __('Min width', ['es' => 'Ancho min','el' => 'Ελάχ. πλάτος'])],
      ['g' => 'style','attr' => 'minHeight','in' => 'range', 'min' => 0, 'max' => 100, 'u' => 'vh',
        'label' => __('Min height', ['es' => 'Altura min','el' => 'Ελάχ. ύψος'])],
      ['g' => 'style','attr' => 'width','in' => 'range', 'min' => 5, 'max' => 400, 'step' => 1, 'u' => 'px',
        'label' => __('Width', ['es' => 'Ancho','el' => 'Mήκος']), 'param' => 'width'],
      ['g' => 'style','attr' => 'height','in' => 'range', 'min' => 20, 'max' => 700, 'step' => 5, 'u' => 'px',
        'label' => __('Height', ['es' => 'Altura','el' => 'Υψος']), 'param' => 'height'],
      ['g' => 'style','attr' => 'aspect-ratio','in' => 'text', //'placeholder'=>'2/1',
      'label' => __('Aspect ratio', ['es' => 'Relación de aspecto'])],
    ],
  ],
  [
    'label' => __('Spacing', ['es' => 'Espaciado']),
    'basic' => 3,
    'options' => [
      ['g' => 'style','attr' => 'padding','in' => 'range', 'min' => 0, 'max' => 40, 'u' => 'px',
        'label' => __('Padding')],
        ['g' => 'style','attr' => 'margin-left','in' => 'range', 'min' => -80, 'max' => 80, 'u' => 'px',
        'label' => __('Margin left', ['es' => 'Margen izquierdo'])],
      ['g' => 'style','attr' => 'margin-top','in' => 'range', 'min' => -80, 'max' => 80, 'u' => 'px',
        'label' => __('Margin top', ['es' => 'Margen superior'])],
      ['g' => 'style','attr' => 'margin-right','in' => 'range', 'min' => -80, 'max' => 80, 'u' => 'px',
        'label' => __('Margin right', ['es' => 'Margen derecho'])],
      ['g' => 'style','attr' => 'margin-bottom','in' => 'range', 'min' => -80, 'max' => 80, 'u' => 'px',
        'label' => __('Margin bottom', ['es' => 'Margen abajo'])],
      ['g' => 'style','attr' => 'padding','in' => 'text', 'label' => __('Padding')],
      ['g' => 'style','attr' => 'margin','in' => 'text', 'label' => __('Margin')],
    ],
  ],
  [
    'label' => __('Background', ['es' => 'Fondo']),
    'basic' => 5,
    'options' => [
      ['g' => 'class','prefix' => 'bg-',
        'label' => __('Background color', ['es' => 'Color de fondo'])],
      ['g' => 'style','attr' => 'backgroundColor','in' => 'color', 'ci' => 1,
        'label' => __('Background color', ['es' => 'Color de fondo'])],
      ['g' => 'bgimage','attr' => 'backgroundImage'],
      ['g' => 'style','attr' => 'backgroundSize','in' => 'select', 'options' => [
        '','cover','contain','50%','auto'
      ], 'label' => __('Background size', ['es' => 'Tamaño de fondo'])],
      ['g' => 'style','attr' => 'backgroundPosition','in' => 'select', 'options' => [
        '','top','bottom','left','right','center','center 20%','center 80%','20% center','80% center'
      ], 'label' => __('Background position', ['es' => 'Posición de fondo'])],
      ['g' => 'style','attr' => 'backgroundRepeat','in' => 'select', 'options' => [
        '','no-repeat','repeat','repeat-x','repeat-y','space','round'
      ], 'label' => __('Background repeat', ['es' => 'Repetir fondo'])],
      ['g' => 'style','attr' => 'backgroundAttachment','in' => 'select', 'options' => [
        '','scroll','fixed','local'
      ], 'label' => __('Background attachment', ['es' => 'Adjunto de fondo'])],
      ['g' => 'style','attr' => 'backgroundClip','in' => 'select', 'options' => [
        '','border-box','padding-box','content-box','text'
      ], 'label' => __('Background clip', ['es' => 'Clip de fondo'])],
      ['g' => 'style','attr' => 'background','in' => 'text',
        'label' => __('Background')],
    ],
  ],
  [
    'label' => __('Border', ['es' => 'Borde']),
    'basic' => 3,
    'options' => [
      ['g' => 'style','attr' => 'borderColor','in' => 'color', 'ci' => 2, 'label' => __('Border color', ['es' => 'Color de borde'])],
      ['g' => 'style','attr' => 'borderWidth','in' => 'range', 'min' => 0, 'max' => 8, 'u' => 'px', 'label' => __('Border width', ['es' => 'Ancho de borde'])],
      ['g' => 'style','attr' => 'borderRadius','in' => 'range', 'min' => 0, 'max' => 80, 'step' => 1, 'u' => 'px', 'label' => __('Border radius', ['es' => 'Radio de borde'])],
      ['g' => 'style','attr' => 'borderStyle','in' => 'select', 'options' => [
        '','solid','dashed','dotted','double','groove'
      ], 'label' => __('Border style', ['es' => 'Estilo de fondo'])],
      ['g' => 'style','attr' => 'box-shadow','in' => 'text', 'label' => __('Box shadow', ['es' => 'Sombra de la caja'])],
      ['g' => 'style','attr' => 'shape-outside','in' => 'select', 'options' => [
        '','circle(50%)','ellipse','margin-box','content-box','border-box','padding-box'
      ], 'label' => __('Shape outside', ['es' => 'Forma exterior'])],
      ['g' => 'clip-path'],
      ['g' => 'style','attr' => 'border-top-left-radius','in' => 'range', 'min' => 0, 'max' => 60, 'step' => 1, 'u' => 'px', 'label' => __('Top left radius', ['es' => 'Radio arriba izquerda'])],
      ['g' => 'style','attr' => 'border-top-right-radius','in' => 'range', 'min' => 0, 'max' => 60, 'step' => 1, 'u' => 'px', 'label' => __('Top right radius', ['es' => 'Radio arriba derecha'])],
      ['g' => 'style','attr' => 'border-bottom-left-radius','in' => 'range', 'min' => 0, 'max' => 60, 'step' => 1, 'u' => 'px', 'label' => __('Bottom left radius', ['es' => 'Radio abajo izquerda'])],
      ['g' => 'style','attr' => 'border-bottom-right-radius','in' => 'range', 'min' => 0, 'max' => 60, 'step' => 1, 'u' => 'px', 'label' => __('Bottom right radius', ['es' => 'Radio abajo derecha'])],
      ['g' => 'style','attr' => 'border','in' => 'text',
        'label' => __('Border')],
    ],
  ],
  [
    'label' => __('Display', ['es' => 'Mostrar']),
    'options' => [
      ['g' => 'view-display'],
      ['g' => 'style','attr' => 'display','in' => 'select', 'options' => [
        '','block','inline','inline-block','flex','inline-flex','grid','inline-grid','flow-root'
      ], 'label' => __('Display', ['es' => 'Mostrar'])],
      ['g' => 'style','attr' => 'opacity','in' => 'range', 'min' => 0, 'max' => 1, 'step' => 0.2, 'u' => '',
        'label' => __('Opacity', ['es' => 'Opacidad'])],
      ['g' => 'style','attr' => 'float','in' => 'select', 'options' => [
        '','left','right','none','inherit'
      ],  'u' => ' 0.5s', 'label' => __('Float', ['es' => 'Flotar'])],
      ['g' => 'style','attr' => 'overflow','in' => 'select', 'options' => [
        '','visible','hidden','scroll','auto'
      ],  'u' => ' 0.5s', 'label' => __('Overflow', ['es' => 'Desbordamiento'])],
      ['g' => 'style','attr' => 'z-index', 'in' => 'number','common' => true, 'label' => __('Z-index', ['es' => 'Índice Z'])],
    ],
  ],
  [
    'label' => __('Animation', ['es' => 'Animación']),
    'basic' => 4,
    'options' => [
      ['g' => 'style','attr' => 'animationName','in' => 'select','noemail' => true, 'options' => [
        '','move-left','move-right','move-up','move-down','fade-in','fade-out','expand','beat'
      ], 'label' => __('Animation', ['es' => 'Animación'])],
      ['g' => 'style','attr' => 'animationDuration','in' => 'range', 'min' => 0.1, 'max' => 6, 'step' => 0.1, 'u' => 's',
        'label' => __('Animation duration', ['es' => 'Duracion','el' => 'Animation time'])],
      ['g' => 'style','attr' => 'animationDelay','in' => 'range', 'min' => 0, 'max' => 10, 'step' => 1, 'u' => 's',
        'label' => __('Animation delay', ['es' => 'Tardanza','el' => 'Animation delay'])],
      ['g' => 'style','attr' => 'animationIterationCount','in' => 'select', 'options' => [
        '','1','2','3','infinite'
      ], 'label' => __('Iterations', ['es' => 'Iterationes'])],
      ['g' => 'style','attr' => 'animationTimingFuntion','in' => 'select', 'options' => [
        '','ease','linear','ease-in','ease-out','ease-in-out'
      ], 'label' => __('Speed curve', ['es' => 'Curva de velocidad'])],
    ],
  ],
  [
    'label' => __('Mask', ['es' => 'Máscara']),
    'options' => [
      ['g' => 'maskimage','attr' => 'maskImage', 'label' => __('Mask image', ['es' => 'Máscara de imagen'])],
      ['g' => 'style','attr' => 'maskSize','in' => 'select', 'options' => [
        '','cover','contain','100% 100%'
      ], 'label' => __('Mask size', ['es' => 'Tamaño de máscara'])],
      ['g' => 'style','attr' => 'maskRepeat','in' => 'select', 'options' => [
        '','no-repeat','repeat-x','repeat-y'
      ], 'label' => __('Repeat', ['es' => 'Repetir'])],
      ['g' => 'style','attr' => 'maskPosition','in' => 'select', 'options' => [
        '','top','bottom','left','right','center','top left','top right','bottom left','bottom right'
      ], 'label' => __('Position', ['es' => 'Posición'])],
      //https://developer.mozilla.org/en-US/docs/Web/CSS/mask-border
    ],
  ],
  [
    'label' => __('Position', ['es' => 'Posición']),
    'tag' => 'position',
    'options' => [
      ['g' => 'style','attr' => 'position','in' => 'select', 'options' => [
        '','absolute','fixed','relative','static'
      ], 'label' => __('Position', ['es' => 'Posición'])],
      ['g' => 'style','attr' => 'left','in' => 'range', 'min' => 0, 'max' => 100, 'step' => 1, 'u' => '%',
        'label' => __('Left', ['es' => 'Izquierda']),'if' => 'absolutePos'],
      ['g' => 'style','attr' => 'right','in' => 'range', 'min' => 0, 'max' => 100, 'step' => 1, 'u' => '%',
        'label' => __('Right', ['es' => 'Derecha']),'if' => 'absolutePos'],
      ['g' => 'style','attr' => 'top','in' => 'range', 'min' => 0, 'max' => 100, 'step' => 1, 'u' => '%',
        'label' => __('Top', ['es' => 'Arriba']),'if' => 'absolutePos'],
      ['g' => 'style','attr' => 'bottom','in' => 'range', 'min' => 0, 'max' => 100, 'step' => 1, 'u' => '%',
        'label' => __('Bottom', ['es' => 'Abajo']),'if' => 'absolutePos'],
      ['g' => 'style','attr' => 'width','in' => 'range', 'min' => 1, 'max' => 100, 'step' => 1, 'u' => '%',
        'label' => __('Width', ['es' => 'Ancho','el' => 'Mήκος']),'if' => 'absolutePos'],
      ['g' => 'style','attr' => 'height','in' => 'range', 'min' => 1, 'max' => 100, 'step' => 1, 'u' => '%',
        'label' => __('Height', ['es' => 'Altura','el' => 'Υψος']),'if' => 'absolutePos'],
    ],
  ],
  [
    'label' => __('Transform', ['es' => 'Transformacion']),
    'options' => [
      ['g' => 'transform','attr' => 'rotate', 'min' => -0.5, 'max' => 0.5, 'step' => 0.01, 'u' => 'turn',
        'label' => __('Rotate', ['es' => 'Rotar'])],
      ['g' => 'transform','attr' => 'scale', 'min' => 0, 'max' => 3, 'step' => 0.2, 'u' => '',
        'label' => __('Scale', ['es' => 'Escalar'])],
      ['g' => 'transform','attr' => 'translateX', 'min' => -50, 'max' => 50, 'step' => 2, 'u' => '%',
        'label' => __('Translate X', ['es' => 'Mover X'])],
      ['g' => 'transform','attr' => 'translateY', 'min' => -50, 'max' => 50, 'step' => 2, 'u' => '%',
        'label' => __('Translate Y', ['es' => 'Mover Y'])],
      ['g' => 'transform','attr' => 'skew', 'min' => -50, 'max' => 50, 'step' => 2, 'u' => 'deg',
        'label' => __('Skew', ['es' => 'Sesgar'])],
    ],
  ],
//  [
//  'label'=>__('Events', ['es'=>'Eventos']),
//    'options'=>[
//      ['g'=>'prop','attr'=>'onclick','common'=>true],
//      ['g'=>'prop','attr'=>'oninput','common'=>true],
//    ],
//  ],
];
