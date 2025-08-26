<div id="sb_globals" class="sb_content">
  <p class="sb_p py-2"><?=__('_sb_globals_p', [
    'en' => 'Changes on these settings will have an imediate affect in all pages',
    'es' => 'Estas configuraciónes tienen un efecto inmediato en todas las páginas',
    'el' => 'Οι αλλαγές σε αυτές τις ρυθμίσεις θα έχουν άμεση επίδραση σε όλες τις σελίδες'
    ])?></p>
  <ul class="theme-options-list" style="font-size:90%">
    <li onclick="theme_options('<?=Config::get('theme')?>', '<?=__('Header', ['es' => 'Cabeza'])?>', 'header')">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-navbar" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#444444" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><line x1="4" y1="9" x2="20" y2="9" /></svg>
      <span><?=__('Header', ['es' => 'Cabeza'])?></span>
    </li>
    <li id="sb_globals_page" onclick="theme_options('<?=Config::get('theme')?>', '<?=__('Page layout')?>', 'page')">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-navbar" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#444444" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><line x1="15" y1="4" x2="15" y2="20" /></svg>
      <span><?=__('Page layout')?></span>
    </li>
    <li onclick="theme_options('<?=Config::get('theme')?>', '<?=__('Footer', ['es' => 'Piel'])?>', 'footer')">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-navbar" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#444444" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><line x1="4" y1="15" x2="20" y2="15" /></svg>
      <span><?=__('Footer', ['es' => 'Piel'])?></span>
    </li>
    <li onclick="theme_options('<?=Config::get('theme')?>', '<?=__('Notification bar', ['es' => 'Barra de notificación'])?>', 'topbar')">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-circle" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#444444" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 8v4" /><path d="M12 16h.01" /></svg>
      <span><?=__('Notification bar', ['es' => 'Barra de notificación'])?></span>
    </li>
    <li id="sb_globals_fonts" :class="{'blinking-sb-global':selectedFonts==0}" @click="sbGlobalFonts()">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-typography" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#444444" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="20" x2="7" y2="20" /><line x1="14" y1="20" x2="21" y2="20" /><line x1="6.9" y1="15" x2="13.8" y2="15" /><line x1="10.2" y1="6.3" x2="16" y2="20" /><polyline points="5 20 11 4 13 4 20 20" /></svg>
      <span><?=__('Typography')?></span>
    </li>
    <li id="sb_globals_colors" :class="{'blinking-sb-global':selectedColors==0}" @click="sbGlobalColors()">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-palette" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#444444" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21a9 9 0 1 1 0 -18a9 8 0 0 1 9 8a4.5 4 0 0 1 -4.5 4h-2.5a2 2 0 0 0 -1 3.75a1.3 1.3 0 0 1 -1 2.25" /><circle cx="7.5" cy="10.5" r=".5" fill="currentColor" /><circle cx="12" cy="7.5" r=".5" fill="currentColor" /><circle cx="16.5" cy="10.5" r=".5" fill="currentColor" /></svg>
      <span><?=__('Colors')?></span>
    </li>
    <li onclick="theme_options('<?=Config::get('theme')?>', '<?=__('Buttons', ['es' => 'Botones'])?>', 'btn')">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-square" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#444444" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="6" width="16" height="12" rx="2" /></svg>
      <span><?=__('Buttons', ['es' => 'Botones'])?></span>
    </li>
    <li onclick="website_settings()">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#444444" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><circle cx="12" cy="12" r="3" /></svg>
      <span><?=__('Website settings')?></span>
    </li>
    <li onclick="theme_options('<?=Config::get('theme')?>', '<?=__('CSS')?>', 'stylesheet')">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-code-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#444444" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 12h6" /><path d="M12 9v6" /><path d="M6 19a2 2 0 0 1 -2 -2v-4l-1 -1l1 -1v-4a2 2 0 0 1 2 -2" /><path d="M18 19a2 2 0 0 0 2 -2v-4l1 -1l-1 -1v-4a2 2 0 0 0 -2 -2" /></svg>
      <span><?=__('CSS')?></span>
    </li>
    <li onclick="theme_options('<?=Config::get('theme')?>', '<?=__('WhatsApp Widget')?>', 'wa')">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-whatsapp" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#444444" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" /><path d="M9 10a0.5 .5 0 0 0 1 0v-1a0.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a0.5 .5 0 0 0 0 -1h-1a0.5 .5 0 0 0 0 1" /></svg>
      <span><?=__('WhatsApp Widget')?></span>
    </li>
<?php if (Config::inPackages('embed-code')) : ?>
    <li onclick="addon_options('embed-code', '<?=__('Snippets')?>', 'btn')">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-code" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#444444" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="7 8 3 12 7 16" /><polyline points="17 8 21 12 17 16" /><line x1="14" y1="4" x2="10" y2="20" /></svg>
      <span><?=__('Snippets')?></span>
    </li>
<?php endif; ?>
<?php foreach (Config::getList('sb-globals') as $sbg) :
    if (Config::packages($sbg['package'])) : ?>
    <li onclick="addon_options('<?=$sbg['package']?>', '<?=__($sbg['label'])?>', 'btn')">
            <?=$sbg['icon']?>
      <span><?=__($sbg['label'])?></span>
    </li>
    <?php endif;
endforeach; ?>

  </ul>
</div>

