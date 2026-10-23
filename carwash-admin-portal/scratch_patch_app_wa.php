<?php
$file = 'resources/views/layouts/app.blade.php';
$content = file_get_contents($file);

$oldHtml = <<<HTML
      <div class="dropdown" id="notifDropdown">
        <div class="icon-btn" id="notifToggle"><i class="ti ti-bell"></i><span class="dot" id="notifDot"></span></div>
        <div class="dropdown-menu dropdown-menu-wide" id="notifMenu">
          <div class="dropdown-header">Notifications<span class="dropdown-header-actions"><span class="link" id="markAllRead">Mark all read</span><span class="link link-danger" id="clearAllNotifs">Clear</span></span></div>
          <div class="notif-list" id="notifList"></div>
        </div>
      </div>
HTML;

$newHtml = <<<HTML
      <div class="dropdown" id="waDropdown">
        <div class="icon-btn" id="waToggle"><i class="ti ti-message-circle"></i><span class="dot" id="waDot" style="display:none;background:red;"></span></div>
        <div class="dropdown-menu dropdown-menu-wide" id="waMenu">
          <div class="dropdown-header">WhatsApp CRM<span class="dropdown-header-actions"></span></div>
          <div class="notif-list" id="waList" style="max-height:300px;overflow-y:auto;padding:8px;"></div>
        </div>
      </div>
      <div class="dropdown" id="notifDropdown">
        <div class="icon-btn" id="notifToggle"><i class="ti ti-bell"></i><span class="dot" id="notifDot"></span></div>
        <div class="dropdown-menu dropdown-menu-wide" id="notifMenu">
          <div class="dropdown-header">Notifications<span class="dropdown-header-actions"><span class="link" id="markAllRead">Mark all read</span><span class="link link-danger" id="clearAllNotifs">Clear</span></span></div>
          <div class="notif-list" id="notifList"></div>
        </div>
      </div>
HTML;

$content = str_replace($oldHtml, $newHtml, $content);
file_put_contents($file, $content);
echo "Injected WhatsApp dropdown.\n";
