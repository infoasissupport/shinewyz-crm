<?php
$file = 'public/js/queue.js';
$content = file_get_contents($file);

$oldCode = <<<JS
      var viewJobBtn = e.target.closest('[data-view-job]');
      if (viewJobBtn) {
        window.location.href = '/jobcards?id=' + encodeURIComponent(viewJobBtn.getAttribute('data-view-job'));
        return;
      }

      var target = e.target.closest('[data-bay-action]');
JS;

$newCode = <<<JS
      var tabBtn = e.target.closest('.bay-tab-btn');
      if (tabBtn) {
        var bName = tabBtn.getAttribute('data-bay-name');
        var tIdx = parseInt(tabBtn.getAttribute('data-tab-index'), 10);
        window._bayTabsState[bName] = tIdx;
        loadQueue(); // re-render quickly
        return;
      }

      var viewJobBtn = e.target.closest('[data-view-job]');
      if (viewJobBtn) {
        window.location.href = '/jobcards?id=' + encodeURIComponent(viewJobBtn.getAttribute('data-view-job'));
        return;
      }

      var target = e.target.closest('[data-bay-action]');
JS;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched listener.\n";
