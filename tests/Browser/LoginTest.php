<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    public function test_login_otomatis(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->waitForText('SDM', 10);

            // Mengetik Email dengan efek Typewriter
            $this->ketikPelan($browser, 'email_institusi', 'admin@telkomuniversity.ac.id');

            $browser->pause(500);

            // Mengetik Password dengan efek Typewriter
            $this->ketikPelan($browser, 'password', 'password123');

            $browser->pause(1000)
                    ->check('remember')
                    ->press('LOG IN')
                    ->pause(3000)
                    ->assertPathIs('/'); 
        });
    }

    /**
     * Fungsi Helper Custom untuk efek mengetik
     */
    private function ketikPelan($browser, $name, $text)
    {
        // 1. Klik dulu elemennya agar fokus
        $browser->click("input[name='{$name}']");
        
        // 2. Bersihkan input jika ada isinya
        $browser->keys("input[name='{$name}']", ['{control}', 'a'], '{backspace}');

        // 3. Ketik per karakter
        foreach (str_split($text) as $char) {
            $browser->append($name, $char);
            
            // Jeda acak antara 50ms - 150ms agar terlihat natural
            $browser->pause(random_int(50, 150)); 
        }
    }
}