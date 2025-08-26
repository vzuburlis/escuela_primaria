<!--div id="tourblanket" style="background:rgba(250,250,250,.4);position:fixed;left:0;right:0;top:0;placement:0"></div-->
<link rel="stylesheet" href="assets/core/vue-tour/vue-tour.css">
<style>
:root{
  --tour-color: #111111;
  --tour-background: #d0f9fb;
  --tour-btn-border: #115F69;
}
#tourapp{
  position: fixed;
  top: 0;
  left:0;
  right: 0;
  bottom: 0;
  z-index: 9999990;
  pointer-events:none;
  font: 15px Arial;
  /*background: #00000050;*/
}
.v-tour{
  /*transform:translate(50px,-40px)*/
}
.v-step,.v-step__button{
  color:var(--tour-color)!important;
  border-color:var(--tour-color)!important;
  background:var(--tour-background)!important;
}
.v-step__button-next{
  background:var(--tour-btn-border) !important;
  color :#ffffff!important;
}
.v-step .v-step__arrow[data-v-7c9c03f0]{
  border-color:var(--tour-background);
}
.v-step .v-step__button{
  opacity:0.8;
  border-color:var(--tour-btn-border)!important;
}
.v-step .v-step__button:hover{
  opacity:1
}

</style>
<div id="tourapp">
    <v-tour name="myTour" :steps="steps" :callbacks="myCallbacks"></v-tour>
    <div class="vtour0" style="position:fixed;right:50%;transform:translateX(50%);top:60%"></div>
</div>

<script src="assets/core/vue-tour/vue-tour.umd.min.js"></script>
<script>


