<?php
$file = 'public/js/jobcards.js';
$content = file_get_contents($file);

$oldCode = <<<JS
          await saveCard(card);
          window.SW.closeModal('jobCardModal');
          window.SW.showToast('#' + card.id + ' moved to ' + STAGE_LABEL[card.stage]);
          renderTable();
JS;

$newCode = <<<JS
          await saveCard(card);
          window.SW.closeModal('jobCardModal');
          window.SW.showToast('#' + card.id + ' moved to ' + STAGE_LABEL[card.stage]);
          renderTable();
          if (card.stage === 'completed' && window.SW && window.SW.loadGlobal) {
            window.SW.loadGlobal();
          }
JS;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Added loadGlobal to jobcards.js\n";
