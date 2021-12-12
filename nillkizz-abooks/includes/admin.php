<?php
add_action('admin_menu', function () {
  add_menu_page('Настройки плагина аудиокнин', 'ABooks Settings', '', 'abooks-settings', '', '', 79);
});
