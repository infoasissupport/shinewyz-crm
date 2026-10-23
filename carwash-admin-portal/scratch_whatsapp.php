<?php
$file = 'app/Http/Controllers/CarwashApiController.php';
$content = file_get_contents($file);

$oldCode = <<<PHP
        if ((\$validated['stage'] ?? null) === 'completed' && ! \$wasCompleted) {
            \$billingInvoice = \$this->createBillingInvoiceForJobCard(\$freshCard);
        }
PHP;

$newCode = <<<PHP
        if ((\$validated['stage'] ?? null) === 'completed' && ! \$wasCompleted) {
            \$billingInvoice = \$this->createBillingInvoiceForJobCard(\$freshCard);
            
            // Send WhatsApp message if phone is provided
            if (filled(\$freshCard->phone) && class_exists('\App\Models\WhatsappChat')) {
                \$customerName = \$freshCard->customer ?: 'Customer';
                \$initials = \Illuminate\Support\Str::of(\$customerName)->trim()->explode(' ')->map(fn (\$part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(\$part, 0, 1)))->join('');
                
                \App\Models\WhatsappChat::create([
                    'user_id' => \$userId,
                    'name' => \$customerName,
                    'customer_id' => null,
                    'branch_id' => \$freshCard->branch_id,
                    'initials' => \$initials,
                    'msg' => 'Hello ' . \$customerName . ', your vehicle (' . \$freshCard->vehicle . ') has been professionally washed and is ready for pickup. Thank you for choosing Shinewise!',
                    'time' => 'just now',
                    'phone' => \$freshCard->phone,
                    'opt_in' => true,
                ]);
            }
        }
PHP;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "Injected Whatsapp message.\n";
