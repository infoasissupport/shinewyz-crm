<?php
$file = 'public/js/queue.js';
$content = file_get_contents($file);

// Replace activeBays logic
$oldLogic = <<<JS
      var activeBays = {};
      var bayIdByName = {};
      bays.forEach(function (bay) {
        activeBays[bay.name] = null;
        bayIdByName[bay.name] = bay.id;
      });

      jobcards.forEach(function (card) {
        if (card.stage !== 'completed' && card.stage !== 'cancelled' && activeBays[card.bay] === null) {
          activeBays[card.bay] = card;
        }
      });
JS;

$newLogic = <<<JS
      window._bayTabsState = window._bayTabsState || {};
      
      var activeBays = {};
      var bayIdByName = {};
      bays.forEach(function (bay) {
        activeBays[bay.name] = [];
        bayIdByName[bay.name] = bay.id;
      });

      jobcards.forEach(function (card) {
        if (card.stage !== 'completed' && card.stage !== 'cancelled' && activeBays[card.bay] !== undefined) {
          activeBays[card.bay].push(card);
        }
      });
JS;
$content = str_replace($oldLogic, $newLogic, $content);

// Replace bay sorting logic
$oldSort = <<<JS
        if (baySortMode !== 'default') {
          bayNames = bayNames.slice().sort(function (a, b) {
            return bayRank(activeBays[a], baySortMode) - bayRank(activeBays[b], baySortMode);
          });
        }
JS;

$newSort = <<<JS
        if (baySortMode !== 'default') {
          bayNames = bayNames.slice().sort(function (a, b) {
            var cA = activeBays[a].length ? activeBays[a][0] : null;
            var cB = activeBays[b].length ? activeBays[b][0] : null;
            return bayRank(cA, baySortMode) - bayRank(cB, baySortMode);
          });
        }
JS;
$content = str_replace($oldSort, $newSort, $content);

// Replace rendering logic
$oldRenderStart = <<<JS
        bayNames.forEach(function (bayName) {
          var card = activeBays[bayName];
          var bayId = bayIdByName[bayName];
          var el = document.createElement('div');
          el.className = 'card bay-card';
          el.setAttribute('data-bay-id', bayId);
          el.setAttribute('data-bay-name', bayName);
          var bayActionsHtml =
            '<span class="bay-actions">' +
              '<button type="button" class="bay-action-btn delete" data-bay-action="delete-bay" title="Delete bay">' + BAY_TRASH_SVG + '</button>' +
            '</span>';
          if (card) {
            var isWashing = card.stage === 'inprogress';
JS;

$newRenderStart = <<<JS
        bayNames.forEach(function (bayName) {
          var cards = activeBays[bayName];
          if (cards.length) {
            // Sort them so oldest is first (index 0) if desired. The array is currently newest first because of getJobCards order.
            // Let's reverse it so the oldest is first, or keep newest. We will just keep the array as is.
          }
          
          if (window._bayTabsState[bayName] === undefined || window._bayTabsState[bayName] >= cards.length) {
             window._bayTabsState[bayName] = 0;
          }
          var activeIndex = window._bayTabsState[bayName];
          var card = cards.length > 0 ? cards[activeIndex] : null;
          
          var bayId = bayIdByName[bayName];
          var el = document.createElement('div');
          el.className = 'card bay-card';
          el.setAttribute('data-bay-id', bayId);
          el.setAttribute('data-bay-name', bayName);
          var bayActionsHtml =
            '<span class="bay-actions">' +
              '<button type="button" class="bay-action-btn delete" data-bay-action="delete-bay" title="Delete bay">' + BAY_TRASH_SVG + '</button>' +
            '</span>';
            
          if (card) {
            var isWashing = card.stage === 'inprogress';
JS;
$content = str_replace($oldRenderStart, $newRenderStart, $content);

$oldRenderEnd = <<<JS
              '<div class="bay-meta">' +
                '<span class="bay-meta-item"><i class="ti ti-calendar-event"></i>Started: ' + escapeHtml(startTimeLabel) + '</span>' +
                '<span class="bay-meta-item"><i class="ti ti-clock-hour-4"></i>' + escapeHtml(elapsedLabel) + '</span>' +
              '</div>' +
              '<button type="button" class="btn view-job-btn" data-view-job="' + escapeHtml(card.id) + '"><i class="ti ti-clipboard-check"></i>View Job Card</button>';
          } else {
JS;

$newRenderEnd = <<<JS
              '<div class="bay-meta">' +
                '<span class="bay-meta-item"><i class="ti ti-calendar-event"></i>Started: ' + escapeHtml(startTimeLabel) + '</span>' +
                '<span class="bay-meta-item"><i class="ti ti-clock-hour-4"></i>' + escapeHtml(elapsedLabel) + '</span>' +
              '</div>' +
              '<button type="button" class="btn view-job-btn" data-view-job="' + escapeHtml(card.id) + '" style="margin-bottom:8px;"><i class="ti ti-clipboard-check"></i>View Job Card</button>';
              
            // Add tabs if multiple
            if (cards.length > 1) {
               var tabsHtml = '<div style="display:flex;gap:4px;overflow-x:auto;padding-bottom:4px;margin-top:4px;">';
               cards.forEach(function(c, idx) {
                  var isActive = idx === activeIndex;
                  var bg = isActive ? 'var(--accent)' : 'var(--bg-3)';
                  var fg = isActive ? '#fff' : 'var(--text-2)';
                  var plate = parseVehicleString(c.vehicle).plate || c.id;
                  tabsHtml += '<button type="button" class="bay-tab-btn" data-bay-name="'+escapeHtml(bayName)+'" data-tab-index="'+idx+'" style="flex:1;font-size:11px;padding:4px 8px;border:none;border-radius:4px;background:'+bg+';color:'+fg+';cursor:pointer;white-space:nowrap;font-weight:600;">' + escapeHtml(plate) + '</button>';
               });
               tabsHtml += '</div>';
               el.innerHTML += tabsHtml;
            }
          } else {
JS;
$content = str_replace($oldRenderEnd, $newRenderEnd, $content);

file_put_contents($file, $content);
echo "Patched rendering.\n";