setTimeout(function () {

tourapp = new Vue({
  el: "#tourapp",
  data: {
    steps: [
      {
        target: '.vtour0',  // We're using document.querySelector() under the hood
        content: '<?=__('Welcome to page builder', [
          'es' => 'Bienvenid@ en el editor de paginas'
        ])?>',
        params: {
          placement: 'top' // Any valid Popper.js placement. See https://popper.js.org/popper-documentation.html#Popper.placements
        }
      },
      {
        target: '.header-container>#headerlogo',
        content: '<?=__('Select the title o logo for your website', [
          'es' => 'Selecciona el título o logotipo de tu sitio web'
        ])?>',
        params: {
          placement: 'top'
        }
      },
      {
        target: '.header-container>#nav',
        content: '<?=__('Select the menu to configure it', [
          'es' => 'Selecciona  el menú para configurarlo'
        ])?>',
        params: {
          placement: 'top'
        }
      },
      {
        target: '#sb_tab1',
        content: '<?=__('Change the website\'s global values from this list of tools', [
          'es' => 'Cambia los valores globales de tu sitio desde esta barra de herramientas'
        ])?>',
        params: {
          placement: 'left'
        }
      },
      {
        target: '#sb_globals_fonts',
        content: '<?=__('Here you can select combinations of preset fonts or customize them', [
          'es' => 'Aquí podrás seleccionar combinaciones de tipografías preestablecidas o personalizadas'
        ])?>',
        params: {
          placement: 'left'
        }
      },
      {
        target: '#sb_globals_colors',
        content: '<?=__('Select the color palette that best represents the values ​​and personality of your brand', [
          'es' => 'Selecciona la paleta de colores que represente mejor los valores y personalidad de tu marca'
        ])?>',
        params: {
          placement: 'left'
        }
      },
      {
        target: '#sb_globals_page',
        content: '<?=__('You can also modify the background of your website with a new color or image', [
          'es' => 'También puedes modificar el fondo de tu sitio web con un color nuevo o imagen'
        ])?>',
        params: {
          placement: 'left'
        }
      },
      {
        target: '#sb_tab2',
        content: '<?=__('Inside the toolbar you can also find elements that you can add in the blocks of your site by dragging and dropping them in the desired area', [
          'es' => 'Dentro de la barra de herramientas también puedes encontrar elementos que puedes agregar en los bloques de tu sitio al arrastrarlos y soltarlos en el área deseada'
        ])?>',
        params: {
          placement: 'left'
        }
      },
      {
        target: '.vtour-header-btn',
        content: '<?=__('Edit site menu color and alignment', [
          'es' => 'Edita el color y alineación del menú del sitio'
          ])?>',
        params: {
          placement: 'left'
        }
      },
      {
        target: '.block-edit-btn',
        content: '<?=__('Edit the alignment of the text within the block', [
          'es' => 'Edita la alineación del texto dentro del bloque '
          ])?>',
        params: {
          placement: 'left'
        }
      },
      {
        target: '.block-design-btn',
        content: '<?=__('Modify the section background, borders and add an input animation', [
          'es' => 'Modifica el fondo del sección, los bordes y agrega una animación de entrada'
          ])?>',
        params: {
          placement: 'left'
        }
      },
      {
        target: '.block-swap-btn',
        content: '<?=__('Swap the sections to reorganize the information', [
          'es' => 'Intercambia las secciónes para re organizar la información'
          ])?>',
        params: {
          placement: 'left'
        }
      },
      {
        target: '.block-del-btn',
        content: '<?=__('Elimina el bloque por completo', [
          'es' => 'Elimina el bloque por completo'
          ])?>',
        params: {
          placement: 'left'
        }
      },
      {
        target: '.block-add-btn',
        content: '<?=__('Add a new block', [
          'es' => 'Agrega un nuevo bloque'
          ])?>',
        params: {
          placement: 'bottom'
        }
      },
      {
        target: '.vtour4',
        content: '<?=__('Preview your responsive website on different device sizes', [
          'es' => 'Previsualiza tu sitio web responsivo en distintos tamaños de dispositivos'
        ])?>',
        params: {
          placement: 'bottom'
        }
      },
      {
        target: '.vtour5_',
        content: '<?=__('Returns before the last change made', [
          'es' => 'Regresa antes del último cambio realizado'
        ])?>',
        params: {
          placement: 'bottom'
        }
      },
      {
        target: '.vtour5',
        content: '<?=__('Delete all changes that have not been savede', [
          'es' => 'Elimina todos los cambios que no han sido guardados'
        ])?>',
        params: {
          placement: 'bottom'
        }
      },
      {
        target: '.vtour-public',
        content: '<?=__('View the published website', [
          'es' => 'Visualiza el sitio web publicado'
          ])?>',
        params: {
          placement: 'bottom'
        }
      },
      {
        target: '.vtour-public-btn',
        content: '<?=__('Save and publish all the changes that have been made on the website', [
          'es' => 'Guarda y publica todos los cambios que han sido realizados en la página web'
          ])?>',
        params: {
          placement: 'bottom'
        }
      },
    ],
    myCallbacks: {
      onStop: function(currentStep) {
        clearInterval(tourFixint)
        tourFixPosition(100)
      },
      onNextStep: function(currentStep) {
        if (currentStep==6) {
          sb_tab2.click()
        }
        tourFixPosition(currentStep+1)
      },
      onPreviousStep: function(currentStep) {
        tourFixPosition(currentStep-1)
        if (currentStep==7) {
          sb_tab1.click()
        }
      }
    }
  },
  mounted: function () {
    step = <?=User::meta(Session::userId(), 'pagebuilder-tour') ?? 0?>;
    this.$tours['myTour'].currentStep = step
    //this.$tours['myTour'].start()
    this.$tours['myTour'].options.labels.buttonNext = 'Siguente'//'Proximo'
    this.$tours['myTour'].options.labels.buttonPrevious = 'Anterior'
    this.$tours['myTour'].options.labels.buttonSkip = 'Cerrar'
    this.$tours['myTour'].options.labels.buttonStop = 'Cerrar'
    tourFixPosition(step)
  }
})

}, 200);

tourFixint = setInterval(function(){ window.scrollTo(0, 0); }, 30);

function tourFixPosition(step) {
  if (step>2 && step<8 && document.querySelector('.edit-component-close')) {
    document.querySelector('.edit-component-close').click()
  }
  if (step>2 && step<8 && document.querySelector('.edit-sidebar-close')) {
    document.querySelector('.edit-sidebar-close').click()
  }
  
  document.getElementById('tourapp').style.transform = 'unset'
  if (step>3 && step<8) {
    document.getElementById('tourapp').style.transform = 'translateX(21em)'
  }
  if (step==3) {
    document.getElementById('tourapp').style.transform = 'translateX(15em)'
  }

  g.post('updateTour/pagebuilder-tour', '&step='+step, function(data) {});
}

</script>
