<?php
$file = 'public/js/manager.js';
$content = file_get_contents($file);

$oldCode = <<<JS
        if (res.ok) {
          showToast('Wash job completed successfully!', 'ti-circle-check');
          closeModal('jobcardDetailsModal');
          var responseData = await res.json().catch(function () { return null; });
          await loadAllData();
JS;

$newCode = <<<JS
        if (res.ok) {
          showToast('Wash job completed successfully!', 'ti-circle-check');
          closeModal('jobcardDetailsModal');
          var responseData = await res.json().catch(function () { return null; });
          await loadAllData();
          if (window.SW && window.SW.loadGlobal) window.SW.loadGlobal();
JS;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched manager.js\n";
