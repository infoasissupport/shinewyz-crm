<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$oldCode = <<<PHP
    public function updateVehicle(Request \$request, \$id)
    {
        \$validated = \$request->validate([
            'brand' => 'required|string|max:255',
            'colour' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'fuel' => 'required|string|max:255',
        ]);
PHP;

$newCode = <<<PHP
    public function updateVehicle(Request \$request, \$id)
    {
        \$validated = \$request->validate([
            'brand' => 'required|string|max:255',
            'colour' => 'nullable|string|max:255',
            'type' => 'required|string|max:255',
            'fuel' => 'nullable|string|max:255',
            'last_wash' => 'nullable|string|max:255',
        ]);
PHP;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Patched vehicle update API.\n";
