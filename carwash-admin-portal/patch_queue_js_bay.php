<?php
$file = 'public/js/queue.js';
$content = file_get_contents($file);

$oldCode = <<<JS
        if (res.ok) {
          window.SW.closeModal('addBayModal');
          window.SW.showToast('Bay added successfully');
          addBayForm.reset();
          loadQueue();
        } else {
          alert('Could not add the bay. Please try again.');
        }
JS;

$newCode = <<<JS
        if (res.ok) {
          window.SW.closeModal('addBayModal');
          window.SW.showToast('Bay added successfully');
          addBayForm.reset();
          loadQueue();
        } else {
          var errData = await res.json().catch(()=>({}));
          alert(errData.message || 'Could not add the bay. Please try again.');
        }
JS;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched queue.js.\n";
