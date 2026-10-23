<?php
$file = 'public/js/common.js';
$content = file_get_contents($file);

$oldCode = <<<JS
  function initWhatsapp() {
    var list = document.getElementById('waList');
    if (!list) return;
    list.innerHTML = '';
JS;

$newCode = <<<JS
  function initWhatsapp() {
    var list = document.getElementById('waList');
    var dot = document.getElementById('waDot');
    if (dot) dot.style.display = WHATSAPP_CHATS.length ? 'block' : 'none';
    if (!list) return;
    list.innerHTML = '';
JS;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched initWhatsapp for dot.\n";
